<?php

use App\Models\ChatMember;

it('canModerate creator va admin uchun true', function () {
    expect(ChatMember::factory()->make(['role' => 'creator'])->canModerate())->toBeTrue()
        ->and(ChatMember::factory()->make(['role' => 'admin'])->canModerate())->toBeTrue()
        ->and(ChatMember::factory()->make(['role' => 'member'])->canModerate())->toBeFalse();
});

it('isMutedNow muddat o\'tgan bo\'lsa false', function () {
    $member = ChatMember::factory()->make(['is_muted' => true, 'muted_until' => now()->subHour()]);
    expect($member->isMutedNow())->toBeFalse();
});

it('isMutedNow muddat ichida true', function () {
    $member = ChatMember::factory()->make(['is_muted' => true, 'muted_until' => now()->addHour()]);
    expect($member->isMutedNow())->toBeTrue();
});
