<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\DeclarationOfOriginality;
use App\Models\MagangAktif;
use App\Models\User;

class DeclarationOfOriginalityPolicy
{
    /**
     * Can the user upload a declaration for this magang?
     * Only the student who owns the magang can upload.
     */
    public function upload(User $user, MagangAktif $magang): bool
    {
        if ($user->role !== UserRole::STUDENT) {
            return false;
        }

        $mahasiswa = $user->mahasiswa;
        if (! $mahasiswa) {
            return false;
        }

        // Verify the magang belongs to this student
        $magang->loadMissing('pendaftaran');

        return $magang->pendaftaran->mahasiswa_id === $mahasiswa->id;
    }

    /**
     * Can the user update (replace file) this declaration?
     * Only the owning student, and only when not yet approved.
     */
    public function update(User $user, DeclarationOfOriginality $declaration): bool
    {
        if ($user->role !== UserRole::STUDENT) {
            return false;
        }

        if (! $declaration->canBeUpdated()) {
            return false;
        }

        return $this->isOwner($user, $declaration);
    }

    /**
     * Can the user view this declaration?
     * Owner student, supervising dosen pembimbing, or dosen prodi.
     */
    public function view(User $user, DeclarationOfOriginality $declaration): bool
    {
        return $this->isOwner($user, $declaration)
            || $this->isSupervisingDosen($user, $declaration);
    }

    /**
     * Can the user download the declaration file?
     * Same rules as view.
     */
    public function download(User $user, DeclarationOfOriginality $declaration): bool
    {
        return $this->view($user, $declaration);
    }

    /**
     * Can the user review (approve/reject) this declaration?
     * Dosen Pembimbing (only for their supervised students) or Dosen Prodi.
     */
    public function review(User $user, DeclarationOfOriginality $declaration): bool
    {
        if ($user->role === UserRole::SUPERVISOR_1) {
            return $this->isSupervisingDosen($user, $declaration);
        }

        return false;
    }

    // ──────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────

    /**
     * Check if the user is the student who owns this declaration.
     */
    private function isOwner(User $user, DeclarationOfOriginality $declaration): bool
    {
        if ($user->role !== UserRole::STUDENT) {
            return false;
        }

        $mahasiswa = $user->mahasiswa;
        if (! $mahasiswa) {
            return false;
        }

        $declaration->loadMissing('magangAktif.pendaftaran');

        return $declaration->magangAktif->pendaftaran->mahasiswa_id === $mahasiswa->id;
    }

    /**
     * Check if the user is the supervising dosen pembimbing for this declaration's magang.
     */
    private function isSupervisingDosen(User $user, DeclarationOfOriginality $declaration): bool
    {
        if ($user->role !== UserRole::SUPERVISOR_1) {
            return false;
        }

        $dosen = $user->dosen;
        if (! $dosen) {
            return false;
        }

        $declaration->loadMissing('magangAktif.pembimbingAssignment');

        return $declaration->magangAktif->supervisor_kampus_id === $dosen->id
            || $declaration->magangAktif->supervisor_kampus_id === $user->id
            || ($declaration->magangAktif->pembimbingAssignment && $declaration->magangAktif->pembimbingAssignment->dosen_id === $dosen->id);
    }
}
