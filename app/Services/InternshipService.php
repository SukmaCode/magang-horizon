<?php

namespace App\Services;

use App\Enums\StatusApproval;
use App\Enums\StatusTahapan;
use App\Models\Dosen;
use App\Models\LaporanAkhir;
use App\Models\MagangAktif;
use Illuminate\Support\Facades\DB;

class InternshipService
{
    /**
     * Assign supervisors to an active internship.
     *
     * @param  MagangAktif  $magang
     * @param  int  $dosenId  Campus supervisor (dosen) ID
     * @param  int  $supervisorIndustriId  Industry supervisor (user) ID
     * @return MagangAktif
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
     * @param  MagangAktif  $magang
     * @param  StatusTahapan  $targetStatus
     * @return MagangAktif
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
     */
    public function uploadAgreementIndustri(MagangAktif $magang, string $filePath): MagangAktif
    {
        $magang->update(['file_agreement_industri' => $filePath]);

        activity('internship')
            ->performedOn($magang)
            ->log('Industry agreement uploaded');

        return $magang;
    }

    /**
     * Upload agreement file signed by student.
     */
    public function uploadAgreementMahasiswa(MagangAktif $magang, string $filePath): MagangAktif
    {
        $magang->update(['file_agreement_mahasiswa' => $filePath]);

        activity('internship')
            ->performedOn($magang)
            ->log('Student agreement uploaded');

        return $magang;
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
            throw new \Exception('Industry agreement must be uploaded before starting internship execution.');
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
