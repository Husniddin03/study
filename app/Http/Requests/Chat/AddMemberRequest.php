<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class AddMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'             => ['required', 'uuid', 'exists:users,id'],
            'role'                => ['nullable', 'string', 'in:member,admin'],
            'can_send_messages'   => ['boolean'],
            'can_send_tests'      => ['boolean'],
            'can_create_exam'     => ['boolean'],
            'can_manage_members'  => ['boolean'],
        ];
    }
}
