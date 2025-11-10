<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blocker;
use App\Models\Card;
use App\Models\Project;
use App\Models\User;
use App\Support\CardPresenter;
use App\Support\ProjectPresenter;

class AdminAppController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()->role !== 'admin') {
                abort(403, 'Hanya admin yang dapat mengakses resource ini.');
            }

            return $next($request);
        });
    }

    public function dashboard()
    {
        $projects = Project::with([
            'boards.cards.subtasks',
            'members.user:user_id,full_name,role',
        ])->get();

        $projectSummaries = $projects->map(fn ($project) => ProjectPresenter::summarize($project));

        $totalProjects = $projectSummaries->count();
        $totalCards = $projects->sum(fn ($project) => $project->boards->sum(fn ($board) => $board->cards->count()));
        $completedCards = $projects->sum(fn ($project) => $project->boards->sum(fn ($board) => $board->cards->where('status', 'done')->count()));
        $openBlockers = Blocker::whereIn('status', ['pending', 'in_progress'])->count();
        $activeMembers = User::whereIn('role', ['team_lead', 'developer', 'designer'])
            ->where('current_task_status', 'working')
            ->count();

        $stats = [
            [
                'label' => 'Total Proyek',
                'value' => $totalProjects,
                'description' => 'Seluruh proyek aktif',
            ],
            [
                'label' => 'Card Selesai',
                'value' => $completedCards,
                'description' => "Dari {$totalCards} total card",
            ],
            [
                'label' => 'Anggota Aktif',
                'value' => $activeMembers,
                'description' => 'Sedang mengerjakan tugas',
            ],
            [
                'label' => 'Blocker Terbuka',
                'value' => $openBlockers,
                'description' => 'Perlu perhatian segera',
            ],
        ];

        $spotlightMembers = User::whereIn('role', ['team_lead', 'developer', 'designer'])
            ->withCount('projectMembers')
            ->orderByDesc('project_members_count')
            ->limit(5)
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->user_id,
                    'name' => $user->full_name,
                    'role' => $user->role,
                    'projects' => $user->project_members_count,
                    'status' => $user->current_task_status,
                ];
            });

        $recentCards = Card::with([
            'board.project:project_id,project_name',
            'assignments.user:user_id,full_name,role',
        ])
            ->withCount('comments')
            ->orderByDesc('card_id')
            ->limit(6)
            ->get()
            ->map(fn ($card) => CardPresenter::simple($card));

        return response()->json([
            'stats' => $stats,
            'projects' => $projectSummaries->take(5)->values(),
            'spotlight_members' => $spotlightMembers,
            'recent_cards' => $recentCards,
        ]);
    }

    public function monitoring()
    {
        $projects = Project::with([
            'boards.cards' => function ($query) {
                $query->select('card_id', 'board_id', 'status');
            },
            'members.user:user_id,full_name,role',
        ])->orderByDesc('created_at')->get();

        $recentCards = Card::with([
            'board.project:project_id,project_name',
            'assignments.user:user_id,full_name,role',
        ])
            ->withCount('comments')
            ->orderByDesc('card_id')
            ->limit(10)
            ->get()
            ->map(fn ($card) => CardPresenter::simple($card));

        return response()->json([
            'projects' => $projects->map(fn ($project) => ProjectPresenter::summarize($project)),
            'recent_cards' => $recentCards,
        ]);
    }

    public function users()
    {
        $users = User::whereIn('role', ['team_lead', 'developer', 'designer'])
            ->withCount('projectMembers')
            ->orderBy('role')
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->user_id,
                    'name' => $user->full_name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->current_task_status,
                    'projects' => $user->project_members_count,
                ];
            });

        return response()->json(['data' => $users]);
    }

    public function reports()
    {
        $userData = User::whereIn('role', ['team_lead', 'developer', 'designer'])
            ->select('role', 'current_task_status')
            ->get();

        $userStatus = [
            'labels' => ['Team Lead Working', 'Team Lead Idle', 'Developer Working', 'Developer Idle', 'Designer Working', 'Designer Idle'],
            'data' => [
                $userData->where('role', 'team_lead')->where('current_task_status', 'working')->count(),
                $userData->where('role', 'team_lead')->where('current_task_status', 'idle')->count(),
                $userData->where('role', 'developer')->where('current_task_status', 'working')->count(),
                $userData->where('role', 'developer')->where('current_task_status', 'idle')->count(),
                $userData->where('role', 'designer')->where('current_task_status', 'working')->count(),
                $userData->where('role', 'designer')->where('current_task_status', 'idle')->count(),
            ],
        ];

        $cards = Card::select('status')->get();
        $taskStatus = [
            'labels' => ['To Do', 'In Progress', 'Review', 'Done'],
            'data' => [
                $cards->where('status', 'todo')->count(),
                $cards->where('status', 'in_progress')->count(),
                $cards->where('status', 'review')->count(),
                $cards->where('status', 'done')->count(),
            ],
        ];

        $projects = Project::with('boards.cards')->get();
        $progressBuckets = [
            '0-25%' => 0,
            '26-50%' => 0,
            '51-75%' => 0,
            '76-100%' => 0,
        ];

        foreach ($projects as $project) {
            $summary = ProjectPresenter::summarize($project);
            $progress = (int) $summary['progress'];

            if ($progress <= 25) {
                $progressBuckets['0-25%']++;
            } elseif ($progress <= 50) {
                $progressBuckets['26-50%']++;
            } elseif ($progress <= 75) {
                $progressBuckets['51-75%']++;
            } else {
                $progressBuckets['76-100%']++;
            }
        }

        $projectStatus = [
            'labels' => array_keys($progressBuckets),
            'data' => array_values($progressBuckets),
        ];

        return response()->json([
            'user_status' => $userStatus,
            'task_status' => $taskStatus,
            'project_progress' => $projectStatus,
        ]);
    }
}
