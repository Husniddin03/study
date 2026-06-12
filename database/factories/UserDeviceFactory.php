<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserDeviceFactory extends Factory
{
    protected $model = \App\Models\UserDevice::class;

    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'device_token' => Str::random(40),
            'platform'     => 'android',
            'device_name'  => 'Pixel 8',
            'is_active'    => true,
            'last_used_at' => now(),
        ];
    }
}
