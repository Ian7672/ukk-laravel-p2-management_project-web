<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Board;
use App\Models\User;
use App\Models\Card;
use App\Models\Subtask;

class ProjectController extends Controller
{
    /**
     * Dashboard utama (redirect sesuai role user)
     */
    public function index()
    {
        $role = auth()->user()->role;

        switch ($role) {
        case 'admin':
            $projects = \App\Models\Project::with('boards', 'members.user')->get();
            $users = \App\Models\User::all();
            return view('admin.dashboard', compact('projects', 'users'));

            case 'team_lead':
                return $this->teamLeadDashboard();

            case 'developer':
                return $this->developerDashboard();

            case 'designer':
                return $this->designerDashboard();

            default:
                abort(403, 'Role tidak dikenal');
        }
    }

    /**
     * ================= ADMIN =================
     */

    // Form buat proyek


    // Simpan proyek baru
    public function store(Request $request)
{
    if (auth()->user()->role !== 'admin') abort(403);

    $request->validate([
        'project_name' => 'required|string|max:255',
        'description'  => 'nullable|string',
        'deadline'     => 'nullable|date',
        'team_lead_id' => 'required|exists:users,user_id',
    ]);

    // Pastikan user terpilih adalah team lead dan idle
    $teamLead = \App\Models\User::where('user_id', $request->team_lead_id)
        ->where('role', 'team_lead')
        ->where('current_task_status', 'idle')
        ->first();

    if (!$teamLead) {
        return back()->with('error', '⚠️ Team Lead tidak valid atau sedang aktif di proyek lain.');
    }

    // Buat proyek baru
    $project = \App\Models\Project::create([
        'project_name' => $request->project_name,
        'description'  => $request->description,
        'deadline'     => $request->deadline,
        'status'       => 'proses',
        'created_by'   => auth()->id(),
    ]);

    // Masukkan Admin ke project_members
    \App\Models\ProjectMember::create([
        'project_id' => $project->project_id,
        'user_id'    => auth()->id(),
        'role'       => 'admin',
        'joined_at'  => now(),
    ]);

    // Masukkan Team Lead ke project_members
    \App\Models\ProjectMember::create([
        'project_id' => $project->project_id,
        'user_id'    => $teamLead->user_id,
        'role'       => 'admin',
        'joined_at'  => now(),
    ]);

    // Update status team lead jadi working
    $teamLead->update(['current_task_status' => 'working']);

    // Buat default boards
    $boards = ['To Do', 'In Progress', 'Review', 'Done'];
    foreach ($boards as $i => $board) {
        \App\Models\Board::create([
            'project_id' => $project->project_id,
            'board_name' => $board,
            'position'   => $i + 1,
        ]);
    }

    return redirect()->route('dashboard')->with('success', '✅ Proyek baru berhasil dibuat dan Team Lead ditugaskan.');
}



    // Detail proyek (Admin)
    public function show(Project $project)
{
    $project = Project::with(['boards.cards', 'members.user'])->findOrFail($project->project_id);

    if (auth()->user()->role !== 'admin') abort(403);

    // 🔹 Ambil semua user kecuali admin
    $users = User::where('role', '!=', 'admin')->get()->map(function ($user) {
        $isWorking = Card::whereHas('assignments', function($q) use ($user) {
            $q->where('user_id', $user->user_id);
        })->where('status', 'in_progress')->exists()
        ||
        Subtask::where('status', 'in_progress')
            ->whereHas('card.assignments', function($q) use ($user) {
                $q->where('user_id', $user->user_id);
            })->exists();

        $user->status = $isWorking ? 'Working' : 'Idle';
        return $user;
    });

    return view('admin.projects.show', compact('project', 'users'));
}

    /**
     * ================= TEAM LEAD =================
     */

