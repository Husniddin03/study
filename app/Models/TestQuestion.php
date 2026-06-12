<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'test_id',
        'subject',
        'block_name',
        'content_type',
        'content',
        'image_url',
        'formula',
        'extra_content',
        'answer_type',
        'order_index',
        'points',
        'explanation',
    ];

    protected function casts(): array
    {
        return [
            'extra_content' => 'array',
            'order_index'   => 'integer',
            'points'        => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('order_index');
    }

    public function correctOptions()
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->where('is_correct', true);
    }

    public function answers()
    {
        return $this->hasMany(AttemptAnswer::class, 'question_id');
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeOfSubject($query, string $subject)
    {
        return $query->where('subject', $subject);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isMultipleChoice(): bool
    {
        return $this->answer_type === 'multiple';
    }

    public function isOpenText(): bool
    {
        return $this->answer_type === 'open_text';
    }

    public function hasFormula(): bool
    {
        return !empty($this->formula);
    }

    public function hasImage(): bool
    {
        return !empty($this->image_url);
    }

    public function getCorrectOptionIds(): array
    {
        return $this->correctOptions()->pluck('id')->toArray();
    }
}
