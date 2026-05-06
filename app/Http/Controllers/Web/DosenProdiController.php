<?php

namespace App\Http\Controllers\Web;

use App\Enums\StatusTahapan;
use App\Http\Controllers\Controller;
use App\Models\MagangAktif;
use App\Models\Penilaian;
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
        // ✅ Filter di database, bukan di PHP collection
        $totalActive = MagangAktif::count();
        $totalFinished = MagangAktif::where('status_tahapan', StatusTahapan::LULUS)->count();

        // ✅ Query terpisah untuk recent list — hanya ambil 5 record
        $recentMagangs = MagangAktif::with('pendaftaran.mahasiswa', 'pendaftaran.industri')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'mahasiswa' => $m->pendaftaran->mahasiswa->nama_lengkap,
                'industri' => $m->pendaftaran->industri->nama_perusahaan,
                'status' => $m->status_tahapan->label(),
            ])
            ->values();

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
            $penilaian = Penilaian::findOrFail($penilaianId);

            // ✅ GradingService::verify() sekarang juga handle transisi status ke LULUS
            $this->gradingService->verify($penilaian);

            return back()->with('success', 'Verifikasi kelulusan berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
