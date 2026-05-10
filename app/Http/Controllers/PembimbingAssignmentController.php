<?php

namespace App\Http\Controllers;

use App\Services\PembimbingAssignmentService;
use App\Models\MagangAktif;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PembimbingAssignmentController extends Controller
{
    protected PembimbingAssignmentService $assignmentService;

    public function __construct(PembimbingAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * Store the dosen pembimbing assignment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'magang_ids' => 'required|array|min:1',
            'magang_ids.*' => 'exists:magang_aktifs,id',
            'dosen_id' => 'required|exists:dosens,id',
        ]);

        foreach ($request->magang_ids as $magangId) {
            $this->assignmentService->assignPembimbing(
                $magangId,
                $request->dosen_id,
                $request->user()->id
            );
        }

        return redirect()->back()->with('success', 'Dosen pembimbing berhasil ditugaskan.');
    }

    /**
     * Dosen Pembimbing: View list of guided students.
     */
    public function indexBimbingan(Request $request)
    {
        $dosen = $request->user()->dosen;
        
        $assignments = $dosen->pembimbingAssignments()
            ->with(['magangAktif.pendaftaran.mahasiswa.user', 'suratKeputusan'])
            ->get();

        return Inertia::render('DosenPembimbing/ListBimbingan', [
            'assignments' => $assignments
        ]);
    }
}
