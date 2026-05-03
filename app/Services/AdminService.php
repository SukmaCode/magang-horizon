<?php

namespace App\Services;

use App\Models\Dosen;
use App\Models\MagangAktif;
use App\Models\Periode;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\Support\Str;

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
        $totalUsers  = User::count();
        $totalMagang = MagangAktif::count();
        $totalLulus  = MagangAktif::where('status_tahapan', 'lulus')->count();

        $recentUsers = User::latest()->take(5)->get()->map(fn ($u) => [
            'id'         => $u->id,
            'name'       => $u->name ?? $u->username,
            'email'      => $u->email,
            'role'       => $u->role->value,
            'created_at' => $u->created_at->format('d M Y'),
        ]);

        return compact('totalUsers', 'totalMagang', 'totalLulus', 'recentUsers');
    }

    // ──────────────────────────────────────
    // Kelola Periode
    // ──────────────────────────────────────

    /**
     * Ambil list semua periode untuk halaman Kelola Periode.
     */
    public function getPeriodes(): \Illuminate\Database\Eloquent\Collection
    {
        return Periode::latest()->get();
    }

    /**
     * Business rule: hanya boleh ada 1 periode aktif.
     * Nonaktifkan semua periode lain sebelum membuat yang baru.
     */
    public function createPeriode(array $data): Periode
    {
        if (!empty($data['is_active'])) {
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
        $dosens = Dosen::with('user')->get()->map(fn ($d) => [
            'id'   => $d->id,
            'nama' => $d->nama_dosen,
            'nip'  => $d->nip,
        ]);

        $magangs = MagangAktif::with('pendaftaran.mahasiswa', 'pendaftaran.industri')
            ->whereNull('supervisor_kampus_id')
            ->where('status_agreement', 'accepted')
            ->get()
            ->map(fn ($m) => [
                'id'       => $m->id,
                'mahasiswa'=> $m->pendaftaran->mahasiswa->nama_lengkap,
                'nim'      => $m->pendaftaran->mahasiswa->nim,
                'industri' => $m->pendaftaran->industri->nama_perusahaan,
            ]);

        $assignedMagangs = MagangAktif::with('pendaftaran.mahasiswa', 'supervisorKampus')
            ->whereNotNull('supervisor_kampus_id')
            ->latest()
            ->take(20)
            ->get()
            ->map(fn ($m) => [
                'id'       => $m->id,
                'mahasiswa'=> $m->pendaftaran->mahasiswa->nama_lengkap,
                'dosen'    => $m->supervisorKampus->nama_dosen,
            ]);

        return compact('dosens', 'magangs', 'assignedMagangs');
    }



    /**
     * Tetapkan dosen pembimbing ke sejumlah magang aktif.
     *
     * Logic: kelompokkan magang berdasarkan supervisor_industri masing-masing
     * agar bisa bulk-update dan menghindari N query UPDATE.
     *
     * @param  int    $dosenId    ID dosen yang akan jadi pembimbing kampus
     * @param  int[]  $magangIds  Daftar ID magang_aktif yang akan di-assign
     */
    public function assignPembimbing(int $dosenId, array $magangIds): void
    {
        $magangs = MagangAktif::with('pendaftaran.industri')
            ->whereIn('id', $magangIds)
            ->get();

        // Kelompokkan ID berdasarkan supervisor_industri_id agar bisa bulk-update
        $groupedIds = [];
        foreach ($magangs as $magang) {
            $supervisorIndustriId = $magang->pendaftaran->industri->user_id ?? '';
            $groupedIds[$supervisorIndustriId][] = $magang->id;
        }

        foreach ($groupedIds as $supervisorIndustriId => $ids) {
            MagangAktif::whereIn('id', $ids)->update([
                'supervisor_kampus_id'   => $dosenId,
                'supervisor_industri_id' => $supervisorIndustriId !== '' ? $supervisorIndustriId : null,
                'status_tahapan'         => 'pelaksanaan',
                'tanggal_mulai'          => now(),
                'sk_pembimbing_path'     => $this->generateSkPath(),
            ]);
        }
    }

    // ──────────────────────────────────────
    // Manajemen User
    // ──────────────────────────────────────

    /**
     * Ambil daftar user terpaginasi untuk halaman Manajemen User.
     */
    public function getManajemenUserData(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return User::latest()->paginate(15)->through(fn ($u) => [
            'id'         => $u->id,
            'username'   => $u->username,
            'email'      => $u->email,
            'role'       => $u->role->value,
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
        $magangs = MagangAktif::with('pendaftaran.mahasiswa', 'pendaftaran.industri', 'penilaian', 'sertifikat')
            ->where('status_tahapan', 'lulus')
            ->doesntHave('sertifikat')
            ->get()
            ->map(fn ($m) => [
                'id'          => $m->id,
                'mahasiswa'   => $m->pendaftaran->mahasiswa->nama_lengkap,
                'nim'         => $m->pendaftaran->mahasiswa->nim,
                'industri'    => $m->pendaftaran->industri->nama_perusahaan,
                'nilai_akhir' => $m->penilaian?->nilai_akhir,
            ]);

        $sertifikats = Sertifikat::with('magangAktif.pendaftaran.mahasiswa')
            ->latest()
            ->take(15)
            ->get()
            ->map(fn ($s) => [
                'id'       => $s->id,
                'nomor'    => $s->nomor_sertifikat,
                'mahasiswa'=> $s->magangAktif->pendaftaran->mahasiswa->nama_lengkap,
                'tanggal'  => $s->tanggal_terbit->format('d M Y'),
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
        if ($magangAktif->status_tahapan->value !== 'lulus') {
            throw new \Exception('Mahasiswa belum dinyatakan lulus oleh Dosen Prodi.');
        }

        if ($magangAktif->sertifikat) {
            throw new \Exception('Sertifikat sudah diterbitkan.');
        }

        return Sertifikat::create([
            'magang_id'        => $magangAktif->id,
            'nomor_sertifikat' => $this->generateNomorSertifikat($magangAktif),
            'file_sertifikat'  => 'certificates/cert_' . $magangAktif->id . '.pdf',
            'tanggal_terbit'   => now(),
            'is_valid'         => true,
        ]);
    }

    // ──────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────

    /**
     * Generate nama file SK Pembimbing (sementara, MVP placeholder).
     * Dipusatkan di sini agar mudah diganti saat implementasi PDF asli.
     */
    private function generateSkPath(): string
    {
        return 'generated_sk_' . Str::random(10) . '.pdf';
    }

    /**
     * Generate nomor sertifikat dengan format: CERT-YYYY-XXXX
     * Dipusatkan di sini agar format mudah diubah tanpa menyentuh controller.
     */
    private function generateNomorSertifikat(MagangAktif $magangAktif): string
    {
        return 'CERT-' . date('Y') . '-' . str_pad($magangAktif->id, 4, '0', STR_PAD_LEFT);
    }
}
