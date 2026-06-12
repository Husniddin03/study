<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'test_id',
        'user_id',
        'access_id',
        'status',
        'total_questions',
        'answered_count',
        'correct_count',
        'score',
        'total_points',
        'earned_points',
        'subject_scores',
        'started_at',
        'submitted_at',
        'time_spent_seconds',
        'tab_switch_count',
        'is_flagged',
        'cheat_log',
        'rank',
    ];

    protected function casts(): array
    {
        return [
            'is_flagged'          => 'boolean',
            'subject_scores'      => 'array',
            'cheat_log'           => 'array',
            'started_at'          => 'datetime',
            'submitted_at'        => 'datetime',
            'score'               => 'decimal:2',
            'total_questions'     => 'integer',
            'answered_count'      => 'integer',
            'correct_count'       => 'integer',
            'total_points'        => 'integer',
            'earned_points'       => 'integer',
            'tab_switch_count'    => 'integer',
            'time_spent_seconds'  => 'integer',
            'rank'                => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function access()
    {
        return $this->belongsTo(TestAccess::class, 'access_id');
    }

    public function answers()
    {
        return $this->hasMany(AttemptAnswer::class, 'attempt_id');
    }

    public function examParticipant()
    {
        return $this->hasOne(ExamParticipant::class, 'attempt_id');
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isInProgress(): bool  { return $this->status === 'in_progress'; }
    public function isSubmitted(): bool   { return $this->status === 'submitted'; }
    public function isInvalidated(): bool { return $this->status === 'invalidated'; }

    public function getScorePercent(): float
    {
        if ($this->total_points === 0) return 0;
        return round(($this->earned_points / $this->total_points) * 100, 2);
    }

    public function getFormattedTime(): string
    {
        $seconds = $this->time_spent_seconds ?? 0;
        return sprintf('%02d:%02d', intdiv($seconds, 60), $seconds % 60);
    }

    public function logCheatEvent(string $type, array $extra = []): void
    {
        $log = $this->cheat_log ?? [];
        $log[] = array_merge(['type' => $type, 'at' => now()->toISOString()], $extra);
        $this->update(['cheat_log' => $log]);
    }

    public function flagAndInvalidate(string $reason): void
    {
        $this->logCheatEvent('invalidated', ['reason' => $reason]);
        $this->update(['is_flagged' => true, 'status' => 'invalidated']);
    }
}
