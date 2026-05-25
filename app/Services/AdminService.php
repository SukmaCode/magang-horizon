<?php

namespace App\Services;

use App\Enums\StatusTahapan;
use App\Models\Dosen;
use App\Models\MagangAktif;
use App\Models\PembimbingAssignment;
use App\Models\Periode;
use App\Models\Sertifikat;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminService
{
    // ──────────────────────────────────────
    // Dashboard
    // ──────────────────────────────────────

    /**
     * Ambil data statistik untuk halaman dashboard admin.
     */
    public function getDashboardStats(): array
    {
        $totalUsers = User::count();
        $totalMagang = MagangAktif::count();
        $totalLulus = MagangAktif::where('status_tahapan', 'lulus')->count();

        $recentUsers = User::latest()->take(5)->get()->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name ?? $u->username,
            'email' => $u->email,
            'role' => $u->role->value,
            'created_at' => $u->created_at?->format('d M Y') ?? '-',
        ]);

        return compact('totalUsers', 'totalMagang', 'totalLulus', 'recentUsers');
    }

    // ──────────────────────────────────────
    // Kelola Periode
    // ──────────────────────────────────────

    /**
     * Ambil list semua periode untuk halaman Kelola Periode.
     */
    public function getPeriodes(): Collection
    {
        return Periode::latest()->get();
    }

    /**
     * Business rule: hanya boleh ada 1 periode aktif.
     * Nonaktifkan semua periode lain sebelum membuat yang baru.
     */
    public function createPeriode(array $data): Periode
    {
        if (! empty($data['is_active'])) {
            Periode::query()->update(['is_active' => false]);
        }

        return Periode::create($data);
    }

    /**
     * Toggle status aktif sebuah periode.
     * Business rule: jika diaktifkan, nonaktifkan periode lain terlebih dahulu.
     */
    public function togglePeriode(Periode $periode, bool $isActive): void
    {
        if ($isActive) {
            Periode::query()->update(['is_active' => false]);
        }

        $periode->update(['is_active' => $isActive]);
    }

    // ──────────────────────────────────────
    // Assign Pembimbing
    // ──────────────────────────────────────

    /**
     * Ambil data dosen & magang untuk halaman Assign Pembimbing.
     */
    public function getAssignPembimbingData(): array
    {
        $dosens = Dosen::with('user')
            ->whereHas('user', fn ($q) => $q->where('role', 'dosen_pembimbing'))
            ->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'nama' => $d->nama_dosen,
                'nip' => $d->nip,
            ]);

        // Tampilkan magang aktif yang belum punya assignment di tabel pembimbing_assignments
        $magangs = MagangAktif::with('pendaftaran.mahasiswa', 'pendaftaran.industri')
            ->doesntHave('pembimbingAssignment')
            ->where('status_agreement', 'accepted')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'mahasiswa' => $m->pendaftaran->mahasiswa->nama_lengkap,
                'nim' => $m->pendaftaran->mahasiswa->nim,
                'industri' => $m->pendaftaran->industri->nama_perusahaan,
            ]);

        // Ambil daftar penugasan terbaru dari tabel pembimbing_assignments
        $assignedMagangs = PembimbingAssignment::with('magangAktif.pendaftaran.mahasiswa', 'dosen')
            ->latest()
            ->take(20)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'mahasiswa' => $a->magangAktif->pendaftaran->mahasiswa->nama_lengkap,
                'dosen' => $a->dosen->nama_dosen,
            ]);

        return compact('dosens', 'magangs', 'assignedMagangs');
    }

    // assignPembimbing() telah dipindahkan ke PembimbingAssignmentService
    // Fitur assign pembimbing hanya sebagai bukti resmi penugasan,
    // TIDAK mempengaruhi status_tahapan atau alur kelulusan mahasiswa.

    // ──────────────────────────────────────
    // Manajemen User
    // ──────────────────────────────────────

    /**
     * Ambil daftar user terpaginasi untuk halaman Manajemen User.
     */
    public function getManajemenUserData(?string $search = null): LengthAwarePaginator
    {
        // Menggunakan Query Builder dan Eager Loading
        $query = User::with(['mahasiswa', 'dosen', 'industri']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate(15)->withQueryString()->through(fn ($u) => [
            'id' => $u->id,
            'username' => $u->username,
            'email' => $u->email,
            'role' => $u->role->value,
            'role_label' => $u->role->label(),
        ]);
    }

    // ──────────────────────────────────────
    // Verifikasi Kelulusan & Sertifikat
    // ──────────────────────────────────────

    /**
     * Ambil data untuk halaman verifikasi kelulusan.
     */
    public function getVerifikasiKelulusanData(): array
    {
        $magangs = MagangAktif::with('pendaftaran.mahasiswa', 'pendaftaran.industri', 'penilaian.performanceEvaluation', 'penilaian.internshipEvaluation', 'sertifikat')
            ->where('status_tahapan', StatusTahapan::LULUS)
            ->where(function ($query) {
                $query->whereDoesntHave('sertifikat')
                      ->orWhereHas('sertifikat', function ($subQuery) {
                          $subQuery->whereNull('nomor_sertifikat');
                      });
            })
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'mahasiswa' => $m->pendaftaran->mahasiswa->nama_lengkap,
                'nim' => $m->pendaftaran->mahasiswa->nim,
                'industri' => $m->pendaftaran->industri->nama_perusahaan,
                'nilai_akhir' => $m->penilaian?->nilai_akhir,
            ]);

        $sertifikats = Sertifikat::with('magangAktif.pendaftaran.mahasiswa')
            ->whereNotNull('nomor_sertifikat')
            ->latest()
            ->take(15)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'nomor' => $s->nomor_sertifikat,
                'mahasiswa' => $s->magangAktif->pendaftaran->mahasiswa->nama_lengkap,
                'tanggal' => $s->tanggal_terbit?->format('d M Y') ?? '-',
            ]);

        return compact('magangs', 'sertifikats');
    }

    /**
     * Terbitkan sertifikat kelulusan untuk satu mahasiswa.
     *
     * @throws \Exception jika mahasiswa belum lulus atau sudah punya sertifikat.
     */
    public function terbitkanSertifikat(MagangAktif $magangAktif): Sertifikat
    {
        // Check if the status is NOT LULUS (or null/empty, just in case)
        if ($magangAktif->status_tahapan !== StatusTahapan::LULUS) {
            throw new \Exception('Mahasiswa belum dinyatakan lulus oleh Dosen Prodi.');
        }

        if ($magangAktif->sertifikat && $magangAktif->sertifikat->nomor_sertifikat !== null) {
            $path = $magangAktif->sertifikat->file_sertifikat_path;
            if ($path && \Illuminate\Support\Facades\Storage::disk('private')->exists($path)) {
                throw new \Exception('Sertifikat sudah diterbitkan.');
            }
        }

        // Delegate to PdfService to generate the PDF file and update the database record
        return app(PdfService::class)->generateCertificate($magangAktif);
    }

    // ──────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────

    // private function generateNomorSertifikat has been moved to PdfService

}
