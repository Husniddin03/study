<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'creator_id',
        'title',
        'description',
        'type',
        'visibility',
        'duration_minutes',
        'max_attempts',
        'show_answers_after',
        'shuffle_questions',
        'shuffle_options',
        'passing_score',
        'dtm_config',
        'anti_cheat_enabled',
        'require_hotspot',
        'block_tab_switch',
        'block_copy_paste',
        'require_camera',
        'tab_switch_limit',
        'is_published',
        'available_from',
        'available_until',
        'attempt_count',
        'avg_score',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'show_answers_after'  => 'boolean',
            'shuffle_questions'   => 'boolean',
            'shuffle_options'     => 'boolean',
            'anti_cheat_enabled'  => 'boolean',
            'require_hotspot'     => 'boolean',
            'block_tab_switch'    => 'boolean',
            'block_copy_paste'    => 'boolean',
            'require_camera'      => 'boolean',
            'is_published'        => 'boolean',
            'dtm_config'          => 'array',
            'tags'                => 'array',
            'available_from'      => 'datetime',
            'available_until'     => 'datetime',
            'avg_score'           => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function questions()
    {
        return $this->hasMany(TestQuestion::class)->orderBy('order_index');
    }

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function accesses()
    {
        return $this->hasMany(TestAccess::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_published', true)
            ->where(fn($q) => $q->whereNull('available_from')
                ->orWhere('available_from', '<=', now()))
            ->where(fn($q) => $q->whereNull('available_until')
                ->orWhere('available_until', '>=', now()));
    }

    // ── Helpers ────────────────────────────────────────────

    public function isDtm(): bool     { return $this->type === 'dtm'; }
    public function isQuiz(): bool    { return $this->type === 'quiz'; }
    public function isPublic(): bool  { return $this->visibility === 'public'; }
    public function isPrivate(): bool { return $this->visibility === 'private'; }

    public function totalQuestions(): int
    {
        return $this->questions()->count();
    }

    public function getUserAttemptCount(string $userId): int
    {
        return $this->attempts()->where('user_id', $userId)->count();
    }

    public function canUserAttempt(string $userId): bool
    {
        if ($this->max_attempts === 0) return true;
        return $this->getUserAttemptCount($userId) < $this->max_attempts;
    }

    public function getLeaderboard(int $limit = 10)
    {
        return $this->attempts()
            ->where('status', 'submitted')
            ->orderByDesc('score')
            ->orderBy('time_spent_seconds')
            ->limit($limit)
            ->with('user:id,username,full_name,avatar_url')
            ->get();
    }
}
