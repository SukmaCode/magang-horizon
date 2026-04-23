<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusApproval;
use App\Http\Controllers\Controller;
use App\Models\LaporanAkhir;
use App\Models\MagangAktif;
use App\Services\GradingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(
        private readonly GradingService $gradingService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'magang_id' => 'required|exists:magang_aktifs,id',
            'file' => 'required|file|mimes:pdf|max:20480',
        ]);

        $magang = MagangAktif::findOrFail($request->input('magang_id'));
        $path = $request->file('file')->store('documents/laporan', 'private');
        $laporan = $this->gradingService->uploadLaporan($magang, $path);

        return response()->json(['message' => 'Laporan uploaded.', 'data' => $laporan], 201);
    }

    public function show(LaporanAkhir $laporanAkhir): JsonResponse
    {
        return response()->json($laporanAkhir->load('magangAktif'));
    }

    public function review(Request $request, LaporanAkhir $laporanAkhir): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:revisi,disetujui',
            'catatan_revisi' => 'nullable|string|max:2000',
        ]);

        $status = StatusApproval::from($request->input('status'));
        $result = $this->gradingService->reviewLaporan($laporanAkhir, $status, $request->input('catatan_revisi'));

        return response()->json(['message' => "Laporan {$status->label()}.", 'data' => $result]);
    }
}
