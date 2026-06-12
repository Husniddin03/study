<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'username'     => $this->username,
            'phone'        => $this->when($this->id === optional($request->user())->id, $this->phone),
            'email'        => $this->when($this->id === optional($request->user())->id, $this->email),
            'full_name'    => $this->full_name,
            'avatar_url'   => $this->avatar_url,
            'bio'          => $this->bio,
            'is_verified'  => $this->is_verified,
            'is_active'    => $this->is_active,
            'is_online'    => $this->isOnline(),
            'last_seen_at' => $this->last_seen_at,
            'settings'     => $this->when($this->id === optional($request->user())->id, $this->settings),
            'created_at'   => $this->created_at,
        ];
    }
}
