<?php

namespace App\Http\Requests\Test;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'              => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string', 'max:2000'],
            'type'               => ['required', 'string', 'in:dtm,quiz,custom'],
            'visibility'         => ['required', 'string', 'in:public,private,unlisted'],
            'duration_minutes'   => ['nullable', 'integer', 'min:0'],
            'max_attempts'       => ['nullable', 'integer', 'min:0'],
            'show_answers_after' => ['boolean'],
            'shuffle_questions'  => ['boolean'],
            'shuffle_options'    => ['boolean'],
            'passing_score'      => ['nullable', 'integer', 'min:0', 'max:100'],
            'dtm_config'         => ['nullable', 'array'],
            'anti_cheat_enabled' => ['boolean'],
            'require_hotspot'    => ['boolean'],
            'block_tab_switch'   => ['boolean'],
            'block_copy_paste'   => ['boolean'],
            'require_camera'     => ['boolean'],
            'tab_switch_limit'   => ['nullable', 'integer', 'min:0'],
            'is_published'       => ['boolean'],
            'available_from'     => ['nullable', 'date'],
            'available_until'    => ['nullable', 'date', 'after_or_equal:available_from'],
            'tags'               => ['nullable', 'array'],
            'tags.*'             => ['string', 'max:50'],
        ];
    }
}
