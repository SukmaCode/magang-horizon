<?php

namespace App\Http\Middleware;

use App\Enums\StatusTahapan;
use App\Models\MagangAktif;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInternshipStatus
{
    /**
     * Handle an incoming request.
     * Usage: middleware('internship.status:pelaksanaan')
     */
    public function handle(Request $request, Closure $next, string ...$allowedStatuses): Response
    {
        $magangId = $request->route('magangAktif') ?? $request->input('magang_id');

        if (! $magangId) {
            return response()->json(['message' => 'Magang ID is required.'], 400);
        }

        $magang = $magangId instanceof MagangAktif
            ? $magangId
            : MagangAktif::find($magangId);

        if (! $magang) {
            return response()->json(['message' => 'Internship not found.'], 404);
        }

        $currentStatus = $magang->status_tahapan->value;

        if (! in_array($currentStatus, $allowedStatuses)) {
            $allowed = implode(', ', $allowedStatuses);
            return response()->json([
                'message' => "Action not allowed in current stage ({$currentStatus}). Allowed: {$allowed}.",
            ], 403);
        }

        return $next($request);
    }
}
