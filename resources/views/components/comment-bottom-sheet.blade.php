@once
<style>
    .comment-sheet {
        position: fixed;
        inset: 0;
        display: grid;
        align-items: flex-end;
        justify-items: center;
        pointer-events: none;
        z-index: 1300;
        transition: opacity 0.25s ease;
    }

    .comment-sheet.hidden {
        display: none;
    }

    .comment-sheet__overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(10px);
        pointer-events: auto;
    }

    .comment-sheet__panel {
        position: relative;
        width: min(640px, 100% - 1.5rem);
        max-height: 85vh;
        background: rgba(17, 24, 39, 0.96);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 28px 28px 0 0;
        box-shadow: 0 -20px 45px rgba(15, 23, 42, 0.45);
        padding: 1.5rem 1.5rem 1.25rem;
        pointer-events: auto;
        transform: translateY(0);
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .comment-sheet__handle {
        width: 48px;
        height: 4px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.4);
        margin: 0 auto;
    }

    .comment-sheet__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
    }

    .comment-sheet__title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #f8fafc;
    }

    .comment-sheet__subtitle {
        font-size: 0.875rem;
        color: rgba(226, 232, 240, 0.7);
        margin-bottom: 0.25rem;
    }

    .comment-sheet__close {
        border: none;
        background: rgba(148, 163, 184, 0.15);
        color: rgba(226, 232, 240, 0.85);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
    }

    .comment-sheet__close:hover {
        background: rgba(99, 102, 241, 0.25);
        color: #ffffff;
    }

    .comment-sheet__body {
        flex: 1 1 auto;
        overflow-y: auto;
        padding-right: 0.35rem;
    }

    .comment-sheet__comments {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .comment-sheet__form textarea {
        resize: vertical;
        min-height: 90px;
    }

    .comment-sheet__form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 0.75rem;
    }

    .comment-sheet-open {
        overflow: hidden;
    }

    .comment-sheet .loading-skeleton {
        background: rgba(31, 41, 55, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        padding: 1rem;
    }

    .comment-sheet .loading-spinner {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid rgba(148, 163, 184, 0.5);
        border-top-color: transparent;
        display: inline-block;
        animation: comment-spinner 0.75s linear infinite;
    }

    .comment-sheet .skeleton-header {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .comment-sheet .skeleton-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(148, 163, 184, 0.2);
    }

    .comment-sheet .skeleton-line {
        height: 12px;
        border-radius: 999px;
        background: linear-gradient(90deg, rgba(148, 163, 184, 0.18) 25%, rgba(148, 163, 184, 0.36) 50%, rgba(148, 163, 184, 0.18) 75%);
        background-size: 200% 100%;
        animation: comment-skeleton 1.5s ease infinite;
        margin-bottom: 0.5rem;
    }

    .comment-sheet .skeleton-line.short {
        width: 60%;
    }

    .comment-sheet .skeleton-line.medium {
        width: 80%;
    }

    @keyframes comment-skeleton {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    @keyframes comment-spinner {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .comment-sheet .empty-state-comments {
        text-align: center;
        padding: 2rem 1rem;
        border: 1px dashed rgba(148, 163, 184, 0.35);
        border-radius: 16px;
        background: rgba(15, 23, 42, 0.35);
        color: rgba(226, 232, 240, 0.7);
    }

    .comment-sheet .empty-state-comments i {
        font-size: 2rem;
        color: rgba(129, 140, 248, 0.6);
    }

    [data-theme="light"] .comment-sheet__overlay {
        background: rgba(15, 23, 42, 0.35);
    }

    [data-theme="light"] .comment-sheet__panel {
        background: rgba(255, 255, 255, 0.97);
        border: 1px solid rgba(203, 213, 225, 0.8);
        box-shadow: 0 -16px 40px rgba(148, 163, 184, 0.25);
    }

    [data-theme="light"] .comment-sheet__title {
        color: #0f172a;
    }

    [data-theme="light"] .comment-sheet__subtitle {
        color: #475569;
    }

    [data-theme="light"] .comment-sheet__close {
        background: rgba(226, 232, 240, 0.8);
        color: #1f2937;
    }

    [data-theme="light"] .comment-sheet__close:hover {
        background: rgba(129, 140, 248, 0.25);
        color: #1d4ed8;
    }

    [data-theme="light"] .comment-sheet .loading-skeleton {
        background: rgba(248, 250, 252, 0.75);
        border: 1px solid rgba(203, 213, 225, 0.7);
    }

    [data-theme="light"] .comment-sheet .skeleton-avatar {
        background: rgba(129, 140, 248, 0.25);
    }

    [data-theme="light"] .comment-sheet .skeleton-line {
        background: linear-gradient(90deg, rgba(148, 163, 184, 0.16) 25%, rgba(148, 163, 184, 0.34) 50%, rgba(148, 163, 184, 0.16) 75%);
    }

    [data-theme="light"] .comment-sheet .empty-state-comments {
        background: rgba(248, 250, 252, 0.9);
        border-color: rgba(203, 213, 225, 0.8);
        color: #475569;
    }

    [data-theme="light"] .comment-sheet .loading-spinner {
        border-color: rgba(99, 102, 241, 0.45);
        border-top-color: transparent;
    }
</style>

<div class="comment-sheet hidden" id="commentSheet" aria-hidden="true">
    <div class="comment-sheet__overlay" data-comment-sheet-close></div>
    <div class="comment-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="commentSheetTitle">
        <div class="comment-sheet__handle"></div>
        <div class="comment-sheet__header">
            <div class="flex-grow-1">
                <p class="comment-sheet__subtitle mb-1" id="commentSheetSubtitle"></p>
                <h5 class="comment-sheet__title mb-0" id="commentSheetTitle">Comments</h5>
            </div>
            <button type="button" class="comment-sheet__close" data-comment-sheet-close aria-label="Close comments">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="comment-sheet__body">
            <div id="commentSheetComments" class="comment-sheet__comments"></div>
        </div>

        <form id="commentSheetForm" class="comment-sheet__form" data-comment-type="" data-entity-id="">
            <textarea name="comment_text" class="form-control" placeholder="Write a comment..." required></textarea>
            <div class="comment-sheet__form-actions">
                <button type="submit" class="btn-modern">
                    <i class="bi bi-send me-1"></i> Send Comment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(() => {
    if (window.CommentSheet) {
        return;
    }

    const sheet = document.getElementById('commentSheet');
    if (!sheet) {
        return;
    }

    const commentsEl = document.getElementById('commentSheetComments');
    const titleEl = document.getElementById('commentSheetTitle');
    const subtitleEl = document.getElementById('commentSheetSubtitle');
    const formEl = document.getElementById('commentSheetForm');
    const textareaEl = formEl.querySelector('textarea');
    const closeTriggers = sheet.querySelectorAll('[data-comment-sheet-close]');
    const overlay = sheet.querySelector('.comment-sheet__overlay');
    const state = { type: null, entityId: null };
    const csrfToken = '{{ csrf_token() }}';

    const routes = {
        card: {
            list: id => `/comments/card/${id}`,
            store: id => `/comments/ajax-card/${id}`
        },
        subtask: {
            list: id => `/comments/subtask/${id}`,
            store: id => `/comments/ajax-subtask/${id}`
        },
        project: {
            list: id => `/comments/project/${id}`,
            store: id => `/comments/ajax-project/${id}`
        }
    };

    const escapeHtml = value => {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    };

    const generateSkeletons = (count = 3) => {
        let skeletons = '';
        for (let i = 0; i < count; i++) {
            skeletons += `
                <div class="loading-skeleton">
                    <div class="skeleton-header">
                        <div class="skeleton-avatar"></div>
                        <div style="flex: 1;">
                            <div class="skeleton-line medium"></div>
                            <div class="skeleton-line short"></div>
                        </div>
                    </div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line short"></div>
                </div>
            `;
        }
        return skeletons;
    };

    const emptyState = () => `
        <div class="empty-state-comments">
            <i class="bi bi-chat"></i>
            <p class="mt-2 mb-0">No comments yet</p>
            <small class="text-muted">Be the first to comment</small>
        </div>
    `;

    const renderComments = (comments, type, entityId, level = 0) => {
        if (!comments || comments.length === 0) {
            return level === 0 ? emptyState() : '';
        }

        return comments.map(comment => {
            const commentId = comment.comment_id;
            const author = escapeHtml(comment.user?.full_name || 'Unknown');
            const createdAt = comment.created_at
                ? new Date(comment.created_at).toLocaleString('id-ID', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                })
                : '';
            const text = escapeHtml(comment.comment_text || '');
            const replies = Array.isArray(comment.replies) ? comment.replies : [];
            const repliesHtml = renderComments(replies, type, entityId, level + 1);

            return `
                <div class="comment${level > 0 ? ' nested-comment' : ''}" id="comment-${commentId}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong>${author}</strong>
                        ${createdAt ? `<small>${createdAt}</small>` : ''}
                    </div>
                    <p class="mb-2">${text.replace(/\n/g, '<br>')}</p>
                    <button class="btn-reply reply-toggle" data-parent="${commentId}">
                        <i class="bi bi-reply me-1"></i>Reply
                    </button>
                    <form class="reply-form mt-3 d-none"
                          data-comment-type="${type}"
                          data-entity-id="${entityId}"
                          data-parent="${commentId}">
                        <div class="mb-2">
                            <textarea name="comment_text" class="form-control" rows="2" placeholder="Write a reply..." required></textarea>
                        </div>
                        <div class="reply-form-buttons">
                            <button type="submit" class="btn-modern btn-sm">
                                <i class="bi bi-send me-1"></i> Send Reply
                            </button>
                            <button type="button" class="btn-cancel cancel-reply" data-parent="${commentId}">
                                <i class="bi bi-x me-1"></i> Cancel
                            </button>
                        </div>
                    </form>
                    ${repliesHtml
                        ? `<div class="replies ms-4 mt-3 border-start ps-3" style="border-color: rgba(139, 92, 246, 0.3) !important;">${repliesHtml}</div>`
                        : `<div class="replies ms-4 mt-3 border-start ps-3" style="border-color: rgba(139, 92, 246, 0.3) !important;"></div>`}
                </div>
            `;
        }).join('');
    };

    const loadComments = async () => {
        const { type, entityId } = state;
        if (!type || !entityId || !routes[type]) {
            return;
        }

        commentsEl.innerHTML = generateSkeletons(3);

        try {
            const response = await fetch(routes[type].list(entityId), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                throw new Error(`Failed to load comments (${response.status})`);
            }

            const data = await response.json();
            commentsEl.innerHTML = renderComments(Array.isArray(data) ? data : [], type, entityId);
        } catch (error) {
            console.error('Failed to load comments:', error);
            commentsEl.innerHTML = `
                <div class="empty-state-comments">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p class="mt-2 mb-0">Failed to load comments</p>
                    <small class="text-muted">Please try again later.</small>
                </div>
            `;
        }
    };

    const closeSheet = () => {
        if (sheet.classList.contains('hidden')) {
            return;
        }
        sheet.classList.add('hidden');
        document.body.classList.remove('comment-sheet-open');
        state.type = null;
        state.entityId = null;
        formEl.dataset.commentType = '';
        formEl.dataset.entityId = '';
        textareaEl.value = '';
    };

    const openSheet = ({ type, entityId, title, subtitle }) => {
        if (!routes[type]) {
            console.warn('Unsupported comment type:', type);
            return;
        }

        state.type = type;
        state.entityId = entityId;
        formEl.dataset.commentType = type;
        formEl.dataset.entityId = entityId;

        titleEl.textContent = title || 'Comments';
        subtitleEl.textContent = subtitle || '';
        textareaEl.value = '';

        document.body.classList.add('comment-sheet-open');
        sheet.classList.remove('hidden');
        sheet.setAttribute('aria-hidden', 'false');

        loadComments();
        textareaEl.focus({ preventScroll: true });
    };

    formEl.addEventListener('submit', async event => {
        event.preventDefault();
        const { type, entityId } = state;
        if (!type || !entityId || !routes[type]) {
            return;
        }

        const text = textareaEl.value.trim();
        if (!text) {
            alert('Comment cannot be empty');
            return;
        }

        const submitBtn = formEl.querySelector('button[type="submit"]');
        const originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Sending...';
        submitBtn.disabled = true;

        try {
            const response = await fetch(routes[type].store(entityId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ comment_text: text })
            });

            if (!response.ok) {
                const payload = await response.json().catch(() => ({}));
                throw new Error(payload.error || 'Failed to submit comment');
            }

            textareaEl.value = '';
            await loadComments();
        } catch (error) {
            console.error('Failed to submit comment:', error);
            alert(error.message || 'Failed to submit comment. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        }
    });

    document.body.addEventListener('submit', async event => {
        const form = event.target;
        if (!form.classList || !form.classList.contains('reply-form')) {
            return;
        }

        event.preventDefault();

        const type = form.dataset.commentType;
        const entityId = form.dataset.entityId;
        const parentId = form.dataset.parent;
        const textarea = form.querySelector('textarea[name="comment_text"]');

        if (!type || !entityId || !parentId || !textarea) {
            return;
        }

        const text = textarea.value.trim();
        if (!text) {
            alert('Reply cannot be empty');
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Sending...';
        submitBtn.disabled = true;

        try {
            const response = await fetch(routes[type].store(entityId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ comment_text: text, parent_id: parentId })
            });

            if (!response.ok) {
                const payload = await response.json().catch(() => ({}));
                throw new Error(payload.error || 'Failed to submit reply');
            }

            textarea.value = '';
            form.classList.add('d-none');
            await loadComments();
        } catch (error) {
            console.error('Failed to submit reply:', error);
            alert(error.message || 'Failed to submit reply. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        }
    });

    document.body.addEventListener('click', event => {
        const toggle = event.target.closest('.reply-toggle');
        if (toggle) {
            event.preventDefault();
            const parentId = toggle.dataset.parent;
            const form = document.querySelector(`.reply-form[data-parent="${parentId}"]`);
            if (!form) {
                return;
            }

            document.querySelectorAll('.reply-form').forEach(other => {
                if (other !== form) {
                    other.classList.add('d-none');
                }
            });

            form.classList.toggle('d-none');
            const textarea = form.querySelector('textarea[name="comment_text"]');
            if (textarea && !form.classList.contains('d-none')) {
                textarea.focus({ preventScroll: false });
            }
            return;
        }

        const cancel = event.target.closest('.cancel-reply');
        if (cancel) {
            event.preventDefault();
            const parentId = cancel.dataset.parent;
            const form = document.querySelector(`.reply-form[data-parent="${parentId}"]`);
            if (form) {
                form.classList.add('d-none');
                const textarea = form.querySelector('textarea[name="comment_text"]');
                if (textarea) {
                    textarea.value = '';
                }
            }
        }
    });

    closeTriggers.forEach(trigger => {
        trigger.addEventListener('click', closeSheet);
    });

    overlay.addEventListener('click', closeSheet);

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !sheet.classList.contains('hidden')) {
            closeSheet();
        }
    });

    document.addEventListener('click', event => {
        const trigger = event.target.closest('[data-comment-sheet-trigger]');
        if (!trigger) {
            return;
        }

        const type = trigger.dataset.commentType;
        const entityId = trigger.dataset.commentId;
        if (!type || !entityId) {
            return;
        }

        event.preventDefault();
        openSheet({
            type,
            entityId,
            title: trigger.dataset.commentTitle || 'Comments',
            subtitle: trigger.dataset.commentSubtitle || ''
        });
    });

    window.CommentSheet = {
        open({ type, entityId, title = 'Comments', subtitle = '' }) {
            openSheet({ type, entityId, title, subtitle });
        },
        close: closeSheet,
        reload: loadComments
    };
})();
</script>
@endonce
