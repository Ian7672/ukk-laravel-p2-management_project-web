<?php

namespace App\Support;

use App\Models\Card;
use Illuminate\Support\Carbon;

class CardPresenter
{
    public static function simple(Card $card): array
    {
        return [
            'id' => $card->card_id,
            'title' => $card->card_title,
            'status' => $card->status,
            'priority' => $card->priority,
            'description' => $card->description,
            'due_date' => self::formatDate($card->due_date),
            'project' => $card->board && $card->board->project ? [
                'id' => $card->board->project->project_id,
                'name' => $card->board->project->project_name,
            ] : null,
            'board' => $card->board ? [
                'id' => $card->board->board_id,
                'name' => $card->board->board_name,
            ] : null,
            'assignments' => $card->assignments->map(function ($assignment) {
                if (!$assignment->user) {
                    return null;
                }

                return [
                    'id' => $assignment->user->user_id,
                    'name' => $assignment->user->full_name,
                    'role' => $assignment->user->role,
                    'status' => $assignment->assignment_status,
                ];
            })->filter()->values(),
            'comments_count' => $card->comments_count ?? 0,
        ];
    }

    public static function detailed(Card $card): array
    {
        $payload = self::simple($card);
        $payload['subtasks'] = $card->subtasks->map(function ($subtask) {
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
                        'status' => $blocker->status,
                        'priority' => $blocker->priority,
                        'requested_by' => $blocker->user ? [
                            'id' => $blocker->user->user_id,
                            'name' => $blocker->user->full_name,
                        ] : null,
                        'assigned_to' => $blocker->assignedTo ? [
                            'id' => $blocker->assignedTo->user_id,
                            'name' => $blocker->assignedTo->full_name,
                        ] : null,
                        'solution' => $blocker->solution,
                        'resolved_at' => self::formatDate($blocker->resolved_at),
                        'rejected_at' => self::formatDate($blocker->rejected_at),
                    ];
                }),
            ];
        });

        return $payload;
    }

    private static function formatDate($value): ?string
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
