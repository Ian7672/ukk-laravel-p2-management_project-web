<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Card;
use App\Models\Subtask;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman generate laporan
     */
    public function index()
    {
        $projects = Project::all();
        $users = User::whereIn('role', ['team_lead', 'developer', 'designer'])
            ->orderBy('role')
            ->orderBy('full_name')
            ->get();

        return view('admin.reports.index', compact('projects', 'users'));
    }

    /**
     * Generate laporan proyek
     */
    public function generateProjectReport(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,project_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'required|in:pdf'
        ]);

        $project = Project::with(['boards.cards.subtasks', 'members.user'])
            ->findOrFail($request->project_id);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Data statistik proyek
        $projectStats = $this->getProjectStats($project, $startDate, $endDate);
        
        // Data tim
        $teamData = $this->getTeamData($project);
        
        // Data progress
        $progressData = $this->getProgressData($project, $startDate, $endDate);

        return $this->renderProjectReport($project, $projectStats, $teamData, $progressData, $request->input('format'));
    }

    /**
     * Generate laporan tim
     */
    public function generateTeamReport(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'required|in:pdf'
        ]);

        $user = User::with(['projectMembers.project', 'assignments.card'])
            ->findOrFail($request->user_id);

        if ($request->filled('role') && $user->role !== $request->role) {
            return back()->withErrors([
                'user_id' => 'Anggota yang dipilih tidak sesuai dengan role yang difilter.',
            ])->withInput();
        }

        if ($request->filled('role') && $user->role !== $request->role) {
            return back()->withErrors([
                'user_id' => 'Anggota yang dipilih tidak sesuai dengan role yang difilter.',
            ])->withInput();
        }

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Data performa user
        $userStats = $this->getUserStats($user, $startDate, $endDate);
        
        // Data task completion
        $taskData = $this->getUserTaskData($user, $startDate, $endDate);

        return $this->renderUserReport($user, $userStats, $taskData, $request->input('format'));
    }

    /**
     * Generate laporan umum
     */
    public function generateGeneralReport(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'required|in:pdf'
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Data umum sistem
        $generalStats = $this->getGeneralStats($startDate, $endDate);
        
        // Data proyek aktif
        $activeProjects = $this->getActiveProjects($startDate, $endDate);
        
        // Data performa tim
        $teamPerformance = $this->getTeamPerformance($startDate, $endDate);

        return $this->renderGeneralReport($generalStats, $activeProjects, $teamPerformance, $request->input('format'));
    }

    /**
     * Get project statistics
     */
    private function getProjectStats($project, $startDate, $endDate)
    {
        $query = $project->boards()->withCount(['cards' => function($q) use ($startDate, $endDate) {
            if ($startDate) $q->where('created_at', '>=', $startDate);
            if ($endDate) $q->where('created_at', '<=', $endDate);
        }]);

        $totalCards = $project->boards->sum('cards_count');
        $completedCards = $project->boards->sum(function($board) {
            return $board->cards()->where('status', 'done')->count();
        });

        $totalSubtasks = $project->boards->sum(function($board) {
            return $board->cards->sum(function($card) {
                return $card->subtasks->count();
            });
        });

        $completedSubtasks = $project->boards->sum(function($board) {
            return $board->cards->sum(function($card) {
                return $card->subtasks()->where('status', 'done')->count();
            });
        });

        return [
            'total_cards' => $totalCards,
            'completed_cards' => $completedCards,
            'completion_rate' => $totalCards > 0 ? round(($completedCards / $totalCards) * 100, 2) : 0,
            'total_subtasks' => $totalSubtasks,
            'completed_subtasks' => $completedSubtasks,
            'subtask_completion_rate' => $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100, 2) : 0
        ];
    }

    /**
     * Get team data
     */
    private function getTeamData($project)
    {
        return $project->members()->with('user')->get()->map(function($member) use ($project) {
            $user = $member->user;
            $assignedCards = $user->assignments()->whereHas('card', function($q) use ($project) {
                $q->whereHas('board', function($bq) use ($project) {
                    $bq->where('project_id', $project->project_id);
                });
            })->count();

            $completedCards = $user->assignments()->whereHas('card', function($q) use ($project) {
                $q->whereHas('board', function($bq) use ($project) {
                    $bq->where('project_id', $project->project_id);
                })->where('status', 'done');
            })->count();

            return [
                'name' => $user->full_name,
                'role' => $member->role_in_project ?? $member->role,
                'assigned_cards' => $assignedCards,
                'completed_cards' => $completedCards,
                'completion_rate' => $assignedCards > 0 ? round(($completedCards / $assignedCards) * 100, 2) : 0
            ];
        });
    }

    /**
     * Get progress data
     */
    private function getProgressData($project, $startDate, $endDate)
    {
        $query = $project->boards()->with(['cards.subtasks']);
        
        if ($startDate) {
            $query->whereDate('assigned_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('assigned_at', '<=', $endDate);
        }

        $boards = $query->get();
        
        return $boards->map(function($board) {
            $totalCards = $board->cards->count();
            $completedCards = $board->cards->where('status', 'done')->count();
            
            return [
                'board_name' => $board->board_name,
                'total_cards' => $totalCards,
                'completed_cards' => $completedCards,
                'completion_rate' => $totalCards > 0 ? round(($completedCards / $totalCards) * 100, 2) : 0
            ];
        });
    }

    /**
     * Get user statistics
     */
    private function getUserStats($user, $startDate, $endDate)
    {
        $query = $user->assignments()->with('card');
        
        if ($startDate) {
            $query->whereDate('assigned_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('assigned_at', '<=', $endDate);
        }

        $assignments = $query->get();
        $totalTasks = $assignments->count();
        $completedTasks = $assignments->where('card.status', 'done')->count();

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
            'current_status' => $user->current_task_status
        ];
    }

    /**
     * Get user task data
     */
    private function getUserTaskData($user, $startDate, $endDate)
    {
        $query = $user->assignments()->with(['card.subtasks']);
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->get()->map(function($assignment) {
            $card = $assignment->card;
            $totalSubtasks = $card->subtasks->count();
            $completedSubtasks = $card->subtasks->where('status', 'done')->count();
            
            return [
                'card_title' => $card->card_title,
                'status' => $card->status,
                'total_subtasks' => $totalSubtasks,
                'completed_subtasks' => $completedSubtasks,
                'subtask_completion_rate' => $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100, 2) : 0
            ];
        });
    }

    /**
     * Get general statistics
     */
    private function getGeneralStats($startDate, $endDate)
    {
        $query = Project::query();
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $totalProjects = $query->count();
        $activeProjects = $totalProjects; // Semua proyek dianggap aktif karena tidak ada kolom status
        $totalUsers = User::whereIn('role', ['team_lead', 'developer', 'designer'])->count();
        $totalCards = Card::count();
        $completedCards = Card::where('status', 'done')->count();

        return [
            'total_projects' => $totalProjects,
            'active_projects' => $activeProjects,
            'total_users' => $totalUsers,
            'total_cards' => $totalCards,
            'completed_cards' => $completedCards,
            'overall_completion_rate' => $totalCards > 0 ? round(($completedCards / $totalCards) * 100, 2) : 0
        ];
    }

    /**
     * Get active projects
     */
    private function getActiveProjects($startDate, $endDate)
    {
        $query = Project::with(['boards.cards']); // Semua proyek dianggap aktif
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->get()->map(function($project) {
            $totalCards = $project->boards->sum(function($board) {
                return $board->cards->count();
            });
            $completedCards = $project->boards->sum(function($board) {
                return $board->cards->where('status', 'done')->count();
            });
            
            return [
                'project_name' => $project->project_name,
                'total_cards' => $totalCards,
                'completed_cards' => $completedCards,
                'completion_rate' => $totalCards > 0 ? round(($completedCards / $totalCards) * 100, 2) : 0
            ];
        });
    }

    /**
     * Get team performance
     */
    private function getTeamPerformance($startDate, $endDate)
    {
        $query = User::with(['assignments.card'])->whereIn('role', ['team_lead', 'developer', 'designer']);
        
        if ($startDate) {
            $query->whereHas('assignments', function($q) use ($startDate) {
                $q->whereDate('assigned_at', '>=', $startDate);
            });
        }
        if ($endDate) {
            $query->whereHas('assignments', function($q) use ($endDate) {
                $q->whereDate('assigned_at', '<=', $endDate);
            });
        }

        return $query->get()->map(function($user) {
            $assignments = $user->assignments;
            $totalTasks = $assignments->count();
            $completedTasks = $assignments->where('card.status', 'done')->count();
            
            return [
                'name' => $user->full_name,
                'role' => $user->role,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0
            ];
        });
    }

    private function renderProjectReport($project, $projectStats, $teamData, $progressData, $format)
    {
        $view = 'admin.reports.project-pdf';

        return view($view, [
            'project' => $project,
            'projectStats' => $projectStats,
            'teamData' => $teamData,
            'progressData' => $progressData,
            'format' => $format,
        ]);
    }

    private function renderUserReport($user, $userStats, $taskData, $format)
    {
        $view = 'admin.reports.user-pdf';

        return view($view, [
            'user' => $user,
            'userStats' => $userStats,
            'taskData' => $taskData,
            'format' => $format,
        ]);
    }

    private function renderGeneralReport($generalStats, $activeProjects, $teamPerformance, $format)
    {
        $view = 'admin.reports.general-pdf';

        return view($view, [
            'generalStats' => $generalStats,
            'activeProjects' => $activeProjects,
            'teamPerformance' => $teamPerformance,
            'format' => $format,
        ]);
    }
}
