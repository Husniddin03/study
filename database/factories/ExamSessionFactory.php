<?php

namespace Database\Factories;

use App\Models\TestAccess;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExamSessionFactory extends Factory
{
    protected $model = \App\Models\ExamSession::class;

    public function definition(): array
    {
        return [
            'access_id'        => TestAccess::factory()->exam(),
            'host_user_id'     => User::factory(),
            'session_code'     => strtoupper(Str::random(6)),
            'network_ssid'     => 'EXAM_WIFI',
            'network_ip_range' => '192.168.1.0/24',
            'status'           => 'waiting',
            'started_at'       => null,
            'ended_at'         => null,
            'connected_count'  => 0,
            'max_allowed'      => 30,
            'monitoring_log'   => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active', 'started_at' => now()]);
    }

    public function finished(): static
    {
        return $this->state(fn () => ['status' => 'finished', 'ended_at' => now()]);
    }
}
