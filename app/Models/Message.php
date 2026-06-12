<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'type',
        'content',
        'file_url',
        'file_name',
        'file_size',
        'test_id',
        'test_access_id',
        'reply_to_id',
        'forwarded_from_id',
        'is_pinned',
        'is_deleted',
        'reactions',
        'read_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned'   => 'boolean',
            'is_deleted'  => 'boolean',
            'reactions'   => 'array',
            'read_by'     => 'array',
            'created_at'  => 'datetime',
            'updated_at'  => 'datetime',
            'deleted_at'  => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function testAccess()
    {
        return $this->belongsTo(TestAccess::class);
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to_id');
    }

    public function forwardedFrom()
    {
        return $this->belongsTo(Message::class, 'forwarded_from_id');
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeVisible($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isTestShare(): bool
    {
        return $this->type === 'test_share';
    }

    public function isReadBy(string $userId): bool
    {
        return in_array($userId, $this->read_by ?? []);
    }

    public function markReadBy(string $userId): void
    {
        $readBy = $this->read_by ?? [];
        if (!in_array($userId, $readBy)) {
            $readBy[] = $userId;
            $this->update(['read_by' => $readBy]);
        }
    }
}
