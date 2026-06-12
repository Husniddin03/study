<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $hideAnswers = $this->additional['hide_answers'] ?? false;

        return [
            'id'          => $this->id,
            'question_id' => $this->question_id,
            'content'     => $this->content,
            'image_url'   => $this->image_url,
            'formula'     => $this->formula,
            'label'       => $this->getLabel(),
            'is_correct'  => $this->when(! $hideAnswers, $this->is_correct),
            'order_index' => $this->order_index,
        ];
    }
}
