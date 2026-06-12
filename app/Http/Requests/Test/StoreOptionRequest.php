<?php

namespace App\Http\Requests\Test;

use Illuminate\Foundation\Http\FormRequest;

class StoreOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content'     => ['required', 'string'],
            'image_url'   => ['nullable', 'string', 'max:1024'],
            'formula'     => ['nullable', 'string'],
            'is_correct'  => ['boolean'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
