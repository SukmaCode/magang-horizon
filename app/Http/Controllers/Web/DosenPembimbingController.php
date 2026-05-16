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
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

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

            $pendingLaporan = $magangs->filter(fn ($m) => $m->laporanAkhir?->status_approval_kampus === StatusApproval::PENDING)->count();
            $studentsToGrade = $magangs->filter(fn ($m) => $m->penilaian === null || $m->penilaian->nilai_kampus === null)->count();

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
        $bimbingans = [];

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
                    'approval_letter_file' => $l->approval_letter_file,
                    'updated_at' => $l->updated_at->format('d M Y H:i'),
                ]);
                
            $bimbingans = \App\Models\Bimbingan::whereIn('magang_id', $magangIds)
                ->with('magangAktif.pendaftaran.mahasiswa')
                ->orderBy('tanggal', 'desc')
                ->get()
                ->groupBy('magang_id')
                ->map(fn($group) => $group->map(fn($b) => [
                    'id' => $b->id,
                    'tanggal' => $b->tanggal->format('Y-m-d'),
                    'tanggal_display' => $b->tanggal->format('d M Y'),
                    'catatan' => $b->catatan,
                    'is_approved' => $b->is_approved,
                    'mahasiswa_nama' => $b->magangAktif->pendaftaran->mahasiswa->nama_lengkap,
                ]))->toArray();
        }

        return Inertia::render('DosenPembimbing/ReviewLaporan', [
            'laporans' => $laporans,
            'bimbingans' => $bimbingans,
        ]);
    }

    public function submitReviewLaporan(Request $request, LaporanAkhir $laporan)
    {
        $request->validate([
            'status' => 'required|in:revisi,disetujui',
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            $user = $request->user();
            $dosen = $user->dosen;
            
            $this->authorizeLaporanAccess($dosen, $laporan);

            $statusEnum = $request->status === 'disetujui' ? StatusApproval::DISETUJUI : StatusApproval::REVISI;
            
            $this->gradingService->reviewLaporan($laporan, $statusEnum, $request->input('catatan'), $user);

            return back()->with('success', 'Review laporan berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function approveBimbingan(Request $request, \App\Models\Bimbingan $bimbingan)
    {
        $user = $request->user();
        $dosen = $user->dosen;
        if (! $dosen || $bimbingan->magangAktif->supervisor_kampus_id !== $dosen->id) {
            abort(403, 'Unauthorized access.');
        }

        $bimbingan->update(['is_approved' => true]);
        return back()->with('success', 'Bimbingan disetujui.');
    }

    public function rejectBimbingan(Request $request, \App\Models\Bimbingan $bimbingan)
    {
        $user = $request->user();
        $dosen = $user->dosen;
        if (! $dosen || $bimbingan->magangAktif->supervisor_kampus_id !== $dosen->id) {
            abort(403, 'Unauthorized access.');
        }

        $bimbingan->delete(); // Or add a reason/status, but for now reject = delete or we could keep it with is_approved=false.
        // Usually rejected bimbingan can just be deleted so student can re-enter. Let's delete.
        return back()->with('success', 'Bimbingan ditolak dan dihapus.');
    }

    public function downloadLaporan(Request $request, LaporanAkhir $laporan)
    {
        $user = $request->user();
        $dosen = $user->dosen;

        if (!$dosen) {
            abort(403, 'Unauthorized access.');
        }

        // Verify this laporan belongs to a student supervised by this dosen
        $magangIds = $dosen->magangAktifs()->pluck('id');
        if (!$magangIds->contains($laporan->magang_id)) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        if (!$laporan->file_laporan) {
            return back()->with('error', 'File laporan belum tersedia.');
        }

        $path = storage_path('app/private/'.$laporan->file_laporan);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        $mahasiswaName = $laporan->magangAktif->pendaftaran->mahasiswa->nama_lengkap ?? 'Mahasiswa';

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function generateApprovalLetter(Request $request, LaporanAkhir $laporan)
    {
        $user = $request->user();
        $dosen = $user->dosen;

        if (!$dosen) {
            abort(403, 'Unauthorized access.');
        }

        $this->authorizeLaporanAccess($dosen, $laporan);
        
        return app(\App\Services\PdfService::class)->streamApprovalLetter($laporan, $user, $dosen);
    }

    public function downloadApprovalLetter(Request $request, LaporanAkhir $laporan)
    {
        $user = $request->user();
        $dosen = $user->dosen;

        if (! $dosen) {
            abort(403, 'Unauthorized access.');
        }

        $magangIds = $dosen->magangAktifs()->pluck('id');
        if (! $magangIds->contains($laporan->magang_id)) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        if (! $laporan->approval_letter_file) {
            return back()->with('error', 'File Approval Letter belum tersedia.');
        }

        $path = storage_path('app/private/'.$laporan->approval_letter_file);

        if (! file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // ──────────────────────────────────────
    // Authorization Helpers
    // ──────────────────────────────────────

    private function authorizeLaporanAccess(?\App\Models\Dosen $dosen, LaporanAkhir $laporan): void
    {
        if (!$dosen) {
            abort(403, 'Unauthorized access.');
        }

        $magangIds = $dosen->magangAktifs()->pluck('id');
        if (!$magangIds->contains($laporan->magang_id)) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }
    }
}
