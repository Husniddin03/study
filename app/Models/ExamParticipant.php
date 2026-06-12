<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamParticipant extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'user_id',
        'attempt_id',
        'device_ip',
        'device_info',
        'status',
        'connected_at',
        'disconnected_at',
        'external_request_count',
        'tab_switch_count',
        'is_flagged',
        'violation_log',
    ];

    protected function casts(): array
    {
        return [
            'is_flagged'             => 'boolean',
            'violation_log'          => 'array',
            'connected_at'           => 'datetime',
            'disconnected_at'        => 'datetime',
            'external_request_count' => 'integer',
            'tab_switch_count'       => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function session()
    {
        return $this->belongsTo(ExamSession::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attempt()
    {
        return $this->belongsTo(TestAttempt::class, 'attempt_id');
    }

    // ── Helpers ────────────────────────────────────────────

    public function flag(string $violationType, array $extra = []): void
    {
        $log = $this->violation_log ?? [];
        $log[] = array_merge([
            'type' => $violationType,
            'at'   => now()->toISOString(),
        ], $extra);

        $this->update([
            'is_flagged'    => true,
            'violation_log' => $log,
        ]);

        // Host userga bildirishnoma
        $this->session->host->notifications()->create([
            'id'             => \Illuminate\Support\Str::uuid(),
            'type'           => 'exam_alert',
            'title'          => 'Shubhali harakat aniqlandi',
            'body'           => $this->user->username . ': ' . $violationType,
            'reference_id'   => $this->session_id,
            'reference_type' => 'exam_session',
        ]);
    }

    public function recordTabSwitch(): void
    {
        $this->increment('tab_switch_count');
        $this->flag('tab_switch', ['count' => $this->tab_switch_count + 1]);
    }

    public function recordExternalRequest(string $url): void
    {
        $this->increment('external_request_count');
        $this->flag('external_request', ['url' => $url]);
    }
}
