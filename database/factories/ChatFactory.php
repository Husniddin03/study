<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type'                  => 'group',
            'name'                  => fake()->words(2, true),
            'username'              => null,
            'description'           => fake()->optional()->sentence(),
            'avatar_url'            => null,
            'created_by'            => User::factory(),
            'is_public'             => false,
            'is_exam_mode'          => false,
            'exam_monitor_tabs'     => false,
            'exam_monitor_copy'     => false,
            'exam_require_selfie'   => false,
            'exam_hotspot_required' => false,
            'member_count'          => 1,
            'settings'              => null,
        ];
    }

    public function private(): static
    {
        return $this->state(fn () => ['type' => 'private', 'name' => null]);
    }

    public function channel(): static
    {
        return $this->state(fn () => ['type' => 'channel', 'is_public' => true]);
    }

    public function examMode(): static
    {
        return $this->state(fn () => ['is_exam_mode' => true, 'exam_hotspot_required' => true]);
    }
}
