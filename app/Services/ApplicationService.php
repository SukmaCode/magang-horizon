<?php

namespace App\Services;

use App\Enums\StatusAgreement;
use App\Enums\StatusSeleksi;
use App\Models\MagangAktif;
use App\Models\Pendaftaran;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApplicationService
{
    /**
     * Create a new internship application.
     *
     *
     * @throws ValidationException
     */
    public function apply(int $mahasiswaId, int $industriId): Pendaftaran
    {
        // Validate profile completeness (LinkedIn + CV required)
        $mahasiswa = \App\Models\Mahasiswa::findOrFail($mahasiswaId);

        if (! $mahasiswa->hasLinkedIn()) {
            throw ValidationException::withMessages([
                'linkedin_url' => ['Lengkapi profil LinkedIn terlebih dahulu sebelum melamar magang.'],
            ]);
        }

        if (! $mahasiswa->cv_file_path) {
            throw ValidationException::withMessages([
                'cv_file' => ['Upload CV terlebih dahulu sebelum melamar magang.'],
            ]);
        }

        // Check if student already has an active application to this industry
        $existingActive = Pendaftaran::where('mahasiswa_id', $mahasiswaId)
            ->where('industri_id', $industriId)
            ->where('status_seleksi', StatusSeleksi::PENDING)
            ->exists();

        if ($existingActive) {
            throw ValidationException::withMessages([
                'industri_id' => ['Anda sudah memiliki pendaftaran aktif ke industri ini.'],
            ]);
        }

        // Check if student already has an accepted internship WITH a signed agreement
        $hasAccepted = Pendaftaran::where('mahasiswa_id', $mahasiswaId)
            ->whereIn('status_seleksi', [StatusSeleksi::DITERIMA, StatusSeleksi::MENUNGGU_MAHASISWA])
            ->whereHas('magangAktif', function ($q) {
                $q->where('status_agreement', StatusAgreement::ACCEPTED);
            })
            ->exists();

        if ($hasAccepted) {
            throw ValidationException::withMessages([
                'mahasiswa_id' => ['Anda telah menandatangani Agreement dari industri dan tidak dapat mengirim lamaran baru.'],
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
