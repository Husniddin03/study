<?php

namespace App\Http\Requests\Access;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_exam'               => ['sometimes', 'boolean'],
            'exam_duration_minutes' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'exam_starts_at'        => ['sometimes', 'nullable', 'date'],
            'exam_ends_at'          => ['sometimes', 'nullable', 'date', 'after_or_equal:exam_starts_at'],
            'max_participants'      => ['sometimes', 'nullable', 'integer', 'min:1'],
            'require_hotspot'       => ['sometimes', 'boolean'],
            'block_tab_switch'      => ['sometimes', 'boolean'],
            'require_camera'        => ['sometimes', 'boolean'],
            'is_active'             => ['sometimes', 'boolean'],
            'expires_at'            => ['sometimes', 'nullable', 'date'],
        ];
    }
}
