<?php

namespace App\Http\Requests;

use App\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf', 'max:20480'],
            'type' => ['required', Rule::enum(DocumentType::class)],
            'documentable_type' => ['required', 'string'],
            'documentable_id' => ['required', 'integer'],
        ];
    }
}
