<?php

namespace App\Http\Controllers;

use App\Services\SuratKeputusanService;
use App\Models\SuratKeputusanPembimbing;
use App\Models\PembimbingAssignment;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratKeputusanController extends Controller
{
    protected SuratKeputusanService $skService;

    public function __construct(SuratKeputusanService $skService)
    {
        $this->skService = $skService;
    }

    /**
     * Kaprodi: View list of assignments to upload SK.
     */
    public function indexUpload()
    {
        $assignments = PembimbingAssignment::with(['magangAktif.pendaftaran.mahasiswa.user', 'dosen.user', 'suratKeputusan'])->get();
        return Inertia::render('DosenProdi/UploadSK', [
            'assignments' => $assignments
        ]);
    }

    /**
     * Kaprodi: Store the uploaded SK.
     */
    public function store(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:pembimbing_assignments,id',
            'nomor_sk' => 'required|string|max:255',
            'tanggal_sk' => 'required|date',
            'file_sk' => 'required|mimes:pdf|max:10240', // Max 10MB
        ]);

        $this->skService->uploadSK(
            $request->assignment_id,
            $request->nomor_sk,
            $request->tanggal_sk,
            $request->file('file_sk'),
            $request->user()->id
        );

        return redirect()->back()->with('success', 'SK Pembimbing berhasil diunggah.');
    }

    /**
     * Dosen Pembimbing: Download their SK.
     */
    public function download(Request $request, SuratKeputusanPembimbing $sk)
    {
        $user = $request->user();

        // Authorization: Only Dosen Prodi, Admin, or the assigned Dosen can download
        if ($user->role !== UserRole::SUPERVISOR_2 && $user->role !== UserRole::ADMIN) {
            $dosen = $user->dosen;
            if (!$dosen || $dosen->id !== $sk->assignment->dosen_id) {
                abort(403, 'Anda tidak berhak mengunduh SK ini.');
            }
        }

        if (!Storage::disk('public')->exists($sk->file_sk)) {
            abort(404, 'File SK tidak ditemukan.');
        }

        return Storage::disk('public')->download($sk->file_sk, 'SK_Pembimbing_' . $sk->nomor_sk . '.pdf');
    }
}
