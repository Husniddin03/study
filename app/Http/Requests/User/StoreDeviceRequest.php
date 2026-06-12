<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_token' => ['required', 'string', 'max:512'],
            'platform'     => ['required', 'string', 'in:ios,android,web'],
            'device_name'  => ['nullable', 'string', 'max:255'],
        ];
    }
}
