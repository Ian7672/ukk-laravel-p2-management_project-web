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

        if ($project->status === 'approved') {
            return back()->with('error', '❌ Project sudah approved, tidak bisa menambah anggota.');
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
            return back()->with('error', '⚠ User sedang bekerja di project lain, tunggu sampai project di-approve admin!');
        }

        // Tambahkan user ke project
        ProjectMember::create([
            'project_id' => $projectId,
            'user_id'    => $user->user_id,
            'role'       => $request->role, // Gunakan role dari form, bukan dari user
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

        if ($member->project->status === 'approved') {
            return back()->with('error', '❌ Project sudah approved, tidak bisa edit anggota.');
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

        // Update user_id ke user baru
        $member->update([
            'user_id' => $newUser->user_id,
            'role'    => $request->role ?? $newUser->role, // Gunakan role dari form jika ada
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

        if ($member->project->status === 'approved') {
            return back()->with('error', '❌ Project sudah approved, tidak bisa hapus anggota.');
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

        // Jika semua project sudah approved → anggap belum ada project aktif
        $hasApprovedOnly = $projects->every(fn($p) => $p->status === 'approved');

        return view('developer.myteam', compact('projects', 'user', 'hasApprovedOnly'));
    }

    public function designerTeam()
    {
        $user = auth()->user();

        // Cari semua project yang diikuti designer ini
        $projects = Project::whereHas('members', function($q) use ($user) {
            $q->where('user_id', $user->user_id);
        })->with(['members.user', 'members'])->get();

        // Jika semua project sudah approved → anggap belum ada project aktif
        $hasApprovedOnly = $projects->every(fn($p) => $p->status === 'approved');

        return view('designer.myteam', compact('projects', 'user', 'hasApprovedOnly'));
    }

    // AJAX Add Member
    public function add(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'role'    => 'required|in:team_lead,developer,designer',
        ]);

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

        // Tambahkan ke project
        \App\Models\ProjectMember::create([
            'project_id' => $project->project_id,
            'user_id'    => $user->user_id,
            'role'       => $request->role,
            'joined_at'  => now(),
        ]);

        // 🔥 Update status user sebelum response dikirim
        $user->update(['current_task_status' => 'working']);
        $user->refresh(); // pastikan data diambil ulang

        return response()->json([
            'status'  => 'success',
            'message' => "✅ {$user->username} berhasil ditambahkan.",
            'user'    => [
                'user_id'  => $user->user_id,
                'username' => $user->username,
            ],
            'role'    => $request->role,
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

    $hasApprovedOnly = $projects->where('status', 'approved')->count() > 0 && 
                      $projects->where('status', '!=', 'approved')->count() === 0;

    return view('designer.myteam', compact('projects', 'user', 'hasApprovedOnly'));
}

public function fetchMembers(Project $project)
{
    $members = $project->members()->with('user')->get();
    return response()->json($members);
}

}