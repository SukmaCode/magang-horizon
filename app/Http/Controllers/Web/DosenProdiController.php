<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MagangAktif;
use App\Models\User;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DosenProdiController extends Controller
{
    public function __construct(
        private readonly GradingService $gradingService,
    ) {}

    // ──────────────────────────────────────
    // Dashboard (Monitoring Makro)
    // ──────────────────────────────────────

    public function dashboard(Request $request)
    {
        // For MVP, just count all active internships
        $activeMagangs = MagangAktif::with('pendaftaran.mahasiswa', 'pendaftaran.industri')->get();
        
        $totalActive = $activeMagangs->count();
        $totalFinished = $activeMagangs->where('status_tahapan', 'lulus')->count();
        
        $recentMagangs = $activeMagangs->sortByDesc('created_at')->take(5)->map(fn ($m) => [
            'id' => $m->id,
            'mahasiswa' => $m->pendaftaran->mahasiswa->nama_lengkap,
            'industri' => $m->pendaftaran->industri->nama_perusahaan,
            'status' => $m->status_tahapan->label(),
        ])->values();

        return Inertia::render('DosenProdi/Dashboard', [
            'totalActive' => $totalActive,
            'totalFinished' => $totalFinished,
            'recentMagangs' => $recentMagangs,
        ]);
    }

    // ──────────────────────────────────────
    // Verifikasi Kelulusan (TTD Persetujuan)
    // ──────────────────────────────────────

    public function verifikasiKelulusan(Request $request)
    {
        // Get all magangs that have complete grades but not yet verified
        $magangs = MagangAktif::with('pendaftaran.mahasiswa', 'penilaian', 'laporanAkhir')
            ->whereHas('penilaian', function ($q) {
                $q->whereNotNull('nilai_industri')
                  ->whereNotNull('nilai_kampus')
                  ->where('status_verifikasi_admin', false);
            })
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'mahasiswa' => [
                    'nama_lengkap' => $m->pendaftaran->mahasiswa->nama_lengkap,
                    'nim' => $m->pendaftaran->mahasiswa->nim,
                ],
                'nilai_industri' => $m->penilaian->nilai_industri,
                'nilai_kampus' => $m->penilaian->nilai_kampus,
                'nilai_akhir' => $m->penilaian->nilai_akhir,
                'penilaian_id' => $m->penilaian->id,
            ]);

        return Inertia::render('DosenProdi/VerifikasiKelulusan', [
            'magangs' => $magangs,
        ]);
    }

    public function submitVerifikasi(Request $request, $penilaianId)
    {
        try {
            $penilaian = \App\Models\Penilaian::findOrFail($penilaianId);
            $this->gradingService->verify($penilaian);
            
            // Advance status to LULUS
            $penilaian->magangAktif->update(['status_tahapan' => \App\Enums\StatusTahapan::LULUS]);

            return back()->with('success', 'Verifikasi kelulusan berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
