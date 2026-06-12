<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMember extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'chat_id',
        'user_id',
        'role',
        'can_send_messages',
        'can_send_tests',
        'can_create_exam',
        'can_manage_members',
        'is_muted',
        'muted_until',
        'joined_at',
        'invited_by',
    ];

    protected function casts(): array
    {
        return [
            'can_send_messages'   => 'boolean',
            'can_send_tests'      => 'boolean',
            'can_create_exam'     => 'boolean',
            'can_manage_members'  => 'boolean',
            'is_muted'            => 'boolean',
            'muted_until'         => 'datetime',
            'joined_at'           => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // ── Helpers ────────────────────────────────────────────

    public function isCreator(): bool  { return $this->role === 'creator'; }
    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function canModerate(): bool { return in_array($this->role, ['creator', 'admin']); }

    public function isMutedNow(): bool
    {
        if (!$this->is_muted) return false;
        if ($this->muted_until && $this->muted_until->isPast()) return false;
        return true;
    }
}
