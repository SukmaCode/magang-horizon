<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenilaianRequest;
use App\Models\MagangAktif;
use App\Models\Penilaian;
use App\Services\GradingService;
use Illuminate\Http\JsonResponse;

class PenilaianController extends Controller
{
    public function __construct(
        private readonly GradingService $gradingService
    ) {}

    public function gradeIndustry(StorePenilaianRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $magang = MagangAktif::findOrFail($validated['magang_id']);
        $result = $this->gradingService->gradeByIndustry($magang, $validated['nilai']);

        return response()->json(['message' => 'Nilai industri submitted.', 'data' => $result]);
    }

    public function gradeCampus(StorePenilaianRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $magang = MagangAktif::findOrFail($validated['magang_id']);
        $result = $this->gradingService->gradeByCampus($magang, $validated['nilai']);

        return response()->json(['message' => 'Nilai kampus submitted.', 'data' => $result]);
    }

    public function verify(Penilaian $penilaian): JsonResponse
    {
        $result = $this->gradingService->verify($penilaian);

        return response()->json([
            'message' => 'Penilaian verified.',
            'data' => $result,
            'nilai_akhir' => $result->nilai_akhir,
        ]);
    }

    public function show(Penilaian $penilaian): JsonResponse
    {
        return response()->json([
            'data' => $penilaian->load('magangAktif'),
            'nilai_akhir' => $penilaian->nilai_akhir,
        ]);
    }
}
