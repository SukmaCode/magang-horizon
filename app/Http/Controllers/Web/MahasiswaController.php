<?php

namespace App\Http\Controllers\Web;

use App\Enums\DocumentType;
use App\Enums\StatusTahapan;
use App\Http\Controllers\Controller;
use App\Models\Industri;
use App\Models\Logbook;
use App\Models\MagangAktif;
use App\Services\ApplicationService;
use App\Services\DailyLogService;
use App\Services\DocumentService;
use App\Services\GradingService;
use App\Services\InternshipService;
use App\Services\MahasiswaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use function PHPUnit\Framework\isNull;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Bimbingan;

class MahasiswaController extends Controller
{
    public function __construct(
        private readonly ApplicationService $applicationService,
        private readonly DocumentService $documentService,
        private readonly DailyLogService $dailyLogService,
        private readonly GradingService $gradingService,
        private readonly InternshipService $internshipService,
        private readonly MahasiswaService $mahasiswaService,
    ) {}

    // ──────────────────────────────────────
    // Apply Magang (Kirim CV)
    // ──────────────────────────────────────

    public function kirimCV(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        // ✅ List industri tetap di sini — ini data statis untuk form, bukan business logic
        $industris = Industri::select('id', 'nama_perusahaan', 'alamat', 'kontak_person')
            ->orderBy('nama_perusahaan')
            ->get();

        // ✅ Query kompleks & mapping dipindah ke MahasiswaService
        $data = $mahasiswa
            ? $this->mahasiswaService->getKirimCvData($mahasiswa)
            : ['pendaftarans' => [], 'hasAccepted' => false, 'pendingCount' => 0, 'cvUploaded' => false];

        return Inertia::render('Mahasiswa/KirimCV', [
            'industris' => $industris,
            'pendaftarans' => $data['pendaftarans'],
            'hasAccepted' => $data['hasAccepted'],
            'pendingCount' => $data['pendingCount'],
            'maxApplications' => 3,
            'cvUploaded' => $data['cvUploaded'],
            'linkedinFilled' => $mahasiswa?->hasLinkedIn() ?? false,
        ]);
    }

