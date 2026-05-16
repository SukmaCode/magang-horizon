<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\MagangAktif;
use App\Models\PortfolioEvaluation;
use App\Models\User;

class PortfolioEvaluationPolicy
{
    /**
     * Can the user create an evaluation for this magang?
     * - Industry supervisor of the magang
     * - Dosen pembimbing supervising the magang
     */
    public function create(User $user, MagangAktif $magang): bool
    {
        return $this->isEvaluator($user, $magang);
    }

    /**
     * Can the user update this evaluation?
     * Same as create but evaluation must not be finalized.
     */
    public function update(User $user, PortfolioEvaluation $evaluation): bool
    {
        if (! $evaluation->canBeEdited()) {
            return false;
        }

        $evaluation->loadMissing('magangAktif');

        return $this->isEvaluator($user, $evaluation->magangAktif);
    }

    /**
     * Can the user view this evaluation?
     * - The evaluator (always)
     * - Owning student (finalized only)
     * - Dosen pembimbing supervising the magang
     */
    public function view(User $user, PortfolioEvaluation $evaluation): bool
    {
        // Evaluator can always see
        if ($evaluation->evaluator_id === $user->id) {
            return true;
        }

        $evaluation->loadMissing('magangAktif.pendaftaran');

        // Dosen pembimbing supervising this magang
        if ($user->role === UserRole::SUPERVISOR_1 && $user->dosen) {
            if ($evaluation->magangAktif->supervisor_kampus_id === $user->dosen->id) {
                return true;
            }
        }

        // Owning student (finalized only)
        if ($user->role === UserRole::STUDENT && $evaluation->isFinalized()) {
            $mahasiswa = $user->mahasiswa;
            if ($mahasiswa && $evaluation->magangAktif->pendaftaran->mahasiswa_id === $mahasiswa->id) {
                return true;
            }
        }

        // Dosen Prodi can view all
        if ($user->role === UserRole::SUPERVISOR_2) {
            return true;
        }

        return false;
    }

    /**
     * Can the user download the PDF?
     * Only if evaluation is finalized + user can view it.
     */
    public function download(User $user, PortfolioEvaluation $evaluation): bool
    {
        if (! $evaluation->isFinalized()) {
            return false;
        }

        return $this->view($user, $evaluation);
    }

    // ──────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────

    /**
     * Check if the user is a valid evaluator for this magang.
     */
    private function isEvaluator(User $user, MagangAktif $magang): bool
    {
        $magang->loadMissing('pendaftaran');

        // Industry supervisor
        if ($user->role === UserRole::INDUSTRY) {
            $industri = $user->industri;
            if ($industri && $magang->pendaftaran->industri_id === $industri->id) {
                return true;
            }
        }

        // Dosen pembimbing
        if ($user->role === UserRole::SUPERVISOR_1) {
            $dosen = $user->dosen;
            if ($dosen && $magang->supervisor_kampus_id === $dosen->id) {
                return true;
            }
        }

        return false;
    }
}
