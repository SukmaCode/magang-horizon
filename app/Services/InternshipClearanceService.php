<?php

namespace App\Services;

use App\Enums\ClearanceStatus;
use App\Models\InternshipClearance;
use App\Models\MagangAktif;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class InternshipClearanceService
{
    // ──────────────────────────────────────
    // Data Providers (Queries & Mapping)
    // ──────────────────────────────────────

    public function getIndustryClearanceData(\App\Models\Industri $industri): array
    {
        return MagangAktif::whereHas('pendaftaran', function ($q) use ($industri) {
            $q->where('industri_id', $industri->id)
              ->where('status_seleksi', \App\Enums\StatusSeleksi::DITERIMA);
        })
            ->where('status_agreement', \App\Enums\StatusAgreement::ACCEPTED)
            ->with('pendaftaran.mahasiswa', 'internshipClearance')
            ->get()
            ->map(function (MagangAktif $m) {
                $clearance = $m->internshipClearance;

                return [
                    'id' => $m->id,
                    'mahasiswa' => [
                        'nama_lengkap' => $m->pendaftaran->mahasiswa->nama_lengkap,
                        'nim' => $m->pendaftaran->mahasiswa->nim,
                        'prodi' => $m->pendaftaran->mahasiswa->prodi,
                    ],
                    'status_tahapan' => $m->status_tahapan->value,
                    'status_tahapan_label' => $m->status_tahapan->label(),
                    'clearance' => $clearance ? [
                        'id' => $clearance->id,
                        'original_filename' => $clearance->original_filename,
                        'status' => $clearance->status->value,
                        'status_label' => $clearance->status->label(),
                        'status_color' => $clearance->status->badgeColor(),
                        'uploaded_at' => $clearance->uploaded_at?->format('d M Y H:i') ?? '-',
                        'can_update' => $clearance->canBeUpdatedByIndustri(),
                        'rejection_note' => $clearance->rejection_note,
                    ] : null,
                ];
            })
            ->values()
            ->toArray();
    }

    public function getStudentClearanceData(\App\Models\Mahasiswa $mahasiswa): array
    {
        $clearance = null;
        $pdfBase64 = null;
        $magang = MagangAktif::whereHas('pendaftaran', function ($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id)
              ->where('status_seleksi', 'diterima');
        })
        ->where('status_agreement', \App\Enums\StatusAgreement::ACCEPTED)
        ->with('internshipClearance.reviewer', 'pendaftaran.industri')->latest()->first();

        if ($magang && $magang->internshipClearance) {
            $cl = $magang->internshipClearance;

            $clearance = [
                'id' => $cl->id,
                'original_filename' => $cl->original_filename,
                'status' => $cl->status->value,
                'status_label' => $cl->status->label(),
                'status_color' => $cl->status->badgeColor(),
                'rejection_note' => $cl->rejection_note,
                'uploaded_at' => $cl->uploaded_at?->format('d M Y H:i') ?? '-',
                'submitted_at' => $cl->submitted_at?->format('d M Y H:i'),
                'reviewer_name' => $cl->reviewer?->dosen?->nama_dosen ?? $cl->reviewer?->username,
                'reviewed_at' => $cl->reviewed_at?->format('d M Y H:i'),
                'can_submit' => $cl->canBeSubmitted(),
            ];

            // Generate base64 PDF for preview
            $path = storage_path('app/private/' . $cl->file_path);
            if (file_exists($path)) {
                $pdfBase64 = 'data:application/pdf;base64,' . base64_encode(file_get_contents($path));
            }
        }

        return [
            'clearance' => $clearance,
            'pdfBase64' => $pdfBase64,
            'hasMagang' => $magang !== null,
            'industriName' => $magang?->pendaftaran?->industri?->nama_perusahaan ?? '-',
        ];
    }

    public function getReviewClearancesData(User $user)
    {
        $query = InternshipClearance::with([
            'magangAktif.pendaftaran.mahasiswa',
            'magangAktif.pendaftaran.industri',
            'reviewer',
        ])->where('status', '!=', ClearanceStatus::UPLOADED);

        if ($user->role === \App\Enums\UserRole::SUPERVISOR_1) {
            $dosen = $user->dosen;
            if (! $dosen) {
                return [];
            }
            $magangIds = $dosen->magangAktifs()->pluck('id');
            $query->whereIn('magang_aktif_id', $magangIds);
        }

        return $query->latest('submitted_at')
            ->get()
            ->map(fn (InternshipClearance $c) => [
                'id' => $c->id,
                'mahasiswa' => [
                    'nama_lengkap' => $c->magangAktif->pendaftaran->mahasiswa->nama_lengkap,
                    'nim' => $c->magangAktif->pendaftaran->mahasiswa->nim,
                ],
                'industri' => $c->magangAktif->pendaftaran->industri->nama_perusahaan ?? '-',
                'original_filename' => $c->original_filename,
                'status' => $c->status->value,
                'status_label' => $c->status->label(),
                'status_color' => $c->status->badgeColor(),
                'rejection_note' => $c->rejection_note,
                'submitted_at' => $c->submitted_at?->format('d M Y H:i'),
                'uploaded_at' => $c->uploaded_at?->format('d M Y H:i') ?? '-',
                'reviewer_name' => $c->reviewer?->dosen?->nama_dosen ?? $c->reviewer?->username,
                'reviewed_at' => $c->reviewed_at?->format('d M Y H:i'),
            ]);
    }

    // ──────────────────────────────────────
    // Actions
    // ──────────────────────────────────────

    /**
     * Upload a new Clearance file (by Industri).
     */
    public function upload(MagangAktif $magang, UploadedFile $file): InternshipClearance
    {
        $this->validateFile($file);

        $path = $file->store('internship-clearances/' . $magang->id, 'private');

        $clearance = InternshipClearance::create([
            'magang_aktif_id' => $magang->id,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'status' => ClearanceStatus::UPLOADED,
            'uploaded_at' => now(),
        ]);

        activity('clearance')
            ->performedOn($clearance)
            ->withProperties(['original_name' => $file->getClientOriginalName()])
            ->log('Clearance Issued By Company uploaded by industry');

        return $clearance;
    }

    /**
     * Update (replace) an existing Clearance file (by Industri).
     * Resets status to uploaded so the mahasiswa can re-submit.
     */
    public function update(InternshipClearance $clearance, UploadedFile $file): InternshipClearance
    {
        $this->validateFile($file);

        if (! $clearance->canBeUpdatedByIndustri()) {
            throw ValidationException::withMessages([
                'file' => ['Dokumen yang sudah disetujui atau sedang dalam proses verifikasi tidak dapat diubah.'],
            ]);
        }

        // Delete old file
        if ($clearance->file_path && Storage::disk('private')->exists($clearance->file_path)) {
            Storage::disk('private')->delete($clearance->file_path);
        }

        // Store new file
        $path = $file->store('internship-clearances/' . $clearance->magang_aktif_id, 'private');

        $clearance->update([
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'status' => ClearanceStatus::UPLOADED,
            'submitted_at' => null,
            'reviewer_id' => null,
            'reviewed_at' => null,
            'rejection_note' => null,
            'uploaded_at' => now(),
        ]);

        activity('clearance')
            ->performedOn($clearance)
            ->withProperties(['original_name' => $file->getClientOriginalName()])
            ->log('Clearance Issued By Company updated by industry');

        return $clearance->fresh();
    }

    /**
     * Submit clearance to dosen for review (by Mahasiswa).
     */
    public function submit(InternshipClearance $clearance): InternshipClearance
    {
        if (! $clearance->canBeSubmitted()) {
            throw ValidationException::withMessages([
                'status' => ['Dokumen ini tidak dapat disubmit untuk verifikasi.'],
            ]);
        }

        $clearance->update([
            'status' => ClearanceStatus::PENDING,
            'submitted_at' => now(),
            'reviewer_id' => null,
            'reviewed_at' => null,
            'rejection_note' => null,
        ]);

        activity('clearance')
            ->performedOn($clearance)
            ->log('Clearance Issued By Company submitted for review by student');

        return $clearance->fresh();
    }

    /**
     * Approve the clearance.
     */
    public function approve(InternshipClearance $clearance, User $reviewer): InternshipClearance
    {
        if (! $clearance->isPending()) {
            throw ValidationException::withMessages([
                'status' => ['Hanya dokumen dengan status pending yang dapat disetujui.'],
            ]);
        }

        $clearance->update([
            'status' => ClearanceStatus::APPROVED,
            'reviewer_id' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_note' => null,
        ]);

        activity('clearance')
            ->performedOn($clearance)
            ->causedBy($reviewer)
            ->log('Clearance Issued By Company approved');

        return $clearance->fresh();
    }

    /**
     * Reject the clearance with a rejection note.
     */
    public function reject(InternshipClearance $clearance, User $reviewer, string $rejectionNote): InternshipClearance
    {
        if (! $clearance->isPending()) {
            throw ValidationException::withMessages([
                'status' => ['Hanya dokumen dengan status pending yang dapat ditolak.'],
            ]);
        }

        $clearance->update([
            'status' => ClearanceStatus::REJECTED,
            'reviewer_id' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_note' => $rejectionNote,
        ]);

        activity('clearance')
            ->performedOn($clearance)
            ->causedBy($reviewer)
            ->withProperties(['rejection_note' => $rejectionNote])
            ->log('Clearance Issued By Company rejected');

        return $clearance->fresh();
    }

    /**
     * Get file response for download or inline preview.
     *
     * @return array{path: string, name: string, mime: string}
     */
    public function getFileResponse(InternshipClearance $clearance): array
    {
        if (! Storage::disk('private')->exists($clearance->file_path)) {
            throw new \RuntimeException('File Clearance Issued By Company tidak ditemukan di storage.');
        }

        return [
            'path' => Storage::disk('private')->path($clearance->file_path),
            'name' => $clearance->original_filename,
            'mime' => 'application/pdf',
        ];
    }

    /**
     * Validate uploaded file type and size.
     */
    private function validateFile(UploadedFile $file): void
    {
        if (! in_array($file->getClientMimeType(), ['application/pdf'])) {
            throw ValidationException::withMessages([
                'file' => ['File harus berformat PDF.'],
            ]);
        }

        // Max 10MB (10240 KB)
        if (($file->getSize() / 1024) > 10240) {
            throw ValidationException::withMessages([
                'file' => ['Ukuran file maksimal 10MB.'],
            ]);
        }
    }
}
