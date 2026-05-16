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
    // ──────────────────────────────────────
    // Data Providers (Queries & Mapping)
    // ──────────────────────────────────────

    public function getStudentDeclarationData(\App\Models\Mahasiswa $mahasiswa): array
    {
        $declaration = null;
        $pdfBase64 = null;
        $magang = MagangAktif::whereHas('pendaftaran', function ($q) use ($mahasiswa) {
                $q->where('mahasiswa_id', $mahasiswa->id);
            })
            ->whereNotNull('supervisor_kampus_id')
            ->with('declarationOfOriginality.reviewer')
            ->latest()
            ->first();

        if ($magang && $magang->declarationOfOriginality) {
            $decl = $magang->declarationOfOriginality;

            $declaration = [
                'id' => $decl->id,
                'original_filename' => $decl->original_filename,
                'status' => $decl->status->value,
                'status_label' => $decl->status->label(),
                'status_color' => $decl->status->badgeColor(),
                'rejection_note' => $decl->rejection_note,
                'uploaded_at' => $decl->uploaded_at->format('d M Y H:i'),
                'reviewer_name' => $decl->reviewer?->dosen?->nama_dosen ?? $decl->reviewer?->username,
                'reviewed_at' => $decl->reviewed_at?->format('d M Y H:i'),
                'can_update' => $decl->canBeUpdated(),
            ];

            // Generate base64 PDF for preview
            $path = storage_path('app/private/' . $decl->file_path);
            if (file_exists($path)) {
                $pdfBase64 = 'data:application/pdf;base64,' . base64_encode(file_get_contents($path));
            }
        }

        return [
            'declaration' => $declaration,
            'pdfBase64' => $pdfBase64,
            'hasMagang' => $magang !== null,
        ];
    }

    public function getReviewDeclarationsData(User $user)
    {
        $query = DeclarationOfOriginality::with([
            'magangAktif.pendaftaran.mahasiswa',
            'magangAktif.pendaftaran.industri',
            'reviewer',
        ]);

        if ($user->role === \App\Enums\UserRole::SUPERVISOR_1) {
            $dosen = $user->dosen;
            if (!$dosen) {
                return [];
            }
            $query->whereHas('magangAktif', function ($q) use ($dosen, $user) {
                $q->where('supervisor_kampus_id', $dosen->id)
                  ->orWhere('supervisor_kampus_id', $user->id)
                  ->orWhereHas('pembimbingAssignment', function ($q2) use ($dosen) {
                      $q2->where('dosen_id', $dosen->id);
                  });
            });
        }

        return $query->latest('uploaded_at')
            ->get()
            ->map(fn (DeclarationOfOriginality $d) => [
                'id' => $d->id,
                'mahasiswa' => [
                    'nama_lengkap' => $d->magangAktif->pendaftaran->mahasiswa->nama_lengkap,
                    'nim' => $d->magangAktif->pendaftaran->mahasiswa->nim,
                ],
                'industri' => $d->magangAktif->pendaftaran->industri->nama_perusahaan ?? '-',
                'original_filename' => $d->original_filename,
                'status' => $d->status->value,
                'status_label' => $d->status->label(),
                'status_color' => $d->status->badgeColor(),
                'rejection_note' => $d->rejection_note,
                'uploaded_at' => $d->uploaded_at->format('d M Y H:i'),
                'reviewer_name' => $d->reviewer?->dosen?->nama_dosen ?? $d->reviewer?->username,
                'reviewed_at' => $d->reviewed_at?->format('d M Y H:i'),
            ]);
    }

    // ──────────────────────────────────────
    // Actions
    // ──────────────────────────────────────

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
