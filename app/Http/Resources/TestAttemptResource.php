<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'test_id'            => $this->test_id,
            'user_id'            => $this->user_id,
            'access_id'          => $this->access_id,
            'status'             => $this->status,
            'total_questions'    => $this->total_questions,
            'answered_count'     => $this->answered_count,
            'correct_count'      => $this->correct_count,
            'score'              => $this->score,
            'score_percent'      => $this->getScorePercent(),
            'total_points'       => $this->total_points,
            'earned_points'      => $this->earned_points,
            'subject_scores'     => $this->subject_scores,
            'started_at'         => $this->started_at,
            'submitted_at'       => $this->submitted_at,
            'time_spent_seconds' => $this->time_spent_seconds,
            'formatted_time'     => $this->getFormattedTime(),
            'tab_switch_count'   => $this->tab_switch_count,
            'is_flagged'         => $this->is_flagged,
            'cheat_log'          => $this->cheat_log,
            'rank'               => $this->rank,
            'test'               => new TestResource($this->whenLoaded('test')),
            'user'               => new UserResource($this->whenLoaded('user')),
            'answers'            => AttemptAnswerResource::collection($this->whenLoaded('answers')),
        ];
    }
}
