<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role'                => ['sometimes', 'string', 'in:member,admin'],
            'can_send_messages'   => ['sometimes', 'boolean'],
            'can_send_tests'      => ['sometimes', 'boolean'],
            'can_create_exam'     => ['sometimes', 'boolean'],
            'can_manage_members'  => ['sometimes', 'boolean'],
            'is_muted'            => ['sometimes', 'boolean'],
            'muted_until'         => ['sometimes', 'nullable', 'date'],
        ];
    }
}
