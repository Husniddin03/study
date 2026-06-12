<?php

namespace Database\Factories;

use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamParticipantFactory extends Factory
{
    protected $model = \App\Models\ExamParticipant::class;

    public function definition(): array
    {
        return [
            'session_id'             => ExamSession::factory(),
            'user_id'                => User::factory(),
            'attempt_id'             => null,
            'device_ip'              => fake()->ipv4(),
            'device_info'            => 'Android 14 / Chrome',
            'status'                 => 'connected',
            'connected_at'           => now(),
            'disconnected_at'        => null,
            'external_request_count' => 0,
            'tab_switch_count'       => 0,
            'is_flagged'             => false,
            'violation_log'          => null,
        ];
    }

    public function flagged(): static
    {
        return $this->state(fn () => ['is_flagged' => true]);
    }
}
