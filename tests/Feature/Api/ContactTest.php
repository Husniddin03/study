<?php

use App\Models\User;

it('kontakt qo\'shadi', function () {
    actingUser();
    $other = User::factory()->create();

    $this->postJson('/api/contacts', ['contact_id' => $other->id, 'nickname' => 'Do\'st'])
        ->assertCreated()
        ->assertJsonPath('data.contact_id', $other->id);
});

it('o\'zini kontakt qila olmaydi', function () {
    $user = actingUser();
    $this->postJson('/api/contacts', ['contact_id' => $user->id])
        ->assertStatus(422);
});

it('kontaktlar ro\'yxatini qaytaradi', function () {
    $user = actingUser();
    $user->contacts()->create(['contact_id' => User::factory()->create()->id, 'created_at' => now()]);

    $this->getJson('/api/contacts')->assertOk()
        ->assertJsonCount(1, 'data');
});

it('kontaktni bloklaydi', function () {
    $user = actingUser();
    $contact = $user->contacts()->create(['contact_id' => User::factory()->create()->id, 'created_at' => now()]);

    $this->postJson("/api/contacts/{$contact->id}/block")->assertOk()
        ->assertJsonPath('data.is_blocked', true);
});

it('boshqaning kontaktini bloklay olmaydi', function () {
    actingUser();
    $stranger = User::factory()->create();
    $contact = $stranger->contacts()->create(['contact_id' => User::factory()->create()->id, 'created_at' => now()]);

    $this->postJson("/api/contacts/{$contact->id}/block")->assertForbidden();
});
