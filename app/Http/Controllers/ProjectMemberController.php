<?php

namespace App\Http\Controllers;

use App\Models\ProjectMember;
use App\Models\Project;
use App\Models\User;
use App\Models\Subtask;
use Illuminate\Http\Request;

class ProjectMemberController extends Controller
{
    /**
     * Tambahkan anggota baru ke proyek
     */
    public function addMember(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        if ($project->status === 'selesai') {
            return back()->with('error', '❌ Project sudah selesai, tidak bisa menambah anggota.');
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return back()->with('error', '❌ User tidak ditemukan!');
        }
       
        // 🚫 CEK: User tidak boleh admin
        if ($user->role === 'admin') {
            return back()->with('error', '🚫 Admin tidak bisa ditambahkan sebagai anggota proyek.');
        }
       
        // ✅ Batasi role yang boleh gabung ke project
        $allowedRoles = ['team_lead','developer','designer']; // Hapus admin dari allowed roles
        if (!in_array($user->role, $allowedRoles)) {
           return back()->with('error', '🚫 Role user tidak valid untuk bergabung ke project.');
        }
        
        // Kalau user statusnya sudah working → tolak
        if ($user->current_task_status === 'working') {
            return back()->with('error', '⚠ User sedang bekerja di project lain, tunggu sampai project tersebut selesai!');
        }

        $projectRole = $this->resolveProjectRole($user);

        // Tambahkan user ke project
        ProjectMember::create([
            'project_id' => $projectId,
            'user_id'    => $user->user_id,
            'role'       => $projectRole,
            'joined_at'  => now(),
        ]);

        // Update status user jadi working
        $user->update(['current_task_status' => 'working']);

        return back()->with('success', '✅ Anggota berhasil ditambahkan ke project!');
    }

    /**
     * Update User
     */
    public function updateUser(Request $request, $memberId)
    {
        $member = ProjectMember::with('project')->findOrFail($memberId);
        
        // ❌ Cek jika ada subtask in_progress di project ini
        $hasStarted = Subtask::whereHas('card.board', function($q) use ($member) {
            $q->where('project_id', $member->project->project_id);
        })->where('status', 'in_progress')->exists();

        if ($hasStarted) {
            return back()->with('error', '⚠ Tidak bisa edit anggota, subtask sudah berjalan.');
        }

        if ($member->project->status === 'selesai') {
            return back()->with('error', '❌ Project sudah selesai, tidak bisa edit anggota.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,user_id'
        ]);

        $newUser = User::find($request->user_id);

        if (!$newUser) {
            return back()->with('error', '❌ User tidak ditemukan.');
        }
       
        // 🚫 CEK: User baru tidak boleh admin
        if ($newUser->role === 'admin') {
            return back()->with('error', '🚫 Admin tidak bisa ditambahkan sebagai anggota proyek.');
        }

        // ✅ Batasi role yang boleh gabung ke project
        $allowedRoles = ['team_lead','developer','designer']; // Hapus admin
        if (!in_array($newUser->role, $allowedRoles)) {
            return back()->with('error', '🚫 Role user tidak valid untuk bergabung ke project.');
        }

        // Pastikan user baru bukan sedang working di project lain
        if ($newUser->current_task_status === 'working') {
            return back()->with('error', '⚠ User sedang bekerja di project lain.');
        }

        // Reset status user lama jadi idle
        $member->user->update(['current_task_status' => 'idle']);

        $projectRole = $this->resolveProjectRole($newUser);

        // Update user_id ke user baru
        $member->update([
            'user_id' => $newUser->user_id,
            'role'    => $projectRole,
        ]);

        // Update status user baru jadi working
        $newUser->update(['current_task_status' => 'working']);

        return back()->with('success', '✅ Anggota berhasil diganti!');
    }

    /**
     * Hapus anggota
     */
    public function deleteMember($memberId)
    {
        $member = ProjectMember::with(['project', 'user'])->findOrFail($memberId);
        
        // ❌ Cek jika ada subtask in_progress di project ini
        $hasStarted = Subtask::whereHas('card.board', function($q) use ($member) {
            $q->where('project_id', $member->project->project_id);
        })->where('status', 'in_progress')->exists();

        if ($hasStarted) {
            return back()->with('error', '⚠ Tidak bisa hapus anggota, subtask sudah berjalan.');
        }

        if ($member->project->status === 'selesai') {
            return back()->with('error', '❌ Project sudah selesai, tidak bisa hapus anggota.');
        }

        // reset status user jadi idle
        $member->user->update(['current_task_status' => 'idle']);

        $member->delete();

        return back()->with('success', '🗑 Anggota berhasil dihapus!');
    }

