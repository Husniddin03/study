<?php

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\User;

/** To'liq test tayyorlaydi: 2 savol, har birida to'g'ri variant */
function makeReadyTest(User $creator): Test
{
    $test = Test::factory()->published()->create(['creator_id' => $creator->id]);

    $q1 = TestQuestion::factory()->create(['test_id' => $test->id, 'points' => 1, 'subject' => 'matematika']);
    $q1->options()->createMany([
        ['content' => 'A', 'is_correct' => false, 'order_index' => 0],
        ['content' => 'B', 'is_correct' => true,  'order_index' => 1],
    ]);

    $q2 = TestQuestion::factory()->create(['test_id' => $test->id, 'points' => 1, 'subject' => 'fizika']);
    $q2->options()->createMany([
        ['content' => 'C', 'is_correct' => true,  'order_index' => 0],
        ['content' => 'D', 'is_correct' => false, 'order_index' => 1],
    ]);

    return $test;
}

it('testni boshlaydi va savollarni javoblarsiz qaytaradi', function () {
    $user = actingUser();
    $test = makeReadyTest($user);

    $response = $this->postJson('/api/attempts/start', ['test_id' => $test->id])
        ->assertCreated()
        ->assertJsonPath('data.attempt.status', 'in_progress');

    // to'g'ri javob (is_correct) yashirilgan bo'lishi kerak
    $firstOption = $response->json('data.questions.0.options.0');
    expect($firstOption)->not->toHaveKey('is_correct');
});

it('davom etayotgan urinish bo\'lsa yangisini ochmaydi', function () {
    $user = actingUser();
    $test = makeReadyTest($user);

    $first  = $this->postJson('/api/attempts/start', ['test_id' => $test->id])->json('data.attempt.id');
    $second = $this->postJson('/api/attempts/start', ['test_id' => $test->id])->json('data.attempt.id');

    expect($first)->toBe($second);
});

it('to\'g\'ri javoblar avtomatik baholanadi va 100% beradi', function () {
    $user = actingUser();
    $test = makeReadyTest($user);
    $questions = $test->questions()->with('options')->get();

    $attemptId = $this->postJson('/api/attempts/start', ['test_id' => $test->id])->json('data.attempt.id');

    foreach ($questions as $q) {
        $correctId = $q->options->firstWhere('is_correct', true)->id;
        $this->postJson("/api/attempts/{$attemptId}/answer", [
            'question_id'        => $q->id,
            'selected_option_id' => $correctId,
        ])->assertOk();
    }

    $this->postJson("/api/attempts/{$attemptId}/submit")
        ->assertOk()
        ->assertJsonPath('data.status', 'submitted')
        ->assertJsonPath('data.score', '100.00')
        ->assertJsonPath('data.correct_count', 2);
});

it('yarim to\'g\'ri javob 50% beradi va fan kesimi hisoblanadi', function () {
    $user = actingUser();
    $test = makeReadyTest($user);
    $questions = $test->questions()->with('options')->get();

    $attemptId = $this->postJson('/api/attempts/start', ['test_id' => $test->id])->json('data.attempt.id');

    // 1-savol to'g'ri, 2-savol noto'g'ri
    $q1 = $questions[0]; $q2 = $questions[1];
    $this->postJson("/api/attempts/{$attemptId}/answer", [
        'question_id' => $q1->id,
        'selected_option_id' => $q1->options->firstWhere('is_correct', true)->id,
    ]);
    $this->postJson("/api/attempts/{$attemptId}/answer", [
        'question_id' => $q2->id,
        'selected_option_id' => $q2->options->firstWhere('is_correct', false)->id,
    ]);

    $response = $this->postJson("/api/attempts/{$attemptId}/submit")->assertOk();
    expect($response->json('data.score'))->toBe('50.00');
    expect($response->json('data.subject_scores'))->toHaveKeys(['matematika', 'fizika']);
});

it('yakunlangan urinishga javob qabul qilmaydi', function () {
    $user = actingUser();
    $test = makeReadyTest($user);
    $attemptId = $this->postJson('/api/attempts/start', ['test_id' => $test->id])->json('data.attempt.id');
    $this->postJson("/api/attempts/{$attemptId}/submit");

    $q = $test->questions()->first();
    $this->postJson("/api/attempts/{$attemptId}/answer", [
        'question_id' => $q->id, 'selected_option_id' => $q->options()->first()->id,
    ])->assertStatus(422);
});

it('boshqaning urinishini ko\'ra olmaydi', function () {
    actingUser();
    $other = User::factory()->create();
    $test = makeReadyTest($other);
    $attemptId = \App\Models\TestAttempt::factory()->create([
        'test_id' => $test->id, 'user_id' => $other->id,
    ])->id;

    $this->getJson("/api/attempts/{$attemptId}")->assertForbidden();
});

it('tab-switch limiti oshsa urinish bekor qilinadi', function () {
    $user = actingUser();
    $test = Test::factory()->published()->antiCheat()->create(['creator_id' => $user->id, 'tab_switch_limit' => 2]);
    TestQuestion::factory()->create(['test_id' => $test->id]);

    $attemptId = $this->postJson('/api/attempts/start', ['test_id' => $test->id])->json('data.attempt.id');

    $this->postJson("/api/attempts/{$attemptId}/cheat-log", ['type' => 'tab_switch'])->assertOk();
    $this->postJson("/api/attempts/{$attemptId}/cheat-log", ['type' => 'tab_switch'])->assertStatus(422);

    expect(\App\Models\TestAttempt::find($attemptId)->status)->toBe('invalidated');
});

it('urinishlar soni tugaganda yangi urinish ochmaydi', function () {
    $user = actingUser();
    $test = Test::factory()->published()->create(['creator_id' => $user->id, 'max_attempts' => 1]);
    TestQuestion::factory()->create(['test_id' => $test->id]);

    \App\Models\TestAttempt::factory()->submitted()->create(['test_id' => $test->id, 'user_id' => $user->id]);

    $this->postJson('/api/attempts/start', ['test_id' => $test->id])->assertStatus(422);
});
