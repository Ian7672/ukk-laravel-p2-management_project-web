<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // 🟦 Halaman login/register
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.combined');
    }

    // 🟩 Proses login
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $user = User::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            session([
                'user_id'   => $user->user_id,
                'role'      => $user->role,
                'username'  => $user->username,
                'full_name' => $user->full_name
            ]);

            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Username atau password salah.');
    }

    // 🟥 Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // 🟨 Proses register + auto-login
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username',
            'email'     => 'required|email|max:255|unique:users,email',
            'password'  => 'required|string|min:6|confirmed',
            'role'      => 'required|in:team_lead,developer,designer',
        ]);

        // ✅ Simpan user baru
        $user = User::create([
            'full_name'            => $request->full_name,
            'username'             => $request->username,
            'email'                => $request->email,
            'password'             => Hash::make($request->password),
            'role'                 => $request->role,
            'current_task_status'  => 'idle',
        ]);

        // 🔐 Langsung login otomatis
        Auth::login($user);
        $request->session()->regenerate();

        session([
            'user_id'   => $user->user_id,
            'role'      => $user->role,
            'username'  => $user->username,
            'full_name' => $user->full_name
        ]);

        // 🧭 Arahkan ke dashboard
        return redirect()->route('dashboard')
            ->with('success', "Selamat datang, {$user->full_name}! Akunmu telah berhasil dibuat dan kamu sudah login otomatis 🎉");
    }
}
