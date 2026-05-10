<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\StudentProfileService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentProfileController extends Controller
{
    public function __construct(
        private readonly StudentProfileService $profileService,
    ) {}

    /**
     * Display the student's profile page.
     */
    public function show(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;

        if (! $mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $data = $this->profileService->getProfileData($mahasiswa);
        $completeness = $this->profileService->getProfileCompleteness($mahasiswa);

        return Inertia::render('Mahasiswa/Profil', [
            'profile' => $data,
            'completeness' => $completeness,
        ]);
    }

    /**
     * Update profile text fields (bio, skills, linkedin_url, nomor_hp).
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nomor_hp' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'skills' => 'nullable|string|max:1000',
            'linkedin_url' => [
                'nullable',
                'url',
                'regex:/^https?:\/\/(www\.)?linkedin\.com\/in\/.+$/i',
            ],
        ], [
            'linkedin_url.regex' => 'Format LinkedIn URL tidak valid. Contoh: https://www.linkedin.com/in/username',
            'bio.max' => 'Bio maksimal 500 karakter.',
            'skills.max' => 'Deskripsi skills maksimal 1000 karakter.',
        ]);

        $mahasiswa = $request->user()->mahasiswa;

        if (! $mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $this->profileService->updateProfile($mahasiswa, $validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Upload or replace profile photo.
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'photo.required' => 'Pilih foto yang akan diupload.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format foto harus JPG, JPEG, atau PNG.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $mahasiswa = $request->user()->mahasiswa;

        if (! $mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $this->profileService->uploadProfilePhoto($mahasiswa, $request->file('photo'));

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Delete profile photo.
     */
    public function deletePhoto(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;

        if (! $mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $this->profileService->deleteProfilePhoto($mahasiswa);

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }
}
