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
        $lastCert = Sertifikat::whereYear('tanggal_terbit', $year)
            ->orderByDesc('id')
            ->first();

        $sequence = $lastCert ? ((int) explode('-', $lastCert->nomor_sertifikat)[2] ?? 0) + 1 : 1;

        return sprintf('CERT-%d-%04d', $year, $sequence);
    }
}
