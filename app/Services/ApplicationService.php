<?php

namespace App\Services;

use App\Enums\StatusSeleksi;
use App\Models\MagangAktif;
use App\Models\Mahasiswa;
use App\Models\Pendaftaran;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Support\Facades\DB;

class ApplicationService
{
    /**
     * Create a new internship application.
     *
     * @param  int  $mahasiswaId
     * @param  int  $industriId
     * @return Pendaftaran
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function apply(int $mahasiswaId, int $industriId): Pendaftaran
    {
        // Check if student already has an active application to this industry
        $existingActive = Pendaftaran::where('mahasiswa_id', $mahasiswaId)
            ->where('industri_id', $industriId)
            ->where('status_seleksi', StatusSeleksi::PENDING)
            ->exists();

        if ($existingActive) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'industri_id' => ['Anda sudah memiliki pendaftaran aktif ke industri ini.'],
            ]);
        }

        // Check if student already has an accepted internship
        // Exclude pendaftarans where the agreement was rejected by the student
        $hasAccepted = Pendaftaran::where('mahasiswa_id', $mahasiswaId)
            ->where('status_seleksi', StatusSeleksi::DITERIMA)
            ->whereHas('magangAktif', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('status_agreement')
                        ->orWhere('status_agreement', '!=', 'rejected');
                });
            })
            ->exists();

        if ($hasAccepted) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'mahasiswa_id' => ['Anda sudah diterima di industri lain.'],
            ]);
        }

        return Pendaftaran::create([
            'mahasiswa_id' => $mahasiswaId,
            'industri_id' => $industriId,
            'status_seleksi' => StatusSeleksi::PENDING,
        ]);
    }

    /**
     * Accept a student's application (by industry).
     *
     * @param  Pendaftaran  $pendaftaran
     * @param  string|null  $keterangan
     * @return Pendaftaran
     *
     * @throws \Exception
     */
    public function accept(Pendaftaran $pendaftaran, ?string $keterangan = null): Pendaftaran
    {
        $this->validateTransition($pendaftaran, StatusSeleksi::DITERIMA);

        return DB::transaction(function () use ($pendaftaran, $keterangan) {
            $pendaftaran->update([
                'status_seleksi' => StatusSeleksi::DITERIMA,
                'keterangan_industri' => $keterangan,
            ]);

            // Auto-create active internship record
            MagangAktif::create([
                'pendaftaran_id' => $pendaftaran->id,
                'status_tahapan' => 'persiapan',
            ]);

            // Notify the student
            $this->notifyStudent($pendaftaran);

            return $pendaftaran->fresh(['magangAktif']);
        });
    }

    /**
     * Reject a student's application (by industry).
     *
     * @param  Pendaftaran  $pendaftaran
     * @param  string  $reason
     * @return Pendaftaran
     *
     * @throws \Exception
     */
    public function reject(Pendaftaran $pendaftaran, string $reason): Pendaftaran
    {
        $this->validateTransition($pendaftaran, StatusSeleksi::DITOLAK);

        $pendaftaran->update([
            'status_seleksi' => StatusSeleksi::DITOLAK,
            'keterangan_industri' => $reason,
        ]);

        // Notify the student
        $this->notifyStudent($pendaftaran);

        return $pendaftaran->fresh();
    }

    /**
     * Reset rejected application — creates a new pendaftaran for re-application.
     *
     * @param  int  $mahasiswaId
     * @param  int  $industriId
     * @return Pendaftaran
     */
    public function reApply(int $mahasiswaId, int $industriId): Pendaftaran
    {
        return $this->apply($mahasiswaId, $industriId);
    }

    /**
     * Get applications for a specific industry.
     */
    public function getByIndustri(int $industriId, ?StatusSeleksi $status = null)
    {
        $query = Pendaftaran::with('mahasiswa.user')
            ->where('industri_id', $industriId);

        if ($status) {
            $query->where('status_seleksi', $status);
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Get application history for a student.
     */
    public function getByMahasiswa(int $mahasiswaId)
    {
        return Pendaftaran::with('industri.user')
            ->where('mahasiswa_id', $mahasiswaId)
            ->latest()
            ->paginate(15);
    }

    /**
     * Validate that the status transition is allowed.
     *
     * @throws \Exception
     */
    private function validateTransition(Pendaftaran $pendaftaran, StatusSeleksi $target): void
    {
        if (! $pendaftaran->status_seleksi->canTransitionTo($target)) {
            throw new \Exception(
                "Cannot transition from {$pendaftaran->status_seleksi->value} to {$target->value}"
            );
        }
    }

    /**
     * Notify the student about application status change.
     */
    private function notifyStudent(Pendaftaran $pendaftaran): void
    {
        $student = $pendaftaran->mahasiswa->user;

        $student->notify(
            new ApplicationStatusNotification($pendaftaran)
        );
    }
}
