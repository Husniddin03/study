<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        

        Schema::create('chats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['private', 'group', 'channel']);
            $table->string('name', 100)->nullable();           // private uchun null
            $table->string('username', 50)->unique()->nullable(); // guruh public link
            $table->string('description', 500)->nullable();
            $table->string('avatar_url')->nullable();
            $table->uuid('created_by');
            $table->boolean('is_public')->default(false);

            // Exam rejimi — anti-cheat uchun
            $table->boolean('is_exam_mode')->default(false);
            $table->boolean('exam_monitor_tabs')->default(false);   // tab switching detect
            $table->boolean('exam_monitor_copy')->default(false);   // copy-paste bloklash
            $table->boolean('exam_require_selfie')->default(false); // selfie talab
            $table->boolean('exam_hotspot_required')->default(false); // hotspot rejimi

            $table->integer('member_count')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');
            $table->index('type');
            $table->index('is_public');
            $table->index('created_by');
        });

        Schema::create('chat_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('chat_id');
            $table->uuid('user_id');
            $table->enum('role', ['creator', 'admin', 'member', 'guest'])->default('member');

            // Ruxsatlar (granular permissions)
            $table->boolean('can_send_messages')->default(true);
            $table->boolean('can_send_tests')->default(true);
            $table->boolean('can_create_exam')->default(false);  // exam boshlash
            $table->boolean('can_manage_members')->default(false);

            $table->boolean('is_muted')->default(false);
            $table->timestamp('muted_until')->nullable();
            $table->timestamp('joined_at')->useCurrent();
            $table->uuid('invited_by')->nullable();

            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invited_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['chat_id', 'user_id']);
            $table->index(['user_id', 'chat_id']);
        });

        Schema::create('tests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('creator_id');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->enum('type', ['quiz', 'dtm', 'block', 'open', 'mixed'])->default('quiz');

            // Kirish huquqi
            // 'public'   — hamma ko'radi va ishlaydi
            // 'link'     — faqat havola bilan
            // 'private'  — faqat ruxsat berilganlarga
            $table->enum('visibility', ['public', 'link', 'private'])->default('private');

            // Vaqt va urinish sozlamalari
            $table->integer('duration_minutes')->nullable();     // null = cheksiz
            $table->integer('max_attempts')->default(1);        // necha marta ishlash mumkin
            $table->boolean('show_answers_after')->default(true); // tugatgandan keyin javoblarni ko'rsin
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);
            $table->integer('passing_score')->default(0);       // o'tish bali (%)

            // DTM maxsus sozlamalari
            $table->json('dtm_config')->nullable();
            // Misol: {
            //   "main_subjects": [{"subject": "Matematika", "question_count": 30}, {...}],
            //   "required_subjects": [{"subject": "Ona tili", "question_count": 10}, ...]
            // }

            // Anti-cheat
            $table->boolean('anti_cheat_enabled')->default(false);
            $table->boolean('require_hotspot')->default(false);
            $table->boolean('block_tab_switch')->default(false);
            $table->boolean('block_copy_paste')->default(false);
            $table->boolean('require_camera')->default(false);
            $table->integer('tab_switch_limit')->default(3); // necha marta tabga chiqqanda ogohlantirish

            $table->boolean('is_published')->default(false);
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();

            $table->integer('attempt_count')->default(0); // umumiy urinishlar soni
            $table->decimal('avg_score', 5, 2)->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('creator_id')->references('id')->on('users');
            $table->index('creator_id');
            $table->index('type');
            $table->index('visibility');
            $table->index('is_published');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('chat_id');
            $table->uuid('sender_id');
            $table->enum('type', ['text', 'image', 'file', 'voice', 'test_share', 'system'])->default('text');
            $table->text('content')->nullable();
            $table->string('file_url')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable(); // bytes

            // Test ulashish
            $table->uuid('test_id')->nullable();         // qaysi test ulashilgan
            $table->uuid('test_access_id')->nullable();  // qanday kirish huquqi bilan

            // Reply / Forward
            $table->uuid('reply_to_id')->nullable();     // reply qilingan xabar
            $table->uuid('forwarded_from_id')->nullable(); // forward qilingan

            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->json('reactions')->nullable();        // {'❤️': ['user_id1', ...]}
            $table->json('read_by')->nullable();          // ['user_id1', ...]

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users');
            $table->foreign('test_id')->references('id')->on('tests')->nullOnDelete();
            $table->foreign('reply_to_id')->references('id')->on('messages')->nullOnDelete();

            $table->index(['chat_id', 'created_at']);
            $table->index('sender_id');
            $table->index('test_id');
        });

        Schema::create('test_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('test_id');
            $table->string('subject', 100)->nullable();  // DTM: "Matematika", "Fizika"...
            $table->string('block_name', 100)->nullable(); // Blok test uchun

            $table->enum('content_type', ['text', 'image', 'formula', 'mixed'])->default('text');
            $table->text('content');                    // Asosiy savol matni
            $table->string('image_url')->nullable();    // Savoldagi rasm
            $table->text('formula')->nullable();        // LaTeX formula string
            $table->json('extra_content')->nullable();  // qo'shimcha media/kontentlar

            $table->enum('answer_type', ['single', 'multiple', 'open_text', 'true_false'])->default('single');
            $table->integer('order_index')->default(0); // tartib
            $table->integer('points')->default(1);      // ball
            $table->text('explanation')->nullable();    // to'g'ri javob izohi

            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
            $table->index(['test_id', 'order_index']);
            $table->index(['test_id', 'subject']);
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('question_id');
            $table->text('content');                     // variant matni
            $table->string('image_url')->nullable();     // variantdagi rasm
            $table->text('formula')->nullable();         // LaTeX formula
            $table->boolean('is_correct')->default(false);
            $table->integer('order_index')->default(0); // A, B, C, D tartib

            $table->foreign('question_id')->references('id')->on('test_questions')->onDelete('cascade');
            $table->index(['question_id', 'order_index']);
        });

        // Har bir foydalanuvchining test ishlash sessiyasi
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('test_id');
            $table->uuid('user_id');
            $table->uuid('access_id')->nullable();  // qaysi access orqali kirgan

            // Status
            // 'in_progress' — ishlayapti
            // 'submitted'   — topshirdi
            // 'timed_out'   — vaqt tugadi
            // 'invalidated' — anti-cheat buzilgan
            $table->enum('status', ['in_progress', 'submitted', 'timed_out', 'invalidated'])->default('in_progress');

            // Natija
            $table->integer('total_questions')->default(0);
            $table->integer('answered_count')->default(0);
            $table->integer('correct_count')->default(0);
            $table->decimal('score', 6, 2)->default(0);     // foizda
            $table->integer('total_points')->default(0);
            $table->integer('earned_points')->default(0);

            // DTM bo'yicha fanlar natijalari
            $table->json('subject_scores')->nullable();
            // Misol: {"Matematika": {"correct": 25, "total": 30}, "Fizika": {...}}

            // Vaqt
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_spent_seconds')->nullable();

            // Anti-cheat log
            $table->integer('tab_switch_count')->default(0);
            $table->boolean('is_flagged')->default(false);       // shubhali harakat
            $table->json('cheat_log')->nullable();
            // Misol: [{"event": "tab_switch", "at": "2024-01-01T10:05:00"}]

            $table->integer('rank')->nullable();   // test ichida reyting

            $table->foreign('test_id')->references('id')->on('tests');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unique(['test_id', 'user_id', 'started_at']); // bir vaqtda bir sessiya
            $table->index(['test_id', 'score']);
            $table->index(['user_id', 'started_at']);
            $table->index('status');
        });

        // Har bir savolga berilgan javob
        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('attempt_id');
            $table->uuid('question_id');
            $table->uuid('selected_option_id')->nullable(); // single/multiple uchun
            $table->json('selected_option_ids')->nullable(); // multiple choice uchun
            $table->text('open_answer')->nullable();          // ochiq javob uchun
            $table->boolean('is_correct')->nullable();
            $table->integer('points_earned')->default(0);
            $table->integer('time_spent_seconds')->nullable(); // bu savolga ketgan vaqt
            $table->timestamp('answered_at')->useCurrent();

            $table->foreign('attempt_id')->references('id')->on('test_attempts')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('test_questions');
            $table->foreign('selected_option_id')->references('id')->on('question_options')->nullOnDelete();

            $table->unique(['attempt_id', 'question_id']);
            $table->index('attempt_id');
        });

        Schema::create('test_accesses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('test_id');
            $table->uuid('granted_by');

            // Kirish maqsadi: guruhgami yoki alohida usergami
            // 'chat'    — chatdagi barcha a'zolarga
            // 'user'    — alohida bir foydalanuvchiga
            // 'public'  — hamma uchun (visibility dan keladi)
            $table->enum('access_type', ['chat', 'user', 'public'])->default('chat');
            $table->uuid('chat_id')->nullable();  // guruhga bersa
            $table->uuid('user_id')->nullable();  // alohida usergа bersa

            // Exam sozlamalari (bu ulash uchun maxsus)
            $table->boolean('is_exam')->default(false);
            $table->integer('exam_duration_minutes')->nullable(); // bu ulash uchun vaqt
            $table->timestamp('exam_starts_at')->nullable();      // boshlanish vaqti
            $table->timestamp('exam_ends_at')->nullable();        // tugash vaqti
            $table->integer('max_participants')->nullable();

            // Anti-cheat bu ulash uchun
            $table->boolean('require_hotspot')->default(false);
            $table->boolean('block_tab_switch')->default(false);
            $table->boolean('require_camera')->default(false);

            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->string('invite_code', 20)->unique()->nullable(); // maxsus kod bilan kirish
            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
            $table->foreign('granted_by')->references('id')->on('users');
            $table->foreign('chat_id')->references('id')->on('chats')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->index(['test_id', 'access_type']);
            $table->index(['chat_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
        });

        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('access_id');       // test_access ga bog'liq
            $table->uuid('host_user_id');    // hotspot beruvchi (boshlovchi)
            $table->string('session_code', 10)->unique(); // "DTM2024" kabi kod
            $table->string('network_ssid', 100)->nullable(); // hotspot nomi
            $table->string('network_ip_range', 50)->nullable(); // masalan 192.168.43.x

            // Status
            $table->enum('status', ['waiting', 'active', 'finished', 'cancelled'])->default('waiting');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            // Monitoring
            $table->integer('connected_count')->default(0);
            $table->integer('max_allowed')->nullable();
            $table->json('monitoring_log')->nullable(); // hodisalar logi

            $table->timestamps();

            $table->foreign('access_id')->references('id')->on('test_accesses')->onDelete('cascade');
            $table->foreign('host_user_id')->references('id')->on('users');
            $table->index('session_code');
            $table->index('status');
        });

        // Exam sessiondagi ishtirokchilar va ularning holatini kuzatish
        Schema::create('exam_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('session_id');
            $table->uuid('user_id');
            $table->uuid('attempt_id')->nullable();  // boshlagan urinish

            $table->string('device_ip', 45)->nullable();     // ulanish IP
            $table->string('device_info', 200)->nullable();  // browser/qurilma info

            // Status
            $table->enum('status', ['connected', 'disconnected', 'suspicious', 'kicked'])->default('connected');
            $table->timestamp('connected_at')->useCurrent();
            $table->timestamp('disconnected_at')->nullable();

            // Xavfli harakatlar
            $table->integer('external_request_count')->default(0); // tashqi so'rovlar
            $table->integer('tab_switch_count')->default(0);
            $table->boolean('is_flagged')->default(false);
            $table->json('violation_log')->nullable();
            // Misol: [{"type": "external_site", "url": "google.com", "at": "..."}]

            $table->foreign('session_id')->references('id')->on('exam_sessions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('attempt_id')->references('id')->on('test_attempts')->nullOnDelete();

            $table->unique(['session_id', 'user_id']);
            $table->index(['session_id', 'status']);
            $table->index('is_flagged');
        });

        Schema::create('user_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');     // kim qo'shgan
            $table->uuid('contact_id'); // kim qo'shilgan
            $table->string('nickname', 100)->nullable(); // shaxsiy laqab
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'contact_id']);
            $table->index(['user_id', 'is_blocked']);
        });

        // Bildirishnomalar
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');       // kimga
            $table->enum('type', [
                'message',           // yangi xabar
                'test_shared',       // test ulashildi
                'exam_started',      // exam boshlandi
                'exam_alert',        // anti-cheat ogohlantirish
                'test_result',       // natija tayyor
                'chat_invite',       // guruhga taklif
                'system',            // tizim xabari
            ]);
            $table->string('title', 200);
            $table->text('body')->nullable();
            $table->json('data')->nullable();  // qo'shimcha ma'lumot
            $table->uuid('reference_id')->nullable(); // bog'liq element ID
            $table->string('reference_type', 50)->nullable(); // 'test', 'chat', 'attempt'
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });

        // Foydalanuvchi qurilmalari (push notification uchun)
        Schema::create('user_devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('device_token', 500); // FCM/APNS token
            $table->enum('platform', ['ios', 'android', 'web']);
            $table->string('device_name', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'is_active']);
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('test_attempts');
        Schema::dropIfExists('test_accesses');
        Schema::dropIfExists('exam_sessions');
        Schema::dropIfExists('exam_participants');
        Schema::dropIfExists('user_contacts');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('user_devices');
    }
};
