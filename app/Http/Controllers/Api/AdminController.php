<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdminApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        protected AdminApiService $adminApiService
    ) {}

    /**
     * List all users with optional role filter.
     */
    public function users(Request $request): JsonResponse
    {
        $users = $this->adminApiService->getUsers($request->query('role'));

        return response()->json($users);
    }

    /**
     * Dashboard statistics.
     */
    public function dashboard(): JsonResponse
    {
        $stats = $this->adminApiService->getDashboardStats();

        return response()->json($stats);
    }

    /**
     * Get activity log / audit trail.
     */
    public function activityLog(Request $request): JsonResponse
    {
        $logs = $this->adminApiService->getActivityLogs($request->query('log_name'));

        return response()->json($logs);
    }
}
