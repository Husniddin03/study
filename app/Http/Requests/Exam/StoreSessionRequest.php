<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class StoreSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'access_id'        => ['required', 'uuid', 'exists:test_accesses,id'],
            'network_ssid'     => ['nullable', 'string', 'max:255'],
            'network_ip_range' => ['nullable', 'string', 'max:64'],
            'max_allowed'      => ['nullable', 'integer', 'min:1'],
        ];
    }
}
