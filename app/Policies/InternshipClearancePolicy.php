<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\InternshipClearance;
use App\Models\MagangAktif;
use App\Models\User;

class InternshipClearancePolicy
{
    /**
     * Can the user upload a clearance for this magang?
     * Only the industri supervising this magang can upload.
     */
    public function upload(User $user, MagangAktif $magang): bool
    {
        if ($user->role !== UserRole::INDUSTRY) {
            return false;
        }

        $industri = $user->industri;
        if (! $industri) {
            return false;
        }

        // Verify the magang belongs to this industri
        $magang->loadMissing('pendaftaran');

        return $magang->pendaftaran->industri_id === $industri->id;
    }

    /**
     * Can the user update (replace file) this clearance?
     * Only the owning industri, and only when status allows it.
     */
    public function update(User $user, InternshipClearance $clearance): bool
    {
        if ($user->role !== UserRole::INDUSTRY) {
            return false;
        }

        if (! $clearance->canBeUpdatedByIndustri()) {
            return false;
        }

        return $this->isOwningIndustri($user, $clearance);
    }

    /**
     * Can the user view this clearance?
     * Owning industri, owning student, supervising dosen pembimbing, or dosen prodi.
     */
    public function view(User $user, InternshipClearance $clearance): bool
    {
        return $this->isOwningIndustri($user, $clearance)
            || $this->isOwnerStudent($user, $clearance)
            || $this->isSupervisingDosen($user, $clearance)
            || $user->role === UserRole::SUPERVISOR_2; // Dosen Prodi can view all
    }

    /**
     * Can the user download the clearance file?
     * Same rules as view.
     */
    public function download(User $user, InternshipClearance $clearance): bool
    {
        return $this->view($user, $clearance);
    }

    /**
     * Can the user submit this clearance for review?
     * Only the owning student, when status is uploaded or rejected.
     */
    public function submit(User $user, InternshipClearance $clearance): bool
    {
        if ($user->role !== UserRole::STUDENT) {
            return false;
        }

        if (! $clearance->canBeSubmitted()) {
            return false;
        }

        return $this->isOwnerStudent($user, $clearance);
    }

    /**
     * Can the user review (approve/reject) this clearance?
     * Dosen Pembimbing (only for their supervised students) or Dosen Prodi.
     */
    public function review(User $user, InternshipClearance $clearance): bool
    {
        if ($user->role === UserRole::SUPERVISOR_2) {
            return true; // Dosen Prodi can review all
        }

        if ($user->role === UserRole::SUPERVISOR_1) {
            return $this->isSupervisingDosen($user, $clearance);
        }

        return false;
    }

    // ──────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────

    /**
     * Check if the user is the industri that owns this clearance's magang.
     */
    private function isOwningIndustri(User $user, InternshipClearance $clearance): bool
    {
        if ($user->role !== UserRole::INDUSTRY) {
            return false;
        }

        $industri = $user->industri;
        if (! $industri) {
            return false;
        }

        $clearance->loadMissing('magangAktif.pendaftaran');

        return $clearance->magangAktif->pendaftaran->industri_id === $industri->id;
    }

    /**
     * Check if the user is the student who owns this clearance.
     */
    private function isOwnerStudent(User $user, InternshipClearance $clearance): bool
    {
        if ($user->role !== UserRole::STUDENT) {
            return false;
        }

        $mahasiswa = $user->mahasiswa;
        if (! $mahasiswa) {
            return false;
        }

        $clearance->loadMissing('magangAktif.pendaftaran');

        return $clearance->magangAktif->pendaftaran->mahasiswa_id === $mahasiswa->id;
    }

    /**
     * Check if the user is the supervising dosen pembimbing for this clearance's magang.
     */
    private function isSupervisingDosen(User $user, InternshipClearance $clearance): bool
    {
        if ($user->role !== UserRole::SUPERVISOR_1) {
            return false;
        }

        $dosen = $user->dosen;
        if (! $dosen) {
            return false;
        }

        $clearance->loadMissing('magangAktif');

        return $clearance->magangAktif->supervisor_kampus_id === $dosen->id;
    }
}
