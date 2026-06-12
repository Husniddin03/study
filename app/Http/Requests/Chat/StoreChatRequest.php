<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'                  => ['required', 'string', 'in:private,group,channel'],
            'name'                  => ['required_unless:type,private', 'nullable', 'string', 'max:255'],
            'username'              => ['nullable', 'string', 'max:32', 'alpha_dash', 'unique:chats,username'],
            'description'           => ['nullable', 'string', 'max:1000'],
            'avatar_url'            => ['nullable', 'string', 'max:1024'],
            'is_public'             => ['boolean'],
            'is_exam_mode'          => ['boolean'],
            'exam_monitor_tabs'     => ['boolean'],
            'exam_monitor_copy'     => ['boolean'],
            'exam_require_selfie'   => ['boolean'],
            'exam_hotspot_required' => ['boolean'],
            'settings'              => ['nullable', 'array'],
            // private chat ochishda 2-tomon useri
            'member_id'             => ['required_if:type,private', 'nullable', 'uuid', 'exists:users,id'],
        ];
    }
}
