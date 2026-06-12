<?php

namespace App\Http\Requests\Test;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'              => ['sometimes', 'string', 'max:255'],
            'description'        => ['sometimes', 'nullable', 'string', 'max:2000'],
            'type'               => ['sometimes', 'string', 'in:dtm,quiz,custom'],
            'visibility'         => ['sometimes', 'string', 'in:public,private,unlisted'],
            'duration_minutes'   => ['sometimes', 'nullable', 'integer', 'min:0'],
            'max_attempts'       => ['sometimes', 'nullable', 'integer', 'min:0'],
            'show_answers_after' => ['sometimes', 'boolean'],
            'shuffle_questions'  => ['sometimes', 'boolean'],
            'shuffle_options'    => ['sometimes', 'boolean'],
            'passing_score'      => ['sometimes', 'nullable', 'integer', 'min:0', 'max:100'],
            'dtm_config'         => ['sometimes', 'nullable', 'array'],
            'anti_cheat_enabled' => ['sometimes', 'boolean'],
            'require_hotspot'    => ['sometimes', 'boolean'],
            'block_tab_switch'   => ['sometimes', 'boolean'],
            'block_copy_paste'   => ['sometimes', 'boolean'],
            'require_camera'     => ['sometimes', 'boolean'],
            'tab_switch_limit'   => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_published'       => ['sometimes', 'boolean'],
            'available_from'     => ['sometimes', 'nullable', 'date'],
            'available_until'    => ['sometimes', 'nullable', 'date', 'after_or_equal:available_from'],
            'tags'               => ['sometimes', 'nullable', 'array'],
            'tags.*'             => ['string', 'max:50'],
        ];
    }
}
