<?php

namespace App\Http\Controllers\Web;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\MagangAktif;
use App\Models\Periode;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // ──────────────────────────────────────
    // Dashboard (Rekap Sistem)
    // ──────────────────────────────────────

    public function dashboard(Request $request)
    {
        $totalUsers = User::count();
        $totalMagang = MagangAktif::count();
        $totalLulus = MagangAktif::where('status_tahapan', 'lulus')->count();

        $recentUsers = User::latest()->take(5)->get()->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name ?? $u->username,
            'email' => $u->email,
            'role' => $u->role->value,
            'created_at' => $u->created_at->format('d M Y'),
        ]);

        return Inertia::render('Admin/Dashboard', [
            'totalUsers' => $totalUsers,
            'totalMagang' => $totalMagang,
            'totalLulus' => $totalLulus,
            'recentUsers' => $recentUsers,
        ]);
    }

    // ──────────────────────────────────────
    // Kelola Periode Magang
    // ──────────────────────────────────────

    public function kelolaPeriode()
    {
        $periodes = Periode::latest()->get();
        return Inertia::render('Admin/Periode', [
            'periodes' => $periodes
        ]);
    }

    public function storePeriode(Request $request)
    {
        $request->validate([
            'tahun_akademik' => 'required|string',
            'semester' => 'required|in:ganjil,genap,pendek',
            'tanggal_buka' => 'required|date',
            'tanggal_tutup' => 'required|date|after:tanggal_buka',
            'is_active' => 'boolean'
        ]);

        if ($request->is_active) {
            // Deactivate others
            Periode::query()->update(['is_active' => false]);
        }

        Periode::create($request->all());

        return back()->with('success', 'Periode magang berhasil ditambahkan.');
    }

    public function updatePeriode(Request $request, Periode $periode)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        if ($request->is_active) {
            Periode::query()->update(['is_active' => false]);
        }

        $periode->update(['is_active' => $request->is_active]);

        return back()->with('success', 'Status periode berhasil diubah.');
    }

    // ──────────────────────────────────────
    // Assign Dosen Pembimbing
    // ──────────────────────────────────────

    public function assignPembimbing()
    {
        $dosens = Dosen::with('user')->get()->map(fn ($d) => [
            'id' => $d->id,
            'nama' => $d->nama_dosen,
            'nip' => $d->nip,
        ]);

        $magangs = MagangAktif::with('pendaftaran.mahasiswa', 'pendaftaran.industri')
            ->whereNull('supervisor_kampus_id')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'mahasiswa' => $m->pendaftaran->mahasiswa->nama_lengkap,
                'nim' => $m->pendaftaran->mahasiswa->nim,
                'industri' => $m->pendaftaran->industri->nama_perusahaan,
            ]);

        $assignedMagangs = MagangAktif::with('pendaftaran.mahasiswa', 'supervisorKampus')
            ->whereNotNull('supervisor_kampus_id')
            ->latest()
            ->take(20)
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'mahasiswa' => $m->pendaftaran->mahasiswa->nama_lengkap,
                'dosen' => $m->supervisorKampus->nama_dosen,
            ]);

        return Inertia::render('Admin/AssignPembimbing', [
            'dosens' => $dosens,
            'magangs' => $magangs,
            'assignedMagangs' => $assignedMagangs,
        ]);
    }

    public function storeAssignPembimbing(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'magang_ids' => 'required|array',
            'magang_ids.*' => 'exists:magang_aktifs,id'
        ]);

        MagangAktif::whereIn('id', $request->magang_ids)->update([
            'supervisor_kampus_id' => $request->dosen_id,
            'sk_pembimbing_path' => 'generated_sk_'.Str::random(10).'.pdf', // Dummy PDF path MVP
        ]);

        return back()->with('success', 'Dosen pembimbing berhasil ditetapkan ke mahasiswa terpilih.');
    }

    // ──────────────────────────────────────
    // Verifikasi Kelulusan (Penerbitan Sertifikat)
    // ──────────────────────────────────────

    public function verifikasiKelulusan()
    {
        // Get all magangs that are marked as LULUS (from Dosen Prodi) but don't have certificates
        $magangs = MagangAktif::with('pendaftaran.mahasiswa', 'pendaftaran.industri', 'penilaian', 'sertifikat')
            ->where('status_tahapan', 'lulus')
            ->doesntHave('sertifikat')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'mahasiswa' => $m->pendaftaran->mahasiswa->nama_lengkap,
                'nim' => $m->pendaftaran->mahasiswa->nim,
                'industri' => $m->pendaftaran->industri->nama_perusahaan,
                'nilai_akhir' => $m->penilaian?->nilai_akhir,
            ]);

        $sertifikats = Sertifikat::with('magangAktif.pendaftaran.mahasiswa')
            ->latest()
            ->take(15)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'nomor' => $s->nomor_sertifikat,
                'mahasiswa' => $s->magangAktif->pendaftaran->mahasiswa->nama_lengkap,
                'tanggal' => $s->tanggal_terbit->format('d M Y'),
            ]);

        return Inertia::render('Admin/VerifikasiKelulusan', [
            'magangs' => $magangs,
            'sertifikats' => $sertifikats,
        ]);
    }

    public function terbitkanSertifikat(Request $request, MagangAktif $magangAktif)
    {
        if ($magangAktif->status_tahapan->value !== 'lulus') {
            return back()->with('error', 'Mahasiswa belum dinyatakan lulus oleh Dosen Prodi.');
        }

        if ($magangAktif->sertifikat) {
            return back()->with('error', 'Sertifikat sudah diterbitkan.');
        }

        Sertifikat::create([
            'magang_id' => $magangAktif->id,
            'nomor_sertifikat' => 'CERT-'.date('Y').'-'.str_pad($magangAktif->id, 4, '0', STR_PAD_LEFT),
            'file_sertifikat' => 'certificates/cert_'.$magangAktif->id.'.pdf', // Placeholder
            'tanggal_terbit' => now(),
            'is_valid' => true,
        ]);

        return back()->with('success', 'Sertifikat kelulusan resmi diterbitkan.');
    }

    // ──────────────────────────────────────
    // Manajemen User
    // ──────────────────────────────────────

    public function manajemenUser()
    {
        $users = User::latest()->paginate(15)->through(fn ($u) => [
            'id' => $u->id,
            'username' => $u->username,
            'email' => $u->email,
            'role' => $u->role->value,
            'role_label' => $u->role->label(),
        ]);

        return Inertia::render('Admin/ManajemenUser', [
            'users' => $users,
        ]);
    }
}
