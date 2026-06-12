<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'chat_id'            => $this->chat_id,
            'user_id'            => $this->user_id,
            'role'               => $this->role,
            'can_send_messages'  => $this->can_send_messages,
            'can_send_tests'     => $this->can_send_tests,
            'can_create_exam'    => $this->can_create_exam,
            'can_manage_members' => $this->can_manage_members,
            'is_muted'           => $this->is_muted,
            'muted_until'        => $this->muted_until,
            'joined_at'          => $this->joined_at,
            'user'               => new UserResource($this->whenLoaded('user')),
        ];
    }
}
