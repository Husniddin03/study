<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'access_id',
        'host_user_id',
        'session_code',
        'network_ssid',
        'network_ip_range',
        'status',
        'started_at',
        'ended_at',
        'connected_count',
        'max_allowed',
        'monitoring_log',
    ];

    protected function casts(): array
    {
        return [
            'monitoring_log'  => 'array',
            'started_at'      => 'datetime',
            'ended_at'        => 'datetime',
            'connected_count' => 'integer',
            'max_allowed'     => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function access()
    {
        return $this->belongsTo(TestAccess::class, 'access_id');
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function participants()
    {
        return $this->hasMany(ExamParticipant::class, 'session_id');
    }

    public function activeParticipants()
    {
        return $this->hasMany(ExamParticipant::class, 'session_id')
            ->where('status', 'connected');
    }

    public function flaggedParticipants()
    {
        return $this->hasMany(ExamParticipant::class, 'session_id')
            ->where('is_flagged', true);
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ── Helpers ────────────────────────────────────────────

    public function isActive(): bool   { return $this->status === 'active'; }
    public function isWaiting(): bool  { return $this->status === 'waiting'; }
    public function isFinished(): bool { return $this->status === 'finished'; }

    public function start(): void
    {
        $this->update(['status' => 'active', 'started_at' => now()]);
    }

    public function finish(): void
    {
        $this->update(['status' => 'finished', 'ended_at' => now()]);
    }

    public function logEvent(string $type, array $data = []): void
    {
        $log = $this->monitoring_log ?? [];
        $log[] = array_merge(['type' => $type, 'at' => now()->toISOString()], $data);
        $this->update(['monitoring_log' => $log]);
    }
}
