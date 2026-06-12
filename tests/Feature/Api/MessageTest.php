<?php

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;

function memberChat(User $user, array $memberAttrs = []): Chat
{
    $chat = Chat::factory()->create(['created_by' => $user->id]);
    $chat->chatMembers()->create(array_merge([
        'user_id' => $user->id, 'role' => 'creator',
        'can_send_messages' => true, 'can_manage_members' => true, 'joined_at' => now(),
    ], $memberAttrs));
    return $chat;
}

it('xabar yuboradi', function () {
    $user = actingUser();
    $chat = memberChat($user);

    $this->postJson("/api/chats/{$chat->id}/messages", ['type' => 'text', 'content' => 'Salom'])
        ->assertCreated()
        ->assertJsonPath('data.content', 'Salom');
});

it('a\'zo bo\'lmagan chatga xabar yubora olmaydi', function () {
    actingUser();
    $chat = Chat::factory()->create();

    $this->postJson("/api/chats/{$chat->id}/messages", ['type' => 'text', 'content' => 'x'])
        ->assertForbidden();
});

it('ovozsizlantirilgan a\'zo xabar yubora olmaydi', function () {
    $user = actingUser();
    $chat = memberChat($user, ['is_muted' => true, 'muted_until' => now()->addHour()]);

    $this->postJson("/api/chats/{$chat->id}/messages", ['type' => 'text', 'content' => 'x'])
        ->assertForbidden();
});

it('xabarlar ro\'yxatini qaytaradi (o\'chirilganlarsiz)', function () {
    $user = actingUser();
    $chat = memberChat($user);
    Message::factory()->count(3)->create(['chat_id' => $chat->id, 'sender_id' => $user->id]);
    Message::factory()->deleted()->create(['chat_id' => $chat->id, 'sender_id' => $user->id]);

    $this->getJson("/api/chats/{$chat->id}/messages")
        ->assertOk()->assertJsonCount(3, 'data.items');
});

it('faqat o\'z xabarini tahrirlaydi', function () {
    $user = actingUser();
    $chat = memberChat($user);
    $msg = Message::factory()->create(['chat_id' => $chat->id, 'sender_id' => User::factory()->create()->id]);

    $this->putJson("/api/chats/{$chat->id}/messages/{$msg->id}", ['content' => 'hack'])
        ->assertForbidden();
});

it('xabarga reaksiya qo\'shadi va olib tashlaydi (toggle)', function () {
    $user = actingUser();
    $chat = memberChat($user);
    $msg = Message::factory()->create(['chat_id' => $chat->id, 'sender_id' => $user->id]);

    $this->postJson("/api/chats/{$chat->id}/messages/{$msg->id}/react", ['emoji' => '👍'])
        ->assertOk();
    expect($msg->fresh()->reactions)->toHaveKey('👍');

    // toggle off
    $this->postJson("/api/chats/{$chat->id}/messages/{$msg->id}/react", ['emoji' => '👍']);
    expect($msg->fresh()->reactions ?? [])->not->toHaveKey('👍');
});

it('xabarni o\'qildi deb belgilaydi', function () {
    $user = actingUser();
    $chat = memberChat($user);
    $msg = Message::factory()->create(['chat_id' => $chat->id, 'sender_id' => $user->id]);

    $this->postJson("/api/chats/{$chat->id}/messages/{$msg->id}/read")->assertOk();
    expect($msg->fresh()->isReadBy($user->id))->toBeTrue();
});
