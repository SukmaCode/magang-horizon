<?php

namespace App\Services;

use App\Enums\EvaluationStatus;
use App\Enums\StatusSeleksi;
use App\Models\EvaluationScore;
use App\Models\InternshipEvaluation;
use App\Models\MagangAktif;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InternshipEvaluationService
{
    public function __construct(
        private readonly GradingService $gradingService,
    ) {}

    /**
     * Get all magangs that a supervisor can evaluate.
     */
    public function getEvaluableMagangs(User $supervisor): array
    {
        $industri = $supervisor->industri;

        if (! $industri) {
            return [];
        }

        return MagangAktif::whereHas('pendaftaran', function ($q) use ($industri) {
            $q->where('industri_id', $industri->id)
                ->where('status_seleksi', StatusSeleksi::DITERIMA);
        })
            ->with([
                'pendaftaran.mahasiswa',
                'internshipEvaluation.scores',
                'logbooks',
                'penilaian',
            ])
            ->get()
            ->map(fn (MagangAktif $m) => [
                'id' => $m->id,
                'mahasiswa' => [
                    'nama_lengkap' => $m->pendaftaran->mahasiswa->nama_lengkap,
                    'nim' => $m->pendaftaran->mahasiswa->nim,
                    'prodi' => $m->pendaftaran->mahasiswa->prodi,
                ],
                'status_tahapan' => $m->status_tahapan->value,
                'status_tahapan_label' => $m->status_tahapan->label(),
                'total_logbook' => $m->logbooks->count(),
                'approved_logbook' => $m->logbooks->where('is_approved_industri', true)->count(),
                'evaluation' => $m->internshipEvaluation ? [
                    'id' => $m->internshipEvaluation->id,
                    'status' => $m->internshipEvaluation->status->value,
                    'status_label' => $m->internshipEvaluation->status->label(),
                    'nilai_akhir' => $m->internshipEvaluation->nilai_akhir,
                    'updated_at' => $m->internshipEvaluation->updated_at->format('d M Y H:i'),
                ] : null,
            ])
            ->values()
            ->toArray();
    }

    /**
     * Get evaluation data for a specific magang (used in create/edit form).
     */
    public function getEvaluationFormData(MagangAktif $magang): array
    {
        $magang->load([
            'pendaftaran.mahasiswa',
            'pendaftaran.industri',
            'internshipEvaluation.scores',
        ]);

        $evaluation = $magang->internshipEvaluation;

        // Build scores map: komponen => nilai
        $scoresMap = [];
        if ($evaluation) {
            foreach ($evaluation->scores as $score) {
                $scoresMap[$score->komponen] = $score->nilai;
            }
        }

        return [
            'magang' => [
                'id' => $magang->id,
                'mahasiswa' => [
                    'nama_lengkap' => $magang->pendaftaran->mahasiswa->nama_lengkap,
                    'nim' => $magang->pendaftaran->mahasiswa->nim,
                    'prodi' => $magang->pendaftaran->mahasiswa->prodi,
                ],
                'industri' => [
                    'nama_perusahaan' => $magang->pendaftaran->industri->nama_perusahaan,
                ],
                'tanggal_mulai' => $magang->tanggal_mulai?->format('d M Y'),
                'tanggal_selesai' => $magang->tanggal_selesai?->format('d M Y'),
            ],
            'evaluation' => $evaluation ? [
                'id' => $evaluation->id,
                'status' => $evaluation->status->value,
                'status_label' => $evaluation->status->label(),
                'catatan_supervisor' => $evaluation->catatan_supervisor,
                'tanggal_evaluasi' => $evaluation->tanggal_evaluasi?->format('Y-m-d'),
                'nilai_akhir' => $evaluation->nilai_akhir,
                'can_edit' => $evaluation->canBeEdited(),
            ] : null,
            'scores' => $scoresMap,
            'components' => EvaluationScore::COMPONENTS,
        ];
    }

    /**
     * Save or update evaluation with scores (keeps status as draft).
     */
    public function saveEvaluation(MagangAktif $magang, User $supervisor, array $data): InternshipEvaluation
    {
        return DB::transaction(function () use ($magang, $supervisor, $data) {
            $evaluation = InternshipEvaluation::updateOrCreate(
                ['magang_id' => $magang->id],
                [
                    'supervisor_id' => $supervisor->id,
                    'catatan_supervisor' => $data['catatan_supervisor'] ?? null,
                    'tanggal_evaluasi' => $data['tanggal_evaluasi'] ?? now()->toDateString(),
                ]
            );

            // Prevent editing finalized evaluations
            if ($evaluation->isFinalized()) {
                throw new \Exception('Evaluasi yang sudah difinalisasi tidak dapat diubah.');
            }

            // Upsert scores
            foreach ($data['scores'] as $komponen => $nilai) {
                if (! EvaluationScore::isValidComponent($komponen)) {
                    continue;
                }

                EvaluationScore::updateOrCreate(
                    [
                        'evaluation_id' => $evaluation->id,
                        'komponen' => $komponen,
                    ],
                    ['nilai' => $nilai]
                );
            }

            // Recalculate average
            $evaluation->refresh();
            $evaluation->update([
                'nilai_akhir' => $evaluation->calculateNilaiAkhir(),
            ]);

            activity('evaluation')
                ->performedOn($evaluation)
                ->causedBy($supervisor)
                ->withProperties(['status' => $evaluation->status->value])
                ->log('Evaluation saved as draft');

            return $evaluation->fresh(['scores']);
        });
    }

    /**
     * Submit evaluation (draft → submitted).
     */
    public function submitEvaluation(InternshipEvaluation $evaluation): InternshipEvaluation
    {
        if (! $evaluation->status->canTransitionTo(EvaluationStatus::SUBMITTED)) {
            throw new \Exception("Tidak dapat mengubah status dari {$evaluation->status->label()} ke Submitted.");
        }

        if (! $evaluation->isComplete()) {
            throw new \Exception('Semua komponen penilaian harus diisi sebelum submit.');
        }

        $evaluation->update([
            'status' => EvaluationStatus::SUBMITTED,
            'nilai_akhir' => $evaluation->calculateNilaiAkhir(),
        ]);

        activity('evaluation')
            ->performedOn($evaluation)
            ->log('Evaluation submitted');

        return $evaluation->fresh();
    }

    /**
     * Finalize evaluation (submitted → finalized).
     * Also syncs the average score to the legacy penilaians table.
     */
    public function finalizeEvaluation(InternshipEvaluation $evaluation): InternshipEvaluation
    {
        if (! $evaluation->status->canTransitionTo(EvaluationStatus::FINALIZED)) {
            throw new \Exception("Tidak dapat mengubah status dari {$evaluation->status->label()} ke Finalized.");
        }

        if (! $evaluation->isComplete()) {
            throw new \Exception('Semua komponen penilaian harus diisi sebelum finalisasi.');
        }

        return DB::transaction(function () use ($evaluation) {
            $nilaiAkhir = $evaluation->calculateNilaiAkhir();

            $evaluation->update([
                'status' => EvaluationStatus::FINALIZED,
                'nilai_akhir' => $nilaiAkhir,
                'finalized_at' => now(),
            ]);

            // ✅ Sync to legacy penilaians table for backward compatibility
            $magang = $evaluation->magangAktif;
            $this->gradingService->gradeByIndustry($magang, $nilaiAkhir);

            activity('evaluation')
                ->performedOn($evaluation)
                ->withProperties(['nilai_akhir' => $nilaiAkhir])
                ->log('Evaluation finalized — score synced to penilaians');

            return $evaluation->fresh(['scores']);
        });
    }

    /**
     * Get evaluation for a student's active internship.
     */
    public function getStudentEvaluation(Mahasiswa $mahasiswa): ?array
    {
        $pendaftaran = $mahasiswa->pendaftarans()
            ->where('status_seleksi', 'diterima')
            ->with('magangAktif.internshipEvaluation.scores')
            ->latest()
            ->first();

        $magang = $pendaftaran?->magangAktif;
        $evaluation = $magang?->internshipEvaluation;

        if (! $evaluation) {
            return null;
        }

        // Build scores array with labels
        $scores = [];
        foreach (EvaluationScore::COMPONENTS as $key => $label) {
            $score = $evaluation->scores->firstWhere('komponen', $key);
            $scores[] = [
                'komponen' => $key,
                'label' => $label,
                'nilai' => $score?->nilai,
            ];
        }

        return [
            'evaluation' => [
                'id' => $evaluation->id,
                'status' => $evaluation->status->value,
                'status_label' => $evaluation->status->label(),
                'nilai_akhir' => $evaluation->nilai_akhir,
                'catatan_supervisor' => $evaluation->catatan_supervisor,
                'tanggal_evaluasi' => $evaluation->tanggal_evaluasi?->format('d M Y'),
                'finalized_at' => $evaluation->finalized_at?->format('d M Y H:i'),
                'can_download' => $evaluation->status->canDownload(),
            ],
            'scores' => $scores,
            'magang' => [
                'id' => $magang->id,
                'tanggal_mulai' => $magang->tanggal_mulai?->format('d M Y'),
                'tanggal_selesai' => $magang->tanggal_selesai?->format('d M Y'),
            ],
        ];
    }
}
