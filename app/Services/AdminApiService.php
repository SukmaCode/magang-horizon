<?php

namespace App\Services;

use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\MagangAktif;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Activitylog\Models\Activity;

class AdminApiService
{
    /**
     * Get paginated users with optional role filtering.
     *
     * @param string|null $role
     * @return LengthAwarePaginator
     */
    public function getUsers(?string $role = null): LengthAwarePaginator
    {
        $query = User::with('roles');

        if ($role) {
            $query->role($role);
        }

        return $query->latest()->paginate(20);
    }

    /**
     * Get statistics for the admin dashboard.
     *
     * @return array
     */
    public function getDashboardStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_pendaftaran' => Pendaftaran::count(),
            'pending_pendaftaran' => Pendaftaran::pending()->count(),
            'active_magang' => MagangAktif::where('status_tahapan', 'pelaksanaan')->count(),
            'completed_magang' => MagangAktif::where('status_tahapan', 'lulus')->count(),
        ];
    }

    /**
     * Get paginated activity logs with optional log_name filtering.
     *
     * @param string|null $logName
     * @return LengthAwarePaginator
     */
    public function getActivityLogs(?string $logName = null): LengthAwarePaginator
    {
        $query = Activity::with(['causer', 'subject'])->latest();

        if ($logName) {
            $query->where('log_name', $logName);
        }

        return $query->paginate(20);
    }
}
