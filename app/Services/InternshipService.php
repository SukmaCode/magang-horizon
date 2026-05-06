<?php

namespace App\Services;

use App\Enums\StatusAgreement;
use App\Enums\StatusApproval;
use App\Enums\StatusTahapan;
use App\Models\Dosen;
use App\Models\MagangAktif;
use App\Notifications\AgreementResponseNotification;
use App\Notifications\AgreementUploadedNotification;
use Illuminate\Support\Facades\DB;

class InternshipService
{
    /**
     * Assign supervisors to an active internship.
     *
     * @param  int  $dosenId  Campus supervisor (dosen) ID
     * @param  int  $supervisorIndustriId  Industry supervisor (user) ID
     */
    public function assignSupervisors(MagangAktif $magang, int $dosenId, int $supervisorIndustriId): MagangAktif
    {
        // Validate that the dosen exists
        Dosen::findOrFail($dosenId);

        $magang->update([
            'supervisor_kampus_id' => $dosenId,
            'supervisor_industri_id' => $supervisorIndustriId,
        ]);

        activity('internship')
            ->performedOn($magang)
            ->withProperties([
                'supervisor_kampus_id' => $dosenId,
                'supervisor_industri_id' => $supervisorIndustriId,
            ])
            ->log('Supervisors assigned to internship');

        return $magang->fresh(['supervisorKampus', 'supervisorIndustri']);
    }

    /**
     * Transition internship to next stage.
     *
     *
     * @throws \Exception
     */
    public function transitionTo(MagangAktif $magang, StatusTahapan $targetStatus): MagangAktif
    {
        $currentStatus = $magang->status_tahapan;

        // Validate the transition is allowed
        if (! $currentStatus->canTransitionTo($targetStatus)) {
            throw new \Exception(
                "Invalid transition from {$currentStatus->value} to {$targetStatus->value}"
            );
        }

        // Run pre-transition checks
        $this->validatePreConditions($magang, $targetStatus);

        return DB::transaction(function () use ($magang, $targetStatus, $currentStatus) {
            $magang->update([
                'status_tahapan' => $targetStatus,
                'tanggal_mulai' => $targetStatus === StatusTahapan::PELAKSANAAN
                    ? ($magang->tanggal_mulai ?? now()->toDateString())
                    : $magang->tanggal_mulai,
                'tanggal_selesai' => $targetStatus === StatusTahapan::PENUTUPAN
                    ? now()->toDateString()
                    : $magang->tanggal_selesai,
            ]);

            activity('internship')
                ->performedOn($magang)
                ->withProperties([
                    'from' => $currentStatus->value,
                    'to' => $targetStatus->value,
                ])
                ->log("Internship transitioned from {$currentStatus->value} to {$targetStatus->value}");

            return $magang->fresh();
        });
    }

    /**
     * Upload agreement file for industry.
     * Sets status to PENDING and notifies the student.
     */
    public function uploadAgreementIndustri(MagangAktif $magang, string $filePath): MagangAktif
    {
        $mahasiswaId = $magang->pendaftaran->mahasiswa_id;
        $hasAccepted = MagangAktif::whereHas('pendaftaran', function ($q) use ($mahasiswaId) {
            $q->where('mahasiswa_id', $mahasiswaId);
        })->where('status_agreement', StatusAgreement::ACCEPTED)->exists();

        if ($hasAccepted) {
            $magang->update([
                'file_agreement_industri' => $filePath,
                'status_agreement' => StatusAgreement::REJECTED,
                'alasan_tolak_agreement' => 'Otomatis ditolak karena mahasiswa telah menyetujui agreement dari perusahaan lain.',
                'file_agreement_mahasiswa' => null,
            ]);

            // Notify the industry supervisor
            $industriUser = $magang->pendaftaran->industri->user;
            $industriUser->notify(new AgreementResponseNotification($magang, 'rejected'));

            activity('internship')
                ->performedOn($magang)
                ->log('Industry agreement uploaded but auto-rejected (student already accepted another)');

            return $magang;
        }

        $magang->update([
            'file_agreement_industri' => $filePath,
            'status_agreement' => StatusAgreement::PENDING,
            // Reset any previous rejection state
            'alasan_tolak_agreement' => null,
            'file_agreement_mahasiswa' => null,
        ]);

        // Notify the student that a new agreement has arrived
        $studentUser = $magang->pendaftaran->mahasiswa->user;
        $studentUser->notify(new AgreementUploadedNotification($magang));

        activity('internship')
            ->performedOn($magang)
            ->log('Industry agreement uploaded, awaiting student response');

        return $magang;
    }

