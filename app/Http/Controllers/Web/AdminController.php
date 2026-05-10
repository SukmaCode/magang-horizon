<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MagangAktif;
use App\Models\Periode;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct(
        private readonly AdminService $adminService,
    ) {}

    // ──────────────────────────────────────
    // Dashboard (Rekap Sistem)
    // ──────────────────────────────────────

    public function dashboard(Request $request)
    {
        return Inertia::render('Admin/Dashboard', $this->adminService->getDashboardStats());
    }

    // ──────────────────────────────────────
    // Kelola Periode Magang
    // ──────────────────────────────────────

    public function kelolaPeriode()
    {
        return Inertia::render('Admin/Periode', [
            'periodes' => $this->adminService->getPeriodes(),
        ]);
    }

    public function storePeriode(Request $request)
    {
        $validated = $request->validate([
            'tahun_akademik' => 'required|string',
            'semester' => 'required|in:ganjil,genap,pendek',
            'tanggal_buka' => 'required|date',
            'tanggal_tutup' => 'required|date|after:tanggal_buka',
            'is_active' => 'boolean',
        ]);

        $this->adminService->createPeriode($validated);

        return back()->with('success', 'Periode magang berhasil ditambahkan.');
    }

    public function updatePeriode(Request $request, Periode $periode)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $this->adminService->togglePeriode($periode, $validated['is_active']);

        return back()->with('success', 'Status periode berhasil diubah.');
    }

    // ──────────────────────────────────────
    // Assign Dosen Pembimbing
    // ──────────────────────────────────────

    public function assignPembimbing()
    {
        return Inertia::render('Admin/AssignPembimbing', $this->adminService->getAssignPembimbingData());
    }

    // storeAssignPembimbing() telah dipindahkan ke PembimbingAssignmentController::store()
    // Assign pembimbing hanya bersifat dokumentasi penugasan resmi,
    // TIDAK mempengaruhi status_tahapan atau alur kelulusan.

    // ──────────────────────────────────────
    // Verifikasi Kelulusan (Penerbitan Sertifikat)
    // ──────────────────────────────────────

    public function verifikasiKelulusan()
    {
        return Inertia::render('Admin/VerifikasiKelulusan', $this->adminService->getVerifikasiKelulusanData());
    }

    public function terbitkanSertifikat(Request $request, MagangAktif $magangAktif)
    {
        try {
            $this->adminService->terbitkanSertifikat($magangAktif);

            return back()->with('success', 'Sertifikat kelulusan resmi diterbitkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Manajemen User
    // ──────────────────────────────────────

    public function manajemenUser()
    {
        return Inertia::render('Admin/ManajemenUser', [
            'users' => $this->adminService->getManajemenUserData(),
        ]);
    }
}
