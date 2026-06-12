<?php

namespace Database\Factories;

use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TestAccessFactory extends Factory
{
    protected $model = \App\Models\TestAccess::class;

    public function definition(): array
    {
        return [
            'test_id'               => Test::factory(),
            'granted_by'            => User::factory(),
            'access_type'           => 'public',
            'chat_id'               => null,
            'user_id'               => null,
            'is_exam'               => false,
            'exam_duration_minutes' => null,
            'exam_starts_at'        => null,
            'exam_ends_at'          => null,
            'max_participants'      => null,
            'require_hotspot'       => false,
            'block_tab_switch'      => false,
            'require_camera'        => false,
            'is_active'             => true,
            'expires_at'            => null,
            'invite_code'           => strtoupper(Str::random(8)),
        ];
    }

    public function exam(): static
    {
        return $this->state(fn () => [
            'is_exam'               => true,
            'exam_duration_minutes' => 60,
            'exam_starts_at'        => now()->subMinute(),
            'exam_ends_at'          => now()->addHour(),
            'require_hotspot'       => true,
        ]);
    }
}
