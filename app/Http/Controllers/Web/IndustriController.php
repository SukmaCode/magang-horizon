<?php

namespace App\Http\Controllers\Web;

use App\Enums\StatusAgreement;
use App\Enums\StatusSeleksi;
use App\Enums\StatusTahapan;
use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\MagangAktif;
use App\Models\Pendaftaran;
use App\Services\ApplicationService;
use App\Services\CompletionLetterService;
use App\Services\DailyLogService;
use App\Services\GradingService;
use App\Services\InternshipService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndustriController extends Controller
{
    public function __construct(
        private readonly ApplicationService $applicationService,
        private readonly CompletionLetterService $completionLetterService,
        private readonly DailyLogService $dailyLogService,
        private readonly GradingService $gradingService,
        private readonly InternshipService $internshipService,
    ) {}

    // ──────────────────────────────────────
    // Dashboard
    // ──────────────────────────────────────

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $industri = $user->industri;

        $pendingCount = 0;
        $activeStudents = 0;
        $pendingLogbooks = 0;
        $recentApplications = [];

        if ($industri) {
            // Pending CV reviews
            $pendingCount = $industri->pendaftarans()
                ->where('status_seleksi', StatusSeleksi::PENDING)
                ->count();

            // Active internship students (via accepted pendaftarans -> magangAktif)
            $activeMagangs = $this->getActiveMagangs($industri);
            $acceptedMagangs = $activeMagangs->filter(fn ($m) => $m->status_agreement === StatusAgreement::ACCEPTED);
            $activeStudents = $acceptedMagangs->count();

            // Pending logbook approvals across all active magangs
            $activeMagangIds = $acceptedMagangs->pluck('id');
            $pendingLogbooks = Logbook::whereIn('magang_id', $activeMagangIds)
                ->where('is_approved_industri', false)
                ->count();

            // Recent applications
            $recentApplications = $industri->pendaftarans()
                ->with('mahasiswa:id,nama_lengkap,nim,prodi')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (Pendaftaran $p) => [
                    'id' => $p->id,
                    'mahasiswa' => [
                        'nama_lengkap' => $p->mahasiswa->nama_lengkap,
                        'nim' => $p->mahasiswa->nim,
                        'prodi' => $p->mahasiswa->prodi,
                    ],
                    'status' => $p->status_seleksi->value,
                    'status_label' => $p->status_seleksi->label(),
                    'created_at' => $p->created_at->format('d M Y'),
                ]);
        }

        $hasSignature = $user->signatures()->exists();

        return Inertia::render('Industri/Dashboard', [
            'pendingCount' => $pendingCount,
            'activeStudents' => $activeStudents,
            'pendingLogbooks' => $pendingLogbooks,
            'recentApplications' => $recentApplications,
            'hasSignature' => $hasSignature,
        ]);
    }

    // ──────────────────────────────────────
    // Seleksi CV (Review Applications)
    // ──────────────────────────────────────

    public function seleksiCV(Request $request)
    {
        $user = $request->user();
        $industri = $user->industri;

        $pendaftarans = [];
        if ($industri) {
            $pendaftarans = $industri->pendaftarans()
                ->with('mahasiswa:id,nama_lengkap,nim,prodi,cv_file_path,linkedin_url,bio,skills,profile_photo_path')
                ->latest()
                ->paginate(15)
                ->through(fn (Pendaftaran $p) => [
                    'id' => $p->id,
                    'mahasiswa' => [
                        'nama_lengkap' => $p->mahasiswa->nama_lengkap,
                        'nim' => $p->mahasiswa->nim,
                        'prodi' => $p->mahasiswa->prodi,
                        'has_cv' => $p->mahasiswa->cv_file_path !== null,
                        'linkedin_url' => $p->mahasiswa->linkedin_url,
                        'bio' => $p->mahasiswa->bio,
                        'skills' => $p->mahasiswa->skills,
                        'profile_photo_url' => $p->mahasiswa->profile_photo_url,
                    ],
                    'status' => $p->status_seleksi->value,
                    'status_label' => $p->status_seleksi->label(),
                    'keterangan' => $p->keterangan_industri,
                    'created_at' => $p->created_at->format('d M Y H:i'),
                    'cv_url' => $p->mahasiswa->cv_file_path
                        ? route('industri.seleksi-cv.download-cv', $p->id)
                        : null,
                ]);
        }

        return Inertia::render('Industri/SeleksiCV', [
            'pendaftarans' => $pendaftarans,
        ]);
    }

    public function acceptApplication(Request $request, Pendaftaran $pendaftaran)
    {
        $request->validate([
            'keterangan' => 'nullable|string|max:500',
        ]);

        $this->authorizeIndustri($request, $pendaftaran);

        try {
            $this->applicationService->accept($pendaftaran, $request->input('keterangan'));

            return back()->with('success', 'Mahasiswa berhasil diterima.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejectApplication(Request $request, Pendaftaran $pendaftaran)
    {
        $request->validate([
            'keterangan' => 'required|string|max:500',
        ], [
            'keterangan.required' => 'Berikan alasan penolakan.',
        ]);

        $this->authorizeIndustri($request, $pendaftaran);

        try {
            $this->applicationService->reject($pendaftaran, $request->input('keterangan'));

            return back()->with('success', 'Lamaran ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Agreement Upload
    // ──────────────────────────────────────

    public function agreement(Request $request)
    {
        $user = $request->user();
        $industri = $user->industri;

        $magangs = [];
        if ($industri) {
            $magangs = $this->getActiveMagangs($industri)
                ->filter(fn ($m) => $m->status_tahapan === StatusTahapan::PERSIAPAN)
                ->map(fn (MagangAktif $m) => [
                    'id' => $m->id,
                    'mahasiswa' => [
                        'nama_lengkap' => $m->pendaftaran->mahasiswa->nama_lengkap,
                        'nim' => $m->pendaftaran->mahasiswa->nim,
                    ],
                    'has_agreement' => $m->file_agreement_industri !== null,
                    'status_agreement' => $m->status_agreement?->value,
                    'alasan_tolak' => $m->alasan_tolak_agreement,
                    'status' => $m->status_tahapan->value,
                ])
                ->values();
        }

        return Inertia::render('Industri/Agreement', [
            'magangs' => $magangs,
        ]);
    }

    public function uploadAgreement(Request $request, MagangAktif $magangAktif)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        try {
            $path = $request->file('file')->store('documents/agreements/industri/'.$magangAktif->id, 'private');
            $this->internshipService->uploadAgreementIndustri($magangAktif, $path);

            return back()->with('success', 'Agreement berhasil diupload.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Approval Logbook
    // ──────────────────────────────────────

    public function persetujuanLogbook(Request $request)
    {
        $user = $request->user();
        $industri = $user->industri;

        $logbooks = [];
        $magangs = [];
        $selectedMagangId = $request->query('magang_id');

        if ($industri) {
            $activeMagangs = $this->getActiveMagangs($industri)
                ->filter(fn ($m) => $m->status_agreement === StatusAgreement::ACCEPTED);
                
            $magangs = $activeMagangs->map(fn (MagangAktif $m) => [
                'id' => $m->id,
                'nama_lengkap' => $m->pendaftaran->mahasiswa->nama_lengkap,
                'nim' => $m->pendaftaran->mahasiswa->nim,
                // ✅ ->logbooks (property) bukan ->logbooks() (method)
                // Memanfaatkan eager load dari getActiveMagangs() — tidak ada query tambahan
                'pending_count' => $m->logbooks->where('is_approved_industri', false)->count(),
            ])->values();

            // If a magang is selected, load its logbooks
            if ($selectedMagangId) {
                $magang = $activeMagangs->firstWhere('id', $selectedMagangId);
                if ($magang) {
                    $logbooks = $magang->logbooks()
                        ->orderBy('tanggal_waktu', 'desc')
                        ->paginate(15)
                        ->through(fn (Logbook $l) => [
                            'id' => $l->id,
                            'tanggal_waktu' => $l->tanggal_waktu->format('d M Y H:i'),
                            'kegiatan' => $l->kegiatan,
                            'status_presensi' => $l->status_presensi->value,
                            'status_presensi_label' => $l->status_presensi->label(),
                            'is_approved' => $l->is_approved_industri,
                            'komentar_industri' => $l->komentar_industri,
                        ]);
                }
            }
        }

        return Inertia::render('Industri/PersetujuanLogbook', [
            'logbooks' => $logbooks,
            'magangs' => $magangs,
            'selectedMagangId' => $selectedMagangId ? (int) $selectedMagangId : null,
        ]);
    }

    public function approveLogbook(Request $request, Logbook $logbook)
    {
        $request->validate([
            'komentar' => 'nullable|string|max:500',
        ]);

        try {
            $this->dailyLogService->approve($logbook, $request->input('komentar'));

            return back()->with('success', 'Logbook berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Internship Completion Letter
    // ──────────────────────────────────────

    public function completionLetter(Request $request)
    {
        $user = $request->user();
        $industri = $user->industri;
        $statusTahapan = StatusTahapan::PELAKSANAAN->value;

        $magangs = $industri
            ? $this->completionLetterService->getMagangsForIndustri($industri)
            : [];

        return Inertia::render('Industri/InternshipCompletionLetter', [
            'magangs' => $magangs,
            'statusTahapan' => $statusTahapan,
        ]);
    }

    public function storeCompletionLetter(Request $request, MagangAktif $magangAktif)
    {
        $validated = $request->validate([
            'posisi_magang' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'deskripsi_tugas' => 'required|string|max:5000',
            'komentar_penutup' => 'required|string|max:5000',
        ], [
            'posisi_magang.required' => 'Posisi magang wajib diisi.',
            'departemen.required' => 'Departemen wajib diisi.',
            'deskripsi_tugas.required' => 'Deskripsi tugas wajib diisi.',
            'komentar_penutup.required' => 'Komentar penutup wajib diisi.',
        ]);

        try {
            $this->completionLetterService->storeCompletionLetter($magangAktif, $validated);

            return back()->with('success', 'Data Internship Completion Letter berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Applicant Profile (View)
    // ──────────────────────────────────────

    /**
     * Show full profile of a student applicant.
     * Only accessible by the industry that received the application.
     */
    public function applicantProfile(Request $request, Pendaftaran $pendaftaran)
    {
        $this->authorizeIndustri($request, $pendaftaran);

        $mahasiswa = $pendaftaran->mahasiswa;

        return Inertia::render('Industri/ApplicantProfile', [
            'pendaftaran' => [
                'id' => $pendaftaran->id,
                'status' => $pendaftaran->status_seleksi->value,
                'status_label' => $pendaftaran->status_seleksi->label(),
                'keterangan' => $pendaftaran->keterangan_industri,
                'created_at' => $pendaftaran->created_at->format('d M Y H:i'),
            ],
            'mahasiswa' => [
                'nama_lengkap' => $mahasiswa->nama_lengkap,
                'nim' => $mahasiswa->nim,
                'prodi' => $mahasiswa->prodi,
                'email' => $mahasiswa->user->email,
                'nomor_hp' => $mahasiswa->nomor_hp,
                'bio' => $mahasiswa->bio,
                'skills' => $mahasiswa->skills,
                'linkedin_url' => $mahasiswa->linkedin_url,
                'profile_photo_url' => $mahasiswa->profile_photo_url,
                'has_cv' => $mahasiswa->cv_file_path !== null,
                'cv_url' => $mahasiswa->cv_file_path
                    ? route('industri.seleksi-cv.download-cv', $pendaftaran->id)
                    : null,
            ],
        ]);
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    /**
     * Get all active magangs linked to this industri.
     */
    private function getActiveMagangs($industri)
    {
        return MagangAktif::whereHas('pendaftaran', function ($q) use ($industri) {
            $q->where('industri_id', $industri->id)
                ->where('status_seleksi', StatusSeleksi::DITERIMA);
        })
            ->with('pendaftaran.mahasiswa', 'logbooks', 'penilaian')
            ->get();
    }

    /**
     * Authorize that the current user owns this pendaftaran's industri.
     */
    private function authorizeIndustri(Request $request, Pendaftaran $pendaftaran): void
    {
        $industri = $request->user()->industri;
        if (! $industri || $pendaftaran->industri_id !== $industri->id) {
            abort(403, 'Unauthorized');
        }
    }
}
