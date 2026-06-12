<?php

use App\Models\User;

it('isOnline 5 daqiqa ichida true qaytaradi', function () {
    $user = User::factory()->make(['last_seen_at' => now()->subMinutes(2)]);
    expect($user->isOnline())->toBeTrue();
});

it('isOnline 5 daqiqadan keyin false qaytaradi', function () {
    $user = User::factory()->make(['last_seen_at' => now()->subMinutes(10)]);
    expect($user->isOnline())->toBeFalse();
});

it('getAuthPassword password_hash ni qaytaradi', function () {
    $user = User::factory()->make(['password_hash' => 'HASHED']);
    expect($user->getAuthPassword())->toBe('HASHED');
});
