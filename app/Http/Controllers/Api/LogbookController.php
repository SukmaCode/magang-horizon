<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLogbookRequest;
use App\Models\Logbook;
use App\Models\MagangAktif;
use App\Services\DailyLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function __construct(
        private readonly DailyLogService $dailyLogService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $magangId = $request->query('magang_id');
        $data = $this->dailyLogService->getByMagang(
            $magangId,
            $request->query('start_date'),
            $request->query('end_date')
        );

        return response()->json($data);
    }

    public function store(StoreLogbookRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $magang = MagangAktif::findOrFail($validated['magang_id']);
        $logbook = $this->dailyLogService->submit($magang, $validated);

        return response()->json([
            'message' => 'Logbook berhasil disubmit.',
            'data' => $logbook,
        ], 201);
    }

    public function approve(Request $request, Logbook $logbook): JsonResponse
    {
        $request->validate(['komentar' => 'nullable|string|max:500']);
        $result = $this->dailyLogService->approve($logbook, $request->input('komentar'));

        return response()->json(['message' => 'Logbook approved.', 'data' => $result]);
    }

    public function check(Logbook $logbook): JsonResponse
    {
        $result = $this->dailyLogService->check($logbook);

        return response()->json(['message' => 'Logbook checked.', 'data' => $result]);
    }

    public function pending(Request $request): JsonResponse
    {
        $magangId = $request->query('magang_id');

        return response()->json($this->dailyLogService->getPendingApproval($magangId));
    }
}