    public function storeApplication(Request $request)
    {
        $request->validate([
            'industri_id' => 'required|exists:industris,id',
            'cv_file' => 'required_without:cv_exists|file|mimes:pdf|max:10240',
        ], [
            'industri_id.required' => 'Pilih industri tujuan magang.',
            'cv_file.required_without' => 'Upload file CV Anda (format PDF).',
            'cv_file.mimes' => 'File CV harus berformat PDF.',
            'cv_file.max' => 'Ukuran file CV maksimal 10MB.',
        ]);

        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        try {
            $this->applicationService->submitApplication(
                $mahasiswa,
                $request->input('industri_id'),
                $request->file('cv_file')
            );

            return back()->with('success', 'Lamaran berhasil dikirim! Tunggu hasil seleksi dari industri.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Agreement
    // ──────────────────────────────────────

    public function agreement(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        // ✅ Foreach loop + mapping dipindah ke MahasiswaService
        $agreements = $mahasiswa
            ? $this->mahasiswaService->getAgreements($mahasiswa)
            : [];

        return Inertia::render('Mahasiswa/Agreement', [
            'agreements' => $agreements,
        ]);
    }

    public function downloadAgreement(Request $request, MagangAktif $magangAktif)
    {
        $user = $request->user();

        if ($magangAktif->pendaftaran->mahasiswa_id !== $user->mahasiswa->id) {
            abort(403, 'Unauthorized access to this document.');
        }

        if (! $magangAktif->file_agreement_industri) {
            return back()->with('error', 'Dokumen agreement belum tersedia.');
        }

        $path = storage_path('app/private/'.$magangAktif->file_agreement_industri);

        if (! file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($path, "Agreement-Industri-{$magangAktif->industri->nama_perusahaan}.pdf");
    }

    public function acceptAgreement(Request $request, MagangAktif $magangAktif)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $user = $request->user();
        if ($magangAktif->pendaftaran->mahasiswa_id !== $user->mahasiswa->id) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $path = $request->file('file')->store('documents/agreements/mahasiswa/'.$magangAktif->id, 'private');
            $this->internshipService->acceptAgreement($magangAktif, $path);

            return back()->with('success', 'Agreement berhasil diterima dan ditandatangani.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejectAgreement(Request $request, MagangAktif $magangAktif)
    {
        $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        $user = $request->user();
        if ($magangAktif->pendaftaran->mahasiswa_id !== $user->mahasiswa->id) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $this->internshipService->rejectAgreement($magangAktif, $request->input('alasan'));

            return back()->with('success', 'Agreement telah ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Dashboard
    // ──────────────────────────────────────

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $data = $this->mahasiswaService->getDashboardData($mahasiswa);

        return Inertia::render('Mahasiswa/Dashboard', [
            'statusMagang' => $data['statusMagang'],
            'statusDescription' => $data['statusDescription'],
            'logbookStats' => $data['logbookStats'],
            'pendaftaranCount' => $data['pendaftaranCount'],
            'recentLogbooks' => $data['recentLogbooks'],
            'hasMagang' => $data['magang'] !== null,
            'magang' => $data['magang'] ? [
                'id' => $data['magang']->id,
                'has_completion_letter' => $data['magang']->sertifikat?->posisi_magang !== null,
            ] : null,
        ]);
    }

    // ──────────────────────────────────────
    // Logbook
    // ──────────────────────────────────────

    public function logbook(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;
        $magang = $this->getActiveMagang($mahasiswa);

        $logbooks = [];
        $canSubmit = false;
        $magangId = null;
        $statusTahapan = null;

        if ($magang) {
            $magangId = $magang->id;
            $statusTahapan = $magang->status_tahapan->allowsDailyLogs();

            // $canSubmit = $magang->status_tahapan->allowsDailyLogs() && $magang->status_agreement->allowsDailyLogs();
            $canSubmit = $magang->status_tahapan->allowsDailyLogs();

            $logbooks = $magang->logbooks()
                ->orderBy('tanggal_waktu', 'desc')
                ->paginate(15)
                ->through(fn (Logbook $l) => [
                    'id' => $l->id,
                    'tanggal' => $l->tanggal_waktu->format('Y-m-d\TH:i'),
                    'tanggal_display' => $l->tanggal_waktu->format('d M Y H:i'),
                    'kegiatan' => $l->kegiatan,
                    'status_presensi' => $l->status_presensi->value,
                    'status_presensi_label' => $l->status_presensi->label(),
                    'is_approved' => $l->is_approved_industri,
                    'komentar_industri' => $l->komentar_industri,
                    'is_checked_kampus' => $l->is_checked_kampus,
                ]);
        }

        return Inertia::render('Mahasiswa/Logbook', [
            'logbooks' => $logbooks,
            'canSubmit' => $canSubmit,
            'magangId' => $magangId,
            'statusTahapan' => $statusTahapan,
        ]);
    }

    public function storeLogbook(Request $request)
    {
        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        if (! $magang) {
            return back()->with('error', 'Anda belum memiliki magang aktif.');
        }

        $validated = $request->validate([
            'kegiatan' => 'required|string|max:2000',
            'status_presensi' => 'nullable|in:hadir,izin,sakit',
            'tanggal_waktu' => [
                'required',
                'date_format:Y-m-d\TH:i',
            ],
        ]);

        try {
            $this->dailyLogService->submit($magang, $validated);

            return back()->with('success', 'Logbook berhasil disubmit.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Laporan Akhir
    // ──────────────────────────────────────

    public function laporanAkhir(Request $request)
    {
        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        $laporan = null;
        $canUpload = false;
        $magangId = null;
        $bimbingans = [];
        $approvedBimbinganCount = 0;

        if ($magang) {
            $magangId = $magang->id;
            
            $bimbinganQuery = $magang->bimbingans()->orderBy('tanggal', 'desc')->get();
            $bimbingans = $bimbinganQuery->map(fn($b) => [
                'id' => $b->id,
                'tanggal' => $b->tanggal->format('Y-m-d'),
                'catatan' => $b->catatan,
                'is_approved' => $b->is_approved,
            ]);
            $approvedBimbinganCount = $magang->bimbingans()->where('is_approved', true)->count();

            $canUpload = $magang->status_tahapan->allowsReportUpload() && $approvedBimbinganCount >= 8;
            $laporan = $magang->laporanAkhir;

            if ($laporan) {
                $laporan = [
                    'id' => $laporan->id,
                    'file_laporan' => $laporan->file_laporan,
                    'status' => $laporan->status_approval_kampus->value,
                    'status_label' => $laporan->status_approval_kampus->label(),
                    'catatan_revisi' => $laporan->catatan_revisi,
                    'updated_at' => $laporan->updated_at->format('d M Y H:i'),
                ];
            }
        }

        return Inertia::render('Mahasiswa/LaporanAkhir', [
            'laporan' => $laporan,
            'canUpload' => $canUpload,
            'magangId' => $magangId,
            'bimbingans' => $bimbingans,
            'approvedBimbinganCount' => $approvedBimbinganCount,
        ]);
    }

    public function storeLaporan(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
        ]);

        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        if (! $magang) {
            return back()->with('error', 'Anda belum memiliki magang aktif.');
        }
        
        $approvedBimbinganCount = $magang->bimbingans()->where('is_approved', true)->count();
        if ($approvedBimbinganCount < 8) {
            return back()->with('error', 'Anda belum memenuhi syarat 8 kali bimbingan yang disetujui.');
        }

        try {
            $path = $request->file('file')->store('documents/laporan', 'private');
            $this->gradingService->uploadLaporan($magang, $path);

            return back()->with('success', 'Laporan akhir berhasil diupload.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storeBimbingan(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'catatan' => 'required|string|max:1000',
        ]);

        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        if (! $magang) {
            return back()->with('error', 'Anda belum memiliki magang aktif.');
        }

        $magang->bimbingans()->create([
            'tanggal' => $request->tanggal,
            'catatan' => $request->catatan,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Catatan bimbingan berhasil ditambahkan.');
    }

    public function generatePdf(Request $request)
    {
        $request->validate([
            'summary' => 'required|string',
            'duties' => 'required|array',
            'knowledge' => 'required|array',
            'skills' => 'required|array',
            'attitude' => 'required|array',
        ]);

        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        if (! $magang) {
            return back()->with('error', 'Anda belum memiliki magang aktif.');
        }
        
        $approvedBimbinganCount = $magang->bimbingans()->where('is_approved', true)->count();
        if ($approvedBimbinganCount < 8) {
            return back()->with('error', 'Anda harus memiliki minimal 8 bimbingan yang disetujui.');
        }

        $data = [
            'studentName' => $user->name,
            'institution' => $magang->industri->nama_perusahaan ?? '-',
            'department' => $user->mahasiswa->programStudi->nama ?? '-',
            'supervisorName' => $magang->supervisorIndustri->name ?? '-',
            'supervisorPosition' => 'Supervisor', 
            'workingHours' => '-', 
            'duration' => ($magang->tanggal_mulai?->format('d M Y') ?? '-') . ' - ' . ($magang->tanggal_selesai?->format('d M Y') ?? '-'),
            'summary' => $request->summary,
            'duties' => $request->duties,
            'knowledge' => $request->knowledge,
            'skills' => $request->skills,
            'attitude' => $request->attitude,
        ];

        $pdf = Pdf::loadView('pdf.laporan_akhir', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Laporan_Akhir_' . str_replace(' ', '_', $user->name) . '.pdf');
    }

    // ──────────────────────────────────────
    // Sertifikat / Kelulusan
    // ──────────────────────────────────────

    public function sertifikat(Request $request)
    {
        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        $data = $this->mahasiswaService->getSertifikatData($magang);

        return Inertia::render('Mahasiswa/Sertifikat', [
            'sertifikat' => $data['sertifikat'],
            'penilaian' => $data['penilaian'],
            'isLulus' => $data['isLulus'],
            'hasMagang' => $magang !== null,
        ]);
    }

    public function downloadSertifikat(Request $request)
    {
        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        if (!$magang || $magang->status_tahapan !== StatusTahapan::LULUS) {
            return back()->with('error', 'Anda belum berhak mendownload sertifikat.');
        }

        $sertifikat = $magang->sertifikat;

        if (!$sertifikat || !$sertifikat->file_sertifikat_path) {
            return back()->with('error', 'Sertifikat belum tersedia.');
        }

        $path = storage_path('app/private/'.$sertifikat->file_sertifikat_path);

        if (!file_exists($path)) {
            return back()->with('error', 'File sertifikat tidak ditemukan.');
        }

        return response()->download($path, "Sertifikat-{$sertifikat->nomor_sertifikat}.pdf");
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    private function getActiveMagang($mahasiswa): ?MagangAktif
    {
        return $mahasiswa?->active_magang;
    }
}
