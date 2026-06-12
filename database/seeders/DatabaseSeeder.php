<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\ExamParticipant;
use App\Models\ExamSession;
use App\Models\Message;
use App\Models\Notification;
use App\Models\TestAccess;
use App\Models\TestAttempt;
use App\Models\User;
use App\Models\UserContact;
use App\Models\UserDevice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        Chat::factory(10)->create();
        Message::factory(10)->create();
        TestAttempt::factory(10)->create();
        TestAccess::factory(10)->create();
        ExamSession::factory(10)->create();
        ExamParticipant::factory(10)->create();
        UserContact::factory(10)->create();
        Notification::factory(10)->create();
        UserDevice::factory(10)->create();
    }
}
