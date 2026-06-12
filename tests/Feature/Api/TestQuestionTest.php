<?php

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\User;

it('savol va variantlarni birga qo\'shadi', function () {
    $user = actingUser();
    $test = Test::factory()->create(['creator_id' => $user->id]);

    $this->postJson("/api/tests/{$test->id}/questions", [
        'content_type' => 'text',
        'content'      => '2+2=?',
        'answer_type'  => 'single',
        'options'      => [
            ['content' => '3', 'is_correct' => false],
            ['content' => '4', 'is_correct' => true],
        ],
    ])->assertCreated()->assertJsonCount(2, 'data.options');
});

it('faqat egasi savol qo\'shadi', function () {
    actingUser();
    $test = Test::factory()->create(['creator_id' => User::factory()->create()->id]);

    $this->postJson("/api/tests/{$test->id}/questions", [
        'content_type' => 'text', 'content' => 'x', 'answer_type' => 'single',
    ])->assertForbidden();
});

it('savollar tartibini o\'zgartiradi', function () {
    $user = actingUser();
    $test = Test::factory()->create(['creator_id' => $user->id]);
    $q1 = TestQuestion::factory()->create(['test_id' => $test->id, 'order_index' => 0]);
    $q2 = TestQuestion::factory()->create(['test_id' => $test->id, 'order_index' => 1]);

    $this->postJson("/api/tests/{$test->id}/questions/reorder", ['order' => [$q2->id, $q1->id]])
        ->assertOk();

    expect($q2->fresh()->order_index)->toBe(0);
    expect($q1->fresh()->order_index)->toBe(1);
});

it('savolni o\'chiradi (variantlari bilan)', function () {
    $user = actingUser();
    $test = Test::factory()->create(['creator_id' => $user->id]);
    $q = TestQuestion::factory()->create(['test_id' => $test->id]);
    $q->options()->create(['content' => 'A', 'order_index' => 0]);

    $this->deleteJson("/api/tests/{$test->id}/questions/{$q->id}")->assertOk();
    $this->assertDatabaseMissing('test_questions', ['id' => $q->id]);
    $this->assertDatabaseMissing('question_options', ['question_id' => $q->id]);
});
