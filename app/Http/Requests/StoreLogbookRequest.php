<?php

namespace App\Http\Requests;

use App\Enums\StatusPresensi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLogbookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('logbook.create');
    }

    public function rules(): array
    {
        return [
            'magang_id' => ['required', 'exists:magang_aktifs,id'],
            'kegiatan' => ['required', 'string', 'max:2000'],
            'status_presensi' => ['nullable', Rule::enum(StatusPresensi::class)],
            'tanggal_waktu' => ['required', 'date'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
