<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'type'           => $this->type,
            'title'          => $this->title,
            'body'           => $this->body,
            'data'           => $this->data,
            'reference_id'   => $this->reference_id,
            'reference_type' => $this->reference_type,
            'is_read'        => $this->is_read,
            'read_at'        => $this->read_at,
            'created_at'     => $this->created_at,
        ];
    }
}
