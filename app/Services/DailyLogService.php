<?php

namespace App\Services;

use App\Enums\StatusPresensi;
use App\Models\Logbook;
use App\Models\MagangAktif;
use App\Notifications\LogbookSubmittedNotification;
use Illuminate\Validation\ValidationException;

class DailyLogService
{
    /**
     * Submit a daily log entry.
     *
     * @param  array  $data  Validated logbook data
     *
     * @throws ValidationException|\Exception
     */
    public function submit(MagangAktif $magang, array $data): Logbook
    {
        // Validate internship is in execution phase
        $this->validateInternshipStatus($magang);

        // Check duplicate entry for the same date
        $alreadyExists = $magang->logbooks()
            ->whereDate('tanggal_waktu', \Carbon\Carbon::parse($data['tanggal_waktu'])->toDateString())
            ->exists();

        if ($alreadyExists) {
            throw ValidationException::withMessages([
                'tanggal_waktu' => ['Anda sudah mengisi logbook pada tanggal tersebut.']
            ]);
        }

        // Use provided datetime from data
        $logbook = Logbook::create([
            'magang_id' => $magang->id,
            'tanggal_waktu' => $data['tanggal_waktu'],
            'kegiatan' => $data['kegiatan'],
            'status_presensi' => $data['status_presensi'] ?? StatusPresensi::HADIR->value,
        ]);

        // Notify industry supervisor (queued)
        $this->notifySupervisor($magang, $logbook);

        return $logbook;
    }

    /**
     * Approve a logbook entry (by industry supervisor).
     */
    public function approve(Logbook $logbook, ?string $komentar = null): Logbook
    {
        $logbook->update([
            'is_approved_industri' => true,
            'komentar_industri' => $komentar,
        ]);

        activity('logbook')
            ->performedOn($logbook)
            ->log('Logbook approved by industry supervisor');

        return $logbook;
    }

    /**
     * Check/review a logbook entry (by campus supervisor).
     */
    public function check(Logbook $logbook): Logbook
    {
        $logbook->update([
            'is_checked_kampus' => true,
        ]);

        activity('logbook')
            ->performedOn($logbook)
            ->log('Logbook checked by campus supervisor');

        return $logbook;
    }

    /**
     * Get logbook entries for an internship.
     */
    public function getByMagang(int $magangId, ?string $startDate = null, ?string $endDate = null)
    {
        $query = Logbook::where('magang_id', $magangId);

        if ($startDate) {
            $query->where('tanggal_waktu', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('tanggal_waktu', '<=', $endDate);
        }

        return $query->orderBy('tanggal_waktu', 'desc')->paginate(15);
    }

    /**
     * Get pending approval logbooks for an industry supervisor.
     */
    public function getPendingApproval(int $magangId)
    {
        return Logbook::where('magang_id', $magangId)
            ->where('is_approved_industri', false)
            ->orderBy('tanggal_waktu', 'desc')
            ->paginate(15);
    }

    /**
     * Validate that the internship is in 'pelaksanaan' status.
     *
     * @throws \Exception
     */
    private function validateInternshipStatus(MagangAktif $magang): void
    {
        if (! $magang->status_tahapan->allowsDailyLogs()) {
            throw new \Exception(
                'Daily logs can only be submitted during the pelaksanaan (execution) phase. '.
                "Current status: {$magang->status_tahapan->value}"
            );
        }
    }

    /**
     * Validate geofence — check if submitted coordinates are within
     * the allowed radius of the industry location.
     *
     * @throws ValidationException
     */

    /**
     * Notify the industry supervisor about a new logbook entry.
     */
    private function notifySupervisor(MagangAktif $magang, Logbook $logbook): void
    {
        $supervisor = $magang->supervisorIndustri;

        if ($supervisor) {
            $supervisor->notify(
                new LogbookSubmittedNotification($logbook)
            );
        }
    }
}
