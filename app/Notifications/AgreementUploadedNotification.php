<?php

namespace App\Notifications;

use App\Models\MagangAktif;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgreementUploadedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly MagangAktif $magang
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $industri = $this->magang->pendaftaran->industri->nama_perusahaan;

        return (new MailMessage)
            ->subject("Agreement Magang Baru dari {$industri}")
            ->greeting("Halo, {$notifiable->username}!")
            ->line("Supervisor Industri dari {$industri} telah mengunggah dokumen agreement magang untuk Anda.")
            ->line('Silakan buka halaman Agreement untuk membaca dokumen tersebut dan memberikan tanggapan (terima atau tolak).')
            ->action('Lihat Agreement', url('/mahasiswa/agreement'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'agreement_uploaded',
            'magang_id' => $this->magang->id,
            'industri' => $this->magang->pendaftaran->industri->nama_perusahaan,
            'message' => 'Agreement magang baru dari '.$this->magang->pendaftaran->industri->nama_perusahaan,
        ];
    }
}
