<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'question_id',
        'content',
        'image_url',
        'formula',
        'is_correct',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'is_correct'  => 'boolean',
            'order_index' => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function question()
    {
        return $this->belongsTo(TestQuestion::class, 'question_id');
    }

    // ── Helpers ────────────────────────────────────────────

    public function getLabel(): string
    {
        // 0→A, 1→B, 2→C, 3→D ...
        return chr(65 + $this->order_index);
    }

    public function hasFormula(): bool
    {
        return !empty($this->formula);
    }
}
