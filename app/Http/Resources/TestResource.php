<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'creator_id'         => $this->creator_id,
            'title'              => $this->title,
            'description'        => $this->description,
            'type'               => $this->type,
            'visibility'         => $this->visibility,
            'duration_minutes'   => $this->duration_minutes,
            'max_attempts'       => $this->max_attempts,
            'show_answers_after' => $this->show_answers_after,
            'shuffle_questions'  => $this->shuffle_questions,
            'shuffle_options'    => $this->shuffle_options,
            'passing_score'      => $this->passing_score,
            'dtm_config'         => $this->dtm_config,
            'anti_cheat_enabled' => $this->anti_cheat_enabled,
            'require_hotspot'    => $this->require_hotspot,
            'block_tab_switch'   => $this->block_tab_switch,
            'block_copy_paste'   => $this->block_copy_paste,
            'require_camera'     => $this->require_camera,
            'tab_switch_limit'   => $this->tab_switch_limit,
            'is_published'       => $this->is_published,
            'available_from'     => $this->available_from,
            'available_until'    => $this->available_until,
            'attempt_count'      => $this->attempt_count,
            'avg_score'          => $this->avg_score,
            'tags'               => $this->tags,
            'questions_count'    => $this->whenCounted('questions'),
            'creator'            => new UserResource($this->whenLoaded('creator')),
            'questions'          => TestQuestionResource::collection($this->whenLoaded('questions')),
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
        ];
    }
}
