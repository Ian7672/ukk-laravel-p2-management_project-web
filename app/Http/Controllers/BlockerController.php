<?php

namespace App\Http\Controllers;

use App\Models\Blocker;
use App\Models\Card;
use App\Models\Subtask;
use App\Models\User;
use Carbon\Carbon;
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

        $query = Blocker::with(['assignedTo'])
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
            'description' => 'required|string|min:10',
            'priority' => 'required|in:low,medium,high,urgent'
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
            ->whereIn('status', ['pending', 'in_progress'])
            ->first();

        if ($existingBlocker) {
            return back()->with('error', 'Anda sudah memiliki blocker yang pending untuk subtask ini.');
        }

        Blocker::create([
            'user_id' => $user->user_id,
            'subtask_id' => $subtask->subtask_id,
            'description' => $request->description,
            'priority' => $request->priority,
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

        $blockers = Blocker::with(['assignedTo'])
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

        $request->validate([
            'description' => 'nullable|string|max:1000',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

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
            ->whereIn('status', ['pending', 'in_progress'])
            ->first();

        if ($existingBlocker) {
            return response()->json([
                'message' => 'Anda sudah memiliki solver yang masih diproses untuk subtask ini.'
            ], 409);
        }

        $description = trim($request->description ?? '');
        if ($description === '') {
            $userName = $user->full_name ?? $user->username;
            $description = sprintf(
                'Permintaan solver otomatis oleh %s pada %s.',
                $userName,
                Carbon::now('Asia/Jakarta')->translatedFormat('d M Y H:i')
            );
        }

        $blocker = Blocker::create([
            'user_id' => $user->user_id,
            'subtask_id' => $subtask->subtask_id,
            'description' => $description,
            'priority' => $request->priority ?? 'medium',
            'status' => 'pending',
        ]);

        $formatted = $this->formatBlockerForResponse($blocker->fresh(['assignedTo']));

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
            $blocker->load(['subtask.card.board.project', 'assignedTo']);
        } else {
            $blocker->load(['assignedTo']);
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

        $query = Blocker::with(['user', 'assignedTo'])
            ->orderBy('priority', 'desc')
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
     * Assign blocker ke team lead
     */
    public function assign(Request $request, Blocker $blocker)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,user_id'
        ]);

        $blocker->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_progress',
            'resolved_at' => null,
            'rejected_at' => null,
        ]);

        return back()->with('success', 'Blocker berhasil ditugaskan ke team lead.');
    }

    /**
     * Resolve blocker
     */
    public function resolve(Request $request, Blocker $blocker)
    {
        $request->validate([
            'solution' => 'required|string|min:10'
        ]);

        $blocker->update([
            'solution' => $request->solution,
            'status' => 'resolved',
            'resolved_at' => now(),
            'rejected_at' => null,
        ]);

        return back()->with('success', 'Blocker berhasil diselesaikan.');
    }

    /**
     * Reject blocker
     */
    public function reject(Request $request, Blocker $blocker)
    {
        $request->validate([
            'reason' => 'required|string|min:10'
        ]);

        $blocker->update([
            'solution' => $request->reason,
            'status' => 'rejected',
            'resolved_at' => null,
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'Blocker ditolak dengan alasan yang diberikan.');
    }

    /**
     * Tampilkan form edit blocker (untuk team lead)
     */
    public function edit(Blocker $blocker)
    {
        $supportsSubtask = $this->supportsSubtaskSchema();

        $teamLeads = User::where('role', 'team_lead')->get();

        if ($supportsSubtask) {
            $blocker->load(['user', 'subtask.card.board.project']);
        } else {
            $blocker->load(['user']);
            $blocker->legacy_card = Card::with(['board.project'])->find($blocker->card_id);
        }

        return view('teamlead.blocker.edit', compact('blocker', 'teamLeads', 'supportsSubtask'));
    }

    /**
     * Update blocker
     */
    public function update(Request $request, Blocker $blocker)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,user_id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,resolved,rejected'
        ]);

        $data = $request->only(['assigned_to', 'priority', 'status']);

        if ($data['status'] === 'resolved') {
            $data['resolved_at'] = now();
            $data['rejected_at'] = null;
        } elseif ($data['status'] === 'rejected') {
            $data['rejected_at'] = now();
            $data['resolved_at'] = null;
        } else {
            $data['resolved_at'] = null;
            $data['rejected_at'] = null;
        }

        $blocker->update($data);

        return back()->with('success', 'Blocker berhasil diperbarui.');
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
        $statusLabels = [
            'pending' => 'Pending',
            'in_progress' => 'Sedang Ditangani',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
        ];

        $statusClasses = [
            'pending' => 'solver-status--pending',
            'in_progress' => 'solver-status--progress',
            'resolved' => 'solver-status--resolved',
            'rejected' => 'solver-status--rejected',
        ];

        $blocker->loadMissing('assignedTo');

        return [
            'blocker_id' => $blocker->blocker_id,
            'description' => $blocker->description,
            'priority' => ucfirst($blocker->priority),
            'status' => $blocker->status,
            'status_label' => $statusLabels[$blocker->status] ?? ucfirst($blocker->status),
            'status_class' => $statusClasses[$blocker->status] ?? 'solver-status--pending',
            'created_at' => $blocker->created_at?->translatedFormat('d M Y H:i'),
            'created_human' => $blocker->created_at?->diffForHumans(),
            'resolved_at' => $blocker->resolved_at?->translatedFormat('d M Y H:i'),
            'resolved_human' => $blocker->resolved_at?->diffForHumans(),
            'rejected_at' => $blocker->rejected_at?->translatedFormat('d M Y H:i'),
            'rejected_human' => $blocker->rejected_at?->diffForHumans(),
            'team_lead' => $blocker->assignedTo?->full_name,
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
