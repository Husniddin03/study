<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username'  => ['required', 'string', 'min:3', 'max:32', 'alpha_dash', 'unique:users,username'],
            'phone'     => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email'     => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:6', 'confirmed'],
            'full_name' => ['required', 'string', 'max:255'],
        ];
    }
}
