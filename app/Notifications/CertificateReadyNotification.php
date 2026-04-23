<?php

namespace App\Notifications;

use App\Models\Sertifikat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Sertifikat $sertifikat
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'certificate_ready',
            'sertifikat_id' => $this->sertifikat->id,
            'nomor_sertifikat' => $this->sertifikat->nomor_sertifikat,
            'message' => 'Sertifikat magang Anda telah diterbitkan.',
        ];
    }
}
