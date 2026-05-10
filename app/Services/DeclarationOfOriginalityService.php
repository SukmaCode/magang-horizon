<?php

namespace App\Services;

use App\Enums\DeclarationStatus;
use App\Models\DeclarationOfOriginality;
use App\Models\MagangAktif;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DeclarationOfOriginalityService
{
    /**
     * Upload a new Declaration of Originality file.
     */
    public function upload(MagangAktif $magang, UploadedFile $file): DeclarationOfOriginality
    {
        $this->validateFile($file);

        $path = $file->store('declarations/' . $magang->id, 'private');

        $declaration = DeclarationOfOriginality::create([
            'magang_aktif_id' => $magang->id,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'status' => DeclarationStatus::PENDING,
            'uploaded_at' => now(),
        ]);

        activity('declaration')
            ->performedOn($declaration)
            ->withProperties(['original_name' => $file->getClientOriginalName()])
            ->log('Declaration of Originality uploaded');

        return $declaration;
    }

    /**
     * Update (replace) an existing Declaration of Originality file.
     * Resets status to pending so the dosen can review again.
     */
    public function update(DeclarationOfOriginality $declaration, UploadedFile $file): DeclarationOfOriginality
    {
        $this->validateFile($file);

        if (! $declaration->canBeUpdated()) {
            throw ValidationException::withMessages([
                'file' => ['Dokumen yang sudah disetujui tidak dapat diubah.'],
            ]);
        }

        // Delete old file
        if ($declaration->file_path && Storage::disk('private')->exists($declaration->file_path)) {
            Storage::disk('private')->delete($declaration->file_path);
        }

        // Store new file
        $path = $file->store('declarations/' . $declaration->magang_aktif_id, 'private');

        $declaration->update([
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'status' => DeclarationStatus::PENDING,
            'reviewer_id' => null,
            'reviewed_at' => null,
            'rejection_note' => null,
            'uploaded_at' => now(),
        ]);

        activity('declaration')
            ->performedOn($declaration)
            ->withProperties(['original_name' => $file->getClientOriginalName()])
            ->log('Declaration of Originality updated');

        return $declaration->fresh();
    }

    /**
     * Approve the declaration.
     */
    public function approve(DeclarationOfOriginality $declaration, User $reviewer): DeclarationOfOriginality
    {
        if (! $declaration->isPending()) {
            throw ValidationException::withMessages([
                'status' => ['Hanya dokumen dengan status pending yang dapat disetujui.'],
            ]);
        }

        $declaration->update([
            'status' => DeclarationStatus::APPROVED,
            'reviewer_id' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_note' => null,
        ]);

        activity('declaration')
            ->performedOn($declaration)
            ->causedBy($reviewer)
            ->log('Declaration of Originality approved');

        return $declaration->fresh();
    }

    /**
     * Reject the declaration with a rejection note.
     */
    public function reject(DeclarationOfOriginality $declaration, User $reviewer, string $rejectionNote): DeclarationOfOriginality
    {
        if (! $declaration->isPending()) {
            throw ValidationException::withMessages([
                'status' => ['Hanya dokumen dengan status pending yang dapat ditolak.'],
            ]);
        }

        $declaration->update([
            'status' => DeclarationStatus::REJECTED,
            'reviewer_id' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_note' => $rejectionNote,
        ]);

        activity('declaration')
            ->performedOn($declaration)
            ->causedBy($reviewer)
            ->withProperties(['rejection_note' => $rejectionNote])
            ->log('Declaration of Originality rejected');

        return $declaration->fresh();
    }

    /**
     * Get file response for download or inline preview.
     *
     * @return array{path: string, name: string, mime: string}
     */
    public function getFileResponse(DeclarationOfOriginality $declaration): array
    {
        if (! Storage::disk('private')->exists($declaration->file_path)) {
            throw new \RuntimeException('File Declaration of Originality tidak ditemukan di storage.');
        }

        return [
            'path' => Storage::disk('private')->path($declaration->file_path),
            'name' => $declaration->original_filename,
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
