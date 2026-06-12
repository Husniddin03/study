<?php

use App\Models\AttemptAnswer;
use App\Models\TestQuestion;

it('single javob to\'g\'ri tekshiriladi', function () {
    $q = TestQuestion::factory()->create(['answer_type' => 'single']);
    $correct = $q->options()->create(['content' => 'A', 'is_correct' => true, 'order_index' => 0]);
    $q->options()->create(['content' => 'B', 'is_correct' => false, 'order_index' => 1]);

    $answer = AttemptAnswer::factory()->create([
        'question_id'        => $q->id,
        'selected_option_id' => $correct->id,
    ]);

    expect($answer->checkCorrectness())->toBeTrue();
});

it('multiple javob faqat barcha to\'g\'ri variantlarda true', function () {
    $q = TestQuestion::factory()->create(['answer_type' => 'multiple']);
    $a = $q->options()->create(['content' => 'A', 'is_correct' => true, 'order_index' => 0]);
    $b = $q->options()->create(['content' => 'B', 'is_correct' => true, 'order_index' => 1]);
    $q->options()->create(['content' => 'C', 'is_correct' => false, 'order_index' => 2]);

    $full = AttemptAnswer::factory()->create([
        'question_id'         => $q->id,
        'selected_option_ids' => [$a->id, $b->id],
    ]);
    expect($full->checkCorrectness())->toBeTrue();

    $partial = AttemptAnswer::factory()->make([
        'question_id'         => $q->id,
        'selected_option_ids' => [$a->id],
    ]);
    expect($partial->checkCorrectness())->toBeFalse();
});

it('open_text doim false qaytaradi (qo\'lda tekshiriladi)', function () {
    $q = TestQuestion::factory()->openText()->create();
    $answer = AttemptAnswer::factory()->make(['question_id' => $q->id, 'open_answer' => 'javob']);
    expect($answer->checkCorrectness())->toBeFalse();
});
