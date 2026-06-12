<?php

namespace Database\Factories;

use App\Models\TestAttempt;
use App\Models\TestQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttemptAnswerFactory extends Factory
{
    protected $model = \App\Models\AttemptAnswer::class;

    public function definition(): array
    {
        return [
            'attempt_id'          => TestAttempt::factory(),
            'question_id'         => TestQuestion::factory(),
            'selected_option_id'  => null,
            'selected_option_ids' => null,
            'open_answer'         => null,
            'is_correct'          => false,
            'points_earned'       => 0,
            'time_spent_seconds'  => 10,
            'answered_at'         => now(),
        ];
    }
}
