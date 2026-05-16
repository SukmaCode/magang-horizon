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
        $statusTahapan = \App\Enums\StatusTahapan::PELAKSANAAN->value;

        $magangs = [];
        if ($industri) {
            $magangs = $this->clearanceService->getIndustryClearanceData($industri);
        }

        return Inertia::render('Industri/Clearance', [
            'magangs' => $magangs,
            'statusTahapan' => $statusTahapan,
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

        $data = $mahasiswa ? $this->clearanceService->getStudentClearanceData($mahasiswa) : [
            'clearance' => null,
            'pdfBase64' => null,
            'hasMagang' => false,
            'industriName' => '-',
        ];

        return Inertia::render('Mahasiswa/Clearance', $data);
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

        $clearances = $this->clearanceService->getReviewClearancesData($user);

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
