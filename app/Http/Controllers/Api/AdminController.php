<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MagangAktif;
use App\Models\Pendaftaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AdminController extends Controller
{
    /**
     * List all users with optional role filter.
     */
    public function users(Request $request): JsonResponse
    {
        $query = User::with('roles');

        if ($request->has('role')) {
            $query->role($request->query('role'));
        }

        return response()->json($query->latest()->paginate(20));
    }

    /**
     * Dashboard statistics.
     */
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'total_users' => User::count(),
            'total_pendaftaran' => Pendaftaran::count(),
            'pending_pendaftaran' => Pendaftaran::pending()->count(),
            'active_magang' => MagangAktif::where('status_tahapan', 'pelaksanaan')->count(),
            'completed_magang' => MagangAktif::where('status_tahapan', 'lulus')->count(),
        ]);
    }

    /**
     * Get activity log / audit trail.
     */
    public function activityLog(Request $request): JsonResponse
    {
        $query = Activity::with(['causer', 'subject'])->latest();

        if ($request->has('log_name')) {
            $query->where('log_name', $request->query('log_name'));
        }

        return response()->json($query->paginate(20));
    }
}
