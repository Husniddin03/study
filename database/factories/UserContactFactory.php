<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserContactFactory extends Factory
{
    protected $model = \App\Models\UserContact::class;

    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'contact_id' => User::factory(),
            'nickname'   => fake()->optional()->firstName(),
            'is_blocked' => false,
            'created_at' => now(),
        ];
    }

    public function blocked(): static
    {
        return $this->state(fn () => ['is_blocked' => true]);
    }
}
