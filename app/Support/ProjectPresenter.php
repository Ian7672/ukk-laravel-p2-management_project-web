<?php

namespace App\Support;

use App\Models\Project;
use Illuminate\Support\Carbon;

class ProjectPresenter
{
    public static function summarize(Project $project): array
    {
        $cards = $project->boards->flatMap(fn ($board) => $board->cards);
        $totalCards = $cards->count();
        $completedCards = $cards->where('status', 'done')->count();
        $progress = $totalCards > 0 ? round($completedCards / $totalCards * 100, 1) : 0;

        $teamLead = $project->members
            ->firstWhere('role', 'team_lead');

        return [
            'id' => $project->project_id,
            'name' => $project->project_name,
            'description' => $project->description,
            'status' => $project->status,
            'deadline' => self::formatDate($project->deadline),
            'created_at' => self::formatDate($project->created_at),
            'total_boards' => $project->boards->count(),
            'total_cards' => $totalCards,
            'completed_cards' => $completedCards,
            'progress' => $progress,
            'team_lead' => $teamLead && $teamLead->user ? [
                'id' => $teamLead->user->user_id,
                'name' => $teamLead->user->full_name,
            ] : null,
            'members_preview' => $project->members
                ->take(4)
                ->map(function ($member) {
                    if (!$member->user) {
                        return null;
                    }

                    return [
                        'id' => $member->user->user_id,
                        'name' => $member->user->full_name,
                        'role' => $member->role ?? $member->user->role,
                    ];
                })
                ->filter()
                ->values(),
            'total_members' => $project->members->count(),
        ];
    }

    public static function detail(Project $project): array
    {
        return [
            'id' => $project->project_id,
            'name' => $project->project_name,
            'description' => $project->description,
            'status' => $project->status,
            'deadline' => self::formatDate($project->deadline),
            'created_at' => self::formatDate($project->created_at),
            'members' => $project->members
                ->map(function ($member) {
                    if (!$member->user) {
                        return null;
                    }

                    return [
                        'id' => $member->user->user_id,
                        'name' => $member->user->full_name,
                        'role' => $member->role ?? $member->user->role,
                    ];
                })
                ->filter()
                ->values(),
            'boards' => $project->boards->map(function ($board) {
                return [
                    'id' => $board->board_id,
                    'name' => $board->board_name,
                    'position' => $board->position,
                    'cards' => $board->cards->map(function ($card) {
                        return [
                            'id' => $card->card_id,
                            'title' => $card->card_title,
                            'status' => $card->status,
                            'priority' => $card->priority,
                            'description' => $card->description,
                            'due_date' => self::formatDate($card->due_date),
                            'estimated_hours' => $card->estimated_hours,
                            'actual_hours' => $card->actual_hours,
                            'comments_count' => $card->comments_count ?? 0,
                            'assignments' => $card->assignments->map(function ($assignment) {
                                if (!$assignment->user) {
                                    return null;
                                }

                                return [
                                    'id' => $assignment->user->user_id,
                                    'name' => $assignment->user->full_name,
                                    'role' => $assignment->user->role,
                                    'status' => $assignment->assignment_status,
                                    'started_at' => self::formatDate($assignment->started_at),
                                    'completed_at' => self::formatDate($assignment->completed_at),
                                ];
                            })->filter()->values(),
                            'subtasks' => $card->subtasks->map(function ($subtask) {
                                return [
                                    'id' => $subtask->subtask_id,
                                    'title' => $subtask->subtask_title,
                                    'status' => $subtask->status,
                                    'description' => $subtask->description,
                                    'position' => $subtask->position,
                                    'estimated_hours' => $subtask->estimated_hours,
                                    'actual_hours' => $subtask->actual_hours,
                                    'blockers' => $subtask->blockers->map(function ($blocker) {
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
                                            'solution' => $blocker->solution,
                                            'resolved_at' => self::formatDate($blocker->resolved_at),
                                            'rejected_at' => self::formatDate($blocker->rejected_at),
                                        ];
                                    }),
                                ];
                            }),
                        ];
                    }),
                ];
            }),
        ];
    }

    public static function formatDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->toIso8601String();
        }

        return Carbon::parse($value)->toIso8601String();
    }
}
