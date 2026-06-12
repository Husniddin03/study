<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'contact_id' => $this->contact_id,
            'nickname'   => $this->nickname,
            'is_blocked' => $this->is_blocked,
            'contact'    => new UserResource($this->whenLoaded('contact')),
            'created_at' => $this->created_at,
        ];
    }
}
