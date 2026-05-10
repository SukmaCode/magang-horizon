<?php

namespace App\Services;

use App\Models\Mahasiswa;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StudentProfileService
{
    /**
     * Get formatted profile data for the Inertia profile page.
     */
    public function getProfileData(Mahasiswa $mahasiswa): array
    {
        return [
            'id' => $mahasiswa->id,
            'nama_lengkap' => $mahasiswa->nama_lengkap,
            'nim' => $mahasiswa->nim,
            'email' => $mahasiswa->user->email,
            'prodi' => $mahasiswa->prodi,
            'nomor_hp' => $mahasiswa->nomor_hp,
            'bio' => $mahasiswa->bio,
            'skills' => $mahasiswa->skills,
            'linkedin_url' => $mahasiswa->linkedin_url,
            'profile_photo_url' => $mahasiswa->profile_photo_url,
            'has_cv' => $mahasiswa->cv_file_path !== null,
            'cv_preview_url' => $mahasiswa->cv_file_path
                ? route('mahasiswa.cv.preview')
                : null,
        ];
    }

    /**
     * Update profile text fields.
     */
    public function updateProfile(Mahasiswa $mahasiswa, array $data): Mahasiswa
    {
        $mahasiswa->update([
            'nomor_hp' => $data['nomor_hp'] ?? $mahasiswa->nomor_hp,
            'bio' => $data['bio'] ?? $mahasiswa->bio,
            'skills' => $data['skills'] ?? $mahasiswa->skills,
            'linkedin_url' => $data['linkedin_url'] ?? $mahasiswa->linkedin_url,
        ]);

        return $mahasiswa->fresh();
    }

    /**
     * Upload and replace profile photo.
     * Stores in storage/app/public/profile-photos/{id}/
     */
    public function uploadProfilePhoto(Mahasiswa $mahasiswa, UploadedFile $photo): string
    {
        // Delete old photo if exists
        if ($mahasiswa->profile_photo_path) {
            Storage::disk('public')->delete($mahasiswa->profile_photo_path);
        }

        $path = $photo->store('profile-photos/'.$mahasiswa->id, 'public');

        $mahasiswa->update(['profile_photo_path' => $path]);

        return $path;
    }

    /**
     * Delete profile photo.
     */
    public function deleteProfilePhoto(Mahasiswa $mahasiswa): void
    {
        if ($mahasiswa->profile_photo_path) {
            Storage::disk('public')->delete($mahasiswa->profile_photo_path);
            $mahasiswa->update(['profile_photo_path' => null]);
        }
    }

    /**
     * Validate LinkedIn URL format.
     * Accepts: https://www.linkedin.com/in/username or https://linkedin.com/in/username
     */
    public function isValidLinkedInUrl(string $url): bool
    {
        return (bool) preg_match('/^https?:\/\/(www\.)?linkedin\.com\/in\/.+$/i', $url);
    }

    /**
     * Calculate profile completeness percentage and status.
     */
    public function getProfileCompleteness(Mahasiswa $mahasiswa): array
    {
        $fields = [
            'nama_lengkap' => $mahasiswa->nama_lengkap !== null,
            'nim' => $mahasiswa->nim !== null,
            'nomor_hp' => $mahasiswa->nomor_hp !== null && $mahasiswa->nomor_hp !== '',
            'bio' => $mahasiswa->bio !== null && $mahasiswa->bio !== '',
            'skills' => $mahasiswa->skills !== null && $mahasiswa->skills !== '',
            'linkedin_url' => $mahasiswa->hasLinkedIn(),
            'cv_file' => $mahasiswa->cv_file_path !== null,
            'profile_photo' => $mahasiswa->profile_photo_path !== null,
        ];

        $completed = array_filter($fields);
        $total = count($fields);
        $percentage = $total > 0 ? round((count($completed) / $total) * 100) : 0;

        return [
            'percentage' => $percentage,
            'fields' => $fields,
            'total' => $total,
            'completed_count' => count($completed),
            'is_ready_to_apply' => $mahasiswa->isProfileComplete(),
        ];
    }
}
