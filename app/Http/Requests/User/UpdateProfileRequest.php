<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'username'   => ['sometimes', 'string', 'min:3', 'max:32', 'alpha_dash', Rule::unique('users', 'username')->ignore($userId)],
            'email'      => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'full_name'  => ['sometimes', 'string', 'max:255'],
            'avatar_url' => ['sometimes', 'nullable', 'string', 'max:1024'],
            'bio'        => ['sometimes', 'nullable', 'string', 'max:500'],
            'settings'   => ['sometimes', 'array'],
        ];
    }
}
