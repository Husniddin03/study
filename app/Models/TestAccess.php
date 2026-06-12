<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAccess extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'test_id',
        'granted_by',
        'access_type',
        'chat_id',
        'user_id',
        'is_exam',
        'exam_duration_minutes',
        'exam_starts_at',
        'exam_ends_at',
        'max_participants',
        'require_hotspot',
        'block_tab_switch',
        'require_camera',
        'is_active',
        'expires_at',
        'invite_code',
    ];

    protected function casts(): array
    {
        return [
            'is_exam'         => 'boolean',
            'require_hotspot' => 'boolean',
            'block_tab_switch'=> 'boolean',
            'require_camera'  => 'boolean',
            'is_active'       => 'boolean',
            'exam_starts_at'  => 'datetime',
            'exam_ends_at'    => 'datetime',
            'expires_at'      => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class, 'access_id');
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class, 'access_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'test_access_id');
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now()));
    }

    public function scopeForChat($query, string $chatId)
    {
        return $query->where('access_type', 'chat')->where('chat_id', $chatId);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('access_type', 'user')->where('user_id', $userId);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExamActive(): bool
    {
        if (!$this->is_exam) return false;
        $now = now();
        $started = !$this->exam_starts_at || $this->exam_starts_at->lte($now);
        $notEnded = !$this->exam_ends_at || $this->exam_ends_at->gte($now);
        return $started && $notEnded && $this->is_active;
    }

    public function getParticipantCount(): int
    {
        return $this->attempts()->count();
    }
}
