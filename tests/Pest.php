<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
| Barcha Feature/Unit testlar TestCase dan meros oladi va har testdan
| oldin bazani tozalaydi (RefreshDatabase).
*/
pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Helper'lar
|--------------------------------------------------------------------------
*/

/** Foydalanuvchi yaratib, uni Sanctum orqali autentifikatsiya qiladi */
function actingUser(array $attributes = []): User
{
    $user = User::factory()->create($attributes);
    Sanctum::actingAs($user);

    return $user;
}

/** Faqat foydalanuvchi yaratadi (login qilmaydi) */
function makeUser(array $attributes = []): User
{
    return User::factory()->create($attributes);
}
