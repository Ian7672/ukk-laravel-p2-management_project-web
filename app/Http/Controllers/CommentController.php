<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Subtask;
use App\Models\Comment;
use App\Models\Project;

class CommentController extends Controller
{
    public function ajaxStore(Request $request, $subtaskId)
    {
        $request->validate([
            'comment_text' => 'required|string',
        ]);

        $comment = new Comment([
            'subtask_id' => $subtaskId,
            'user_id' => auth()->id(),
            'comment_text' => $request->comment_text,
            'parent_id' => $request->parent_id ?? null,
        ]);

        $comment->save();
        $comment->load(['user', 'replies.user']);

        $level = $this->calculateCommentLevel($comment);
        $html = $this->generateCommentHTML($comment, $level, $subtaskId, 'subtask');
        
        return response($html);
    }

    public function ajaxStoreCard(Request $request, $cardId)
    {
        $request->validate([
            'comment_text' => 'required|string',
        ]);

        // Cek apakah card exists
        $card = Card::find($cardId);
        if (!$card) {
            return response()->json(['error' => 'Card tidak ditemukan'], 404);
        }

        $comment = new Comment([
            'card_id' => $cardId,
            'user_id' => auth()->id(),
            'comment_text' => $request->comment_text,
            'parent_id' => $request->parent_id ?? null,
        ]);

        $comment->save();
        
        // Load relationships dengan benar
        $comment->load(['user', 'replies.user']);

        $level = $this->calculateCommentLevel($comment);
        $html = $this->generateCommentHTML($comment, $level, $cardId, 'card');
        
        return response($html);
    }

    public function ajaxStoreProject(Request $request, $projectId)
    {
        $request->validate([
            'comment_text' => 'required|string',
        ]);

        // Cek apakah project exists
        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['error' => 'Project tidak ditemukan'], 404);
        }

        $comment = new Comment([
            'project_id' => $projectId,
            'user_id' => auth()->id(),
            'comment_text' => $request->comment_text,
            'parent_id' => $request->parent_id ?? null,
        ]);

        $comment->save();
        
        // Load relationships dengan benar
        $comment->load(['user', 'replies.user']);

        $level = $this->calculateCommentLevel($comment);
        $html = $this->generateCommentHTML($comment, $level, $projectId, 'project');
        
        return response($html);
    }

    /**
     * Get comments for a subtask
     */
    public function getSubtaskComments($subtaskId)
    {
        try {
            $subtask = Subtask::findOrFail($subtaskId);
            
            // Load comments dengan user dan replies
            $comments = Comment::with(['user', 'replies.user'])
                ->where('subtask_id', $subtaskId)
                ->whereNull('parent_id')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($comments);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load comments'], 500);
        }
    }

    /**
     * Ambil komentar untuk card
     */
    public function getCardComments($cardId)
    {
        $comments = Comment::where('card_id', $cardId)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($comments);
    }

    /**
 * Ambil komentar untuk project dengan deep eager loading
 */
public function getProjectComments($projectId)
{
    $comments = Comment::where('project_id', $projectId)
        ->whereNull('parent_id')
        ->with([
            'user', 
            'replies.user', 
            'replies.replies.user',
            'replies.replies.replies.user',
            'replies.replies.replies.replies.user' // Sampai level 4
        ])
        ->orderBy('created_at', 'desc')
        ->get();
    
    return response()->json($comments);
}

    /**
     * Helper method untuk menghitung level komentar
     */
    private function calculateCommentLevel($comment)
    {
        $level = 0;
        $current = $comment;
        
        while ($current->parent_id) {
            $level++;
            $current = Comment::find($current->parent_id);
            if (!$current) break;
            
            // Safety limit untuk mencegah infinite loop
            if ($level > 10) break;
        }
        
        return $level;
    }

    /**
     * Generate HTML untuk komentar (tanpa partial)
     */
    private function generateCommentHTML($comment, $level, $entityId, $type)
    {
        $userName = $comment->user->full_name ?? 'Unknown';
        $createdAt = $comment->created_at->diffForHumans();
        $commentText = htmlspecialchars($comment->comment_text);
        $commentId = $comment->comment_id;

        $nestedClass = $level > 0 ? 'nested-comment' : '';
        
        $html = <<<HTML
        <div class="comment {$nestedClass}" id="comment-{$commentId}" data-level="{$level}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <strong>{$userName}</strong>
                <small>{$createdAt}</small>
            </div>
            <p class="mb-2">{$commentText}</p>
            
            <button class="btn-modern btn-sm reply-toggle" data-parent="{$commentId}">
                <i class="bi bi-reply me-1"></i>Balas
            </button>

            <form class="reply-form mt-2 d-none" data-{$type}-id="{$entityId}" data-parent="{$commentId}">
                <div class="mb-2">
                    <textarea name="comment_text" class="form-control" rows="2" placeholder="Tulis balasan..." required></textarea>
                </div>
                <button type="submit" class="btn-modern btn-sm">
                    <i class="bi bi-send me-1"></i> Kirim Balasan
                </button>
            </form>

            <div class="replies mt-3 ms-4" id="replies-{$commentId}"></div>
        </div>
        HTML;

        return $html;
    }

    /**
     * Simpan komentar card (non-AJAX)
     */
    public function storeCard(Request $request, $cardId)
    {
        $request->validate([
            'comment_text' => 'required|string',
        ]);

        $comment = new Comment([
            'card_id' => $cardId,
            'user_id' => auth()->id(),
            'comment_text' => $request->comment_text,
            'parent_id' => $request->parent_id ?? null,
        ]);

        $comment->save();

        return back()->with('success', 'Komentar berhasil ditambahkan');
    }

    /**
     * Simpan komentar subtask (non-AJAX)
     */
    public function storeSubtask(Request $request, $subtaskId)
    {
        $request->validate([
            'comment_text' => 'required|string',
        ]);

        $comment = new Comment([
            'subtask_id' => $subtaskId,
            'user_id' => auth()->id(),
            'comment_text' => $request->comment_text,
            'parent_id' => $request->parent_id ?? null,
        ]);

        $comment->save();

        return back()->with('success', 'Komentar berhasil ditambahkan');
    }
}