<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'access_id'        => $this->access_id,
            'host_user_id'     => $this->host_user_id,
            'session_code'     => $this->session_code,
            'network_ssid'     => $this->network_ssid,
            'network_ip_range' => $this->network_ip_range,
            'status'           => $this->status,
            'started_at'       => $this->started_at,
            'ended_at'         => $this->ended_at,
            'connected_count'  => $this->connected_count,
            'max_allowed'      => $this->max_allowed,
            'monitoring_log'   => $this->monitoring_log,
            'host'             => new UserResource($this->whenLoaded('host')),
            'participants'     => ExamParticipantResource::collection($this->whenLoaded('participants')),
            'created_at'       => $this->created_at,
        ];
    }
}
