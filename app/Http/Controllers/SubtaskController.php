<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Card;
use App\Models\TimeLog;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubtaskController extends Controller
{
    /**
     * Show form create subtask
     */
    public function create(Card $card)
    {
        $role = Auth::user()->role;

        $project = $this->projectFromCard($card);
        if ($this->isProjectCompleted($project)) {
            return back()->with('error', 'Proyek ini sudah selesai, subtask baru tidak dapat dibuat.');
        }

        if ($role === 'developer') {
            return view('developer.subtasks.create', compact('card'));
        }

        if ($role === 'designer') {
            return view('designer.subtasks.create', compact('card'));
        }

        abort(403, 'Role tidak diizinkan');
    }

    /**
     * Store new subtask
     */
    public function store(Request $request, Card $card)
    {
        $project = $this->projectFromCard($card);
        if ($this->isProjectCompleted($project)) {
            return back()->with('error', 'Proyek ini sudah selesai, subtask baru tidak dapat dibuat.');
        }

        $request->validate([
            'subtask_title'   => 'required|string|max:150',
            'description'     => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        Subtask::create([
            'card_id'        => $card->card_id,
            'subtask_title'  => $request->subtask_title,
            'description'    => $request->description,
            'estimated_hours'=> $request->estimated_hours,
            'actual_hours'   => 0,
            'status'         => 'todo',
            'position'       => 1,
            'created_at'     => Carbon::now('Asia/Jakarta'),
        ]);

        return back()->with('success', 'Subtask berhasil dibuat');
    }

    /**
     * Start subtask
     */
    public function start(Subtask $subtask)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['developer', 'designer'])) {
            abort(403, 'Hanya developer/designer yang boleh mulai subtask');
        }

        $project = $this->projectFromSubtask($subtask);
        if ($this->isProjectCompleted($project)) {
            return back()->with('error', 'Proyek ini sudah selesai, subtask tidak dapat dimulai.');
        }

        $subtask->update(['status' => 'in_progress']);

        TimeLog::create([
            'card_id'    => $subtask->card_id,
            'subtask_id' => $subtask->subtask_id,
            'user_id'    => $user->user_id,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'end_time'   => null,
            'duration_minutes' => null,
            'description'=> '🚀 Mulai subtask'
        ]);

        $card = $subtask->card;
        $inProgressBoard = Board::where('project_id', $card->board->project_id ?? null)
            ->where('board_name', 'In Progress')
            ->first();

        if ($inProgressBoard) {
            $card->update(['status' => 'in_progress', 'board_id' => $inProgressBoard->board_id]);
        }

        return back()->with('success', '🚀 Subtask dimulai');
    }

    /**
     * Complete subtask -> status review
     */
    public function complete(Subtask $subtask)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['developer', 'designer'])) {
            abort(403, 'Hanya developer/designer yang boleh menyelesaikan subtask');
        }

        $project = $this->projectFromSubtask($subtask);
        if ($this->isProjectCompleted($project)) {
            return back()->with('error', 'Proyek ini sudah selesai, subtask tidak dapat diperbarui.');
        }

        // Tutup log aktif
        $log = TimeLog::where('subtask_id', $subtask->subtask_id)
            ->where('user_id', $user->user_id)
            ->whereNull('end_time')
            ->latest('start_time')
            ->first();

        if ($log) {
            $end = Carbon::now('Asia/Jakarta');
            $start = $log->start_time instanceof Carbon 
                ? $log->start_time 
                : Carbon::parse($log->start_time, 'Asia/Jakarta');
            $minutes = $end->diffInMinutes($start);

            $log->update([
                'end_time' => $end,
                'duration_minutes' => $minutes,
            ]);
        }

        // Hitung ulang actual_hours dari semua log
        $totalMinutes = TimeLog::where('subtask_id', $subtask->subtask_id)->sum('duration_minutes');

        $subtask->update([
            'status' => 'review',
            'actual_hours' => round($totalMinutes / 60, 2),
        ]);

        $card = $subtask->card;
        $reviewBoard = Board::where('project_id', $card->board->project_id ?? null)
            ->where('board_name', 'Review')
            ->first();

        if ($reviewBoard) {
            $card->update(['status' => 'review', 'board_id' => $reviewBoard->board_id]);
        }

        return back()->with('success', 'Subtask selesai. Menunggu approval Team Lead');
    }

    /**
     * Approve subtask -> hanya Team Lead
     */
    public function approve(Subtask $subtask)
    {
        $user = Auth::user();
        if ($user->role !== 'team_lead') {
            abort(403, 'Hanya Team Lead yang boleh approve subtask');
        }

        $project = $this->projectFromSubtask($subtask);
        if ($this->isProjectCompleted($project)) {
            return back()->with('error', 'Proyek ini sudah selesai, tidak ada subtask yang perlu diproses.');
        }

        $subtask->update([
            'status' => 'done',
            'reject_reason' => null,
        ]);

        $card = $subtask->card;
        $unfinished = $card->subtasks()->where('status', '!=', 'done')->count();

        if ($unfinished == 0) {
            $doneBoard = Board::where('project_id', $card->board->project_id ?? null)
                ->where('board_name', 'Done')
                ->first();

            if ($doneBoard) {
                $card->update(['status' => 'done', 'board_id' => $doneBoard->board_id]);
            } else {
                $card->update(['status' => 'done']);
            }
        }

        return back()->with('success', '☑️ Subtask disetujui & card selesai');
    }

    /**
     * Reject subtask -> hanya Team Lead
     */
    public function reject(Request $request, Subtask $subtask)
    {
        $user = Auth::user();
        if ($user->role !== 'team_lead') {
            abort(403, 'Hanya Team Lead yang boleh reject subtask');
        }

        $project = $this->projectFromSubtask($subtask);
        if ($this->isProjectCompleted($project)) {
            return back()->with('error', 'Proyek ini sudah selesai, tidak ada subtask yang perlu diproses.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $subtask->update([
            'status' => 'in_progress',
            'reject_reason' => $request->reason,
        ]);

        $log = TimeLog::where('subtask_id', $subtask->subtask_id)
            ->whereNull('end_time')
            ->latest('start_time')
            ->first();

        if ($log) {
            $end = Carbon::now('Asia/Jakarta');
            $start = $log->start_time instanceof Carbon 
                ? $log->start_time 
                : Carbon::parse($log->start_time, 'Asia/Jakarta');
            $minutes = $end->diffInMinutes($start);

            $log->update([
                'end_time' => $end,
                'duration_minutes' => $minutes,
                'description' => "❌ Ulangi by Team Lead: {$request->reason}",
            ]);
        } else {
            TimeLog::create([
                'card_id'    => $subtask->card_id,
                'subtask_id' => $subtask->subtask_id,
                'user_id'    => $subtask->card->assignments->first()->user_id ?? null,
                'start_time' => Carbon::now('Asia/Jakarta'),
                'end_time'   => Carbon::now('Asia/Jakarta'),
                'duration_minutes' => 0,
                'description' => "❌ Ulangi by Team Lead: {$request->reason}",
            ]);
        }

        TimeLog::create([
            'card_id'    => $subtask->card_id,
            'subtask_id' => $subtask->subtask_id,
            'user_id'    => $subtask->card->assignments->first()->user_id ?? null,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'end_time'   => null,
            'duration_minutes' => null,
            'description' => "🔄 Rework setelah reject",
        ]);

        $totalMinutes = TimeLog::where('subtask_id', $subtask->subtask_id)->sum('duration_minutes');

        $subtask->update([
            'actual_hours' => round($totalMinutes / 60, 2),
        ]);

        $card = $subtask->card;
        $inProgressBoard = Board::where('project_id', $card->board->project_id ?? null)
            ->where('board_name', 'In Progress')
            ->first();

        if ($inProgressBoard) {
            $card->update(['status' => 'in_progress', 'board_id' => $inProgressBoard->board_id]);
        }

        return back()->with('success', '❌ Subtask direject & otomatis masuk ke sesi Rework');
    }
    protected function projectFromCard(?Card $card)
    {
        if (!$card) {
            return null;
        }

        $card->loadMissing('board.project');

        return optional($card->board)->project;
    }

    protected function projectFromSubtask(Subtask $subtask)
    {
        $subtask->loadMissing('card.board.project');

        return optional(optional($subtask->card)->board)->project;
    }

    protected function isProjectCompleted($project): bool
    {
        return $project && $project->status === 'selesai';
    }

}
