<?php

namespace App\Http\Requests\Attempt;

use Illuminate\Foundation\Http\FormRequest;

class CheatLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'  => ['required', 'string', 'in:tab_switch,copy_paste,external_request,no_camera,multiple_faces,offline'],
            'extra' => ['nullable', 'array'],
        ];
    }
}
