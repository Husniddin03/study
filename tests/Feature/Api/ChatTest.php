<?php

use App\Models\Chat;
use App\Models\User;

it('guruh chat yaratadi va yaratuvchini creator qiladi', function () {
    $user = actingUser();

    $response = $this->postJson('/api/chats', ['type' => 'group', 'name' => 'Matematika 11-sinf'])
        ->assertCreated()
        ->assertJsonPath('data.type', 'group');

    $chatId = $response->json('data.id');
    $this->assertDatabaseHas('chat_members', [
        'chat_id' => $chatId,
        'user_id' => $user->id,
        'role'    => 'creator',
    ]);
});

it('private chat ikki a\'zo bilan yaratiladi', function () {
    actingUser();
    $other = User::factory()->create();

    $response = $this->postJson('/api/chats', ['type' => 'private', 'member_id' => $other->id])
        ->assertCreated();

    expect($response->json('data.member_count'))->toBe(2);
});

it('o\'z chatlari ro\'yxatini qaytaradi', function () {
    $user = actingUser();
    $chat = Chat::factory()->create(['created_by' => $user->id]);
    $chat->chatMembers()->create(['user_id' => $user->id, 'role' => 'creator', 'joined_at' => now()]);

    $this->getJson('/api/chats')->assertOk()
        ->assertJsonCount(1, 'data.items');
});

it('a\'zo bo\'lmagan maxfiy chatni ko\'ra olmaydi', function () {
    actingUser();
    $chat = Chat::factory()->create(['is_public' => false]);

    $this->getJson("/api/chats/{$chat->id}")->assertForbidden();
});

it('faqat creator chatni o\'chiradi', function () {
    $user = actingUser();
    $chat = Chat::factory()->create(['created_by' => User::factory()->create()->id]);

    $this->deleteJson("/api/chats/{$chat->id}")->assertForbidden();
});

it('admin chat sozlamalarini yangilaydi', function () {
    $user = actingUser();
    $chat = Chat::factory()->create(['created_by' => $user->id]);
    $chat->chatMembers()->create([
        'user_id' => $user->id, 'role' => 'creator',
        'can_manage_members' => true, 'joined_at' => now(),
    ]);

    $this->putJson("/api/chats/{$chat->id}", ['name' => 'Yangi nom'])
        ->assertOk()->assertJsonPath('data.name', 'Yangi nom');
});

it('creator chatni tark eta olmaydi', function () {
    $user = actingUser();
    $chat = Chat::factory()->create(['created_by' => $user->id]);
    $chat->chatMembers()->create(['user_id' => $user->id, 'role' => 'creator', 'joined_at' => now()]);

    $this->postJson("/api/chats/{$chat->id}/leave")->assertStatus(422);
});
