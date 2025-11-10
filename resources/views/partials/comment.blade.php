<div class="comment-acrylic mb-3">
  <div class="comment-header">
    <strong class="comment-author">{{ $comment->user->full_name ?? 'User' }}</strong>
    <small class="comment-meta">
      ({{ $comment->user->username ?? 'Unknown' }}) • {{ $comment->created_at->format('d M Y H:i') }}
    </small>
  </div>

  <div class="comment-body">
    <p class="comment-text">{{ $comment->comment_text }}</p>

    <!-- Tombol Reply -->
    <button class="btn-reply reply-toggle" data-parent="{{ $comment->comment_id }}">
      <i class="bi bi-reply me-1"></i> Balas
    </button>

    <!-- Form Reply (disembunyikan) -->
    <form class="reply-form mt-3 d-none"
          data-parent="{{ $comment->comment_id }}"
          data-project-id="{{ $comment->project_id }}"
          data-card-id="{{ $comment->card_id }}"
          data-subtask-id="{{ $comment->subtask_id }}">
      <textarea name="comment_text" class="form-control mb-2" rows="2" placeholder="Tulis balasan..." required></textarea>
      <div class="d-flex gap-2">
        <button type="button" class="btn-cancel reply-cancel" onclick="this.closest('.reply-form').classList.add('d-none')">
          Batal
        </button>
        <button type="submit" class="btn-send">
          <i class="bi bi-send me-1"></i> Kirim
        </button>
      </div>
    </form>
  </div>

  <!-- Daftar Balasan -->
  @if($comment->replies && $comment->replies->count())
    <div class="replies ms-4 mt-3 border-start ps-3" style="border-color: rgba(139, 92, 246, 0.3) !important;">
      @foreach($comment->replies as $reply)
        @include('partials.comment', ['comment' => $reply])
      @endforeach
    </div>
  @else
    <div class="replies ms-4 mt-3 border-start ps-3" style="border-color: rgba(139, 92, 246, 0.3) !important;"></div>
  @endif
</div>

<style>
.comment-acrylic {
  background: rgba(31, 41, 55, 0.5);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 1.25rem;
  transition: all 0.3s ease;
}

.comment-acrylic:hover {
  background: rgba(31, 41, 55, 0.6);
  border-color: rgba(139, 92, 246, 0.2);
}

.comment-header {
  margin-bottom: 0.75rem;
}

.comment-author {
  color: #c4b5fd !important;
  font-weight: 600;
  font-size: 0.95rem;
}

.comment-meta {
  color: rgba(255, 255, 255, 0.5) !important;
  font-size: 0.8rem;
}

.comment-body {
  color: #e5e7eb;
}

.comment-text {
  color: #f3f4f6 !important;
  margin-bottom: 1rem;
  line-height: 1.5;
  font-size: 0.9rem;
}

.btn-reply {
  background: rgba(59, 130, 246, 0.2);
  border: 1px solid rgba(59, 130, 246, 0.3);
  color: #93c5fd;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.8rem;
  transition: all 0.3s ease;
  cursor: pointer;
  backdrop-filter: blur(5px);
}

.btn-reply:hover {
  background: rgba(59, 130, 246, 0.3);
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.reply-form {
  background: rgba(17, 24, 39, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 1rem;
  backdrop-filter: blur(10px);
}

.reply-form .form-control {
  background: rgba(31, 41, 55, 0.7) !important;
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #f3f4f6 !important;
  border-radius: 8px;
  backdrop-filter: blur(10px);
  font-size: 0.85rem;
}

.reply-form .form-control:focus {
  background: rgba(31, 41, 55, 0.9) !important;
  border-color: rgba(139, 92, 246, 0.5);
  color: white !important;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
}

.reply-form .form-control::placeholder {
  color: rgba(255, 255, 255, 0.4);
}

.btn-cancel {
  background: rgba(107, 114, 128, 0.3);
  border: 1px solid rgba(107, 114, 128, 0.4);
  color: #d1d5db;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 0.8rem;
  transition: all 0.3s ease;
  cursor: pointer;
  backdrop-filter: blur(5px);
}

.btn-cancel:hover {
  background: rgba(107, 114, 128, 0.4);
  color: white;
}

.btn-send {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.25), rgba(59, 130, 246, 0.25));
  border: 1px solid rgba(16, 185, 129, 0.4);
  color: #6ee7b7;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 0.8rem;
  transition: all 0.3s ease;
  cursor: pointer;
  backdrop-filter: blur(5px);
  font-weight: 500;
}

.btn-send:hover {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.4), rgba(59, 130, 246, 0.4));
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

/* Style untuk replies (komentar nested) */
.replies .comment-acrylic {
  background: rgba(31, 41, 55, 0.4);
  border-color: rgba(255, 255, 255, 0.08);
  padding: 1rem;
}

.replies .comment-acrylic:hover {
  background: rgba(31, 41, 55, 0.5);
}

.replies .comment-author {
  font-size: 0.9rem;
}

.replies .comment-meta {
  font-size: 0.75rem;
}

.replies .comment-text {
  font-size: 0.85rem;
}

.replies .btn-reply {
  font-size: 0.75rem;
  padding: 4px 10px;
}

/* Responsive */
@media (max-width: 768px) {
  .comment-acrylic {
    padding: 1rem;
  }
  
  .replies {
    margin-left: 1rem !important;
    padding-left: 1rem !important;
  }
  
  .reply-form .d-flex {
    flex-direction: column;
  }
  
  .reply-form .d-flex .btn-cancel,
  .reply-form .d-flex .btn-send {
    width: 100%;
    margin-bottom: 0.5rem;
  }
}
</style>
