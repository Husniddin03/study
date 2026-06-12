<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'type'                  => $this->type,
            'name'                  => $this->name,
            'username'              => $this->username,
            'description'           => $this->description,
            'avatar_url'            => $this->avatar_url,
            'is_public'             => $this->is_public,
            'is_exam_mode'          => $this->is_exam_mode,
            'exam_monitor_tabs'     => $this->exam_monitor_tabs,
            'exam_monitor_copy'     => $this->exam_monitor_copy,
            'exam_require_selfie'   => $this->exam_require_selfie,
            'exam_hotspot_required' => $this->exam_hotspot_required,
            'member_count'          => $this->member_count,
            'settings'              => $this->settings,
            'creator'               => new UserResource($this->whenLoaded('creator')),
            'last_message'          => new MessageResource($this->whenLoaded('lastMessage')),
            'my_role'               => $this->when(isset($this->pivot), fn () => $this->pivot->role ?? null),
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
