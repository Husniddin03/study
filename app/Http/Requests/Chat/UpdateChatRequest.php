<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $chatId = $this->route('chat')?->id ?? $this->route('chat');

        return [
            'name'                  => ['sometimes', 'string', 'max:255'],
            'username'              => ['sometimes', 'nullable', 'string', 'max:32', 'alpha_dash', Rule::unique('chats', 'username')->ignore($chatId)],
            'description'           => ['sometimes', 'nullable', 'string', 'max:1000'],
            'avatar_url'            => ['sometimes', 'nullable', 'string', 'max:1024'],
            'is_public'             => ['sometimes', 'boolean'],
            'is_exam_mode'          => ['sometimes', 'boolean'],
            'exam_monitor_tabs'     => ['sometimes', 'boolean'],
            'exam_monitor_copy'     => ['sometimes', 'boolean'],
            'exam_require_selfie'   => ['sometimes', 'boolean'],
            'exam_hotspot_required' => ['sometimes', 'boolean'],
            'settings'              => ['sometimes', 'array'],
        ];
    }
}
