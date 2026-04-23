<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusTahapan;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignSupervisorRequest;
use App\Models\MagangAktif;
use App\Services\InternshipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MagangController extends Controller
{
    public function __construct(
        private readonly InternshipService $internshipService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status') ? StatusTahapan::tryFrom($request->query('status')) : null;
        $data = $this->internshipService->list($status);
        return response()->json($data);
    }

    public function show(MagangAktif $magangAktif): JsonResponse
    {
        return response()->json(
            $this->internshipService->getDetails($magangAktif->id)
        );
    }

    public function assignSupervisor(AssignSupervisorRequest $request, MagangAktif $magangAktif): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->internshipService->assignSupervisors(
            $magangAktif,
            $validated['supervisor_kampus_id'],
            $validated['supervisor_industri_id']
        );

        return response()->json([
            'message' => 'Supervisor berhasil di-assign.',
            'data' => $result,
        ]);
    }

    public function transition(Request $request, MagangAktif $magangAktif): JsonResponse
    {
        $request->validate(['status' => 'required|string']);
        $target = StatusTahapan::from($request->input('status'));
        $result = $this->internshipService->transitionTo($magangAktif, $target);

        return response()->json([
            'message' => "Status berhasil diubah ke {$target->label()}.",
            'data' => $result,
        ]);
    }

    public function uploadAgreement(Request $request, MagangAktif $magangAktif): JsonResponse
    {
        $request->validate(['file' => 'required|file|mimes:pdf|max:10240', 'type' => 'required|in:industri,mahasiswa']);
        $path = $request->file('file')->store('documents/agreements', 'private');

        if ($request->input('type') === 'industri') {
            $this->internshipService->uploadAgreementIndustri($magangAktif, $path);
        } else {
            $this->internshipService->uploadAgreementMahasiswa($magangAktif, $path);
        }

        return response()->json(['message' => 'Agreement uploaded.', 'path' => $path]);
    }
}
