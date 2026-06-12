<?php

use App\Models\ExamSession;
use App\Models\TestAccess;
use App\Models\User;

it('imtihon sessiyasini yaratadi', function () {
    $user = actingUser();
    $access = TestAccess::factory()->exam()->create(['granted_by' => $user->id]);

    $this->postJson('/api/exam-sessions', ['access_id' => $access->id])
        ->assertCreated()
        ->assertJsonPath('data.status', 'waiting')
        ->assertJsonStructure(['data' => ['session_code']]);
});

it('faqat ruxsat egasi sessiya ochadi', function () {
    actingUser();
    $access = TestAccess::factory()->exam()->create(['granted_by' => User::factory()->create()->id]);

    $this->postJson('/api/exam-sessions', ['access_id' => $access->id])->assertForbidden();
});

it('host sessiyani boshlaydi va yakunlaydi', function () {
    $user = actingUser();
    $session = ExamSession::factory()->create(['host_user_id' => $user->id]);

    $this->postJson("/api/exam-sessions/{$session->id}/start")->assertOk()
        ->assertJsonPath('data.status', 'active');
    $this->postJson("/api/exam-sessions/{$session->id}/finish")->assertOk()
        ->assertJsonPath('data.status', 'finished');
});

it('o\'quvchi sessiya kodi bilan qo\'shiladi', function () {
    $host = User::factory()->create();
    $session = ExamSession::factory()->active()->create(['host_user_id' => $host->id, 'session_code' => 'JOIN99']);

    actingUser();
    $this->postJson('/api/exam-sessions/join', ['session_code' => 'JOIN99'])
        ->assertCreated()
        ->assertJsonPath('data.status', 'connected');

    expect($session->fresh()->connected_count)->toBe(1);
});

it('yakunlangan sessiyaga qo\'shilib bo\'lmaydi', function () {
    $host = User::factory()->create();
    ExamSession::factory()->finished()->create(['host_user_id' => $host->id, 'session_code' => 'DONE11']);

    actingUser();
    $this->postJson('/api/exam-sessions/join', ['session_code' => 'DONE11'])->assertStatus(422);
});

it('to\'lgan sessiyaga qo\'shilib bo\'lmaydi', function () {
    $host = User::factory()->create();
    $session = ExamSession::factory()->active()->create([
        'host_user_id' => $host->id, 'session_code' => 'FULL11',
        'max_allowed' => 1, 'connected_count' => 1,
    ]);

    actingUser();
    $this->postJson('/api/exam-sessions/join', ['session_code' => 'FULL11'])->assertStatus(422);
});

it('faqat host ishtirokchilar ro\'yxatini ko\'radi', function () {
    actingUser();
    $session = ExamSession::factory()->create(['host_user_id' => User::factory()->create()->id]);

    $this->getJson("/api/exam-sessions/{$session->id}/participants")->assertForbidden();
});
