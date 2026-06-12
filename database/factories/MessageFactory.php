<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'chat_id'    => Chat::factory(),
            'sender_id'  => User::factory(),
            'type'       => 'text',
            'content'    => fake()->sentence(),
            'is_pinned'  => false,
            'is_deleted' => false,
            'reactions'  => null,
            'read_by'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function deleted(): static
    {
        return $this->state(fn () => ['is_deleted' => true, 'deleted_at' => now()]);
    }

    public function pinned(): static
    {
        return $this->state(fn () => ['is_pinned' => true]);
    }
}
