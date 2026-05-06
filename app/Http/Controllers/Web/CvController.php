<?php

namespace App\Http\Controllers\Web;

use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CvController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) {}

    // ──────────────────────────────────────
    // Mahasiswa: Halaman Manajemen CV
    // ──────────────────────────────────────
    public function index(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $cvBase64 = null;
        if ($mahasiswa && $mahasiswa->cv_file_path) {
            $path = storage_path('app/private/'.$mahasiswa->cv_file_path);
            if (file_exists($path)) {
                $cvBase64 = 'data:application/pdf;base64,'.base64_encode(file_get_contents($path));
            }
        }

        return Inertia::render('Mahasiswa/ManajemenCV', [
            'hasCv' => $mahasiswa && $mahasiswa->cv_file_path !== null,
            'cvBase64' => $cvBase64,
        ]);
    }

    // ──────────────────────────────────────
    // Mahasiswa: Upload & Replace CV
    // ──────────────────────────────────────
    public function upload(Request $request)
    {
        $request->validate([
            'cv_file' => 'required|file|mimes:pdf|max:10240',
        ], [
            'cv_file.required' => 'Pilih file PDF yang akan diupload.',
            'cv_file.mimes' => 'File harus berformat PDF.',
            'cv_file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        // Hapus file lama jika ada
        if ($mahasiswa->cv_file_path) {
            Storage::disk('private')->delete($mahasiswa->cv_file_path);
        }

        $cvFile = $request->file('cv_file');
        $cvPath = $cvFile->store('documents/cv/'.$mahasiswa->id, 'private');

        $mahasiswa->update(['cv_file_path' => $cvPath]);

        // Opsional: simpan di document_service untuk log
        $this->documentService->upload(
            $cvFile,
            DocumentType::CV,
            $mahasiswa,
            $mahasiswa->user_id
        );

        return back()->with('success', 'CV berhasil diupload dan diperbarui.');
    }

    // ──────────────────────────────────────
    // Mahasiswa: Delete CV
    // ──────────────────────────────────────
    public function destroy(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if ($mahasiswa && $mahasiswa->cv_file_path) {
            Storage::disk('private')->delete($mahasiswa->cv_file_path);
            $mahasiswa->update(['cv_file_path' => null]);

            return back()->with('success', 'CV berhasil dihapus.');
        }

        return back()->with('error', 'CV tidak ditemukan.');
    }

    // ──────────────────────────────────────
    // Mahasiswa: Preview Own CV
    // ──────────────────────────────────────
    public function previewCv(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa || ! $mahasiswa->cv_file_path) {
            abort(404, 'CV tidak ditemukan.');
        }

        $path = storage_path('app/private/'.$mahasiswa->cv_file_path);

        if (! file_exists($path)) {
            abort(404, 'File CV fisik tidak ditemukan.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="CV_'.$mahasiswa->nim.'.pdf"',
        ]);
    }

    // ──────────────────────────────────────
    // Industri: Preview Applicant CV
    // ──────────────────────────────────────
    public function viewApplicantCv(Request $request, Pendaftaran $pendaftaran)
    {
        // Otorisasi: Pastikan pendaftaran ini memang untuk industri si user yang login
        $user = $request->user();
        $industri = $user->industri;

        if (! $industri || $pendaftaran->industri_id !== $industri->id) {
            abort(403, 'Akses ditolak.');
        }

        $mahasiswa = $pendaftaran->mahasiswa;

        if (! $mahasiswa || ! $mahasiswa->cv_file_path) {
            abort(404, 'Kandidat ini belum mengupload CV.');
        }

        $path = storage_path('app/private/'.$mahasiswa->cv_file_path);

        if (! file_exists($path)) {
            abort(404, 'File CV fisik tidak ditemukan.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="CV_'.$mahasiswa->nama_lengkap.'.pdf"',
        ]);
    }
}
