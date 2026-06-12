<?php

namespace App\Http\Requests\Test;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject'        => ['sometimes', 'nullable', 'string', 'max:100'],
            'block_name'     => ['sometimes', 'nullable', 'string', 'max:100'],
            'content_type'   => ['sometimes', 'string', 'in:text,image,formula,mixed'],
            'content'        => ['sometimes', 'string'],
            'image_url'      => ['sometimes', 'nullable', 'string', 'max:1024'],
            'formula'        => ['sometimes', 'nullable', 'string'],
            'extra_content'  => ['sometimes', 'nullable', 'array'],
            'answer_type'    => ['sometimes', 'string', 'in:single,multiple,true_false,open_text'],
            'order_index'    => ['sometimes', 'integer', 'min:0'],
            'points'         => ['sometimes', 'integer', 'min:0'],
            'explanation'    => ['sometimes', 'nullable', 'string'],
        ];
    }
}
