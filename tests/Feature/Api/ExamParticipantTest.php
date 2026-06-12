<?php

use App\Models\ExamParticipant;
use App\Models\ExamSession;
use App\Models\User;

it('host ishtirokchini flag qiladi va host\'ga bildirishnoma boradi', function () {
    $host = actingUser();
    $session = ExamSession::factory()->active()->create(['host_user_id' => $host->id]);
    $participant = ExamParticipant::factory()->create([
        'session_id' => $session->id,
        'user_id'    => User::factory()->create()->id,
    ]);

    $this->postJson("/api/exam-participants/{$participant->id}/flag", [
        'violation_type' => 'looking_away',
    ])->assertOk();

    expect($participant->fresh()->is_flagged)->toBeTrue();
    $this->assertDatabaseHas('notifications', [
        'user_id' => $host->id,
        'type'    => 'exam_alert',
    ]);
});

it('o\'quvchining qurilmasi tab-switch xabarini yuboradi', function () {
    $student = actingUser();
    $session = ExamSession::factory()->active()->create();
    $participant = ExamParticipant::factory()->create([
        'session_id' => $session->id, 'user_id' => $student->id,
    ]);

    $this->postJson("/api/exam-participants/{$participant->id}/tab-switch")->assertOk();
    expect($participant->fresh()->tab_switch_count)->toBe(1);
});

it('boshqa o\'quvchi nomidan xabar yubora olmaydi', function () {
    actingUser();
    $session = ExamSession::factory()->active()->create();
    $participant = ExamParticipant::factory()->create([
        'session_id' => $session->id, 'user_id' => User::factory()->create()->id,
    ]);

    $this->postJson("/api/exam-participants/{$participant->id}/tab-switch")->assertForbidden();
});
