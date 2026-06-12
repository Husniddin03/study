<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestAccessResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'test_id'               => $this->test_id,
            'granted_by'            => $this->granted_by,
            'access_type'           => $this->access_type,
            'chat_id'               => $this->chat_id,
            'user_id'               => $this->user_id,
            'is_exam'               => $this->is_exam,
            'exam_duration_minutes' => $this->exam_duration_minutes,
            'exam_starts_at'        => $this->exam_starts_at,
            'exam_ends_at'          => $this->exam_ends_at,
            'max_participants'      => $this->max_participants,
            'require_hotspot'       => $this->require_hotspot,
            'block_tab_switch'      => $this->block_tab_switch,
            'require_camera'        => $this->require_camera,
            'is_active'             => $this->is_active,
            'is_exam_active'        => $this->isExamActive(),
            'expires_at'            => $this->expires_at,
            'invite_code'           => $this->invite_code,
            'participant_count'     => $this->when($this->relationLoaded('attempts'), fn () => $this->attempts->count()),
            'test'                  => new TestResource($this->whenLoaded('test')),
            'created_at'            => $this->created_at,
        ];
    }
}
