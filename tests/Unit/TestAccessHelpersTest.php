<?php

use App\Models\TestAccess;

it('isExamActive faol oraliqda true', function () {
    $access = TestAccess::factory()->make([
        'is_exam'        => true,
        'is_active'      => true,
        'exam_starts_at' => now()->subMinute(),
        'exam_ends_at'   => now()->addHour(),
    ]);
    expect($access->isExamActive())->toBeTrue();
});

it('isExamActive tugagan imtihonda false', function () {
    $access = TestAccess::factory()->make([
        'is_exam'        => true,
        'is_active'      => true,
        'exam_starts_at' => now()->subHours(2),
        'exam_ends_at'   => now()->subHour(),
    ]);
    expect($access->isExamActive())->toBeFalse();
});

it('isExpired muddati o\'tganda true', function () {
    $access = TestAccess::factory()->make(['expires_at' => now()->subDay()]);
    expect($access->isExpired())->toBeTrue();
});
