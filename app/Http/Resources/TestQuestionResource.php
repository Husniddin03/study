<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestQuestionResource extends JsonResource
{
    /**
     * $this->additional(['hide_answers' => true]) bilan
     * to'g'ri javoblarni yashirish mumkin (test topshirayotgan o'quvchi uchun).
     */
    public function toArray(Request $request): array
    {
        $hideAnswers = $this->additional['hide_answers'] ?? false;

        return [
            'id'            => $this->id,
            'test_id'       => $this->test_id,
            'subject'       => $this->subject,
            'block_name'    => $this->block_name,
            'content_type'  => $this->content_type,
            'content'       => $this->content,
            'image_url'     => $this->image_url,
            'formula'       => $this->formula,
            'extra_content' => $this->extra_content,
            'answer_type'   => $this->answer_type,
            'order_index'   => $this->order_index,
            'points'        => $this->points,
            'explanation'   => $this->when(! $hideAnswers, $this->explanation),
            'options'       => $this->whenLoaded('options', function () use ($hideAnswers) {
                                  return $this->options->map(fn($opt) => clone (new QuestionOptionResource($opt))->additional(['hide_answers' => $hideAnswers]));
                               }),
        ];
    }
}
