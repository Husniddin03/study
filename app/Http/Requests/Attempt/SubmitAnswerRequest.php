<?php

namespace App\Http\Requests\Attempt;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_id'          => ['required', 'uuid', 'exists:test_questions,id'],
            'selected_option_id'   => ['nullable', 'uuid', 'exists:question_options,id'],
            'selected_option_ids'  => ['nullable', 'array'],
            'selected_option_ids.*'=> ['uuid', 'exists:question_options,id'],
            'open_answer'          => ['nullable', 'string'],
            'time_spent_seconds'   => ['nullable', 'integer', 'min:0'],
        ];
    }
}
