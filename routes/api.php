<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ChatMemberController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ExamParticipantController;
use App\Http\Controllers\Api\ExamSessionController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\QuestionOptionController;
use App\Http\Controllers\Api\TestAccessController;
use App\Http\Controllers\Api\TestAttemptController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\TestQuestionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Sanctum token auth)
|--------------------------------------------------------------------------
| Laravel 11+ da bu fayl bootstrap/app.php ichida ->withRouting(api: ...)
| orqali ulanadi va avtomatik '/api' prefiksini oladi.
*/

// ── Public (auth talab qilmaydigan) ────────────────────────
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

// ── Protected ──────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ─ Auth / sessiya ─
    Route::get('me',          [AuthController::class, 'me']);
    Route::post('logout',     [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAll']);

    // ─ Profil ─
    Route::get('profile',                   [ProfileController::class, 'show']);
    Route::put('profile',                   [ProfileController::class, 'update']);
    Route::post('profile/change-password',  [ProfileController::class, 'changePassword']);
    Route::delete('profile',                [ProfileController::class, 'destroy']);
    Route::get('users/{user}',              [ProfileController::class, 'showUser']);

    // ─ Kontaktlar ─
    Route::get('contacts',                       [ContactController::class, 'index']);
    Route::post('contacts',                      [ContactController::class, 'store']);
    Route::delete('contacts/{contact}',          [ContactController::class, 'destroy']);
    Route::post('contacts/{contact}/block',      [ContactController::class, 'block']);
    Route::post('contacts/{contact}/unblock',    [ContactController::class, 'unblock']);

    // ─ Qurilmalar (push) ─
    Route::get('devices',           [DeviceController::class, 'index']);
    Route::post('devices',          [DeviceController::class, 'store']);
    Route::delete('devices/{device}', [DeviceController::class, 'destroy']);

    // ─ Chatlar ─
    Route::get('chats',                 [ChatController::class, 'index']);
    Route::post('chats',                [ChatController::class, 'store']);
    Route::get('chats/{chat}',          [ChatController::class, 'show']);
    Route::put('chats/{chat}',          [ChatController::class, 'update']);
    Route::delete('chats/{chat}',       [ChatController::class, 'destroy']);
    Route::post('chats/{chat}/leave',   [ChatController::class, 'leave']);

    // ─ Chat a'zolari ─
    Route::get('chats/{chat}/members',                 [ChatMemberController::class, 'index']);
    Route::post('chats/{chat}/members',                [ChatMemberController::class, 'store']);
    Route::put('chats/{chat}/members/{member}',        [ChatMemberController::class, 'update']);
    Route::delete('chats/{chat}/members/{member}',     [ChatMemberController::class, 'destroy']);

    // ─ Xabarlar ─
    Route::get('chats/{chat}/messages',                      [MessageController::class, 'index']);
    Route::post('chats/{chat}/messages',                     [MessageController::class, 'store']);
    Route::get('chats/{chat}/messages/{message}',            [MessageController::class, 'show']);
    Route::put('chats/{chat}/messages/{message}',            [MessageController::class, 'update']);
    Route::delete('chats/{chat}/messages/{message}',         [MessageController::class, 'destroy']);
    Route::post('chats/{chat}/messages/{message}/pin',       [MessageController::class, 'pin']);
    Route::post('chats/{chat}/messages/{message}/read',      [MessageController::class, 'read']);
    Route::post('chats/{chat}/messages/{message}/react',     [MessageController::class, 'react']);

    // ─ Testlar ─
    Route::get('tests',                      [TestController::class, 'index']);
    Route::post('tests',                     [TestController::class, 'store']);
    Route::get('tests/{test}',               [TestController::class, 'show']);
    Route::put('tests/{test}',               [TestController::class, 'update']);
    Route::delete('tests/{test}',            [TestController::class, 'destroy']);
    Route::post('tests/{test}/publish',      [TestController::class, 'publish']);
    Route::post('tests/{test}/unpublish',    [TestController::class, 'unpublish']);
    Route::post('tests/{test}/duplicate',    [TestController::class, 'duplicate']);
    Route::get('tests/{test}/leaderboard',   [TestController::class, 'leaderboard']);

    // ─ Test savollari ─
    Route::get('tests/{test}/questions',                       [TestQuestionController::class, 'index']);
    Route::post('tests/{test}/questions',                      [TestQuestionController::class, 'store']);
    Route::post('tests/{test}/questions/reorder',              [TestQuestionController::class, 'reorder']);
    Route::get('tests/{test}/questions/{question}',            [TestQuestionController::class, 'show']);
    Route::put('tests/{test}/questions/{question}',            [TestQuestionController::class, 'update']);
    Route::delete('tests/{test}/questions/{question}',         [TestQuestionController::class, 'destroy']);

    // ─ Savol variantlari ─
    Route::post('questions/{question}/options',                [QuestionOptionController::class, 'store']);
    Route::put('questions/{question}/options/{option}',        [QuestionOptionController::class, 'update']);
    Route::delete('questions/{question}/options/{option}',     [QuestionOptionController::class, 'destroy']);

    // ─ Test urinishlari (topshirish) ─
    Route::get('attempts',                       [TestAttemptController::class, 'index']);
    Route::post('attempts/start',                [TestAttemptController::class, 'start']);
    Route::get('attempts/{attempt}',             [TestAttemptController::class, 'show']);
    Route::post('attempts/{attempt}/answer',     [TestAttemptController::class, 'answer']);
    Route::post('attempts/{attempt}/submit',     [TestAttemptController::class, 'submit']);
    Route::post('attempts/{attempt}/cheat-log',  [TestAttemptController::class, 'logCheat']);

    // ─ Test ruxsatlari (ulashish) ─
    Route::get('accesses',                     [TestAccessController::class, 'index']);
    Route::post('accesses',                    [TestAccessController::class, 'store']);
    Route::get('accesses/code/{code}',         [TestAccessController::class, 'byCode']);
    Route::get('accesses/{access}',            [TestAccessController::class, 'show']);
    Route::put('accesses/{access}',            [TestAccessController::class, 'update']);
    Route::delete('accesses/{access}',         [TestAccessController::class, 'destroy']);
    Route::post('accesses/{access}/deactivate',[TestAccessController::class, 'deactivate']);

    // ─ Imtihon sessiyalari (hotspot / proctoring) ─
    Route::get('exam-sessions',                          [ExamSessionController::class, 'index']);
    Route::post('exam-sessions',                         [ExamSessionController::class, 'store']);
    Route::post('exam-sessions/join',                    [ExamSessionController::class, 'join']);
    Route::get('exam-sessions/{session}',               [ExamSessionController::class, 'show']);
    Route::post('exam-sessions/{session}/start',         [ExamSessionController::class, 'start']);
    Route::post('exam-sessions/{session}/finish',        [ExamSessionController::class, 'finish']);
    Route::post('exam-sessions/{session}/leave',         [ExamSessionController::class, 'leave']);
    Route::get('exam-sessions/{session}/participants',   [ExamSessionController::class, 'participants']);
    Route::get('exam-sessions/{session}/flagged',        [ExamSessionController::class, 'flagged']);

    // ─ Imtihon ishtirokchilari (anti-cheat hodisalar) ─
    Route::get('exam-participants/{participant}',                       [ExamParticipantController::class, 'show']);
    Route::post('exam-participants/{participant}/flag',                 [ExamParticipantController::class, 'flag']);
    Route::post('exam-participants/{participant}/tab-switch',           [ExamParticipantController::class, 'reportTabSwitch']);
    Route::post('exam-participants/{participant}/external-request',     [ExamParticipantController::class, 'reportExternalRequest']);

    // ─ Bildirishnomalar ─
    Route::get('notifications',                          [NotificationController::class, 'index']);
    Route::post('notifications/{notification}/read',     [NotificationController::class, 'markRead']);
    Route::post('notifications/read-all',                [NotificationController::class, 'markAllRead']);
    Route::delete('notifications/{notification}',        [NotificationController::class, 'destroy']);
});
