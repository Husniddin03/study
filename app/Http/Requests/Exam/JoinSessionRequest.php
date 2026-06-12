<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class JoinSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_code' => ['required', 'string', 'max:16'],
            'device_info'  => ['nullable', 'string', 'max:512'],
        ];
    }
}
