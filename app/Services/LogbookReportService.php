<?php

namespace App\Services;

use App\Models\MagangAktif;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class LogbookReportService
{
    /**
     * Get the full logbook data by Magang Aktif ID.
     */
    public function getLogbookData(int $magangAktifId): array
    {
        $magangAktif = MagangAktif::with([
            'pendaftaran.mahasiswa',
            'supervisorKampus.user.signatures',
            'supervisorIndustri.signatures',
            'industri',
        ])->findOrFail($magangAktifId);

        // Fetch only approved logbooks
        $logbooks = $magangAktif->logbooks()
            ->approved()
            ->orderBy('tanggal_waktu', 'asc')
            ->get();

        // Get latest signatures
        $dosenUser = $magangAktif->supervisorKampus?->user;
        $industriUser = $magangAktif->supervisorIndustri;

        $dosenSignature = $dosenUser ? $dosenUser->signatures()->latest('signed_at')->first() : null;
        $industriSignature = $industriUser ? $industriUser->signatures()->latest('signed_at')->first() : null;

        return [
            'magangAktif' => $magangAktif,
            'mahasiswa' => $magangAktif->mahasiswa,
            'industri' => $magangAktif->industri,
            'dosen' => $magangAktif->supervisorKampus,
            'supervisorIndustri' => $industriUser,
            'logbooks' => $logbooks,
            'dosenSignature' => $dosenSignature,
            'industriSignature' => $industriSignature,
        ];
    }

    /**
     * Get data prepared for PDF view (includes base64 images).
     */
    public function getPdfData(int $magangAktifId): array
    {
        $data = $this->getLogbookData($magangAktifId);

        // Process images to base64 so DOMPDF can read them correctly from storage
        $data['dosenSignatureBase64'] = $this->getImageBase64($data['dosenSignature']?->signature_image_path);
        $data['industriSignatureBase64'] = $this->getImageBase64($data['industriSignature']?->signature_image_path);

        $data['periode'] = \App\Models\Periode::where('is_active', true)->first();

        return $data;
    }

    /**
     * Generate PDF using dompdf.
     */
    public function generatePdf(int $magangAktifId)
    {
        $data = $this->getPdfData($magangAktifId);

        $pdf = Pdf::loadView('pdf.logbook', $data)->setPaper('a4', 'portrait');

        return $pdf;
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
