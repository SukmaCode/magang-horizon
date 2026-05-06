<?php

namespace App\Notifications;

use App\Models\Logbook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class LogbookSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Logbook $logbook
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $magang = $this->logbook->magangAktif;
        $mahasiswa = $magang->pendaftaran->mahasiswa;

        return (new MailMessage)
            ->subject('New Logbook Entry Submitted')
            ->greeting('Halo!')
            ->line("{$mahasiswa->nama_lengkap} telah mengirim logbook harian.")
            ->line("Tanggal: {$this->logbook->tanggal->format('d M Y')}")
            ->line('Kegiatan: '.Str::limit($this->logbook->kegiatan, 100))
            ->action('Review Logbook', url('/industri/logbook'))
            ->line('Silakan review dan approve logbook tersebut.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'logbook_submitted',
            'logbook_id' => $this->logbook->id,
            'magang_id' => $this->logbook->magang_id,
            'tanggal' => $this->logbook->tanggal->toDateString(),
            'message' => 'Logbook harian baru telah disubmit.',
        ];
    }
}
