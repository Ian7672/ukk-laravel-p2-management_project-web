<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Project;
use App\Support\CardPresenter;
use App\Support\ProjectPresenter;

class MemberAppController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!in_array($request->user()->role, ['developer', 'designer'])) {
                abort(403, 'Hanya developer/designer yang dapat mengakses resource ini.');
            }

            return $next($request);
        });
    }

    public function dashboard()
    {
        $user = auth()->user();

        $cards = Card::whereHas('assignments', function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        })
            ->with([
                'board.project:project_id,project_name',
                'assignments.user:user_id,full_name,role',
            ])
            ->withCount('comments')
            ->orderByDesc('card_id')
            ->limit(15)
            ->get()
            ->map(fn ($card) => CardPresenter::simple($card));

        $projects = Project::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        })
            ->with([
                'boards.cards' => function ($query) {
                    $query->select('card_id', 'board_id', 'status');
                },
                'members.user:user_id,full_name,role',
            ])
            ->orderBy('project_name')
            ->get()
            ->map(fn ($project) => ProjectPresenter::summarize($project));

        return response()->json([
            'cards' => $cards,
            'projects' => $projects,
        ]);
    }

    public function team()
    {
        $user = auth()->user();

        $projects = Project::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        })
            ->with(['members.user'])
            ->get();

        $teams = $projects->map(function ($project) use ($user) {
            return [
                'project' => [
                    'id' => $project->project_id,
                    'name' => $project->project_name,
                ],
                'members' => $project->members
                    ->filter(fn ($member) => $member->user && $member->user->user_id !== $user->user_id)
                    ->map(function ($member) {
                        return [
                            'id' => $member->user->user_id,
                            'name' => $member->user->full_name,
                            'role' => $member->user->role,
                            'status' => $member->user->current_task_status,
                        ];
                    })
                    ->values(),
            ];
        });

        return response()->json(['data' => $teams]);
    }
}
