<?php

namespace App\Http\Requests\Access;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'test_id'               => ['required', 'uuid', 'exists:tests,id'],
            'access_type'           => ['required', 'string', 'in:chat,user,public,link'],
            'chat_id'               => ['required_if:access_type,chat', 'nullable', 'uuid', 'exists:chats,id'],
            'user_id'               => ['required_if:access_type,user', 'nullable', 'uuid', 'exists:users,id'],
            'is_exam'               => ['boolean'],
            'exam_duration_minutes' => ['nullable', 'integer', 'min:1'],
            'exam_starts_at'        => ['nullable', 'date'],
            'exam_ends_at'          => ['nullable', 'date', 'after_or_equal:exam_starts_at'],
            'max_participants'      => ['nullable', 'integer', 'min:1'],
            'require_hotspot'       => ['boolean'],
            'block_tab_switch'      => ['boolean'],
            'require_camera'        => ['boolean'],
            'expires_at'            => ['nullable', 'date'],
        ];
    }
}
