<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes;

    protected $fillable = [
        'username',
        'phone',
        'email',
        'password_hash',
        'full_name',
        'avatar_url',
        'bio',
        'is_verified',
        'is_active',
        'last_seen_at',
        'settings',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'is_verified'   => 'boolean',
            'is_active'     => 'boolean',
            'last_seen_at'  => 'datetime',
            'settings'      => 'array',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_members', 'user_id', 'chat_id')
            ->withPivot(['role', 'can_send_messages', 'can_send_tests', 'can_create_exam',
                         'can_manage_members', 'is_muted', 'muted_until', 'joined_at']);
    }

    public function createdChats()
    {
        return $this->hasMany(Chat::class, 'created_by');
    }

    public function chatMemberships()
    {
        return $this->hasMany(ChatMember::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function createdTests()
    {
        return $this->hasMany(Test::class, 'creator_id');
    }

    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function contacts()
    {
        return $this->hasMany(UserContact::class);
    }

    public function hostedExamSessions()
    {
        return $this->hasMany(ExamSession::class, 'host_user_id');
    }

    public function examParticipations()
    {
        return $this->hasMany(ExamParticipant::class);
    }

    // ── Helpers ────────────────────────────────────────────

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes() < 5;
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }
}
