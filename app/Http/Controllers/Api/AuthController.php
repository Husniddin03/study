<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'username'      => $data['username'],
            'phone'         => $data['phone'],
            'email'         => $data['email'] ?? null,
            'password_hash' => Hash::make($data['password']),
            'full_name'     => $data['full_name'],
            'is_active'     => true,
        ]);

        $token = $user->createToken($data['username'] . '-token')->plainTextToken;

        return $this->created([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Ro\'yxatdan o\'tildi');
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('username', $data['login'])
            ->orWhere('phone', $data['login'])
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
            return $this->error('Login yoki parol noto\'g\'ri', 401);
        }

        if (! $user->is_active) {
            return $this->error('Hisob faol emas', 403);
        }

        $user->update(['last_seen_at' => now()]);

        $token = $user->createToken($data['device_name'] ?? 'api-token')->plainTextToken;

        return $this->success([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Kirish muvaffaqiyatli');
    }

    public function me(Request $request)
    {
        return $this->success(new UserResource($request->user()));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Tizimdan chiqildi');
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->success(null, 'Barcha qurilmalardan chiqildi');
    }
}
