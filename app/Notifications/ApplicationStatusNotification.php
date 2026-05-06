<?php

namespace App\Notifications;

use App\Models\Pendaftaran;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Pendaftaran $pendaftaran
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->pendaftaran->status_seleksi;
        $industri = $this->pendaftaran->industri->nama_perusahaan;

        $mail = (new MailMessage)
            ->subject("Update Status Pendaftaran Magang - {$industri}")
            ->greeting("Halo, {$notifiable->username}!");

        if ($status->value === 'diterima') {
            $mail->line("Selamat! Pendaftaran magang Anda di {$industri} telah DITERIMA.")
                ->line('Silakan lanjutkan ke tahap persiapan.');
        } else {
            $mail->line("Mohon maaf, pendaftaran magang Anda di {$industri} DITOLAK.")
                ->line('Keterangan: '.($this->pendaftaran->keterangan_industri ?? '-'))
                ->line('Anda dapat mendaftar ke industri lain.');
        }

        return $mail->action('Lihat Detail', url('/mahasiswa/pendaftaran'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_status',
            'pendaftaran_id' => $this->pendaftaran->id,
            'status' => $this->pendaftaran->status_seleksi->value,
            'industri' => $this->pendaftaran->industri->nama_perusahaan,
            'message' => "Status pendaftaran: {$this->pendaftaran->status_seleksi->label()}",
        ];
    }
}
