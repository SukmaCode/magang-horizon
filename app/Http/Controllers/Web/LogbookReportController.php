<?php

namespace App\Http\Controllers\Web;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\MagangAktif;
use App\Services\LogbookReportService;
use Illuminate\Http\Request;

class LogbookReportController extends Controller
{
    public function __construct(
        private readonly LogbookReportService $reportService
    ) {}

    public function download(Request $request, int $magangAktifId)
    {
        $magangAktif = MagangAktif::findOrFail($magangAktifId);

        // Authorization: ensure user has access to this logbook.
        // It could be Mahasiswa, Supervisor Industri, or Dosen Pembimbing.
        // We'll perform a basic check.
        $user = $request->user();
        $isMahasiswa = $user->mahasiswa && $user->mahasiswa->id === $magangAktif->mahasiswa->id;
        $isDosen = $magangAktif->supervisor_kampus_id && $user->dosen && $user->dosen->id === $magangAktif->supervisor_kampus_id;
        $isIndustri = $magangAktif->supervisor_industri_id === $user->id;

        if (! $isMahasiswa && ! $isDosen && ! $isIndustri && ! $user->isRole(UserRole::ADMIN)) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh laporan ini.');
        }

        $pdf = $this->reportService->generatePdf($magangAktifId);

        $filename = 'Logbook_Report_'.str_replace(' ', '_', $magangAktif->mahasiswa->nama).'.pdf';

        return $pdf->download($filename);
    }
}
