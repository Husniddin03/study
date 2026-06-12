<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = \App\Models\Notification::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'type'           => 'system',
            'title'          => fake()->sentence(3),
            'body'           => fake()->sentence(),
            'data'           => null,
            'reference_id'   => null,
            'reference_type' => null,
            'is_read'        => false,
            'read_at'        => null,
            'created_at'     => now(),
        ];
    }

    public function read(): static
    {
        return $this->state(fn () => ['is_read' => true, 'read_at' => now()]);
    }
}
