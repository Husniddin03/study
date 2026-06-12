<?php

use App\Models\Notification;

it('bildirishnomalar ro\'yxati va o\'qilmaganlar soni', function () {
    $user = actingUser();
    Notification::factory()->count(2)->create(['user_id' => $user->id]);
    Notification::factory()->read()->create(['user_id' => $user->id]);

    $this->getJson('/api/notifications')->assertOk()
        ->assertJsonPath('data.unread_count', 2)
        ->assertJsonCount(3, 'data.items');
});

it('bittasini o\'qildi deb belgilaydi', function () {
    $user = actingUser();
    $n = Notification::factory()->create(['user_id' => $user->id]);

    $this->postJson("/api/notifications/{$n->id}/read")->assertOk();
    expect($n->fresh()->is_read)->toBeTrue();
});

it('hammasini o\'qildi deb belgilaydi', function () {
    $user = actingUser();
    Notification::factory()->count(3)->create(['user_id' => $user->id]);

    $this->postJson('/api/notifications/read-all')->assertOk();
    expect($user->unreadNotificationsCount())->toBe(0);
});

it('boshqaning bildirishnomasini o\'qiy olmaydi', function () {
    actingUser();
    $n = Notification::factory()->create(['user_id' => makeUser()->id]);

    $this->postJson("/api/notifications/{$n->id}/read")->assertForbidden();
});
