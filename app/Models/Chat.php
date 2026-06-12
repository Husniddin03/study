<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'username',
        'description',
        'avatar_url',
        'created_by',
        'is_public',
        'is_exam_mode',
        'exam_monitor_tabs',
        'exam_monitor_copy',
        'exam_require_selfie',
        'exam_hotspot_required',
        'member_count',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_public'             => 'boolean',
            'is_exam_mode'          => 'boolean',
            'exam_monitor_tabs'     => 'boolean',
            'exam_monitor_copy'     => 'boolean',
            'exam_require_selfie'   => 'boolean',
            'exam_hotspot_required' => 'boolean',
            'settings'              => 'array',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'chat_members', 'chat_id', 'user_id')
            ->withPivot(['role', 'can_send_messages', 'can_send_tests', 'can_create_exam',
                         'can_manage_members', 'is_muted', 'muted_until', 'joined_at'])
            ->withTimestamps();
    }

    public function chatMembers()
    {
        return $this->hasMany(ChatMember::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function testAccesses()
    {
        return $this->hasMany(TestAccess::class);
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isPrivate(): bool
    {
        return $this->type === 'private';
    }

    public function isGroup(): bool
    {
        return $this->type === 'group';
    }

    public function isChannel(): bool
    {
        return $this->type === 'channel';
    }

    public function getMemberRole(string $userId): ?string
    {
        return $this->chatMembers()
            ->where('user_id', $userId)
            ->value('role');
    }
}