    /**
     * Student accepts and signs the agreement.
     */
    public function acceptAgreement(MagangAktif $magang, string $signedFilePath): MagangAktif
    {
        if ($magang->status_agreement !== StatusAgreement::PENDING) {
            throw new \Exception('Agreement tidak dalam status menunggu persetujuan.');
        }

        // Check if student already has another accepted agreement
        $mahasiswaId = $magang->pendaftaran->mahasiswa_id;
        $hasAccepted = MagangAktif::whereHas('pendaftaran', function ($q) use ($mahasiswaId) {
            $q->where('mahasiswa_id', $mahasiswaId);
        })->where('status_agreement', StatusAgreement::ACCEPTED)->exists();

        if ($hasAccepted) {
            throw new \Exception('Anda sudah menandatangani agreement dari perusahaan lain.');
        }

        return DB::transaction(function () use ($magang, $signedFilePath, $mahasiswaId) {
            $magang->update([
                'file_agreement_mahasiswa' => $signedFilePath,
                'status_agreement' => StatusAgreement::ACCEPTED,
            ]);

            activity('internship')
                ->performedOn($magang)
                ->log('Student accepted and signed agreement');

            // Notify the industry supervisor
            $industriUser = $magang->pendaftaran->industri->user;
            $industriUser->notify(new AgreementResponseNotification($magang, 'accepted'));

            // Automatically reject other pending agreements
            $otherMagangs = MagangAktif::whereHas('pendaftaran', function ($q) use ($mahasiswaId, $magang) {
                $q->where('mahasiswa_id', $mahasiswaId)
                    ->where('id', '!=', $magang->pendaftaran_id);
            })->where('status_agreement', '!=', StatusAgreement::REJECTED)->get();

            foreach ($otherMagangs as $other) {
                $other->update([
                    'status_agreement' => StatusAgreement::REJECTED,
                    'alasan_tolak_agreement' => 'Otomatis ditolak karena mahasiswa telah menyetujui agreement dari perusahaan lain.',
                ]);

                // Notify the other industry supervisor
                $otherIndustriUser = $other->pendaftaran->industri->user;
                $otherIndustriUser->notify(new AgreementResponseNotification($other, 'rejected'));
            }

            return $magang->fresh();
        });
    }

    /**
     * Student rejects the agreement.
     */
    public function rejectAgreement(MagangAktif $magang, string $reason): MagangAktif
    {
        if ($magang->status_agreement !== StatusAgreement::PENDING) {
            throw new \Exception('Agreement tidak dalam status menunggu persetujuan.');
        }

        return DB::transaction(function () use ($magang, $reason) {
            $magang->update([
                'status_agreement' => StatusAgreement::REJECTED,
                'alasan_tolak_agreement' => $reason,
            ]);

            activity('internship')
                ->performedOn($magang)
                ->log('Student rejected agreement');

            // Notify the industry supervisor
            $industriUser = $magang->pendaftaran->industri->user;
            $industriUser->notify(new AgreementResponseNotification($magang, 'rejected'));

            return $magang->fresh();
        });
    }

    /**
     * Upload SK Pembimbing by admin.
     */
    public function uploadSkPembimbing(MagangAktif $magang, string $filePath): MagangAktif
    {
        $magang->update(['sk_pembimbing_path' => $filePath]);

        activity('internship')
            ->performedOn($magang)
            ->log('SK Pembimbing uploaded');

        return $magang;
    }

    /**
     * Get internship details with all relationships.
     */
    public function getDetails(int $magangId): MagangAktif
    {
        return MagangAktif::with([
            'pendaftaran.mahasiswa.user',
            'pendaftaran.industri.user',
            'supervisorKampus',
            'supervisorIndustri',
            'logbooks',
            'laporanAkhir',
            'penilaian',
            'sertifikat',
        ])->findOrFail($magangId);
    }

    /**
     * Get all active internships with optional filters.
     */
    public function list(?StatusTahapan $status = null, ?int $supervisorKampusId = null)
    {
        $query = MagangAktif::with([
            'pendaftaran.mahasiswa.user',
            'pendaftaran.industri',
            'supervisorKampus',
        ]);

        if ($status) {
            $query->where('status_tahapan', $status);
        }

        if ($supervisorKampusId) {
            $query->where('supervisor_kampus_id', $supervisorKampusId);
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Validate pre-conditions before a state transition.
     *
     * @throws \Exception
     */
    private function validatePreConditions(MagangAktif $magang, StatusTahapan $target): void
    {
        match ($target) {
            StatusTahapan::PELAKSANAAN => $this->validatePersiapanComplete($magang),
            StatusTahapan::PENUTUPAN => true, // No strict pre-conditions
            StatusTahapan::LULUS => $this->validatePenutupanComplete($magang),
            default => true,
        };
    }

    /**
     * Validate that persiapan phase is complete before starting pelaksanaan.
     *
     * @throws \Exception
     */
    private function validatePersiapanComplete(MagangAktif $magang): void
    {
        if (! $magang->hasSupervisorsAssigned()) {
            throw new \Exception('Supervisors must be assigned before starting internship execution.');
        }

        if (! $magang->hasAgreementsUploaded()) {
            throw new \Exception('Agreement must be uploaded and accepted by student before starting.');
        }

        if ($magang->isAgreementRejected()) {
            throw new \Exception('Agreement was rejected by the student. Cannot proceed.');
        }
    }

    /**
     * Validate that penutupan phase is complete before graduating.
     *
     * @throws \Exception
     */
    private function validatePenutupanComplete(MagangAktif $magang): void
    {
        // Check laporan is approved
        $laporan = $magang->laporanAkhir;
        if (! $laporan || $laporan->status_approval_kampus !== StatusApproval::DISETUJUI) {
            throw new \Exception('Final report must be approved before graduating.');
        }

        // Check penilaian is complete and verified
        $penilaian = $magang->penilaian;
        if (! $penilaian || ! $penilaian->isComplete() || ! $penilaian->isVerified()) {
            throw new \Exception('Grading must be complete and verified by admin before graduating.');
        }
    }
}
