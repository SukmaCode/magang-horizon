<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        // Muat relasi spesifik berdasarkan role
        if ($user) {
            $relation = match ($user->role?->value) {
                'mahasiswa' => 'mahasiswa',
                'supervisor_industri' => 'industri',
                'dosen_pembimbing', 'dosen_prodi' => 'dosen',
                default => null,
            };
            if ($relation) {
                $user->load($relation);
            }
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'username   ' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,

                    // Tabel Mahasiswa
                    'nama_lengkap' => $user->mahasiswa ? $user->mahasiswa->nama_lengkap : null,
                    'prodi' => $user->mahasiswa ? $user->mahasiswa->prodi : null,

                    // Tabel Industri
                    'nama_perusahaan' => $user->industri ? $user->industri->nama_perusahaan : null,
                    'kontak_person' => $user->industri ? $user->industri->kontak_person : null,

                    // Tabel Dosen
                    'nama_dosen' => $user->dosen ? $user->dosen->nama_dosen : null,
                    'nip' => $user->dosen ? $user->dosen->nip : null,

                    // Tabel Admin
                    'nama_admin' => $user->admin ? $user->admin->nama_admin : null,
                ] : null,
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
