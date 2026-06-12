<?php

namespace Database\Factories;

use App\Models\TestQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionOptionFactory extends Factory
{
    protected $model = \App\Models\QuestionOption::class;

    public function definition(): array
    {
        return [
            'question_id' => TestQuestion::factory(),
            'content'     => fake()->word(),
            'image_url'   => null,
            'formula'     => null,
            'is_correct'  => false,
            'order_index' => 0,
        ];
    }

    public function correct(): static
    {
        return $this->state(fn () => ['is_correct' => true]);
    }
}
