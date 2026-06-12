<?php

namespace App\Http\Requests\Test;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject'        => ['nullable', 'string', 'max:100'],
            'block_name'     => ['nullable', 'string', 'max:100'],
            'content_type'   => ['required', 'string', 'in:text,image,formula,mixed'],
            'content'        => ['required', 'string'],
            'image_url'      => ['nullable', 'string', 'max:1024'],
            'formula'        => ['nullable', 'string'],
            'extra_content'  => ['nullable', 'array'],
            'answer_type'    => ['required', 'string', 'in:single,multiple,true_false,open_text'],
            'order_index'    => ['nullable', 'integer', 'min:0'],
            'points'         => ['nullable', 'integer', 'min:0'],
            'explanation'    => ['nullable', 'string'],

            // ixtiyoriy: savol bilan birga variantlar
            'options'                 => ['nullable', 'array'],
            'options.*.content'       => ['required_with:options', 'string'],
            'options.*.image_url'     => ['nullable', 'string', 'max:1024'],
            'options.*.formula'       => ['nullable', 'string'],
            'options.*.is_correct'    => ['boolean'],
            'options.*.order_index'   => ['nullable', 'integer', 'min:0'],
        ];
    }
}
