<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestFactory extends Factory
{
    protected $model = \App\Models\Test::class;

    public function definition(): array
    {
        return [
            'creator_id'         => User::factory(),
            'title'              => fake()->sentence(3),
            'description'        => fake()->optional()->paragraph(),
            'type'               => 'quiz',
            'visibility'         => 'public',
            'duration_minutes'   => 30,
            'max_attempts'       => 0, // cheksiz
            'show_answers_after' => true,
            'shuffle_questions'  => false,
            'shuffle_options'    => false,
            'passing_score'      => 60,
            'dtm_config'         => null,
            'anti_cheat_enabled' => false,
            'require_hotspot'    => false,
            'block_tab_switch'   => false,
            'block_copy_paste'   => false,
            'require_camera'     => false,
            'tab_switch_limit'   => 0,
            'is_published'       => false,
            'available_from'     => null,
            'available_until'    => null,
            'attempt_count'      => 0,
            'avg_score'          => 0,
            'tags'               => ['matematika'],
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['is_published' => true]);
    }

    public function dtm(): static
    {
        return $this->state(fn () => ['type' => 'dtm', 'duration_minutes' => 180]);
    }

    public function private(): static
    {
        return $this->state(fn () => ['visibility' => 'private']);
    }

    public function antiCheat(): static
    {
        return $this->state(fn () => [
            'anti_cheat_enabled' => true,
            'block_tab_switch'   => true,
            'tab_switch_limit'   => 3,
        ]);
    }
}
