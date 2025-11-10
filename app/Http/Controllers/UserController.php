<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user untuk management
     */
    public function index()
    {
        $users = User::whereIn('role', ['team_lead', 'developer', 'designer'])->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Store user baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:team_lead,developer,designer',
        ]);

        User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'current_task_status' => 'idle',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'role' => 'required|in:admin,team_lead,developer,designer',
        ]);

        $updateData = [
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
        ];

        $user->update($updateData);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Approve user (ubah dari pending ke role tertentu)
     */
    public function approve(User $user, Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,team_lead,developer,designer',
        ]);

        $user->update(['role' => $request->role]);
        return back()->with('success', "User {$user->username} disetujui sebagai {$request->role}");
    }

    /**
     * Hapus user pending
     */
    public function reject(User $user)
    {
        $user->delete();
        return back()->with('success', "User {$user->username} ditolak dan dihapus");
    }


    /**
     * Update role user (admin tidak boleh ubah admin)
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,team_lead,developer,designer',
        ]);

        $currentUser = Auth::user();

        // Cegah admin edit sesama admin
        if ($currentUser->role === 'admin' && $user->role === 'admin') {
            return back()->with('error', '🚫 Anda tidak dapat mengubah role sesama admin.');
        }

        // Cegah admin ubah dirinya sendiri
        if ($currentUser->user_id === $user->user_id) {
            return back()->with('error', '⚠ Anda tidak dapat mengubah role diri sendiri.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "✅ Role user {$user->username} berhasil diubah menjadi {$request->role}");
    }

    /**
     * Hapus user (admin tidak bisa hapus sesama admin)
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();

        // Cegah admin hapus admin
        if ($currentUser->role === 'admin' && $user->role === 'admin') {
            return back()->with('error', '🚫 Anda tidak dapat menghapus sesama admin.');
        }

        // Cegah admin hapus diri sendiri
        if ($currentUser->user_id === $user->user_id) {
            return back()->with('error', '⚠ Anda tidak dapat menghapus diri sendiri.');
        }

        $user->delete();

        return back()->with('success', "🗑 User {$user->username} berhasil dihapus.");
    }

    /**
     * Get user projects
     */
    public function getUserProjects(User $user)
    {
        try {
            $projects = $user->projectMembers()
                ->with([
                    'project.boards.cards.subtasks',
                    'project.boards.cards.assignments',
                ])
                ->get()
                ->map(function ($member) use ($user) {
                    $project = $member->project;
                    $joinedAt = $member->joined_at ? Carbon::parse($member->joined_at) : null;

                    if (!$project) {
                        return [
                            'project_id' => null,
                            'project_name' => 'Unknown Project',
                            'role_in_project' => $member->role ?? 'member',
                            'joined_at' => $joinedAt ? $joinedAt->format('d M Y') : 'Unknown Date',
                            'created_at' => $joinedAt ? $joinedAt->toDateString() : null,
                            'progress_percentage' => 0,
                            'progress_status' => 'Tidak Ada Data',
                            'progress_color' => 'secondary',
                            'subtasks_total' => 0,
                            'subtasks_done' => 0,
                            'cards_total' => 0,
                            'cards_done' => 0,
                        ];
                    }

                    $cards = $project->boards->flatMap->cards;

                    $assignedCards = $cards->filter(function ($card) use ($user) {
                        return $card->assignments->contains(function ($assignment) use ($user) {
                            return (int) $assignment->user_id === (int) $user->user_id;
                        });
                    });

                    $assignedSubtasks = $assignedCards->flatMap->subtasks;

                    $roleInProject = $member->role_in_project ?? $member->role ?? 'member';

                    if ($roleInProject === 'team_lead' || $roleInProject === 'admin') {
                        $assignedCards = $project->boards->flatMap->cards;
                        $assignedSubtasks = $assignedCards->flatMap->subtasks;
                    }

                    $subtasksTotal = $assignedSubtasks->count();
                    $subtasksDone = $assignedSubtasks->filter(function ($subtask) {
                        return strtolower($subtask->status ?? '') === 'done';
                    })->count();

                    $cardsTotal = $assignedCards->count();
                    $cardsDone = $assignedCards->filter(function ($card) {
                        return strtolower($card->status ?? '') === 'done';
                    })->count();

                    $progress = $subtasksTotal > 0
                        ? round(($subtasksDone / $subtasksTotal) * 100)
                        : ($cardsTotal > 0 ? round(($cardsDone / $cardsTotal) * 100) : 0);

                    $statusLabel = match (true) {
                        $progress >= 100 => 'Selesai',
                        $progress >= 70 => 'On Track',
                        $progress >= 40 => 'Dalam Progress',
                        $progress > 0 => 'Baru Mulai',
                        default => $cardsTotal > 0 ? 'Belum Ada Subtask' : 'Belum Ada Tugas',
                    };

                    $color = match (true) {
                        $progress >= 100 => 'success',
                        $progress >= 70 => 'info',
                        $progress >= 40 => 'warning',
                        $progress > 0 => 'primary',
                        default => 'secondary',
                    };

                    return [
                        'project_id' => $project->project_id,
                        'project_name' => $project->project_name ?? 'Unknown Project',
                        'role_in_project' => $roleInProject,
                        'joined_at' => $joinedAt ? $joinedAt->format('d M Y') : 'Unknown Date',
                        'created_at' => $joinedAt ? $joinedAt->toDateString() : null,
                        'progress_percentage' => $progress,
                        'progress_status' => $statusLabel,
                        'progress_color' => $color,
                        'subtasks_total' => $subtasksTotal,
                        'subtasks_done' => $subtasksDone,
                        'cards_total' => $cardsTotal,
                        'cards_done' => $cardsDone,
                    ];
                });

            return response()->json(['projects' => $projects]);
        } catch (\Exception $e) {
            return response()->json(['projects' => [], 'error' => $e->getMessage()], 500);
        }
    }
}
