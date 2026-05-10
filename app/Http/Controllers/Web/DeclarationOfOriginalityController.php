<?php

namespace App\Http\Controllers\Web;

use App\Enums\DeclarationStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\DeclarationOfOriginality;
use App\Models\MagangAktif;
use App\Services\DeclarationOfOriginalityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeclarationOfOriginalityController extends Controller
{
    public function __construct(
        private readonly DeclarationOfOriginalityService $declarationService,
    ) {}

    // ──────────────────────────────────────
    // Mahasiswa: Show Declaration Page
    // ──────────────────────────────────────

    public function show(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $declaration = null;
        $pdfBase64 = null;
        $magang = null;

        if ($mahasiswa) {
            // Get the active magang for this student
            $magang = MagangAktif::whereHas('pendaftaran', function ($q) use ($mahasiswa) {
                $q->where('mahasiswa_id', $mahasiswa->id)
                  ->where('status_seleksi', 'diterima');
            })->with('declarationOfOriginality.reviewer')->latest()->first();

            if ($magang && $magang->declarationOfOriginality) {
                $decl = $magang->declarationOfOriginality;

                $declaration = [
                    'id' => $decl->id,
                    'original_filename' => $decl->original_filename,
                    'status' => $decl->status->value,
                    'status_label' => $decl->status->label(),
                    'status_color' => $decl->status->badgeColor(),
                    'rejection_note' => $decl->rejection_note,
                    'uploaded_at' => $decl->uploaded_at->format('d M Y H:i'),
                    'reviewer_name' => $decl->reviewer?->dosen?->nama_dosen ?? $decl->reviewer?->username,
                    'reviewed_at' => $decl->reviewed_at?->format('d M Y H:i'),
                    'can_update' => $decl->canBeUpdated(),
                ];

                // Generate base64 PDF for preview
                $path = storage_path('app/private/' . $decl->file_path);
                if (file_exists($path)) {
                    $pdfBase64 = 'data:application/pdf;base64,' . base64_encode(file_get_contents($path));
                }
            }
        }

        return Inertia::render('Mahasiswa/Declaration', [
            'declaration' => $declaration,
            'pdfBase64' => $pdfBase64,
            'hasMagang' => $magang !== null,
        ]);
    }

    // ──────────────────────────────────────
    // Mahasiswa: Upload New Declaration
    // ──────────────────────────────────────

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ], [
            'file.required' => 'Pilih file PDF yang akan diupload.',
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $magang = MagangAktif::whereHas('pendaftaran', function ($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id)
              ->where('status_seleksi', 'diterima');
        })->latest()->first();

        if (! $magang) {
            return back()->with('error', 'Anda belum memiliki magang aktif.');
        }

        // Policy check
        if ($user->cannot('upload', [DeclarationOfOriginality::class, $magang])) {
            abort(403, 'Anda tidak memiliki akses untuk mengupload dokumen ini.');
        }

        // Check if declaration already exists
        if ($magang->declarationOfOriginality) {
            return back()->with('error', 'Declaration sudah ada. Gunakan fitur update untuk mengganti file.');
        }

        try {
            $this->declarationService->upload($magang, $request->file('file'));

            return back()->with('success', 'Declaration of Originality berhasil diupload.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Mahasiswa: Update (Replace) Declaration
    // ──────────────────────────────────────

    public function update(Request $request, DeclarationOfOriginality $declaration)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ], [
            'file.required' => 'Pilih file PDF yang akan diupload.',
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        // Policy check
        if ($request->user()->cannot('update', $declaration)) {
            abort(403, 'Anda tidak dapat mengubah dokumen ini.');
        }

        try {
            $this->declarationService->update($declaration, $request->file('file'));

            return back()->with('success', 'Declaration of Originality berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Shared: Preview Declaration (inline PDF)
    // ──────────────────────────────────────

    public function preview(Request $request, DeclarationOfOriginality $declaration)
    {
        if ($request->user()->cannot('view', $declaration)) {
            abort(403, 'Akses ditolak.');
        }

        try {
            $fileInfo = $this->declarationService->getFileResponse($declaration);

            return response()->file($fileInfo['path'], [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileInfo['name'] . '"',
            ]);
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Shared: Download Declaration
    // ──────────────────────────────────────

    public function download(Request $request, DeclarationOfOriginality $declaration)
    {
        if ($request->user()->cannot('download', $declaration)) {
            abort(403, 'Akses ditolak.');
        }

        try {
            $fileInfo = $this->declarationService->getFileResponse($declaration);

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

        $query = DeclarationOfOriginality::with([
            'magangAktif.pendaftaran.mahasiswa',
            'magangAktif.pendaftaran.industri',
            'reviewer',
        ]);

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

        $declarations = $query->latest('uploaded_at')
            ->get()
            ->map(fn (DeclarationOfOriginality $d) => [
                'id' => $d->id,
                'mahasiswa' => [
                    'nama_lengkap' => $d->magangAktif->pendaftaran->mahasiswa->nama_lengkap,
                    'nim' => $d->magangAktif->pendaftaran->mahasiswa->nim,
                ],
                'industri' => $d->magangAktif->pendaftaran->industri->nama_perusahaan ?? '-',
                'original_filename' => $d->original_filename,
                'status' => $d->status->value,
                'status_label' => $d->status->label(),
                'status_color' => $d->status->badgeColor(),
                'rejection_note' => $d->rejection_note,
                'uploaded_at' => $d->uploaded_at->format('d M Y H:i'),
                'reviewer_name' => $d->reviewer?->dosen?->nama_dosen ?? $d->reviewer?->username,
                'reviewed_at' => $d->reviewed_at?->format('d M Y H:i'),
            ]);

        return $this->renderReviewPage($user, $declarations);
    }

    // ──────────────────────────────────────
    // Dosen: Approve Declaration
    // ──────────────────────────────────────

    public function approve(Request $request, DeclarationOfOriginality $declaration)
    {
        if ($request->user()->cannot('review', $declaration)) {
            abort(403, 'Anda tidak memiliki akses untuk memverifikasi dokumen ini.');
        }

        try {
            $this->declarationService->approve($declaration, $request->user());

            return back()->with('success', 'Declaration of Originality berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Dosen: Reject Declaration
    // ──────────────────────────────────────

    public function reject(Request $request, DeclarationOfOriginality $declaration)
    {
        $request->validate([
            'rejection_note' => 'required|string|max:1000',
        ], [
            'rejection_note.required' => 'Catatan revisi/alasan penolakan wajib diisi.',
            'rejection_note.max' => 'Catatan maksimal 1000 karakter.',
        ]);

        if ($request->user()->cannot('review', $declaration)) {
            abort(403, 'Anda tidak memiliki akses untuk memverifikasi dokumen ini.');
        }

        try {
            $this->declarationService->reject(
                $declaration,
                $request->user(),
                $request->input('rejection_note')
            );

            return back()->with('success', 'Declaration of Originality berhasil ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────

    private function renderReviewPage($user, $declarations)
    {
        $page = match ($user->role) {
            UserRole::SUPERVISOR_1 => 'DosenPembimbing/VerifikasiDeclaration',
            UserRole::SUPERVISOR_2 => 'DosenProdi/VerifikasiDeclaration',
            default => abort(403),
        };

        return Inertia::render($page, [
            'declarations' => $declarations,
        ]);
    }
}
