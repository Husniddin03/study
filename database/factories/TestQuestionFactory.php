<?php

namespace Database\Factories;

use App\Models\Test;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestQuestionFactory extends Factory
{
    protected $model = \App\Models\TestQuestion::class;

    public function definition(): array
    {
        return [
            'test_id'       => Test::factory(),
            'subject'       => 'matematika',
            'block_name'    => null,
            'content_type'  => 'text',
            'content'       => fake()->sentence() . '?',
            'image_url'     => null,
            'formula'       => null,
            'extra_content' => null,
            'answer_type'   => 'single',
            'order_index'   => 0,
            'points'        => 1,
            'explanation'   => null,
        ];
    }

    public function multiple(): static
    {
        return $this->state(fn () => ['answer_type' => 'multiple']);
    }

    public function openText(): static
    {
        return $this->state(fn () => ['answer_type' => 'open_text']);
    }
}
