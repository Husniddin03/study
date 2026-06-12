<?php

namespace App\Http\Requests\Attempt;

use Illuminate\Foundation\Http\FormRequest;

class StartAttemptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'test_id'   => ['required', 'uuid', 'exists:tests,id'],
            'access_id' => ['nullable', 'uuid', 'exists:test_accesses,id'],
        ];
    }
}
