<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'platform'     => $this->platform,
            'device_name'  => $this->device_name,
            'is_active'    => $this->is_active,
            'last_used_at' => $this->last_used_at,
            'created_at'   => $this->created_at,
        ];
    }
}
