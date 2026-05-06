<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateCertificatePdfJob;
use App\Models\MagangAktif;
use App\Models\Sertifikat;
use Illuminate\Http\JsonResponse;

class SertifikatController extends Controller
{
    public function generate(MagangAktif $magangAktif): JsonResponse
    {
        // Dispatch certificate generation to queue
        GenerateCertificatePdfJob::dispatch($magangAktif);

        return response()->json([
            'message' => 'Certificate generation queued.',
        ]);
    }

    public function show(Sertifikat $sertifikat): JsonResponse
    {
        return response()->json($sertifikat->load('magangAktif'));
    }

    public function download(Sertifikat $sertifikat)
    {
        if (! $sertifikat->file_sertifikat_path) {
            return response()->json(['message' => 'Certificate not yet generated.'], 404);
        }

        $path = storage_path('app/private/'.$sertifikat->file_sertifikat_path);

        if (! file_exists($path)) {
            return response()->json(['message' => 'Certificate file not found.'], 404);
        }

        return response()->download($path, "Sertifikat-{$sertifikat->nomor_sertifikat}.pdf");
    }
}
