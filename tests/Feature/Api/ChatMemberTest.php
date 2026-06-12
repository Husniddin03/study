<?php

use App\Models\Chat;
use App\Models\User;

/** Helper: foydalanuvchi creator bo'lgan chat tayyorlaydi */
function chatOwnedBy(User $user): Chat
{
    $chat = Chat::factory()->create(['created_by' => $user->id]);
    $chat->chatMembers()->create([
        'user_id' => $user->id, 'role' => 'creator',
        'can_manage_members' => true, 'joined_at' => now(),
    ]);
    return $chat;
}

it('admin yangi a\'zo qo\'shadi', function () {
    $user = actingUser();
    $chat = chatOwnedBy($user);
    $newMember = User::factory()->create();

    $this->postJson("/api/chats/{$chat->id}/members", ['user_id' => $newMember->id])
        ->assertCreated();

    $this->assertDatabaseHas('chat_members', ['chat_id' => $chat->id, 'user_id' => $newMember->id]);
});

it('bir xil a\'zoni ikki marta qo\'sha olmaydi', function () {
    $user = actingUser();
    $chat = chatOwnedBy($user);
    $m = User::factory()->create();
    $this->postJson("/api/chats/{$chat->id}/members", ['user_id' => $m->id])->assertCreated();
    $this->postJson("/api/chats/{$chat->id}/members", ['user_id' => $m->id])->assertStatus(422);
});

it('oddiy a\'zo boshqalarni qo\'sha olmaydi', function () {
    $owner = User::factory()->create();
    $chat = chatOwnedBy($owner);
    $member = actingUser();
    $chat->chatMembers()->create(['user_id' => $member->id, 'role' => 'member', 'joined_at' => now()]);

    $this->postJson("/api/chats/{$chat->id}/members", ['user_id' => User::factory()->create()->id])
        ->assertForbidden();
});

it('creator ni o\'chirib bo\'lmaydi', function () {
    $user = actingUser();
    $chat = chatOwnedBy($user);
    $creatorMember = $chat->chatMembers()->where('role', 'creator')->first();

    $this->deleteJson("/api/chats/{$chat->id}/members/{$creatorMember->id}")
        ->assertStatus(422);
});
