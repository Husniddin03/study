<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttemptAnswer extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'selected_option_ids',
        'open_answer',
        'is_correct',
        'points_earned',
        'time_spent_seconds',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'selected_option_ids' => 'array',
            'is_correct'          => 'boolean',
            'points_earned'       => 'integer',
            'time_spent_seconds'  => 'integer',
            'answered_at'         => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function attempt()
    {
        return $this->belongsTo(TestAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(TestQuestion::class, 'question_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    // ── Helpers ────────────────────────────────────────────

    public function checkCorrectness(): bool
    {
        $question = $this->question;

        if ($question->answer_type === 'multiple') {
            $correctIds = $question->getCorrectOptionIds();
            $selected   = $this->selected_option_ids ?? [];
            sort($correctIds);
            sort($selected);
            return $correctIds === $selected;
        }

        if ($question->answer_type === 'single' || $question->answer_type === 'true_false') {
            $correct = $question->correctOptions()->first();
            return $correct && $this->selected_option_id === $correct->id;
        }

        return false; // open_text — qo'lda yoki AI tekshiradi
    }
}
