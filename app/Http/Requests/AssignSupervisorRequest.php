<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignSupervisorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('magang.assign-supervisor');
    }

    public function rules(): array
    {
        return [
            'supervisor_kampus_id' => ['required', 'exists:dosens,id'],
            'supervisor_industri_id' => ['required', 'exists:users,id'],
        ];
    }
}
