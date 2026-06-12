<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attempt\CheatLogRequest;
use App\Http\Requests\Attempt\StartAttemptRequest;
use App\Http\Requests\Attempt\SubmitAnswerRequest;
use App\Http\Resources\TestAttemptResource;
use App\Http\Resources\TestQuestionResource;
use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestAttemptController extends Controller
{
    public function index(Request $request)
    {
        $attempts = $request->user()->testAttempts()
            ->with('test')
            ->orderByDesc('started_at')
            ->paginate(20);

        return $this->success([
            'items' => TestAttemptResource::collection($attempts),
            'meta'  => ['total' => $attempts->total()],
        ]);
    }

    public function start(StartAttemptRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $test = Test::with('questions')->findOrFail($data['test_id']);

        if (! $test->canUserAttempt($user->id)) {
            return $this->error('Urinishlar soni tugagan', 422);
        }

        // davom etayotgan urinish bormi?
        $existing = $test->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            $questions = $test->questions()->with('options')->get();
            return $this->success([
                'attempt'   => new TestAttemptResource($existing),
                'questions' => $questions->map(fn($q) => (new TestQuestionResource($q))->additional(['hide_answers' => true])),
            ], 'Davom etayotgan urinish');
        }

        $attempt = TestAttempt::create([
            'test_id'         => $test->id,
            'user_id'         => $user->id,
            'access_id'       => $data['access_id'] ?? null,
            'status'          => 'in_progress',
            'total_questions' => $test->questions->count(),
            'total_points'    => $test->questions->sum('points'),
            'answered_count'  => 0,
            'correct_count'   => 0,
            'earned_points'   => 0,
            'started_at'      => now(),
        ]);

        // savollarni javoblarsiz qaytaramiz
        $questions = $test->questions()->with('options')->get();

        return $this->created([
            'attempt'   => new TestAttemptResource($attempt),
            'questions' => $questions->map(fn($q) => (new TestQuestionResource($q))->additional(['hide_answers' => true])),
        ], 'Test boshlandi');
    }

    public function show(Request $request, TestAttempt $attempt)
    {
        $this->ensureOwner($request, $attempt);

        return $this->success(new TestAttemptResource($attempt->load(['test', 'answers.question'])));
    }

    public function answer(SubmitAnswerRequest $request, TestAttempt $attempt)
    {
        $this->ensureOwner($request, $attempt);
        abort_if(! $attempt->isInProgress(), 422, 'Urinish faol emas');

        $data     = $request->validated();
        $question = $attempt->test->questions()->with('correctOptions')->findOrFail($data['question_id']);

        $answer = $attempt->answers()->updateOrCreate(
            ['question_id' => $question->id],
            [
                'selected_option_id'  => $data['selected_option_id'] ?? null,
                'selected_option_ids' => $data['selected_option_ids'] ?? null,
                'open_answer'         => $data['open_answer'] ?? null,
                'time_spent_seconds'  => $data['time_spent_seconds'] ?? 0,
                'answered_at'         => now(),
            ]
        );

        // avtomatik tekshirish (open_text dan tashqari)
        $isCorrect = $answer->checkCorrectness();
        $answer->update([
            'is_correct'    => $isCorrect,
            'points_earned' => $isCorrect ? $question->points : 0,
        ]);

        return $this->success(null, 'Javob saqlandi');
    }

    public function submit(Request $request, TestAttempt $attempt)
    {
        $this->ensureOwner($request, $attempt);
        abort_if(! $attempt->isInProgress(), 422, 'Urinish allaqachon yakunlangan');

        DB::transaction(function () use ($attempt) {
            $answers      = $attempt->answers()->with('question')->get();
            $correctCount = $answers->where('is_correct', true)->count();
            $earnedPoints = $answers->sum('points_earned');
            $totalPoints  = max($attempt->total_points, 1);
            $score        = round(($earnedPoints / $totalPoints) * 100, 2);

            // fan kesimida statistika (DTM uchun)
            $subjectScores = [];
            foreach ($answers->groupBy(fn ($a) => $a->question->subject ?? 'umumiy') as $subject => $group) {
                $subjectScores[$subject] = [
                    'correct' => $group->where('is_correct', true)->count(),
                    'total'   => $group->count(),
                    'points'  => $group->sum('points_earned'),
                ];
            }

            $attempt->update([
                'status'             => 'submitted',
                'answered_count'     => $answers->count(),
                'correct_count'      => $correctCount,
                'earned_points'      => $earnedPoints,
                'score'              => $score,
                'subject_scores'     => $subjectScores,
                'submitted_at'       => now(),
                'time_spent_seconds' => $attempt->started_at?->diffInSeconds(now()) ?? 0,
            ]);

            // test statistikasini yangilash
            $test = $attempt->test;
            $test->increment('attempt_count');
            $avg = $test->attempts()->submitted()->avg('score');
            $test->update(['avg_score' => round($avg ?? 0, 2)]);
        });

        return $this->success(
            new TestAttemptResource($attempt->fresh()->load(['test', 'answers.question.options'])),
            'Test yakunlandi'
        );
    }

    public function logCheat(CheatLogRequest $request, TestAttempt $attempt)
    {
        $this->ensureOwner($request, $attempt);
        $data = $request->validated();

        if ($data['type'] === 'tab_switch') {
            $attempt->increment('tab_switch_count');
            $limit = $attempt->test->tab_switch_limit ?? 0;
            if ($limit > 0 && $attempt->tab_switch_count >= $limit) {
                $attempt->flagAndInvalidate('tab_switch_limit_exceeded');
                return $this->error('Limit oshib ketdi, urinish bekor qilindi', 422);
            }
        }

        $attempt->logCheatEvent($data['type'], $data['extra'] ?? []);

        return $this->success(null, 'Hodisa qayd etildi');
    }

    protected function ensureOwner(Request $request, TestAttempt $attempt): void
    {
        abort_if($attempt->user_id !== $request->user()->id, 403);
    }
}