    public function myTeam()
    {
        $user = auth()->user();

        // Cari project yang diikuti user ini
        $projects = Project::whereHas('members', function($q) use ($user) {
            $q->where('user_id', $user->user_id);
        })->with(['members.user'])->get();

        return view('teamlead.myteam', compact('projects', 'user'));
    }

    public function developerTeam()
    {
        $user = auth()->user();

        // Cari semua project yang diikuti developer ini
        $projects = Project::whereHas('members', function($q) use ($user) {
            $q->where('user_id', $user->user_id);
        })->with(['members.user', 'members'])->get();

        // Jika semua project sudah selesai → anggap belum ada project aktif
        $hasCompletedOnly = $projects->every(fn($p) => $p->status === 'selesai');

        return view('developer.myteam', compact('projects', 'user', 'hasCompletedOnly'));
    }

    public function designerTeam()
    {
        $user = auth()->user();

        // Cari semua project yang diikuti designer ini
        $projects = Project::whereHas('members', function($q) use ($user) {
            $q->where('user_id', $user->user_id);
        })->with(['members.user', 'members'])->get();

        // Jika semua project sudah selesai → anggap belum ada project aktif
        $hasCompletedOnly = $projects->every(fn($p) => $p->status === 'selesai');

        return view('designer.myteam', compact('projects', 'user', 'hasCompletedOnly'));
    }

    // AJAX Add Member
    public function add(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'role'    => 'required|in:team_lead,developer,designer',
        ]);

        if ($project->status === 'selesai') {
            return response()->json([
                'status'  => 'error',
                'message' => '❌ Project sudah selesai, tidak bisa menambah anggota baru.',
            ], 422);
        }

        $user = \App\Models\User::find($request->user_id);

        // 🚫 CEK: User tidak boleh admin
        if ($user->role === 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => '🚫 Admin tidak bisa ditambahkan sebagai anggota proyek.',
            ]);
        }

        // 🚫 Jika user sedang aktif → kembalikan warning
        if ($user->current_task_status === 'working') {
            return response()->json([
                'status'  => 'warning',
                'message' => "⚠️ User {$user->username} sedang aktif di proyek lain.",
            ]);
        }

        // ✅ Cegah duplikasi
        if (\App\Models\ProjectMember::where('project_id', $project->project_id)
            ->where('user_id', $user->user_id)->exists()) {
            return response()->json([
                'status'  => 'warning',
                'message' => "⚠️ User {$user->username} sudah menjadi anggota proyek ini.",
            ]);
        }

        $projectRole = $this->resolveProjectRole($user);

        // Tambahkan ke project
        \App\Models\ProjectMember::create([
            'project_id' => $project->project_id,
            'user_id'    => $user->user_id,
            'role'       => $projectRole,
            'joined_at'  => now(),
        ]);

        // 🔥 Update status user sebelum response dikirim
        $user->update(['current_task_status' => 'working']);
        $user->refresh(); // pastikan data diambil ulang

        return response()->json([
            'status'  => 'success',
            'message' => "{$user->username} berhasil ditambahkan.",
            'user'    => [
                'user_id'  => $user->user_id,
                'username' => $user->username,
            ],
            'role'    => $request->role,
            'project_role' => $projectRole,
        ]);
    }

    /**
 * My Team untuk Designer
 */
public function designerMyTeam()
{
    $user = auth()->user();
    
    $projects = Project::whereHas('members', function($q) use ($user) {
        $q->where('user_id', $user->user_id);
    })->with(['members.user'])->get();

    $hasCompletedOnly = $projects->where('status', 'selesai')->count() > 0 && 
                      $projects->where('status', '!=', 'selesai')->count() === 0;

    return view('designer.myteam', compact('projects', 'user', 'hasCompletedOnly'));
}

public function fetchMembers(Project $project)
{
    $members = $project->members()->with('user')->get();
    return response()->json($members);
}


    private function resolveProjectRole(User $user): string
    {
        return $user->role === 'team_lead' ? 'admin' : 'member';
    }

}

