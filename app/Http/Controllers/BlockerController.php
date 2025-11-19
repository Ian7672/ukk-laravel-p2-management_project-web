<?php

namespace App\Http\Controllers;

use App\Models\Blocker;
use App\Models\Card;
use App\Models\Subtask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class BlockerController extends Controller
{
    /**
     * Tampilkan daftar blocker untuk developer/designer
     */
    public function index()
    {
        $user = Auth::user();
        $supportsSubtask = $this->supportsSubtaskSchema();

        $query = Blocker::query()
            ->where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc');

        if ($supportsSubtask) {
            $blockers = $query->with(['subtask.card.board.project'])->get();
        } else {
            $blockers = $query->get();
            $cardMap = Card::with(['board.project'])
                ->whereIn('card_id', $blockers->pluck('card_id')->filter()->unique())
                ->get()
                ->keyBy('card_id');

            $blockers->each(function ($blocker) use ($cardMap) {
                $blocker->legacy_card = $cardMap->get($blocker->card_id);
            });
        }

        return view('blocker.index', compact('blockers', 'supportsSubtask'));
    }

    /**
     * Tampilkan form create blocker
     */
    public function create()
{
    if (!$this->supportsSubtaskSchema()) {
        return redirect()->route('blocker.index')
            ->with('error', 'Struktur data blocker lama masih menggunakan card_id. Jalankan migrasi untuk mengaktifkan pelaporan berdasarkan subtask.');
    }

    $user = Auth::user();

    // Ambil semua card aktif milik user yang punya subtask belum selesai
    $cards = Card::whereHas('assignments', function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        })
        ->where('status', '!=', 'done')
        ->whereHas('subtasks', function ($query) {
            $query->where('status', '!=', 'done');
        })
        ->with([
            'board.project',
            'subtasks' => function ($query) {
                $query->where('status', '!=', 'done')->orderBy('subtask_title');
            },
        ])
        ->orderBy('card_title')
        ->get()
        // 🔹 Encode subtasks ke bentuk JSON-ready agar bisa langsung dipakai di Blade
        ->map(function ($card) {
            $card->encoded_subtasks = $card->subtasks->map(function ($subtask) {
                return [
                    'id' => $subtask->subtask_id,
                    'title' => $subtask->subtask_title,
                    'status' => $subtask->status,
                    'estimated' => $subtask->estimated_hours,
                ];
            })->values();
            return $card;
        });

    return view('blocker.create', compact('cards'));
}


    /**
     * Simpan blocker baru
     */
    public function store(Request $request)
    {
        if (!$this->supportsSubtaskSchema()) {
            return back()->with('error', 'Struktur data blocker belum diperbarui. Jalankan migrasi sebelum melaporkan blocker baru.');
        }

        $request->validate([
            'card_id' => 'required|exists:cards,card_id',
            'subtask_id' => 'required|exists:subtasks,subtask_id',
        ]);

        $user = Auth::user();

        $card = Card::where('card_id', $request->card_id)
            ->where('status', '!=', 'done')
            ->whereHas('assignments', function ($query) use ($user) {
                $query->where('user_id', $user->user_id);
            })
            ->first();

        if (!$card) {
            return back()->with('error', 'Card yang dipilih tidak valid atau Anda tidak ditugaskan pada card tersebut.');
        }

        $subtask = Subtask::where('subtask_id', $request->subtask_id)
            ->where('card_id', $card->card_id)
            ->where('status', '!=', 'done')
            ->first();

        if (!$subtask) {
            return back()->with('error', 'Subtask tidak valid atau sudah selesai.');
        }

        $existingBlocker = Blocker::where('user_id', $user->user_id)
            ->where('subtask_id', $request->subtask_id)
            ->where('status', 'pending')
            ->first();

        if ($existingBlocker) {
            return back()->with('error', 'Anda sudah memiliki blocker yang pending untuk subtask ini.');
        }

        Blocker::create([
            'user_id' => $user->user_id,
            'subtask_id' => $subtask->subtask_id,
            'status' => 'pending'
        ]);

        return redirect()->route('blocker.index')
            ->with('success', 'Blocker berhasil dilaporkan. Team lead akan segera menangani permintaan Anda.');
    }

    /**
     * Ambil daftar solver (blocker) untuk subtask secara AJAX.
     */
    public function subtaskEntries(Subtask $subtask)
    {
        if (!$this->supportsSubtaskSchema()) {
            return response()->json([
                'message' => 'Skema subtask belum didukung pada instalasi ini.'
            ], 400);
        }

        $user = Auth::user();
        if (!$this->userCanAccessSubtask($subtask, $user)) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke subtask ini.'
            ], 403);
        }

        $blockers = Blocker::query()
            ->select('blocker_id', 'subtask_id', 'user_id', 'status', 'created_at')
            ->where('subtask_id', $subtask->subtask_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($blocker) => $this->formatBlockerForResponse($blocker));

        return response()->json([
            'entries' => $blockers,
        ]);
    }

    /**
     * Simpan solver baru melalui AJAX dari dashboard.
     */
    public function storeSubtaskBlocker(Request $request, Subtask $subtask)
    {
        if (!$this->supportsSubtaskSchema()) {
            return response()->json([
                'message' => 'Skema subtask belum didukung pada instalasi ini.'
            ], 400);
        }

        $user = Auth::user();

        if ($subtask->status === 'done') {
            return response()->json([
                'message' => 'Subtask sudah selesai. Solver tidak diperlukan.'
            ], 422);
        }

        if (!$this->userCanAccessSubtask($subtask, $user)) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke subtask ini.'
            ], 403);
        }

        $existingBlocker = Blocker::where('user_id', $user->user_id)
            ->where('subtask_id', $subtask->subtask_id)
            ->where('status', 'pending')
            ->first();

        if ($existingBlocker) {
            return response()->json([
                'message' => 'Anda sudah memiliki solver yang masih diproses untuk subtask ini.'
            ], 409);
        }

        $blocker = Blocker::create([
            'user_id' => $user->user_id,
            'subtask_id' => $subtask->subtask_id,
            'status' => 'pending',
        ]);

        $formatted = $this->formatBlockerForResponse($blocker->fresh());

        return response()->json([
            'message' => 'Solver berhasil dikirim ke Team Lead.',
            'entry' => $formatted,
        ], 201);
    }

    /**
     * Tampilkan detail blocker
     */
    public function show(Blocker $blocker)
    {
        // Pastikan user hanya bisa melihat blocker miliknya
        if ($blocker->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $supportsSubtask = $this->supportsSubtaskSchema();

        if ($supportsSubtask) {
            $blocker->load(['subtask.card.board.project']);
        } else {
            $blocker->legacy_card = Card::with(['board.project'])->find($blocker->card_id);
        }

        return view('blocker.show', compact('blocker', 'supportsSubtask'));
    }

    /**
     * Tampilkan daftar blocker untuk team lead
     */
    public function teamLeadIndex()
    {
        $supportsSubtask = $this->supportsSubtaskSchema();

        $query = Blocker::with(['user'])
            ->orderBy('created_at', 'desc');

        if ($supportsSubtask) {
            $blockers = $query->with(['subtask.card.board.project'])->get();
        } else {
            $blockers = $query->get();
            $cardMap = Card::with(['board.project'])
                ->whereIn('card_id', $blockers->pluck('card_id')->filter()->unique())
                ->get()
                ->keyBy('card_id');

            $blockers->each(function ($blocker) use ($cardMap) {
                $blocker->legacy_card = $cardMap->get($blocker->card_id);
            });
        }

        return view('teamlead.blocker.index', compact('blockers', 'supportsSubtask'));
    }

    /**
     * Tandai blocker selesai oleh team lead.
     */
    public function complete(Blocker $blocker)
    {
        $user = auth()->user();
        if ($user->role !== 'team_lead') {
            abort(403, 'Hanya Team Lead yang dapat menandai blocker selesai.');
        }

        if ($blocker->status === 'selesai') {
            return back()->with('info', 'Blocker sudah ditandai selesai.');
        }

        $blocker->update([
            'status' => 'selesai',
        ]);

        return back()->with('success', 'Blocker berhasil ditandai selesai.');
    }

    private function userCanAccessSubtask(Subtask $subtask, User $user): bool
    {
        if (in_array($user->role, ['admin', 'team_lead'])) {
            return true;
        }

        $card = $subtask->card()->with('assignments')->first();

        if (!$card) {
            return false;
        }

        return $card->assignments->contains(function ($assignment) use ($user) {
            return (int) $assignment->user_id === (int) $user->user_id;
        });
    }

    private function formatBlockerForResponse(Blocker $blocker): array
    {
        $status = strtolower($blocker->status ?? 'pending');
        $normalizedStatus = $status === 'pending' ? 'pending' : 'selesai';

        return [
            'blocker_id' => $blocker->blocker_id,
            'blocker_user_id' => $blocker->user_id,
            'blocker_date' => $blocker->created_at?->format('Y-m-d H:i:s'),
            'status' => $normalizedStatus,
        ];
    }

    private function supportsSubtaskSchema(): bool
    {
        static $cached = null;

        if ($cached === null) {
            $cached = Schema::hasColumn('blockers', 'subtask_id');
        }

        return $cached;
    }
}
