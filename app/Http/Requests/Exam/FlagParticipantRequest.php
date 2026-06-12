<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class FlagParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'violation_type' => ['required', 'string', 'max:64'],
            'extra'          => ['nullable', 'array'],
        ];
    }
}
