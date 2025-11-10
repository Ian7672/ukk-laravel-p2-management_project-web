<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blocker;
use App\Models\Card;
use App\Models\Project;
use App\Support\CardPresenter;
use App\Support\ProjectPresenter;

class TeamLeadAppController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()->role !== 'team_lead') {
                abort(403, 'Hanya team lead yang dapat mengakses resource ini.');
            }

            return $next($request);
        });
    }

    public function dashboard()
    {
        $user = auth()->user();

        $projects = Project::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        })
            ->with([
                'boards.cards' => function ($query) {
                    $query->select('card_id', 'board_id', 'status');
                },
                'members.user',
            ])
            ->orderByDesc('updated_at')
            ->get();

        $cards = Card::whereHas('assignments', function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        })
            ->with([
                'board.project:project_id,project_name',
                'assignments.user:user_id,full_name,role',
            ])
            ->withCount('comments')
            ->orderByDesc('card_id')
            ->limit(10)
            ->get();

        return response()->json([
            'projects' => $projects->map(fn ($project) => ProjectPresenter::summarize($project)),
            'my_cards' => $cards->map(fn ($card) => CardPresenter::simple($card)),
        ]);
    }

    public function solver()
    {
        $user = auth()->user();

        $blockers = Blocker::with([
            'user:user_id,full_name,role',
            'assignedTo:user_id,full_name,role',
            'subtask.card.board.project',
        ])
            ->where(function ($query) use ($user) {
                $query->whereNull('assigned_to')
                    ->orWhere('assigned_to', $user->user_id);
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function (Blocker $blocker) {
                return [
                    'id' => $blocker->blocker_id,
                    'description' => $blocker->description,
                    'priority' => $blocker->priority,
                    'status' => $blocker->status,
                    'requested_by' => $blocker->user ? [
                        'id' => $blocker->user->user_id,
                        'name' => $blocker->user->full_name,
                        'role' => $blocker->user->role,
                    ] : null,
                    'assigned_to' => $blocker->assignedTo ? [
                        'id' => $blocker->assignedTo->user_id,
                        'name' => $blocker->assignedTo->full_name,
                        'role' => $blocker->assignedTo->role,
                    ] : null,
                    'project' => $blocker->subtask && $blocker->subtask->card && $blocker->subtask->card->board && $blocker->subtask->card->board->project ? [
                        'id' => $blocker->subtask->card->board->project->project_id,
                        'name' => $blocker->subtask->card->board->project->project_name,
                    ] : null,
                    'card' => $blocker->subtask && $blocker->subtask->card ? [
                        'id' => $blocker->subtask->card->card_id,
                        'title' => $blocker->subtask->card->card_title,
                    ] : null,
                    'subtask' => $blocker->subtask ? [
                        'id' => $blocker->subtask->subtask_id,
                        'title' => $blocker->subtask->subtask_title,
                    ] : null,
                    'solution' => $blocker->solution,
                    'resolved_at' => ProjectPresenter::formatDate($blocker->resolved_at),
                ];
            });

        return response()->json(['data' => $blockers]);
    }
}
