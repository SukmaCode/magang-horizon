<?php

namespace App\Http\Controllers\Web;

use App\Enums\ClearanceStatus;
use App\Enums\StatusSeleksi;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\InternshipClearance;
use App\Models\MagangAktif;
use App\Services\InternshipClearanceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InternshipClearanceController extends Controller
{
    public function __construct(
        private readonly InternshipClearanceService $clearanceService,
    ) {}

    // ──────────────────────────────────────
    // Industri: Clearance Index Page
    // ──────────────────────────────────────

    public function industryIndex(Request $request)
    {
        $user = $request->user();
        $industri = $user->industri;

        $magangs = [];
        if ($industri) {
            $magangs = MagangAktif::whereHas('pendaftaran', function ($q) use ($industri) {
                $q->where('industri_id', $industri->id)
                  ->where('status_seleksi', StatusSeleksi::DITERIMA);
            })
                ->with('pendaftaran.mahasiswa', 'internshipClearance')
                ->get()
                ->map(function (MagangAktif $m) {
                    $clearance = $m->internshipClearance;

                    return [
                        'id' => $m->id,
                        'mahasiswa' => [
                            'nama_lengkap' => $m->pendaftaran->mahasiswa->nama_lengkap,
                            'nim' => $m->pendaftaran->mahasiswa->nim,
                            'prodi' => $m->pendaftaran->mahasiswa->prodi,
                        ],
                        'status_tahapan' => $m->status_tahapan->value,
                        'status_tahapan_label' => $m->status_tahapan->label(),
                        'clearance' => $clearance ? [
                            'id' => $clearance->id,
                            'original_filename' => $clearance->original_filename,
                            'status' => $clearance->status->value,
                            'status_label' => $clearance->status->label(),
                            'status_color' => $clearance->status->badgeColor(),
                            'uploaded_at' => $clearance->uploaded_at->format('d M Y H:i'),
                            'can_update' => $clearance->canBeUpdatedByIndustri(),
                            'rejection_note' => $clearance->rejection_note,
                        ] : null,
                    ];
                })
                ->values();
        }

        return Inertia::render('Industri/Clearance', [
            'magangs' => $magangs,
        ]);
    }

    // ──────────────────────────────────────
    // Industri: Upload Clearance
    // ──────────────────────────────────────

    public function upload(Request $request, MagangAktif $magangAktif)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ], [
            'file.required' => 'Pilih file PDF yang akan diupload.',
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        // Policy check
        if ($request->user()->cannot('upload', [InternshipClearance::class, $magangAktif])) {
            abort(403, 'Anda tidak memiliki akses untuk mengupload clearance ini.');
        }

        // Check if clearance already exists
        if ($magangAktif->internshipClearance) {
            return back()->with('error', 'Clearance sudah ada. Gunakan fitur update untuk mengganti file.');
        }

        try {
            $this->clearanceService->upload($magangAktif, $request->file('file'));

            return back()->with('success', 'Clearance Issued By Company berhasil diupload.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Industri: Update (Replace) Clearance
    // ──────────────────────────────────────

    public function update(Request $request, InternshipClearance $clearance)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ], [
            'file.required' => 'Pilih file PDF yang akan diupload.',
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        // Policy check
        if ($request->user()->cannot('update', $clearance)) {
            abort(403, 'Anda tidak dapat mengubah dokumen ini.');
        }

        try {
            $this->clearanceService->update($clearance, $request->file('file'));

            return back()->with('success', 'Clearance Issued By Company berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Mahasiswa: Show Clearance Page
    // ──────────────────────────────────────

    public function studentShow(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $clearance = null;
        $pdfBase64 = null;
        $magang = null;

        if ($mahasiswa) {
            $magang = MagangAktif::whereHas('pendaftaran', function ($q) use ($mahasiswa) {
                $q->where('mahasiswa_id', $mahasiswa->id)
                  ->where('status_seleksi', 'diterima');
            })->with('internshipClearance.reviewer', 'pendaftaran.industri')->latest()->first();

            if ($magang && $magang->internshipClearance) {
                $cl = $magang->internshipClearance;

                $clearance = [
                    'id' => $cl->id,
                    'original_filename' => $cl->original_filename,
                    'status' => $cl->status->value,
                    'status_label' => $cl->status->label(),
                    'status_color' => $cl->status->badgeColor(),
                    'rejection_note' => $cl->rejection_note,
                    'uploaded_at' => $cl->uploaded_at->format('d M Y H:i'),
                    'submitted_at' => $cl->submitted_at?->format('d M Y H:i'),
                    'reviewer_name' => $cl->reviewer?->dosen?->nama_dosen ?? $cl->reviewer?->username,
                    'reviewed_at' => $cl->reviewed_at?->format('d M Y H:i'),
                    'can_submit' => $cl->canBeSubmitted(),
                ];

                // Generate base64 PDF for preview
                $path = storage_path('app/private/' . $cl->file_path);
                if (file_exists($path)) {
                    $pdfBase64 = 'data:application/pdf;base64,' . base64_encode(file_get_contents($path));
                }
            }
        }

        return Inertia::render('Mahasiswa/Clearance', [
            'clearance' => $clearance,
            'pdfBase64' => $pdfBase64,
            'hasMagang' => $magang !== null,
            'industriName' => $magang?->pendaftaran?->industri?->nama_perusahaan ?? '-',
        ]);
    }

    // ──────────────────────────────────────
    // Mahasiswa: Submit Clearance to Dosen
    // ──────────────────────────────────────

    public function submit(Request $request, InternshipClearance $clearance)
    {
        // Policy check
        if ($request->user()->cannot('submit', $clearance)) {
            abort(403, 'Anda tidak dapat mengirimkan dokumen ini.');
        }

        try {
            $this->clearanceService->submit($clearance);

            return back()->with('success', 'Clearance berhasil dikirimkan untuk verifikasi Dosen.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Shared: Preview Clearance (inline PDF)
    // ──────────────────────────────────────

    public function preview(Request $request, InternshipClearance $clearance)
    {
        if ($request->user()->cannot('view', $clearance)) {
            abort(403, 'Akses ditolak.');
        }

        try {
            $fileInfo = $this->clearanceService->getFileResponse($clearance);

            return response()->file($fileInfo['path'], [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileInfo['name'] . '"',
            ]);
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Shared: Download Clearance
    // ──────────────────────────────────────

    public function download(Request $request, InternshipClearance $clearance)
    {
        if ($request->user()->cannot('download', $clearance)) {
            abort(403, 'Akses ditolak.');
        }

        try {
            $fileInfo = $this->clearanceService->getFileResponse($clearance);

            return response()->download($fileInfo['path'], $fileInfo['name'], [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Dosen: Review Index Page
    // ──────────────────────────────────────

    public function reviewIndex(Request $request)
    {
        $user = $request->user();

        $query = InternshipClearance::with([
            'magangAktif.pendaftaran.mahasiswa',
            'magangAktif.pendaftaran.industri',
            'reviewer',
        ])->where('status', '!=', ClearanceStatus::UPLOADED); // Only show submitted/pending/approved/rejected

        // Dosen Pembimbing: only their supervised students
        if ($user->role === UserRole::SUPERVISOR_1) {
            $dosen = $user->dosen;
            if (! $dosen) {
                return $this->renderReviewPage($user, []);
            }

            $magangIds = $dosen->magangAktifs()->pluck('id');
            $query->whereIn('magang_aktif_id', $magangIds);
        }

        // Dosen Prodi: can see all — no additional filter needed

        $clearances = $query->latest('submitted_at')
            ->get()
            ->map(fn (InternshipClearance $c) => [
                'id' => $c->id,
                'mahasiswa' => [
                    'nama_lengkap' => $c->magangAktif->pendaftaran->mahasiswa->nama_lengkap,
                    'nim' => $c->magangAktif->pendaftaran->mahasiswa->nim,
                ],
                'industri' => $c->magangAktif->pendaftaran->industri->nama_perusahaan ?? '-',
                'original_filename' => $c->original_filename,
                'status' => $c->status->value,
                'status_label' => $c->status->label(),
                'status_color' => $c->status->badgeColor(),
                'rejection_note' => $c->rejection_note,
                'submitted_at' => $c->submitted_at?->format('d M Y H:i'),
                'uploaded_at' => $c->uploaded_at->format('d M Y H:i'),
                'reviewer_name' => $c->reviewer?->dosen?->nama_dosen ?? $c->reviewer?->username,
                'reviewed_at' => $c->reviewed_at?->format('d M Y H:i'),
            ]);

        return $this->renderReviewPage($user, $clearances);
    }

    // ──────────────────────────────────────
    // Dosen: Approve Clearance
    // ──────────────────────────────────────

    public function approve(Request $request, InternshipClearance $clearance)
    {
        if ($request->user()->cannot('review', $clearance)) {
            abort(403, 'Anda tidak memiliki akses untuk memverifikasi dokumen ini.');
        }

        try {
            $this->clearanceService->approve($clearance, $request->user());

            return back()->with('success', 'Clearance Issued By Company berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Dosen: Reject Clearance
    // ──────────────────────────────────────

    public function reject(Request $request, InternshipClearance $clearance)
    {
        $request->validate([
            'rejection_note' => 'required|string|max:1000',
        ], [
            'rejection_note.required' => 'Catatan revisi/alasan penolakan wajib diisi.',
            'rejection_note.max' => 'Catatan maksimal 1000 karakter.',
        ]);

        if ($request->user()->cannot('review', $clearance)) {
            abort(403, 'Anda tidak memiliki akses untuk memverifikasi dokumen ini.');
        }

        try {
            $this->clearanceService->reject(
                $clearance,
                $request->user(),
                $request->input('rejection_note')
            );

            return back()->with('success', 'Clearance Issued By Company berhasil ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────

    private function renderReviewPage($user, $clearances)
    {
        $page = match ($user->role) {
            UserRole::SUPERVISOR_1 => 'DosenPembimbing/VerifikasiClearance',
            UserRole::SUPERVISOR_2 => 'DosenProdi/VerifikasiClearance',
            default => abort(403),
        };

        return Inertia::render($page, [
            'clearances' => $clearances,
        ]);
    }
}
