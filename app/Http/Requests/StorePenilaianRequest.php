<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenilaianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'magang_id' => ['required', 'exists:magang_aktifs,id'],
            'nilai' => ['required', 'numeric', 'between:0,100'],
        ];
    }
}
