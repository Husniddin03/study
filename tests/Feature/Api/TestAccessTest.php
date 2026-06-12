<?php

use App\Models\Test;
use App\Models\TestAccess;
use App\Models\User;

it('test egasi ruxsat (access) yaratadi', function () {
    $user = actingUser();
    $test = Test::factory()->create(['creator_id' => $user->id]);

    $this->postJson('/api/accesses', [
        'test_id'     => $test->id,
        'access_type' => 'public',
    ])->assertCreated()
      ->assertJsonStructure(['data' => ['invite_code']]);
});

it('test egasi bo\'lmagan ruxsat bera olmaydi', function () {
    actingUser();
    $test = Test::factory()->create(['creator_id' => User::factory()->create()->id]);

    $this->postJson('/api/accesses', ['test_id' => $test->id, 'access_type' => 'public'])
        ->assertForbidden();
});

it('invite kod orqali accessni topadi', function () {
    $user = actingUser();
    $access = TestAccess::factory()->create(['granted_by' => $user->id, 'invite_code' => 'ABC12345']);

    $this->getJson('/api/accesses/code/abc12345')->assertOk()
        ->assertJsonPath('data.id', $access->id);
});

it('accessni deaktivlashtiradi', function () {
    $user = actingUser();
    $access = TestAccess::factory()->create(['granted_by' => $user->id]);

    $this->postJson("/api/accesses/{$access->id}/deactivate")->assertOk()
        ->assertJsonPath('data.is_active', false);
});

it('chat access uchun chat_id majburiy', function () {
    $user = actingUser();
    $test = Test::factory()->create(['creator_id' => $user->id]);

    $this->postJson('/api/accesses', ['test_id' => $test->id, 'access_type' => 'chat'])
        ->assertStatus(422)->assertJsonValidationErrors('chat_id');
});
