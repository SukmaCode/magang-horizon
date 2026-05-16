<?php

namespace App\Services;

use App\Models\MagangAktif;
use App\Models\Sertifikat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Generate a certificate PDF for a completed internship.
     */
    public function generateCertificate(MagangAktif $magang): Sertifikat
    {
        $magang->load([
            'pendaftaran.mahasiswa',
            'pendaftaran.industri',
            'penilaian',
            'supervisorKampus',
        ]);

        $mahasiswa = $magang->pendaftaran->mahasiswa;
        $industri = $magang->pendaftaran->industri;
        $penilaian = $magang->penilaian;

        // Generate certificate number
        $certNumber = $this->generateCertificateNumber();

        // Prepare data for the template
        $data = [
            'nomor_sertifikat' => $certNumber,
            'nama_mahasiswa' => $mahasiswa->nama_lengkap,
            'nim' => $mahasiswa->nim,
            'prodi' => $mahasiswa->prodi,
            'nama_perusahaan' => $industri->nama_perusahaan,
            'tanggal_mulai' => $magang->tanggal_mulai?->format('d F Y'),
            'tanggal_selesai' => $magang->tanggal_selesai?->format('d F Y'),
            'nilai_akhir' => $penilaian?->nilai_akhir,
            'tanggal_terbit' => now()->format('d F Y'),
        ];

        // Generate PDF from Blade template
        $pdf = Pdf::loadView('pdf.certificate', $data);
        $pdf->setPaper('A4', 'landscape');

        // Store the PDF
        $filename = "documents/sertifikat/{$magang->id}/{$certNumber}.pdf";
        Storage::disk('private')->put($filename, $pdf->output());

        // Create or update sertifikat record
        $sertifikat = Sertifikat::updateOrCreate(
            ['magang_id' => $magang->id],
            [
                'nomor_sertifikat' => $certNumber,
                'file_sertifikat_path' => $filename,
                'tanggal_terbit' => now()->toDateString(),
            ]
        );

        activity('certificate')
            ->performedOn($sertifikat)
            ->withProperties(['nomor' => $certNumber])
            ->log('Certificate generated');

        return $sertifikat;
    }

    /**
     * Generate a unique certificate number.
     */
    private function generateCertificateNumber(): string
    {
        $year = now()->year;
        $lastCert = Sertifikat::whereNotNull('nomor_sertifikat')
            ->whereYear('tanggal_terbit', $year)
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($lastCert && $lastCert->nomor_sertifikat) {
            $parts = explode('-', $lastCert->nomor_sertifikat);
            if (isset($parts[2])) {
                $sequence = ((int) $parts[2]) + 1;
            }
        }

        return sprintf('CERT-%d-%04d', $year, $sequence);
    }

    /**
     * Generate and save approval letter PDF for a final report.
     */
    public function generateApprovalLetter(\App\Models\LaporanAkhir $laporan, \App\Models\User $user, \App\Models\Dosen $dosen): void
    {
        $mahasiswa = $laporan->magangAktif->pendaftaran->mahasiswa;
        $signatureBase64 = $this->getSignatureBase64($user);

        $pdf = Pdf::loadView('pdf.approval-letter', [
            'mahasiswa_name' => $mahasiswa->nama_lengkap,
            'study_program' => $mahasiswa->prodi ?? 'Program Studi',
            'mentor_name' => $dosen->nama_lengkap ?? $user->name,
            'mentor_signature' => $signatureBase64,
        ]);

        $fileName = 'approval_letters/approval_letter_' . $mahasiswa->nim . '_' . time() . '.pdf';
        Storage::disk('private')->put($fileName, $pdf->output());

        $laporan->update(['approval_letter_file' => $fileName]);
    }

    /**
     * Stream approval letter PDF.
     */
    public function streamApprovalLetter(\App\Models\LaporanAkhir $laporan, \App\Models\User $user, \App\Models\Dosen $dosen)
    {
        $mahasiswa = $laporan->magangAktif->pendaftaran->mahasiswa;
        $signatureBase64 = $this->getSignatureBase64($user);

        $pdf = Pdf::loadView('pdf.approval-letter', [
            'mahasiswa_name' => $mahasiswa->nama_lengkap,
            'study_program' => $mahasiswa->prodi ?? 'Program Studi',
            'mentor_name' => $dosen->nama_lengkap ?? $user->name,
            'mentor_signature' => $signatureBase64,
        ]);

        return $pdf->stream('Approval_Letter_' . $mahasiswa->nim . '.pdf');
    }

    /**
     * Get signature base64 string.
     */
    private function getSignatureBase64(\App\Models\User $user): ?string
    {
        $signature = $user->signatures()->latest()->first();
        if ($signature && $signature->file_path && Storage::disk('private')->exists($signature->file_path)) {
            $mime = mime_content_type(storage_path('app/private/' . $signature->file_path));
            $data = base64_encode(Storage::disk('private')->get($signature->file_path));
            return 'data:' . $mime . ';base64,' . $data;
        }
        return null;
    }
}

