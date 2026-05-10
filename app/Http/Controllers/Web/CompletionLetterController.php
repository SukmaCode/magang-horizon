<?php

namespace App\Http\Controllers\Web;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\MagangAktif;
use App\Services\CompletionLetterService;
use Illuminate\Http\Request;

class CompletionLetterController extends Controller
{
    public function __construct(
        private readonly CompletionLetterService $completionLetterService
    ) {}

    public function download(Request $request, MagangAktif $magangAktif)
    {
        // Authorization: mahasiswa pemilik, supervisor industri, dosen pembimbing, atau admin
        $user = $request->user();
        $isMahasiswa = $user->mahasiswa && $user->mahasiswa->id === $magangAktif->mahasiswa?->id;
        $isDosen = $magangAktif->supervisor_kampus_id && $user->dosen && $user->dosen->id === $magangAktif->supervisor_kampus_id;
        $isIndustri = $magangAktif->supervisor_industri_id === $user->id;

        if (! $isMahasiswa && ! $isDosen && ! $isIndustri && ! $user->isRole(UserRole::ADMIN)) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh surat ini.');
        }

        // Check that the completion letter data actually exists
        $sertifikat = $magangAktif->sertifikat;
        if (! $sertifikat || ! $sertifikat->posisi_magang) {
            abort(404, 'Data Internship Completion Letter belum diisi oleh industri.');
        }

        $pdf = $this->completionLetterService->generatePdf($magangAktif->id);

        $studentName = str_replace(' ', '_', $magangAktif->mahasiswa?->nama_lengkap ?? 'Student');
        $filename = "Internship_Completion_Letter_{$studentName}.pdf";

        return $pdf->download($filename);
    }
}
