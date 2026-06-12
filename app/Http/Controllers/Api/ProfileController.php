<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return $this->success(new UserResource($request->user()));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user->update($request->validated());

        return $this->success(new UserResource($user), 'Profil yangilandi');
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password_hash)) {
            return $this->error('Joriy parol noto\'g\'ri', 422);
        }

        $user->update(['password_hash' => Hash::make($data['password'])]);
        $user->tokens()->delete();

        return $this->success(null, 'Parol o\'zgartirildi. Qaytadan kiring.');
    }

    public function showUser(User $user)
    {
        return $this->success(new UserResource($user));
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->delete();

        return $this->success(null, 'Hisob o\'chirildi');
    }
}
