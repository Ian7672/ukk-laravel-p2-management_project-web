<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Board;
use App\Models\CardAssignment;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CardController extends Controller
{
    /**
     * Daftar cards dalam board
     */
    public function index(Board $board)
    {
        if (Auth::user()->role !== 'team_lead') {
            abort(403, 'Hanya Team Lead yang bisa melihat daftar card');
        }

       $cards = Card::with(['assignments.user', 'subtasks'])
          ->where('board_id', $board->board_id)
          ->orderBy('position', 'asc')
          ->get()
          ->map(function ($card) {
              $card->actual_hours = $card->subtasks->sum('actual_hours');
              return $card;
        });


        return view('teamlead.cards.index', compact('cards', 'board'));
    }

    /**
     * Form tambah card
     */
    public function create(Board $board)
    {
        if (Auth::user()->role !== 'team_lead') abort(403);

        // cek status project
         $project = Project::findOrFail($board->project_id);
         if ($project->status === 'approved') {
             return redirect()->route('teamlead.projects.show', $project->project_id)
                ->with('error', '⚠ Proyek sudah disetujui, tidak bisa menambahkan card baru.');
            }
        // ✅ ambil member project yang role developer/designer
        $members = \App\Models\User::whereIn('role', ['developer', 'designer'])
            ->whereIn('user_id', function ($q) use ($project) {
                 $q->select('user_id')->from('project_members')->where('project_id', $project->project_id);
            })
            ->get();

        return view('teamlead.cards.create', compact('board' , 'members'));
    }

    /**
     * Simpan card baru + assign user (hanya 1)
     */
   public function store(Request $request, Board $board)
{
    if (Auth::user()->role !== 'team_lead') abort(403);
    
    // cek status project
    $project = Project::findOrFail($board->project_id);
    if ($project->status === 'approved') {
        return response()->json([
            'message' => '⚠ Proyek sudah disetujui, tidak bisa menambahkan card baru.'
        ], 422);
    }

    $request->validate([
        'card_title'      => 'required|string|max:255',
        'description'     => 'nullable|string',
        'priority'        => 'required|in:low,medium,high',
        'estimated_hours' => 'nullable|numeric|min:0',
        'due_date'        => 'nullable|date|after_or_equal:today',
        'username'        => 'required|string',
    ]);

    // cari user
    $user = User::where('username', $request->username)
        ->whereIn('role', ['developer', 'designer'])
        ->first();

    if (!$user) {
        return response()->json([
            'errors' => ['username' => ["❌ Username {$request->username} tidak valid atau bukan developer/designer."]]
        ], 422);
    }

    // cek apakah user sudah tergabung di project
    $isMember = ProjectMember::where('project_id', $project->project_id)
        ->where('user_id', $user->user_id)
        ->exists();

    if (!$isMember) {
        return response()->json([
            'errors' => ['username' => ["❌ User {$user->username} belum tergabung di project {$project->project_name}."]]
        ], 422);
    }

    // cek tugas aktif
    $hasActiveCard = CardAssignment::where('user_id', $user->user_id)
        ->whereHas('card', function ($q) {
            $q->whereIn('status', ['todo', 'in_progress', 'review']);
        })
        ->exists();

    if ($hasActiveCard) {
        return response()->json([
            'errors' => ['username' => ["❌ User {$user->username} sedang ada tugas lain."]]
        ], 422);
    }

    // tentukan position (auto increment)
    $maxPosition = Card::where('board_id', $board->board_id)->max('position');
    $position = $maxPosition ? $maxPosition + 1 : 1;

    $card = Card::create([
        'board_id'        => $board->board_id,
        'card_title'      => $request->card_title,
        'description'     => $request->description,
        'priority'        => $request->priority,
        'estimated_hours' => $request->estimated_hours,
        'due_date'        => $request->due_date,
        'status'          => 'todo',
        'position'        => $position,
        'created_by'      => Auth::id(),
    ]);

    CardAssignment::create([
        'card_id'          => $card->card_id,
        'user_id'          => $user->user_id,
        'assignment_status'=> 'assigned',
        'assigned_at'      => now()
    ]);

    return response()->json([
        'message' => '✅ Card berhasil dibuat & ditugaskan!',
        'card' => $card
    ]);
}


public function update(Request $request, Board $board, Card $card)
{
    if (Auth::user()->role !== 'team_lead') abort(403);

    // cek status project
    $project = Project::findOrFail($board->project_id);
    if ($project->status === 'approved') {
        return redirect()->route('teamlead.cards.index', $board->board_id)
            ->with('error', '⚠ Proyek sudah disetujui, card tidak bisa diperbarui.');
    }

    $request->validate([
        'card_title'      => 'required|string|max:255',
        'description'     => 'nullable|string',
        'priority'        => 'required|in:low,medium,high',
        'estimated_hours' => 'nullable|numeric|min:0',
        'due_date'        => 'nullable|date|after_or_equal:today',
        'username'        => 'required|string',
        'position'        => 'nullable|integer|min:1',
    ]);

    // cari user
    $user = User::where('username', $request->username)->first();

    if (!$user) {
        throw ValidationException::withMessages([
            'username' => "❌ Username {$request->username} tidak ditemukan.",
        ]);
    }

    // pastikan role harus developer/designer
    if (!in_array($user->role, ['developer', 'designer'])) {
        throw ValidationException::withMessages([
            'username' => "❌ User {$user->username} bukan developer atau designer.",
        ]);
    }

    // cek apakah user sudah jadi member di project
    $isMember = \App\Models\ProjectMember::where('project_id', $project->project_id)
        ->where('user_id', $user->user_id)
        ->exists();

    if (!$isMember) {
        throw ValidationException::withMessages([
            'username' => "❌ User {$user->username} belum tergabung dalam project {$project->project_name}.",
        ]);
    }

    // cek apakah user sudah punya tugas aktif lain
    $hasActiveCard = CardAssignment::where('user_id', $user->user_id)
        ->where('card_id', '!=', $card->card_id)
        ->whereHas('card', function ($q) {
            $q->whereIn('status', ['todo', 'in_progress', 'review']);
        })
        ->exists();

    if ($hasActiveCard) {
        throw ValidationException::withMessages([
            'username' => "❌ User {$user->username} sedang ada tugas lain.",
        ]);
    }

    // update card
    $position = $request->filled('position') ? $request->position : 1;

    $card->update([
        'card_title'      => $request->card_title,
        'description'     => $request->description,
        'priority'        => $request->priority,
        'estimated_hours' => $request->estimated_hours,
        'due_date'        => $request->due_date,
        'position'        => $position,
    ]);

    // reset assignment lama
    CardAssignment::where('card_id', $card->card_id)->delete();

    // buat assignment baru
    CardAssignment::create([
        'card_id'          => $card->card_id,
        'user_id'          => $user->user_id,
        'assignment_status'=> 'assigned',
        'assigned_at'      => now()
    ]);

    return redirect()->route('teamlead.cards.index', $board)
        ->with('success', '✅ Card berhasil diperbarui!');
}

    /**
     * Hapus card
     */
    public function destroy(Board $board, Card $card)
    {
        if (Auth::user()->role !== 'team_lead') abort(403);
         // cek status project
         $project = Project::findOrFail($board->project_id);
         if ($project->status === 'approved') {
             return redirect()->route('teamlead.cards.index', $board->board_id)
                  ->with('error', '⚠ Proyek sudah disetujui, card tidak bisa dihapus.');
        }

        $card->delete();
        return redirect()->route('teamlead.cards.index', $board)
            ->with('success', '🗑 Card berhasil dihapus!');
    }
    public function show(Board $board, Card $card)
{
    if (Auth::user()->role !== 'team_lead') abort(403);

    // Ambil semua subtasks card ini
    $card->load(['assignments.user', 'subtasks']);

    return view('teamlead.cards.show', compact('board', 'card'));
}

public function showDeveloper(Card $card)
{
    $user = auth()->user();

    // Pastikan hanya developer/designer yang bisa mengakses
    if (!in_array($user->role, ['developer', 'designer'])) {
        abort(403, 'Akses ditolak.');
    }

    // Ambil data card lengkap dengan subtasks dan komentar
    $card->load(['board.project', 'subtasks', 'comments.user']);

    // Tampilkan ke view
    return view('developer.cards.show', compact('card'));
}


}