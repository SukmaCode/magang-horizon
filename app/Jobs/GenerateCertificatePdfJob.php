<?php

namespace App\Jobs;

use App\Models\MagangAktif;
use App\Notifications\CertificateReadyNotification;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateCertificatePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private readonly MagangAktif $magang
    ) {}

    public function handle(PdfService $pdfService): void
    {
        $sertifikat = $pdfService->generateCertificate($this->magang);

        // Notify the student
        $mahasiswa = $this->magang->pendaftaran->mahasiswa;
        $mahasiswa->user->notify(new CertificateReadyNotification($sertifikat));
    }
}
