<?php

use App\Models\User;
use Database\Factories\UserFactory;

it('foydalanuvchini ro\'yxatdan o\'tkazadi va token qaytaradi', function () {
    $response = $this->postJson('/api/register', [
        'username'              => 'aziz_dev',
        'phone'                 => '+998901112233',
        'email'                 => 'aziz@example.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'full_name'             => 'Aziz Karimov',
    ]);

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data' => ['user' => ['id', 'username'], 'token']]);

    $this->assertDatabaseHas('users', ['username' => 'aziz_dev']);
});

it('takroriy username bilan ro\'yxatdan o\'tkazmaydi', function () {
    User::factory()->create(['username' => 'mavjud']);

    $this->postJson('/api/register', [
        'username'              => 'mavjud',
        'phone'                 => '+998901112244',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'full_name'             => 'Test',
    ])->assertStatus(422)->assertJsonValidationErrors('username');
});

it('to\'g\'ri login bilan token beradi', function () {
    $user = User::factory()->create(['username' => 'aziz']);

    $this->postJson('/api/login', [
        'login'    => 'aziz',
        'password' => UserFactory::$defaultPassword,
    ])->assertOk()->assertJsonPath('success', true)
      ->assertJsonStructure(['data' => ['token']]);
});

it('telefon raqami orqali ham login qiladi', function () {
    $user = User::factory()->create(['phone' => '+998900000000']);

    $this->postJson('/api/login', [
        'login'    => '+998900000000',
        'password' => UserFactory::$defaultPassword,
    ])->assertOk();
});

it('noto\'g\'ri parol bilan login qilmaydi', function () {
    User::factory()->create(['username' => 'aziz']);

    $this->postJson('/api/login', [
        'login'    => 'aziz',
        'password' => 'xato-parol',
    ])->assertStatus(401);
});

it('faol bo\'lmagan hisob login qila olmaydi', function () {
    User::factory()->inactive()->create(['username' => 'bloklangan']);

    $this->postJson('/api/login', [
        'login'    => 'bloklangan',
        'password' => UserFactory::$defaultPassword,
    ])->assertStatus(403);
});

it('me endpointi joriy foydalanuvchini qaytaradi', function () {
    $user = actingUser();

    $this->getJson('/api/me')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);
});

it('autentifikatsiyasiz me endpointiga kira olmaydi', function () {
    $this->getJson('/api/me')->assertUnauthorized();
});

it('logout tokenni o\'chiradi', function () {
    actingUser();
    $this->postJson('/api/logout')->assertOk();
});
