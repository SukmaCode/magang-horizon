<?php

namespace App\Notifications;

use App\Models\MagangAktif;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgreementResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly MagangAktif $magang,
        private readonly string $responseStatus // 'accepted' or 'rejected'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mahasiswa = $this->magang->pendaftaran->mahasiswa->nama_lengkap;

        $mail = (new MailMessage)
            ->greeting("Halo, {$notifiable->username}!");

        if ($this->responseStatus === 'accepted') {
            $mail->subject("Agreement Diterima oleh {$mahasiswa}")
                ->line("Mahasiswa {$mahasiswa} telah menerima dan menandatangani agreement magang.")
                ->line('Proses magang dapat dilanjutkan ke tahap pelaksanaan.')
                ->action('Lihat Agreement', url('/industri/agreement'));
        } else {
            $mail->subject("Agreement Ditolak oleh {$mahasiswa}")
                ->line("Mahasiswa {$mahasiswa} menolak agreement magang.")
                ->line('Alasan: ' . ($this->magang->alasan_tolak_agreement ?? '-'))
                ->line('Anda dapat mengunggah agreement baru jika diperlukan.')
                ->action('Lihat Agreement', url('/industri/agreement'));
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $mahasiswa = $this->magang->pendaftaran->mahasiswa->nama_lengkap;

        $message = $this->responseStatus === 'accepted'
            ? "{$mahasiswa} telah menerima agreement magang"
            : "{$mahasiswa} menolak agreement magang";

        return [
            'type' => 'agreement_response',
            'magang_id' => $this->magang->id,
            'mahasiswa' => $mahasiswa,
            'status' => $this->responseStatus,
            'message' => $message,
        ];
    }
}
