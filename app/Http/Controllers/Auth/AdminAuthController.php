<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AdminAuthController extends Controller
{
    public function login()
    {
        return view('auth.admins.login');
    }

    public function register()
    {
        return view('auth.admins.register');
    }

    public function loginStore(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'User not found');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid password');
        }

        Auth::login($user);

        return redirect()->route('home');
    }

    public function registerStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'role' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => null,
        ]);

        // Mail::to($user->email)->send(new VerifyEmailNotification($user));

        Auth::login($user);

        return redirect()->route('home');
    }

    public function verify(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$user->email_verified_at) {
            $user->update([
                'email_verified_at' => now()
            ]);
        }

        return view('welcome')->with('success', 'Email verified successfully');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('home');
    }
}
