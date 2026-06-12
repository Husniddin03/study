<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'session_id'             => $this->session_id,
            'user_id'                => $this->user_id,
            'attempt_id'             => $this->attempt_id,
            'device_ip'              => $this->device_ip,
            'device_info'            => $this->device_info,
            'status'                 => $this->status,
            'connected_at'           => $this->connected_at,
            'disconnected_at'        => $this->disconnected_at,
            'external_request_count' => $this->external_request_count,
            'tab_switch_count'       => $this->tab_switch_count,
            'is_flagged'             => $this->is_flagged,
            'violation_log'          => $this->violation_log,
            'user'                   => new UserResource($this->whenLoaded('user')),
            'attempt'                => new TestAttemptResource($this->whenLoaded('attempt')),
        ];
    }
}
