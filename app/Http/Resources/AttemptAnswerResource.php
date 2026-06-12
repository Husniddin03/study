<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttemptAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'attempt_id'          => $this->attempt_id,
            'question_id'         => $this->question_id,
            'selected_option_id'  => $this->selected_option_id,
            'selected_option_ids' => $this->selected_option_ids,
            'open_answer'         => $this->open_answer,
            'is_correct'          => $this->is_correct,
            'points_earned'       => $this->points_earned,
            'time_spent_seconds'  => $this->time_spent_seconds,
            'answered_at'         => $this->answered_at,
            'question'            => new TestQuestionResource($this->whenLoaded('question')),
        ];
    }
}
