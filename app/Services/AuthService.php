<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Dosen;
use App\Models\Industri;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user with role-specific profile.
     *
     * @param  array  $data  Validated registration data
     * @return User
     *
     * @throws \Throwable
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $role = UserRole::from($data['role']);

            // Create the user
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'], // Automatically hashed via cast
                'role' => $role->value,
            ]);

            // Assign Spatie role
            $user->assignRole($role->roleName());

            // Create role-specific profile
            $this->createProfile($user, $role, $data);

            return $user->load($this->getProfileRelation($role));
        });
    }

    /**
     * Attempt to authenticate a user.
     *
     * @param  array  $credentials  ['email' => ..., 'password' => ...]
     * @return User
     *
     * @throws ValidationException
     */
    public function login(array $credentials): User
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user;
    }

    /**
     * Create role-specific profile for the user.
     */
    private function createProfile(User $user, UserRole $role, array $data): void
    {
        match ($role) {
            UserRole::STUDENT => Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $data['nim'],
                'nama_lengkap' => $data['nama_lengkap'],
                'prodi' => $data['prodi'] ?? null,
            ]),
            UserRole::INDUSTRY => Industri::create([
                'user_id' => $user->id,
                'nama_perusahaan' => $data['nama_perusahaan'],
                'alamat' => $data['alamat'] ?? null,
                'kontak_person' => $data['kontak_person'] ?? null,
            ]),
            UserRole::SUPERVISOR_1, UserRole::SUPERVISOR_2 => Dosen::create([
                'user_id' => $user->id,
                'nip' => $data['nip'],
                'nama_dosen' => $data['nama_dosen'],
            ]),
            UserRole::ADMIN => null, // Admin has no profile table
        };
    }

    /**
     * Get the profile relationship name based on role.
     */
    private function getProfileRelation(UserRole $role): string
    {
        return match ($role) {
            UserRole::STUDENT => 'mahasiswa',
            UserRole::INDUSTRY => 'industri',
            UserRole::SUPERVISOR_1, UserRole::SUPERVISOR_2 => 'dosen',
            UserRole::ADMIN => 'mahasiswa', // fallback, won't load anything
        };
    }
}
