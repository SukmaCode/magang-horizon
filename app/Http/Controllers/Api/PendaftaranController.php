<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePendaftaranRequest;
use App\Http\Requests\UpdateSeleksiRequest;
use App\Services\ApplicationService;
use App\Models\Pendaftaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function __construct(
        private readonly ApplicationService $applicationService
    ) {}

    /**
     * List applications (filtered by user role).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isRole(\App\Enums\UserRole::STUDENT)) {
            $data = $this->applicationService->getByMahasiswa($user->mahasiswa->id);
        } elseif ($user->isRole(\App\Enums\UserRole::INDUSTRY)) {
            $data = $this->applicationService->getByIndustri($user->industri->id);
        } else {
            $data = Pendaftaran::with(['mahasiswa.user', 'industri'])->latest()->paginate(15);
        }

        return response()->json($data);
    }

    /**
     * Create a new application (student only).
     */
    public function store(StorePendaftaranRequest $request): JsonResponse
    {
        $mahasiswa = $request->user()->mahasiswa;
        $pendaftaran = $this->applicationService->apply(
            $mahasiswa->id,
            $request->validated()['industri_id']
        );

        return response()->json([
            'message' => 'Pendaftaran berhasil disubmit.',
            'data' => $pendaftaran->load('industri'),
        ], 201);
    }

    /**
     * Show a specific application.
     */
    public function show(Pendaftaran $pendaftaran): JsonResponse
    {
        return response()->json(
            $pendaftaran->load(['mahasiswa.user', 'industri.user', 'magangAktif'])
        );
    }

    /**
     * Update selection status (industry only).
     */
    public function updateSeleksi(UpdateSeleksiRequest $request, Pendaftaran $pendaftaran): JsonResponse
    {
        $validated = $request->validated();

        if ($validated['status'] === 'diterima') {
            $result = $this->applicationService->accept($pendaftaran, $validated['keterangan_industri'] ?? null);
        } else {
            $result = $this->applicationService->reject($pendaftaran, $validated['keterangan_industri']);
        }

        return response()->json([
            'message' => "Pendaftaran {$validated['status']}.",
            'data' => $result,
        ]);
    }
}
