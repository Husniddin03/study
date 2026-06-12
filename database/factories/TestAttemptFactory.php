<?php

namespace Database\Factories;

use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestAttemptFactory extends Factory
{
    protected $model = \App\Models\TestAttempt::class;

    public function definition(): array
    {
        return [
            'test_id'            => Test::factory(),
            'user_id'            => User::factory(),
            'access_id'          => null,
            'status'             => 'in_progress',
            'total_questions'    => 5,
            'answered_count'     => 0,
            'correct_count'      => 0,
            'score'              => 0,
            'total_points'       => 5,
            'earned_points'      => 0,
            'subject_scores'     => null,
            'started_at'         => now(),
            'submitted_at'       => null,
            'time_spent_seconds' => 0,
            'tab_switch_count'   => 0,
            'is_flagged'         => false,
            'cheat_log'          => null,
            'rank'               => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn () => [
            'status'        => 'submitted',
            'submitted_at'  => now(),
            'answered_count'=> 5,
            'correct_count' => 4,
            'earned_points' => 4,
            'score'         => 80,
        ]);
    }
}
