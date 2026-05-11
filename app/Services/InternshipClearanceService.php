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
