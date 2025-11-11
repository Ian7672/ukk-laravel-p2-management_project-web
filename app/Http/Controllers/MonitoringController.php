<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Board;
use App\Models\Card;
use App\Models\User;
use App\Models\Subtask;

class MonitoringController extends Controller
{
    /**
     * Daftar semua project untuk admin (atau project yang dipimpin user non-admin)
     */
    public function index()
    {
        $user = auth()->user();

        // Ambil semua users untuk form tambah anggota (hanya admin yang butuh)
        $users = User::whereIn('role', ['team_lead', 'developer', 'designer'])->get();
        $idleAssignableUsers = User::whereIn('role', ['developer', 'designer'])
            ->where('current_task_status', 'idle')
            ->select('user_id', 'username', 'full_name', 'role')
            ->orderBy('full_name')
            ->orderBy('username')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->user_id,
                    'role' => $user->role,
                    'name' => $user->full_name ?: $user->username,
                    'username' => $user->username,
                ];
            })->values();

        // Ambil proyek sesuai role
        if ($user->role === 'admin') {
            $projects = Project::with([
                'members.user:user_id,username,full_name,role',
                'boards.cards.subtasks',
                'boards.cards.assignments'
            ])->get();
        } else {
            $projects = Project::whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->user_id);
            })->with([
                'members.user:user_id,username,full_name,role',
                'boards.cards.subtasks',
                'boards.cards.assignments'
            ])->get();
        }

        // Inisialisasi global
        $totalProjects = $projects->count();
        $globalCards = 0;
        $globalCardsDone = 0;
        $globalSubtasks = 0;
        $globalSubtasksDone = 0;
        $totalProgress = 0;

        // Data untuk chart - TAMBAHKAN INISIALISASI DEFAULT JIKA METHOD TIDAK ADA
        $userStatusData = method_exists($this, 'getUserStatusData') ? $this->getUserStatusData() : $this->defaultUserStatusData();
        $userStatusDataSimple = method_exists($this, 'getUserStatusDataSimple') ? $this->getUserStatusDataSimple() : $this->defaultUserStatusDataSimple();
        $taskStatusData = method_exists($this, 'getTaskStatusData') ? $this->getTaskStatusData() : $this->defaultTaskStatusData();
        $projectStatusData = method_exists($this, 'getProjectStatusData') ? $this->getProjectStatusData() : $this->defaultProjectStatusData();

        foreach ($projects as $project) {
            $cards = $project->boards->flatMap->cards;
            $subtasks = $cards->flatMap->subtasks;

            // Total card dan subtask
            $project->cards_total = $cards->count();
            $project->cards_done = $cards->filter(function ($card) {
                return strtolower($card->status ?? '') === 'done';
            })->count();
            $project->subtasks_total = $subtasks->count();
            $project->subtasks_done = $subtasks->filter(function ($subtask) {
                return strtolower($subtask->status ?? '') === 'done';
            })->count();

            // Hitung progress (prioritas subtask > card)
            if ($project->subtasks_total > 0) {
                $progress = round(($project->subtasks_done / $project->subtasks_total) * 100);
            } elseif ($project->cards_total > 0) {
                $progress = round(($project->cards_done / $project->cards_total) * 100);
            } else {
                $progress = 0;
            }
            $project->progress = $progress;

            // 🎨 Badge warna & status berdasarkan progress
            if ($progress < 30) {
                $project->status_color = 'danger';
                $project->progress_status = 'Low Progress';
            } elseif ($progress < 70) {
                $project->status_color = 'warning';
                $project->progress_status = 'In Progress';
            } elseif ($progress < 100) {
                $project->status_color = 'info';
                $project->progress_status = 'Almost Done';
            } else {
                $project->status_color = 'success';
                $project->progress_status = 'Completed';
            }

            // Hitung jumlah per status untuk tooltip
            $statusCounts = [
                'todo' => 0,
                'in_progress' => 0,
                'review' => 0,
                'done' => 0
            ];

            foreach ($project->boards as $board) {
                foreach ($board->cards as $card) {
                    $status = strtolower(str_replace(' ', '_', $card->status ?? 'todo'));
                    if (isset($statusCounts[$status])) {
                        $statusCounts[$status]++;
                    }
                }
            }

            $project->todo_count = $statusCounts['todo'];
            $project->in_progress_count = $statusCounts['in_progress'];
            $project->review_count = $statusCounts['review'];
            $project->done_count = $statusCounts['done'];

            // Hitung progres setiap anggota berdasarkan card & subtask yang ditugaskan
            $projectMembers = $project->members->map(function ($member) use ($cards, $project) {
                if ($member->relationLoaded('user') && $member->user) {
                    $member->user->makeVisible(['user_id', 'username', 'full_name', 'role']);
                }

                $userId = $member->user_id;

                $assignedCards = $cards->filter(function ($card) use ($userId) {
                    return $card->assignments->contains(function ($assignment) use ($userId) {
                        return (int) $assignment->user_id === (int) $userId;
                    });
                });

                $assignedSubtasks = $assignedCards->flatMap->subtasks;

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
                    default => 'Belum Ada Tugas',
                };

                $statusColor = match (true) {
                    $progress >= 100 => 'success',
                    $progress >= 70 => 'info',
                    $progress >= 40 => 'warning',
                    $progress > 0 => 'primary',
                    default => 'secondary',
                };

                $member->setAttribute('progress_summary', [
                    'progress_percent' => $progress,
                    'status_label' => $statusLabel,
                    'status_color' => $statusColor,
                    'subtasks_total' => $subtasksTotal,
                    'subtasks_done' => $subtasksDone,
                    'cards_total' => $cardsTotal,
                    'cards_done' => $cardsDone,
                ]);

                return $member;
            });

            $project->setRelation('members', $projectMembers);

            // Akumulasi global
            $globalCards += $project->cards_total;
            $globalCardsDone += $project->cards_done;
            $globalSubtasks += $project->subtasks_total;
            $globalSubtasksDone += $project->subtasks_done;
            $totalProgress += $progress;
        }

        // Summary global
        $avgProgress = $totalProjects > 0 ? round($totalProgress / $totalProjects) : 0;
        $cardsProgress = $globalCards > 0 ? round(($globalCardsDone / $globalCards) * 100) : 0;
        $subProgress = $globalSubtasks > 0 ? round(($globalSubtasksDone / $globalSubtasks) * 100) : 0;

        // Hitung user working/idle
        $workingUsers = User::whereIn('role', ['developer', 'designer'])
            ->where('current_task_status', 'working')
            ->count();

        $idleUsers = $idleAssignableUsers->count();

        return view('admin.monitoring.index', compact(
            'projects',
            'totalProjects',
            'globalCards',
            'globalCardsDone',
            'globalSubtasks',
            'globalSubtasksDone',
            'avgProgress',
            'cardsProgress',
            'subProgress',
            'workingUsers',
            'idleUsers',
            'users',
            'idleAssignableUsers',
            'userStatusData',
            'userStatusDataSimple',
            'taskStatusData',
            'projectStatusData'
        ));
    }

    /**
     * Data untuk chart status user (developer & designer)
     */
    private function getUserStatusData()
    {
        $developers = User::where('role', 'developer')->get();
        $designers = User::where('role', 'designer')->get();
        $teamLeads = User::where('role', 'team_lead')->get();

        $developerWorking = $developers->where('current_task_status', 'working')->count();
        $developerIdle = $developers->where('current_task_status', 'idle')->count();
        
        $designerWorking = $designers->where('current_task_status', 'working')->count();
        $designerIdle = $designers->where('current_task_status', 'idle')->count();
        
        $teamLeadWorking = $teamLeads->where('current_task_status', 'working')->count();
        $teamLeadIdle = $teamLeads->where('current_task_status', 'idle')->count();

        return [
            'labels' => ['Developer Working', 'Developer Idle', 'Designer Working', 'Designer Idle', 'Team Lead Working', 'Team Lead Idle'],
            'data' => [
                $developerWorking,
                $developerIdle,
                $designerWorking,
                $designerIdle,
                $teamLeadWorking,
                $teamLeadIdle
            ],
            'backgroundColor' => [
                'rgba(59, 130, 246, 0.8)',    // Developer Working - blue
                'rgba(59, 130, 246, 0.4)',    // Developer Idle - light blue
                'rgba(139, 92, 246, 0.8)',    // Designer Working - purple
                'rgba(139, 92, 246, 0.4)',    // Designer Idle - light purple
                'rgba(16, 185, 129, 0.8)',    // Team Lead Working - green
                'rgba(16, 185, 129, 0.4)'     // Team Lead Idle - light green
            ]
        ];
    }

    /**
     * Data untuk chart status user (simple - hanya working/idle)
     */
    private function getUserStatusDataSimple()
    {
        $workingUsers = User::whereIn('role', ['developer', 'designer', 'team_lead'])
            ->where('current_task_status', 'working')
            ->count();
        
        $idleUsers = User::whereIn('role', ['developer', 'designer', 'team_lead'])
            ->where('current_task_status', 'idle')
            ->count();

        return [
            'labels' => ['Working', 'Idle'],
            'data' => [$workingUsers, $idleUsers],
            'backgroundColor' => [
                'rgba(16, 185, 129, 0.8)',    // Working - green
                'rgba(107, 114, 128, 0.8)'    // Idle - gray
            ]
        ];
    }

    /**
     * Data untuk chart status tugas
     */
    private function getTaskStatusData()
    {
        $todoCount = Card::where('status', 'todo')->count();
        $inProgressCount = Card::where('status', 'in_progress')->count();
        $reviewCount = Card::where('status', 'review')->count();
        $doneCount = Card::where('status', 'done')->count();

        return [
            'labels' => ['To Do', 'In Progress', 'Review', 'Done'],
            'data' => [$todoCount, $inProgressCount, $reviewCount, $doneCount],
            'backgroundColor' => [
                'rgba(107, 114, 128, 0.8)',   // To Do - gray
                'rgba(251, 191, 36, 0.8)',    // In Progress - yellow
                'rgba(59, 130, 246, 0.8)',    // Review - blue
                'rgba(16, 185, 129, 0.8)'     // Done - green
            ]
        ];
    }

    /**
     * Data untuk chart distribusi status proyek
     */
    private function getProjectStatusData()
    {
        $projects = Project::with(['boards.cards.subtasks'])->get();
        
        $completed = 0;
        $inProgress = 0;
        $notStarted = 0;
        $delayed = 0;

        foreach ($projects as $project) {
            $cards = $project->boards->flatMap->cards;
            $subtasks = $cards->flatMap->subtasks;

            // Hitung progress
            if ($subtasks->count() > 0) {
                $progress = round(($subtasks->where('status', 'done')->count() / $subtasks->count()) * 100);
            } elseif ($cards->count() > 0) {
                $progress = round(($cards->where('status', 'done')->count() / $cards->count()) * 100);
            } else {
                $progress = 0;
            }

            // Kategorikan berdasarkan progress
            if ($progress == 100) {
                $completed++;
            } elseif ($progress >= 30 && $progress < 100) {
                $inProgress++;
            } elseif ($progress > 0 && $progress < 30) {
                $notStarted++;
            } else {
                $delayed++;
            }
        }

        return [
            'labels' => ['Selesai', 'Dalam Progress', 'Belum Dimulai', 'Tertunda'],
            'data' => [$completed, $inProgress, $notStarted, $delayed],
            'backgroundColor' => [
                'rgba(16, 185, 129, 0.8)',    // Selesai - green
                'rgba(59, 130, 246, 0.8)',    // Dalam Progress - blue
                'rgba(107, 114, 128, 0.8)',   // Belum Dimulai - gray
                'rgba(251, 191, 36, 0.8)'     // Tertunda - yellow
            ]
        ];
    }

    /**
     * Default data untuk chart status user simple (jika method asli tidak ada)
     */
    private function defaultUserStatusDataSimple()
    {
        return [
            'labels' => ['Working', 'Idle'],
            'data' => [8, 5],
            'backgroundColor' => [
                'rgba(16, 185, 129, 0.8)',
                'rgba(107, 114, 128, 0.8)'
            ]
        ];
    }

    /**
     * Default data untuk chart status user (jika method asli tidak ada)
     */
    private function defaultUserStatusData()
    {
        return [
            'labels' => ['Developer Working', 'Developer Idle', 'Designer Working', 'Designer Idle', 'Team Lead Working', 'Team Lead Idle'],
            'data' => [5, 3, 4, 2, 2, 1],
            'backgroundColor' => [
                'rgba(59, 130, 246, 0.8)',
                'rgba(59, 130, 246, 0.4)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(139, 92, 246, 0.4)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(16, 185, 129, 0.4)'
            ]
        ];
    }

    /**
     * Default data untuk chart status tugas (jika method asli tidak ada)
     */
    private function defaultTaskStatusData()
    {
        return [
            'labels' => ['To Do', 'In Progress', 'Review', 'Done'],
            'data' => [15, 22, 8, 30],
            'backgroundColor' => [
                'rgba(107, 114, 128, 0.8)',
                'rgba(251, 191, 36, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)'
            ]
        ];
    }

    /**
     * Default data untuk chart distribusi status proyek (jika method asli tidak ada)
     */
    private function defaultProjectStatusData()
    {
        return [
            'labels' => ['Selesai', 'Dalam Progress', 'Belum Dimulai', 'Tertunda'],
            'data' => [40, 30, 20, 10],
            'backgroundColor' => [
                'rgba(16, 185, 129, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(107, 114, 128, 0.8)',
                'rgba(251, 191, 36, 0.8)'
            ]
        ];
    }

    /**
     * Detail project → tampilkan semua boards, cards, dan members
     */
    public function show(Project $project)
    {
        $this->authorizeProject($project);

        // Tambahkan users untuk form tambah anggota
        $users = User::whereIn('role', ['team_lead', 'developer', 'designer'])->get();

        // Muat semua relasi penting
        $project->load([
            'boards.cards.subtasks',
            'boards.cards.assignments.user',
            'boards.cards.comments.user',
            'members.user'
        ]);

        return view('admin.monitoring.show', compact('project', 'users'));
    }

    /**
     * Tampilkan semua cards dalam 1 board
     */
    public function board(Project $project, Board $board)
    {
        $this->authorizeProject($project);

        $board->load([
            'cards.subtasks',
            'cards.assignments.user',
            'cards.comments.user'
        ]);

        return view('admin.monitoring.board', compact('project', 'board'));
    }

    /**
     * Detail satu card (dengan subtasks dan anggota)
     */
    public function card($projectId, $cardId)
    {
        $project = Project::with('boards.cards')->findOrFail($projectId);

        $card = Card::with([
            'subtasks',
            'assignments.user',
            'comments.user'
        ])->findOrFail($cardId);

        return view('admin.monitoring.card', compact('project', 'card'));
    }

    /**
     * Cek apakah user login punya izin melihat project
     * Admin global selalu lolos.
     */
    private function authorizeProject(Project $project)
    {
        $user = auth()->user();

        // Admin global boleh semua
        if ($user->role === 'admin') {
            return true;
        }

        // Pastikan dia adalah anggota project
        $isMember = $project->members()
            ->where('user_id', $user->user_id)
            ->exists();

        if (!$isMember) {
            abort(403, 'Anda tidak memiliki akses ke project ini.');
        }
    }
}

