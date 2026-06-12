<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'              => ['required', 'string', 'in:text,image,file,voice,test_share,system'],
            'content'           => ['required_if:type,text', 'nullable', 'string'],
            'file_url'          => ['required_if:type,image,file,voice', 'nullable', 'string', 'max:1024'],
            'file_name'         => ['nullable', 'string', 'max:255'],
            'file_size'         => ['nullable', 'integer', 'min:0'],
            'test_id'           => ['required_if:type,test_share', 'nullable', 'uuid', 'exists:tests,id'],
            'test_access_id'    => ['nullable', 'uuid', 'exists:test_accesses,id'],
            'reply_to_id'       => ['nullable', 'uuid', 'exists:messages,id'],
            'forwarded_from_id' => ['nullable', 'uuid', 'exists:messages,id'],
        ];
    }
}
