<?php

use App\Models\User;
use Database\Factories\UserFactory;

it('profilni ko\'rsatadi', function () {
    $user = actingUser();
    $this->getJson('/api/profile')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);
});

it('profilni yangilaydi', function () {
    actingUser();
    $this->putJson('/api/profile', ['full_name' => 'Yangi Ism', 'bio' => 'Salom'])
        ->assertOk()
        ->assertJsonPath('data.full_name', 'Yangi Ism');
});

it('boshqa user bilan band username ni rad etadi', function () {
    User::factory()->create(['username' => 'band']);
    actingUser();
    $this->putJson('/api/profile', ['username' => 'band'])
        ->assertStatus(422)->assertJsonValidationErrors('username');
});

it('parolni o\'zgartiradi va tokenlarni bekor qiladi', function () {
    $user = actingUser();
    $this->postJson('/api/profile/change-password', [
        'current_password'      => UserFactory::$defaultPassword,
        'password'              => 'yangiParol1',
        'password_confirmation' => 'yangiParol1',
    ])->assertOk();

    expect($user->fresh()->tokens()->count())->toBe(0);
});

it('joriy parol noto\'g\'ri bo\'lsa parolni o\'zgartirmaydi', function () {
    actingUser();
    $this->postJson('/api/profile/change-password', [
        'current_password'      => 'xato',
        'password'              => 'yangiParol1',
        'password_confirmation' => 'yangiParol1',
    ])->assertStatus(422);
});
