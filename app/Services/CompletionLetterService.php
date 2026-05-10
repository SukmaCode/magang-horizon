<?php

namespace App\Services;

use App\Enums\StatusSeleksi;
use App\Models\Industri;
use App\Models\MagangAktif;
use App\Models\Sertifikat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CompletionLetterService
{
    /**
     * Get all active magangs for a given industri with completion letter data.
     */
    public function getMagangsForIndustri(Industri $industri): array
    {
        $magangs = MagangAktif::whereHas('pendaftaran', function ($q) use ($industri) {
            $q->where('industri_id', $industri->id)
                ->where('status_seleksi', StatusSeleksi::DITERIMA);
        })
            ->with(['pendaftaran.mahasiswa', 'sertifikat'])
            ->get()
            ->map(fn (MagangAktif $m) => [
                'id' => $m->id,
                'mahasiswa' => [
                    'nama_lengkap' => $m->pendaftaran->mahasiswa->nama_lengkap,
                    'nim' => $m->pendaftaran->mahasiswa->nim,
                    'prodi' => $m->pendaftaran->mahasiswa->prodi,
                ],
                'tanggal_mulai' => $m->tanggal_mulai?->format('d M Y'),
                'tanggal_selesai' => $m->tanggal_selesai?->format('d M Y'),
                'status' => $m->status_tahapan->value,
                'status_label' => $m->status_tahapan->label(),
                'sertifikat' => $m->sertifikat ? [
                    'id' => $m->sertifikat->id,
                    'posisi_magang' => $m->sertifikat->posisi_magang,
                    'departemen' => $m->sertifikat->departemen,
                    'deskripsi_tugas' => $m->sertifikat->deskripsi_tugas,
                    'komentar_penutup' => $m->sertifikat->komentar_penutup,
                    'has_completion_letter' => $m->sertifikat->posisi_magang !== null,
                ] : null,
            ])
            ->values()
            ->toArray();

        return $magangs;
    }

    /**
     * Store or update the completion letter data for a magang.
     */
    public function storeCompletionLetter(MagangAktif $magangAktif, array $data): Sertifikat
    {
        $sertifikat = Sertifikat::updateOrCreate(
            ['magang_id' => $magangAktif->id],
            [
                'posisi_magang' => $data['posisi_magang'],
                'departemen' => $data['departemen'],
                'deskripsi_tugas' => $data['deskripsi_tugas'],
                'komentar_penutup' => $data['komentar_penutup'],
            ]
        );

        activity('completion_letter')
            ->performedOn($sertifikat)
            ->withProperties([
                'magang_id' => $magangAktif->id,
                'posisi' => $data['posisi_magang'],
            ])
            ->log('Completion letter data saved by industry supervisor');

        return $sertifikat;
    }

    /**
     * Get data prepared for the completion letter PDF view.
     */
    public function getPdfData(int $magangAktifId): array
    {
        $magangAktif = MagangAktif::with([
            'pendaftaran.mahasiswa',
            'pendaftaran.industri',
            'supervisorIndustri.signatures',
            'sertifikat',
        ])->findOrFail($magangAktifId);

        $mahasiswa = $magangAktif->pendaftaran->mahasiswa;
        $industri = $magangAktif->pendaftaran->industri;
        $sertifikat = $magangAktif->sertifikat;
        $supervisorIndustri = $magangAktif->supervisorIndustri;

        // Get industry supervisor signature
        $industriSignature = $supervisorIndustri
            ? $supervisorIndustri->signatures()->latest('signed_at')->first()
            : null;

        return [
            'student_name' => $mahasiswa->nama_lengkap,
            'university_name' => 'Horizon University Indonesia',
            'company_name' => $industri->nama_perusahaan,
            'company_address' => $industri->alamat,
            'position' => $sertifikat?->posisi_magang ?? '-',
            'department' => $sertifikat?->departemen ?? '-',
            'start_date' => $magangAktif->tanggal_mulai?->translatedFormat('jS F Y'),
            'end_date' => $magangAktif->tanggal_selesai?->translatedFormat('jS F Y'),
            'deskripsi_tugas' => $sertifikat?->deskripsi_tugas ?? '',
            'komentar_penutup' => $sertifikat?->komentar_penutup ?? '',
            'location_date' => ($industri->alamat ? explode(',', $industri->alamat)[0] : 'Indonesia') . ', ' . now()->translatedFormat('jS F Y'),
            'supervisorIndustri' => $supervisorIndustri,
            'industriSignatureBase64' => $this->getImageBase64($industriSignature?->signature_image_path),
        ];
    }

    /**
     * Generate completion letter PDF.
     */
    public function generatePdf(int $magangAktifId)
    {
        $data = $this->getPdfData($magangAktifId);

        return Pdf::loadView('pdf.internshipcompletionletter', $data)
            ->setPaper('a4', 'portrait');
    }

    /**
     * Convert storage image path to base64 for dompdf compatibility.
     */
    private function getImageBase64(?string $path): ?string
    {
        if (! $path || ! Storage::disk('private')->exists($path)) {
            return null;
        }

        $image = Storage::disk('private')->get($path);
        $type = pathinfo(Storage::disk('private')->path($path), PATHINFO_EXTENSION);

        return 'data:image/'.$type.';base64,'.base64_encode($image);
    }
}
