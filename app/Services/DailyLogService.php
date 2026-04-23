<?php

namespace App\Services;

use App\Enums\StatusPresensi;
use App\Enums\StatusTahapan;
use App\Models\Industri;
use App\Models\Logbook;
use App\Models\MagangAktif;
use App\Notifications\LogbookSubmittedNotification;
use Illuminate\Validation\ValidationException;

class DailyLogService
{
    /**
     * Submit a daily log entry.
     *
     * @param  MagangAktif  $magang
     * @param  array  $data  Validated logbook data
     * @return Logbook
     *
     * @throws ValidationException|\Exception
     */
    public function submit(MagangAktif $magang, array $data): Logbook
    {
        // Validate internship is in execution phase
        $this->validateInternshipStatus($magang);

        // Validate geofence if coordinates provided
        if (isset($data['latitude'], $data['longitude'])) {
            $this->validateGeofence($magang, $data['latitude'], $data['longitude']);
        }

        // Use server timestamp for the date
        $logbook = Logbook::create([
            'magang_id' => $magang->id,
            'tanggal' => now()->toDateString(),
            'kegiatan' => $data['kegiatan'],
            'status_presensi' => $data['status_presensi'] ?? StatusPresensi::HADIR->value,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ]);

        // Notify industry supervisor (queued)
        $this->notifySupervisor($magang, $logbook);

        return $logbook;
    }

    /**
     * Approve a logbook entry (by industry supervisor).
     *
     * @param  Logbook  $logbook
     * @param  string|null  $komentar
     * @return Logbook
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
            $query->where('tanggal', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('tanggal', '<=', $endDate);
        }

        return $query->orderBy('tanggal', 'desc')->paginate(15);
    }

    /**
     * Get pending approval logbooks for an industry supervisor.
     */
    public function getPendingApproval(int $magangId)
    {
        return Logbook::where('magang_id', $magangId)
            ->where('is_approved_industri', false)
            ->orderBy('tanggal', 'desc')
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
    private function validateGeofence(MagangAktif $magang, float $lat, float $lng): void
    {
        $industri = $magang->pendaftaran->industri;

        // Skip validation if industry has no coordinates set
        if (! $industri->latitude || ! $industri->longitude) {
            return;
        }

        $distance = $this->haversineDistance(
            $lat,
            $lng,
            (float) $industri->latitude,
            (float) $industri->longitude
        );

        $allowedRadius = $industri->geofence_radius ?? config('internship.geofence_radius', 500);

        if ($distance > $allowedRadius) {
            throw ValidationException::withMessages([
                'location' => [
                    "Anda berada di luar radius lokasi magang. Jarak: ".round($distance)."m, Radius: {$allowedRadius}m",
                ],
            ]);
        }
    }

    /**
     * Calculate the Haversine distance between two coordinates in meters.
     */
    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

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