    // Dashboard Team Lead
    public function teamLeadDashboard()
    {
        $userId = auth()->id();

        $projects = Project::whereHas('members', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with('boards', 'members.user')->get();

        return view('teamlead.dashboard', compact('projects'));
    }

    // Detail proyek Team Lead
    public function teamLeadShow(Project $project)
    {
        $userId = auth()->id();

        $project = Project::where('project_id', $project->project_id)
            ->whereHas('members', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with([
                'boards.cards.subtasks',
                'boards.cards.assignments.user',
                'members.user',
            ])
            ->firstOrFail();

        $progressSummary = $this->calculateProjectProgress($project);
        $canComplete = $project->status !== 'selesai' && ($progressSummary['percent'] ?? 0) >= 100;

        return view('teamlead.projects.show', compact('project', 'progressSummary', 'canComplete'));
    }

    /**
     * ================= DEVELOPER =================
     */
   public function developerDashboard()
{
    $userId = auth()->id();

    // ambil semua cards yang di-assign ke developer ini dengan pagination
    $cards = \App\Models\Card::whereHas('assignments', function($q) use ($userId) {
        $q->where('user_id', $userId);
    })
    ->with([
        'board.project',
        'subtasks' => function ($query) {
            $query->with(['blockers' => function ($blockerQuery) {
                $blockerQuery->whereIn('status', ['pending', 'in_progress']);
            }]);
        },
    ]) // tambahkan subtasks dan solver aktif
    ->orderBy('created_at', 'desc')
    ->paginate(10); // 10 item per halaman

    return view('developer.dashboard', compact('cards'));
}

    /**
     * ================= DESIGNER =================
     */
   public function designerDashboard()
{
    $userId = auth()->id();

    // ambil semua cards yang di-assign ke designer ini dengan pagination
    $cards = \App\Models\Card::whereHas('assignments', function($q) use ($userId) {
        $q->where('user_id', $userId);
    })
    ->with([
        'board.project',
        'subtasks' => function ($query) {
            $query->with(['blockers' => function ($blockerQuery) {
                $blockerQuery->whereIn('status', ['pending', 'in_progress']);
            }]);
        },
    ]) // tambahkan subtasks dan solver aktif
    ->orderBy('created_at', 'desc')
    ->paginate(10); // 10 item per halaman

    return view('designer.dashboard', compact('cards'));
}

    public function complete(Project $project)
    {
        $user = auth()->user();
        if ($user->role !== 'team_lead') {
            abort(403, 'Hanya Team Lead yang dapat menyelesaikan proyek');
        }

        $project = Project::where('project_id', $project->project_id)
            ->whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->user_id);
            })
            ->with(['boards.cards.subtasks', 'members.user'])
            ->firstOrFail();

        if ($project->status === 'selesai') {
            return back()->with('info', 'Proyek ini sudah ditandai selesai.');
        }

        $progressSummary = $this->calculateProjectProgress($project);

        if (($progressSummary['percent'] ?? 0) < 100) {
            return back()->with('error', 'Proyek belum mencapai 100% progress.');
        }

        $project->update(['status' => 'selesai']);

        $project->members->each(function ($member) {
            if ($member->user && in_array($member->user->role, ['team_lead', 'developer', 'designer'])) {
                $member->user->update(['current_task_status' => 'idle']);
            }
        });

        return back()->with('success', 'Proyek berhasil ditandai selesai.');
    }

/**
 * Review semua card di board Review (khusus Team Lead)
 */
public function review($board_id)
{
    // pastikan user adalah team lead
    if (auth()->user()->role !== 'team_lead') {
        abort(403, 'Hanya Team Lead yang dapat melakukan review');
    }

    // ambil board beserta project dan cards-nya
    $board = \App\Models\Board::with(['project', 'cards.assignments.user'])
        ->findOrFail($board_id);

    // cek agar board memang "Review"
    if (strtolower($board->board_name) !== 'review') {
        return redirect()->back()->with('error', 'Board ini bukan untuk review.');
    }

    // ambil semua card di board ini
    $cards = $board->cards;

    // tampilkan halaman review
    return view('teamlead.cards.review', compact('board', 'cards'));
}

public function approve($card_id)
{
    $card = \App\Models\Card::findOrFail($card_id);
    $card->status = 'done';
    $card->save();
    return redirect()->back()->with('success', 'Card disetujui dan dipindahkan ke Done!');
}

public function reject($card_id)
{
    $card = \App\Models\Card::findOrFail($card_id);
    $card->status = 'in_progress';
    $card->save();
    return redirect()->back()->with('error', 'Card dikembalikan ke In Progress.');
}

    protected function calculateProjectProgress(Project $project): array
    {
        $project->loadMissing('boards.cards.subtasks');

        $cards = $project->boards->flatMap->cards;
        $subtasks = $cards->flatMap->subtasks;

        $cardsTotal = $cards->count();
        $cardsDone = $cards->filter(fn ($card) => strtolower($card->status ?? '') === 'done')->count();

        $subtasksTotal = $subtasks->count();
        $subtasksDone = $subtasks->filter(fn ($subtask) => strtolower($subtask->status ?? '') === 'done')->count();

        if ($subtasksTotal > 0) {
            $percent = round(($subtasksDone / $subtasksTotal) * 100);
            $basis = 'subtasks';
        } elseif ($cardsTotal > 0) {
            $percent = round(($cardsDone / $cardsTotal) * 100);
            $basis = 'cards';
        } else {
            $percent = 0;
            $basis = 'cards';
        }

        return [
            'percent' => $percent,
            'basis' => $basis,
            'cards_total' => $cardsTotal,
            'cards_done' => $cardsDone,
            'subtasks_total' => $subtasksTotal,
            'subtasks_done' => $subtasksDone,
        ];
    }
}
