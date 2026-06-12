<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'chat_id'            => Chat::factory(),
            'user_id'            => User::factory(),
            'role'               => 'member',
            'can_send_messages'  => true,
            'can_send_tests'     => false,
            'can_create_exam'    => false,
            'can_manage_members' => false,
            'is_muted'           => false,
            'muted_until'        => null,
            'joined_at'          => now(),
            'invited_by'         => null,
        ];
    }

    public function creator(): static
    {
        return $this->state(fn () => [
            'role'               => 'creator',
            'can_send_messages'  => true,
            'can_send_tests'     => true,
            'can_create_exam'    => true,
            'can_manage_members' => true,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin', 'can_manage_members' => true]);
    }

    public function muted(): static
    {
        return $this->state(fn () => ['is_muted' => true, 'muted_until' => now()->addHour()]);
    }
}
