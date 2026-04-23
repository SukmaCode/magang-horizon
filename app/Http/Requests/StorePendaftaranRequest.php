<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pendaftaran.create');
    }

    public function rules(): array
    {
        return [
            'industri_id' => ['required', 'exists:industris,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'industri_id.required' => 'Pilih industri tujuan magang.',
            'industri_id.exists' => 'Industri tidak ditemukan.',
        ];
    }
}
