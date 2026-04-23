<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $role = $this->input('role');

        $rules = [
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::enum(UserRole::class)],
        ];

        // Role-specific validation
        if ($role === UserRole::STUDENT->value) {
            $rules['nim'] = ['required', 'string', 'max:20', 'unique:mahasiswas,nim'];
            $rules['nama_lengkap'] = ['required', 'string', 'max:100'];
            $rules['prodi'] = ['nullable', 'string', 'max:50'];
        }

        if ($role === UserRole::INDUSTRY->value) {
            $rules['nama_perusahaan'] = ['required', 'string', 'max:100'];
            $rules['alamat'] = ['nullable', 'string'];
            $rules['kontak_person'] = ['nullable', 'string', 'max:50'];
            $rules['latitude'] = ['nullable', 'numeric', 'between:-90,90'];
            $rules['longitude'] = ['nullable', 'numeric', 'between:-180,180'];
        }

        if (in_array($role, [UserRole::SUPERVISOR_1->value, UserRole::SUPERVISOR_2->value])) {
            $rules['nip'] = ['required', 'string', 'max:20', 'unique:dosens,nip'];
            $rules['nama_dosen'] = ['required', 'string', 'max:100'];
        }

        return $rules;
    }
}
