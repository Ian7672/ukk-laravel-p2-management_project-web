<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Username atau password salah.'], 422);
        }

        $user->tokens()->where('name', 'mobile-app')->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->transformUser($user),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $this->transformUser($request->user()),
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil keluar.',
        ]);
    }

    private function transformUser(User $user): array
    {
        return [
            'id' => $user->user_id,
            'name' => $user->full_name ?? $user->username,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'menus' => $this->menusForRole($user->role),
        ];
    }

    private function menusForRole(string $role): array
    {
        return match ($role) {
            'admin' => [
                $this->menu('admin_dashboard', 'Dashboard'),
                $this->menu('admin_monitoring', 'Monitoring'),
                $this->menu('admin_users', 'User'),
                $this->menu('admin_reports', 'Laporan'),
                $this->menu('profile', 'Saya'),
            ],
            'team_lead' => [
                $this->menu('teamlead_dashboard', 'Dashboard'),
                $this->menu('teamlead_solver', 'Solver'),
                $this->menu('profile', 'Saya'),
            ],
            default => [
                $this->menu('member_dashboard', 'Dashboard'),
                $this->menu('member_team', 'My Team'),
                $this->menu('profile', 'Saya'),
            ],
        };
    }

    private function menu(string $key, string $label): array
    {
        return [
            'key' => $key,
            'label' => $label,
        ];
    }
}
