<?php

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\User;

it('test yaratadi', function () {
    $user = actingUser();
    $this->postJson('/api/tests', [
        'title'      => 'DTM Matematika',
        'type'       => 'dtm',
        'visibility' => 'public',
    ])->assertCreated()->assertJsonPath('data.creator_id', $user->id);
});

it('o\'z testlarini ko\'radi', function () {
    $user = actingUser();
    Test::factory()->count(2)->create(['creator_id' => $user->id]);

    $this->getJson('/api/tests?mine=1')->assertOk()
        ->assertJsonCount(2, 'data.items');
});

it('faqat egasi testni yangilaydi', function () {
    actingUser();
    $test = Test::factory()->create(['creator_id' => User::factory()->create()->id]);

    $this->putJson("/api/tests/{$test->id}", ['title' => 'hack'])->assertForbidden();
});

it('savolsiz testni e\'lon qila olmaydi', function () {
    $user = actingUser();
    $test = Test::factory()->create(['creator_id' => $user->id]);

    $this->postJson("/api/tests/{$test->id}/publish")->assertStatus(422);
});

it('savolli testni e\'lon qiladi', function () {
    $user = actingUser();
    $test = Test::factory()->create(['creator_id' => $user->id]);
    TestQuestion::factory()->create(['test_id' => $test->id]);

    $this->postJson("/api/tests/{$test->id}/publish")
        ->assertOk()->assertJsonPath('data.is_published', true);
});

it('testni savollari bilan nusxalaydi', function () {
    $user = actingUser();
    $test = Test::factory()->create(['creator_id' => $user->id]);
    $q = TestQuestion::factory()->create(['test_id' => $test->id]);
    $q->options()->create(['content' => 'A', 'is_correct' => true, 'order_index' => 0]);

    $response = $this->postJson("/api/tests/{$test->id}/duplicate")->assertCreated();
    $copyId = $response->json('data.id');

    expect($copyId)->not->toBe($test->id);
    $this->assertDatabaseHas('test_questions', ['test_id' => $copyId]);
});

it('boshqaning maxfiy testini ko\'ra olmaydi', function () {
    actingUser();
    $test = Test::factory()->private()->create(['creator_id' => User::factory()->create()->id]);

    $this->getJson("/api/tests/{$test->id}")->assertForbidden();
});
