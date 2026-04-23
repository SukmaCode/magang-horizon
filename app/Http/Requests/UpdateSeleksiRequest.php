<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeleksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pendaftaran.review');
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:diterima,ditolak'],
            'keterangan_industri' => ['required_if:status,ditolak', 'nullable', 'string', 'max:1000'],
        ];
    }
}
