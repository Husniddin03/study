<?php

use App\Models\TestAttempt;

it('getScorePercent to\'g\'ri foiz hisoblaydi', function () {
    $attempt = TestAttempt::factory()->make(['earned_points' => 8, 'total_points' => 10]);
    expect($attempt->getScorePercent())->toBe(80.0);
});

it('total_points 0 bo\'lsa 0 qaytaradi (nolga bo\'lish yo\'q)', function () {
    $attempt = TestAttempt::factory()->make(['earned_points' => 0, 'total_points' => 0]);
    expect($attempt->getScorePercent())->toEqual(0);
});

it('getFormattedTime mm:ss formatida qaytaradi', function () {
    $attempt = TestAttempt::factory()->make(['time_spent_seconds' => 125]);
    expect($attempt->getFormattedTime())->toBe('02:05');
});

it('status helperlari ishlaydi', function () {
    $attempt = TestAttempt::factory()->make(['status' => 'submitted']);
    expect($attempt->isSubmitted())->toBeTrue()
        ->and($attempt->isInProgress())->toBeFalse();
});
