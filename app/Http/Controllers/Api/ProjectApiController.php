<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Support\ProjectPresenter;
use Illuminate\Http\JsonResponse;

class ProjectApiController extends Controller
{
    /**
     * Return lightweight project summaries for the mobile client.
     */
    public function index(): JsonResponse
    {
        $projects = Project::with([
            'boards.cards' => function ($query) {
                $query->select('card_id', 'board_id', 'status');
            },
            'members.user:user_id,full_name,role',
        ])
            ->orderByDesc('created_at')
            ->get();

        $data = $projects->map(fn (Project $project) => ProjectPresenter::summarize($project));

        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * Return a detailed project payload including boards, cards, and subtasks.
     */
    public function show(Project $project): JsonResponse
    {
        $project = Project::with([
            'members.user:user_id,full_name,role',
            'boards.cards' => function ($cardQuery) {
                $cardQuery
                    ->with([
                        'assignments.user:user_id,full_name,role',
                        'subtasks' => function ($subtaskQuery) {
                            $subtaskQuery->with(['blockers' => function ($blockerQuery) {
                                $blockerQuery->select('blocker_id', 'subtask_id', 'user_id', 'status', 'created_at');
                            }]);
                        },
                    ])
                    ->withCount('comments');
            },
        ])->findOrFail($project->project_id);

        return response()->json([
            'data' => ProjectPresenter::detail($project),
        ]);
    }
}
