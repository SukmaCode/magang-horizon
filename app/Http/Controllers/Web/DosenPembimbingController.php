<?php

namespace App\Http\Controllers\Web;

use App\Enums\StatusApproval;
use App\Http\Controllers\Controller;
use App\Models\LaporanAkhir;
use App\Models\Logbook;
use App\Models\MagangAktif;
use App\Services\DailyLogService;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DosenPembimbingController extends Controller
{
    public function __construct(
        private readonly DailyLogService $dailyLogService,
        private readonly GradingService $gradingService,
    ) {}

    // ──────────────────────────────────────
    // Dashboard
    // ──────────────────────────────────────

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $dosen = $user->dosen;

        $activeStudents = 0;
        $pendingLaporan = 0;
        $studentsToGrade = 0;
        $recentLaporan = [];

        if ($dosen) {
            $magangs = $dosen->magangAktifs()->with('pendaftaran.mahasiswa', 'laporanAkhir', 'penilaian')->get();
            $activeStudents = $magangs->count();

            // ✅ Filter di PHP collection (data sudah di-load dengan eager load)
            $pendingLaporan = $magangs->filter(fn ($m) => $m->laporanAkhir?->status_approval_kampus === StatusApproval::PENDING)->count();
            $studentsToGrade = $magangs->filter(fn ($m) => $m->penilaian === null || $m->penilaian->nilai_kampus === null)->count();

            // ✅ Ambil recent laporan dari magang yang sudah di-load, tidak perlu query terpisah
            $magangIds = $magangs->pluck('id');
            $recentLaporan = LaporanAkhir::whereIn('magang_id', $magangIds)
                ->with('magangAktif.pendaftaran.mahasiswa')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($l) => [
                    'id' => $l->id,
                    'mahasiswa' => $l->magangAktif->pendaftaran->mahasiswa->nama_lengkap,
                    'nim' => $l->magangAktif->pendaftaran->mahasiswa->nim,
                    'status' => $l->status_approval_kampus->value,
                    'status_label' => $l->status_approval_kampus->label(),
                    'updated_at' => $l->updated_at->format('d M Y'),
                ]);
        }

        $hasSignature = $user->signatures()->exists();

        // Ambil SK pembimbing jika sudah di-upload oleh Kaprodi
        $suratKeputusan = null;
        if ($dosen) {
            $assignment = $dosen->pembimbingAssignments()
                ->whereHas('suratKeputusan')
                ->with('suratKeputusan')
                ->first();

            if ($assignment && $assignment->suratKeputusan) {
                $suratKeputusan = [
                    'id' => $assignment->suratKeputusan->id,
                    'nomor_sk' => $assignment->suratKeputusan->nomor_sk,
                    'tanggal_sk' => $assignment->suratKeputusan->tanggal_sk->format('d M Y'),
                ];
            }
        }

        return Inertia::render('DosenPembimbing/Dashboard', [
            'activeStudents' => $activeStudents,
            'pendingLaporan' => $pendingLaporan,
            'studentsToGrade' => $studentsToGrade,
            'recentLaporan' => $recentLaporan,
            'hasSignature' => $hasSignature,
            'suratKeputusan' => $suratKeputusan,
        ]);
    }

    // ──────────────────────────────────────
    // Monitoring Logbook
    // ──────────────────────────────────────

    public function monitoringLogbook(Request $request)
    {
        $user = $request->user();
        $dosen = $user->dosen;

        $magangs = [];
        $logbooks = [];
        $selectedMagangId = $request->query('magang_id');

        if ($dosen) {
            $magangs = $dosen->magangAktifs()
                ->with('pendaftaran.mahasiswa', 'logbooks') // ✅ eager load logbooks
                ->get()
                ->map(fn ($m) => [
                    'id' => $m->id,
                    'nama_lengkap' => $m->pendaftaran->mahasiswa->nama_lengkap,
                    'nim' => $m->pendaftaran->mahasiswa->nim,
                    // ✅ ->logbooks (property) bukan ->logbooks() (method) — tidak ada N+1
                    'total_logbook' => $m->logbooks->count(),
                ]);

            if ($selectedMagangId) {
                $magang = $dosen->magangAktifs()->find($selectedMagangId);
                if ($magang) {
                    $logbooks = $magang->logbooks()
                        ->orderBy('tanggal_waktu', 'desc')
                        ->paginate(15)
                        ->through(fn (Logbook $l) => [
                            'id' => $l->id,
                            'tanggal_waktu' => $l->tanggal_waktu->format('d M Y H:i'),
                            'kegiatan' => $l->kegiatan,
                            'status_presensi_label' => $l->status_presensi->label(),
                            'status_presensi' => $l->status_presensi->value,
                            'is_approved' => $l->is_approved_industri,
                        ]);
                }
            }
        }

        return Inertia::render('DosenPembimbing/MonitoringLogbook', [
            'magangs' => $magangs,
            'logbooks' => $logbooks,
            'selectedMagangId' => $selectedMagangId ? (int) $selectedMagangId : null,
        ]);
    }

    // ──────────────────────────────────────
    // Review Laporan Akhir
    // ──────────────────────────────────────

    public function reviewLaporan(Request $request)
    {
        $user = $request->user();
        $dosen = $user->dosen;

        $laporans = [];

        if ($dosen) {
            $magangIds = $dosen->magangAktifs()->pluck('id');
            $laporans = LaporanAkhir::whereIn('magang_id', $magangIds)
                ->with('magangAktif.pendaftaran.mahasiswa')
                ->latest()
                ->paginate(15)
                ->through(fn (LaporanAkhir $l) => [
                    'id' => $l->id,
                    'mahasiswa' => [
                        'nama_lengkap' => $l->magangAktif->pendaftaran->mahasiswa->nama_lengkap,
                        'nim' => $l->magangAktif->pendaftaran->mahasiswa->nim,
                    ],
                    'file_laporan' => $l->file_laporan,
                    'status' => $l->status_approval_kampus->value,
                    'status_label' => $l->status_approval_kampus->label(),
                    'catatan_revisi' => $l->catatan_revisi,
                    'updated_at' => $l->updated_at->format('d M Y H:i'),
                ]);
        }

        return Inertia::render('DosenPembimbing/ReviewLaporan', [
            'laporans' => $laporans,
        ]);
    }

    public function submitReviewLaporan(Request $request, LaporanAkhir $laporan)
    {
        $request->validate([
            'status' => 'required|in:revisi,disetujui',
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            $statusEnum = $request->status === 'disetujui' ? StatusApproval::DISETUJUI : StatusApproval::REVISI;
            $this->gradingService->reviewLaporan($laporan, $statusEnum, $request->input('catatan'));

            return back()->with('success', 'Review laporan berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadLaporan(Request $request, LaporanAkhir $laporan)
    {
        $user = $request->user();
        $dosen = $user->dosen;

        if (! $dosen) {
            abort(403, 'Unauthorized access.');
        }

        // Verify this laporan belongs to a student supervised by this dosen
        $magangIds = $dosen->magangAktifs()->pluck('id');
        if (! $magangIds->contains($laporan->magang_id)) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        if (! $laporan->file_laporan) {
            return back()->with('error', 'File laporan belum tersedia.');
        }

        $path = storage_path('app/private/'.$laporan->file_laporan);

        if (! file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        $mahasiswaName = $laporan->magangAktif->pendaftaran->mahasiswa->nama_lengkap ?? 'Mahasiswa';

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // ──────────────────────────────────────
    // Input Nilai
    // ──────────────────────────────────────

    public function inputNilai(Request $request)
    {
        $user = $request->user();
        $dosen = $user->dosen;

        $magangs = [];
        if ($dosen) {
            $magangs = $dosen->magangAktifs()
                ->with('pendaftaran.mahasiswa', 'penilaian', 'laporanAkhir')
                ->get()
                ->map(fn (MagangAktif $m) => [
                    'id' => $m->id,
                    'mahasiswa' => [
                        'nama_lengkap' => $m->pendaftaran->mahasiswa->nama_lengkap,
                        'nim' => $m->pendaftaran->mahasiswa->nim,
                        'prodi' => $m->pendaftaran->mahasiswa->prodi,
                    ],
                    'status_laporan' => $m->laporanAkhir?->status_approval_kampus->label() ?? 'Belum Upload',
                    'nilai_kampus' => $m->penilaian?->nilai_kampus,
                    'has_graded' => $m->penilaian?->nilai_kampus !== null,
                ]);
        }

        return Inertia::render('DosenPembimbing/InputNilai', [
            'magangs' => $magangs,
        ]);
    }

    public function storeNilai(Request $request, MagangAktif $magangAktif)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $this->gradingService->gradeByCampus($magangAktif, (float) $request->input('nilai'));

            return back()->with('success', 'Nilai akademis berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
