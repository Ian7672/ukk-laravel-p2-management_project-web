<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Support\CardPresenter;
use Illuminate\Http\JsonResponse;

class CardApiController extends Controller
{
    /**
     * Return the latest cards for dashboard widgets.
     */
    public function recent(): JsonResponse
    {
        $cards = Card::with([
            'board.project:project_id,project_name',
            'assignments.user:user_id,full_name,role',
        ])
            ->withCount('comments')
            ->orderByDesc('card_id')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $cards->map(fn (Card $card) => CardPresenter::simple($card)),
        ]);
    }

    /**
     * Return a detailed card payload (used by mobile detail view).
     */
    public function show(Card $card): JsonResponse
    {
        $card->load([
            'board.project:project_id,project_name',
            'assignments.user:user_id,full_name,role',
            'subtasks.blockers.user:user_id,full_name,role',
            'subtasks.blockers.assignedTo:user_id,full_name,role',
        ])->loadCount('comments');

        return response()->json([
            'data' => CardPresenter::detailed($card),
        ]);
    }
}
