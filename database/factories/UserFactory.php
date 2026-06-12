<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /** Default parol — testlarda ishlatamiz */
    public static string $defaultPassword = 'password123';

    public function definition(): array
    {
        return [
            'username'      => fake()->unique()->userName() . Str::random(3),
            'phone'         => '+99890' . fake()->unique()->numerify('#######'),
            'email'         => fake()->unique()->safeEmail(),
            'password_hash' => Hash::make(self::$defaultPassword),
            'full_name'     => fake()->name(),
            'avatar_url'    => null,
            'bio'           => fake()->optional()->sentence(),
            'is_verified'   => true,
            'is_active'     => true,
            'last_seen_at'  => now(),
            'settings'      => ['theme' => 'light', 'lang' => 'uz'],
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function offline(): static
    {
        return $this->state(fn () => ['last_seen_at' => now()->subHour()]);
    }
}
