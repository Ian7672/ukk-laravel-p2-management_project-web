@extends('layouts.app')
@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    $projectDetailPayload = $projects->map(function ($project) {
        return [
            'id' => $project->project_id,
            'name' => $project->project_name,
            'description' => $project->description,
            'deadline' => $project->deadline ? Carbon::parse($project->deadline)->format('d M Y') : null,
            'boards' => $project->boards->map(function ($board) {
                return [
                    'id' => $board->board_id,
                    'name' => $board->board_name,
                    'cards' => $board->cards->map(function ($card) {
                        return [
                            'id' => $card->card_id,
                            'title' => $card->card_title,
                            'description' => $card->description,
                            'priority' => $card->priority,
                            'status' => $card->status,
                            'deadline' => $card->due_date ? Carbon::parse($card->due_date)->format('d M Y') : null,
                            'subtasks' => $card->subtasks->map(function ($subtask) {
                                return [
                                    'id' => $subtask->subtask_id,
                                    'title' => $subtask->subtask_title,
                                    'status' => $subtask->status,
                                    'actual_hours' => $subtask->actual_hours,
                                ];
                            })->values(),
                        ];
                    })->values(),
                ];
            })->values(),
        ];
    })->values();
@endphp

@section('title', 'Monitoring Proyek')
@section('page-title', 'Monitoring Proyek')

@section('page-toolbar')
<div class="monitoring-toolbar">
    
    <form method="GET" class="search-form d-flex gap-2">
        <input type="text" name="q" class="form-control" placeholder="Cari nama proyek..." value="{{ request('q') }}">
        <button class="btn btn-primary" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>
</div>

@endsection

@section('scripts')
    @parent
    <script>
    (() => {
        const searchInput = document.querySelector('.monitoring-toolbar input[name="q"]');
        const searchForm = searchInput ? searchInput.closest('form') : null;
        const cards = Array.from(document.querySelectorAll('[data-project-card]'));
        const emptyState = document.getElementById('monitoringSearchEmpty');

        if (!searchInput || cards.length === 0) {
            return;
        }

        const filterProjects = (rawTerm = '') => {
            const queryValue = (rawTerm || '').trim();
            const term = queryValue.toLowerCase();
            let visible = 0;

            cards.forEach(card => {
                const keywords = (card.dataset.projectKeywords || '').toLowerCase();
                const isMatch = !term || keywords.includes(term);
                card.classList.toggle('d-none', !isMatch);
                if (isMatch) {
                    visible += 1;
                }
            });

            if (emptyState) {
                emptyState.classList.toggle('d-none', visible !== 0);
            }

            const url = new URL(window.location.href);
            if (queryValue) {
                url.searchParams.set('q', queryValue);
            } else {
                url.searchParams.delete('q');
            }
            window.history.replaceState({}, '', url);
        };

        searchInput.addEventListener('input', event => filterProjects(event.target.value));

        if (searchForm) {
            searchForm.addEventListener('submit', event => {
                event.preventDefault();
                filterProjects(searchInput.value);
            });
        }

        filterProjects(searchInput.value);
    })();
    </script>
    <script>
    (() => {
        const sheet = document.getElementById('monitoringMemberSheet');
        if (!sheet) {
            return;
        }

        const idleUsers = @json($idleAssignableUsers);
        const detailProjects = @json($projectDetailPayload);
        const form = document.getElementById('memberSheetForm');
        const userSelect = document.getElementById('memberSheetUserSelect');
        const roleInput = document.getElementById('memberSheetRoleInput');
        const subtitleEl = document.getElementById('memberSheetSubtitle');
        const emptyState = document.getElementById('memberSheetEmpty');
        const userWrapper = document.getElementById('memberSheetUserWrapper');
        const submitBtn = sheet.querySelector('[data-member-sheet-submit]');
        const dismissTriggers = sheet.querySelectorAll('[data-member-sheet-dismiss]');
        const triggers = document.querySelectorAll('[data-add-member-trigger]');
        const body = document.body;
        let hideTimeout;

        const renderOptions = () => {
            if (!userSelect) {
                return;
            }

            userSelect.innerHTML = '<option value="">-- Pilih anggota idle --</option>';
            idleUsers.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.dataset.role = user.role;
                const roleLabel = user.role === 'developer' ? 'Developer' : 'Designer';
                option.textContent = `${user.name} (${user.username}) • ${roleLabel}`;
                userSelect.appendChild(option);
            });

            const hasIdle = idleUsers.length > 0;
            if (userWrapper) {
                userWrapper.classList.toggle('d-none', !hasIdle);
            }
            if (emptyState) {
                emptyState.classList.toggle('d-none', hasIdle);
            }
            if (submitBtn) {
                submitBtn.disabled = true;
            }
        };

        renderOptions();

        const setBodyScroll = isOpen => {
            body.classList.toggle('member-sheet-open', isOpen);
        };

        const openSheet = ({ projectName, action }) => {
            if (!form || !userSelect) {
                return;
            }

            if (hideTimeout) {
                clearTimeout(hideTimeout);
                hideTimeout = null;
            }

            form.setAttribute('action', action || '#');
            if (subtitleEl) {
                subtitleEl.textContent = projectName || 'Pilih proyek';
            }

            userSelect.value = '';
            roleInput.value = '';
            if (submitBtn) {
                submitBtn.disabled = true;
            }

            sheet.removeAttribute('hidden');
            sheet.classList.remove('hidden');
            sheet.setAttribute('aria-hidden', 'false');
            requestAnimationFrame(() => {
                sheet.classList.add('member-sheet--active');
                setBodyScroll(true);
            });
        };

        const closeSheet = () => {
            sheet.classList.remove('member-sheet--active');
            setBodyScroll(false);
            hideTimeout = setTimeout(() => {
                sheet.classList.add('hidden');
                sheet.setAttribute('hidden', 'true');
                sheet.setAttribute('aria-hidden', 'true');
            }, 220);
        };

        triggers.forEach(trigger => {
            trigger.addEventListener('click', () => {
                openSheet({
                    projectName: trigger.dataset.projectName,
                    action: trigger.dataset.projectAction
                });
            });
        });

        dismissTriggers.forEach(trigger => {
            trigger.addEventListener('click', closeSheet);
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && sheet.classList.contains('member-sheet--active')) {
                closeSheet();
            }
        });

        if (userSelect) {
            userSelect.addEventListener('change', () => {
                const selected = userSelect.selectedOptions[0];
                roleInput.value = selected ? (selected.dataset.role || '') : '';
                if (submitBtn) {
                    submitBtn.disabled = !selected;
                }
            });
        }

        const escapeHtml = value => {
            if (value === null || value === undefined) {
                return '';
            }
            return value.toString()
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        const projectMap = detailProjects.reduce((acc, project) => {
            acc[String(project.id)] = project;
            return acc;
        }, {});

        const detailSheet = document.getElementById('monitoringDetailSheet');
        const detailContent = document.getElementById('detailSheetContent');
        const detailTitle = document.getElementById('detailSheetTitle');
        const detailSubtitle = document.getElementById('detailSheetSubtitle');
        const detailTriggers = document.querySelectorAll('[data-detail-sheet-trigger]');
        const detailDismissTriggers = detailSheet ? detailSheet.querySelectorAll('[data-detail-sheet-dismiss]') : [];
        const setDetailBodyState = isOpen => {
            document.body.classList.toggle('detail-sheet-open', isOpen);
        };

        if (detailSheet && detailSheet.parentElement !== document.body) {
            document.body.appendChild(detailSheet);
        }

        const renderSubtasks = (subtasks = [], cardTitle = '') => {
            if (!subtasks || subtasks.length === 0) {
                return '<div class="detail-empty mt-2">Belum ada subtask untuk card ini.</div>';
            }

            return `
                <div class="detail-subtasks">
                    ${subtasks.map(subtask => `
                        <div class="detail-subtask">
                            <div>
                                <div class="detail-subtask__title">${escapeHtml(subtask.title || 'Subtask')}</div>
                                <div class="detail-subtask__meta">
                                    Status: ${escapeHtml((subtask.status || 'todo').replace(/_/g, ' '))}
                                    ${subtask.actual_hours ? ` · ${escapeHtml(subtask.actual_hours)} jam` : ''}
                                </div>
                            </div>
                            <div class="detail-card__actions">
                                <button type="button"
                                        class="detail-comment-btn"
                                        data-comment-sheet-trigger="true"
                                        data-comment-type="subtask"
                                        data-comment-id="${subtask.id}"
                                        data-comment-title="${escapeHtml(subtask.title || 'Subtask')}"
                                        data-comment-subtitle="${escapeHtml(cardTitle)}">
                                    <i class="bi bi-chat-dots me-1"></i>Komentar
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        };

        const renderCards = (board) => {
            if (!board.cards || board.cards.length === 0) {
                return '<div class="detail-empty">Belum ada card pada board ini.</div>';
            }

            return board.cards.map(card => `
                <div class="detail-card">
                    <div class="detail-card__header">
                        <div>
                            <div class="detail-card__title">${escapeHtml(card.title || 'Card')}</div>
                            <div class="detail-card__meta">
                                Prioritas: ${escapeHtml(card.priority || '-')} · Status: ${escapeHtml((card.status || '-').replace(/_/g, ' '))}
                                ${card.deadline ? ` · Deadline: ${escapeHtml(card.deadline)}` : ''}
                            </div>
                        </div>
                        <div class="detail-card__actions">
                            <button type="button"
                                    class="detail-comment-btn"
                                    data-comment-sheet-trigger="true"
                                    data-comment-type="card"
                                    data-comment-id="${card.id}"
                                    data-comment-title="${escapeHtml(card.title || 'Card')}"
                                    data-comment-subtitle="Board: ${escapeHtml(board.name || '')}">
                                <i class="bi bi-chat-dots me-1"></i>Komentar Card
                            </button>
                        </div>
                    </div>
                    ${card.description ? `<p class="mt-2 mb-2">${escapeHtml(card.description)}</p>` : ''}
                    ${renderSubtasks(card.subtasks, card.title || 'Card')}
                </div>
            `).join('');
        };

        const renderProjectDetail = project => {
            if (!project.boards || project.boards.length === 0) {
                return '<div class="detail-empty">Belum ada board atau card pada proyek ini.</div>';
            }

            return project.boards.map(board => `
                <div class="detail-board">
                    <div class="detail-board__title">${escapeHtml(board.name || 'Board')}</div>
                    ${renderCards(board)}
                </div>
            `).join('');
        };

        const openDetailSheet = projectId => {
            if (!detailSheet || !detailContent) return;
            const project = projectMap[String(projectId)];
            if (!project) {
                detailContent.innerHTML = '<div class="detail-empty">Data proyek tidak ditemukan.</div>';
            } else {
                detailTitle.textContent = project.name || 'Detail Proyek';
                detailSubtitle.textContent = project.deadline
                    ? `Deadline: ${project.deadline}`
                    : 'Detail proyek';
                detailContent.innerHTML = renderProjectDetail(project);
            }

            detailSheet.removeAttribute('hidden');
            detailSheet.classList.remove('hidden');
            detailSheet.setAttribute('aria-hidden', 'false');
            requestAnimationFrame(() => {
                detailSheet.classList.add('detail-sheet--active');
                setDetailBodyState(true);
            });
        };

        const closeDetailSheet = () => {
            if (!detailSheet) return;
            detailSheet.classList.remove('detail-sheet--active');
            setDetailBodyState(false);
            setTimeout(() => {
                detailSheet.classList.add('hidden');
                detailSheet.setAttribute('hidden', 'true');
                detailSheet.setAttribute('aria-hidden', 'true');
            }, 220);
        };

        detailTriggers.forEach(trigger => {
            trigger.addEventListener('click', () => {
                openDetailSheet(trigger.dataset.projectId);
            });
        });

        detailDismissTriggers.forEach(trigger => {
            trigger.addEventListener('click', closeDetailSheet);
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && detailSheet && detailSheet.classList.contains('detail-sheet--active')) {
                closeDetailSheet();
            }
        });

        document.addEventListener('click', event => {
            const commentTrigger = event.target.closest('[data-comment-sheet-trigger]');
            if (!commentTrigger) {
                return;
            }
            if (detailSheet && detailSheet.classList.contains('detail-sheet--active')) {
                closeDetailSheet();
            }
        });
    })();
    </script>
@endsection

@section('content')
<style>
    .monitoring-page {
        width: 100%;
    }

    .monitoring-toolbar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        width: 100%;
    }

    .monitoring-toolbar .toolbar-text {
        color: rgba(226, 232, 240, 0.85) !important;
        margin-bottom: 0;
    }

    [data-theme="light"] .monitoring-toolbar .toolbar-text {
        color: #475569 !important;
    }

    .monitoring-page .search-form .form-control {
        background: rgba(15, 23, 42, 0.4);
        border: 1px solid rgba(148, 163, 184, 0.4);
        color: #e5e7eb;
        border-radius: 14px;
        min-width: 240px;
    }

    .monitoring-page .search-form .form-control::placeholder {
        color: rgba(226, 232, 240, 0.7);
    }

    [data-theme="light"] .monitoring-page .search-form .form-control {
        background: #ffffff;
        border-color: #cbd5f5;
        color: #1f2937;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    }

    [data-theme="light"] .monitoring-page .search-form .form-control::placeholder {
        color: #94a3b8;
    }

    .monitoring-card {
        border-radius: 22px;
        background: rgba(17, 24, 39, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #e5e7eb;
        backdrop-filter: blur(18px);
    }

    .monitoring-card .text-muted {
        color: rgba(148, 163, 184, 0.85) !important;
    }

    [data-theme="light"] .monitoring-card {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #1f2937;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }

    [data-theme="light"] .monitoring-card .text-muted {
        color: #64748b !important;
    }

    .monitoring-page .progress.track {
        height: 10px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.35);
        overflow: hidden;
    }

    .monitoring-page .progress.track .progress-bar {
        background: linear-gradient(135deg, #8b5cf6, #3b82f6);
    }

    .status-card {
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 14px;
        background: rgba(15, 23, 42, 0.4);
        color: #e5e7eb;
        padding: 0.75rem;
    }

    [data-theme="light"] .status-card {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #1f2937;
    }

    .participant-badge {
        background: rgba(15, 23, 42, 0.45);
        color: #e5e7eb;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 0.85rem;
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    [data-theme="light"] .participant-badge {
        background: #eef2ff;
        border-color: #c7d2fe;
        color: #1f2937;
    }

    .badge-deadline {
        background: rgba(139, 92, 246, 0.15);
        color: #c4b5fd;
        border: 1px solid rgba(139, 92, 246, 0.35);
        border-radius: 12px;
        padding: 0.35rem 0.85rem;
        font-size: 0.85rem;
    }

    [data-theme="light"] .badge-deadline {
        background: rgba(59, 130, 246, 0.1);
        color: #1d4ed8;
        border-color: rgba(59, 130, 246, 0.25);
    }

    .btn-add-member {
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.4);
        background: rgba(15, 23, 42, 0.35);
        color: #e5e7eb;
        padding: 0.35rem 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.85rem;
        transition: all 0.25s ease;
    }

    .btn-add-member:hover,
    .btn-add-member:focus-visible {
        border-color: rgba(139, 92, 246, 0.8);
        color: #ffffff;
        background: rgba(139, 92, 246, 0.3);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.45);
    }

    [data-theme="light"] .btn-add-member {
        background: #eef2ff;
        border-color: #c7d2fe;
        color: #312e81;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    }

    [data-theme="light"] .btn-add-member:hover,
    [data-theme="light"] .btn-add-member:focus-visible {
        background: #c7d2fe;
        color: #1e1b4b;
        border-color: #a5b4fc;
    }

    .btn-detail {
        border-radius: 999px;
        border: 1px solid rgba(59, 130, 246, 0.45);
        background: rgba(37, 99, 235, 0.2);
        color: #bfdbfe;
        padding: 0.35rem 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.85rem;
        transition: all 0.25s ease;
    }

    .btn-detail:hover,
    .btn-detail:focus-visible {
        background: rgba(59, 130, 246, 0.35);
        color: #ffffff;
        border-color: rgba(59, 130, 246, 0.75);
        box-shadow: 0 12px 24px rgba(30, 64, 175, 0.35);
    }

    [data-theme="light"] .btn-detail {
        background: #e0f2fe;
        color: #1d4ed8;
        border-color: rgba(59, 130, 246, 0.55);
        box-shadow: 0 10px 22px rgba(30, 64, 175, 0.12);
    }

    [data-theme="light"] .btn-detail:hover,
    [data-theme="light"] .btn-detail:focus-visible {
        background: #bfdbfe;
        color: #1e3a8a;
    }

    .detail-sheet {
        position: fixed;
        inset: 0;
        display: grid;
        align-items: flex-end;
        justify-items: center;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.25s ease;
        z-index: 1400;
    }

    .detail-sheet.hidden {
        display: none;
    }

    .detail-sheet.detail-sheet--active {
        opacity: 1;
        pointer-events: auto;
    }

    .detail-sheet__overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(10px);
    }

    .detail-sheet__panel {
        position: relative;
        width: min(960px, 100% - 1.5rem);
        max-height: 88vh;
        background: rgba(17, 24, 39, 0.96);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 28px 28px 0 0;
        padding: 1.5rem;
        transform: translateY(32px);
        transition: transform 0.25s ease;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .detail-sheet.detail-sheet--active .detail-sheet__panel {
        transform: translateY(0);
    }

    .detail-sheet__handle {
        width: 54px;
        height: 4px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.45);
        margin: 0 auto;
    }

    .detail-sheet__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .detail-sheet__title {
        font-size: 1.4rem;
        font-weight: 600;
        color: #f8fafc;
        margin-bottom: 0.25rem;
    }

    .detail-sheet__subtitle {
        color: rgba(226, 232, 240, 0.7);
        margin-bottom: 0;
    }

    .detail-sheet__close {
        border: none;
        background: rgba(148, 163, 184, 0.18);
        color: rgba(248, 250, 252, 0.9);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .detail-sheet__close:hover {
        background: rgba(99, 102, 241, 0.35);
        color: #ffffff;
    }

    .detail-sheet__body {
        overflow-y: auto;
        padding-right: 0.25rem;
        flex: 1 1 auto;
    }

    .detail-board {
        margin-bottom: 1.25rem;
    }

    .detail-board__title {
        font-size: 1.05rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #e2e8f0;
    }

    .detail-card {
        border: 1px solid rgba(148, 163, 184, 0.5);
        border-radius: 16px;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.6);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);
        margin-bottom: 0.75rem;
    }

    .detail-card__header {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .detail-card__title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .detail-card__meta {
        font-size: 0.85rem;
        color: rgba(226, 232, 240, 0.7);
    }

    .detail-card__actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .detail-comment-btn {
        border-radius: 999px;
        border: 1px solid rgba(59, 130, 246, 0.45);
        background: rgba(59, 130, 246, 0.18);
        color: #bfdbfe;
        padding: 0.2rem 0.85rem;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.2s ease;
    }

    .detail-comment-btn:hover,
    .detail-comment-btn:focus-visible {
        color: #fff;
        border-color: rgba(96, 165, 250, 0.85);
        background: rgba(59, 130, 246, 0.35);
        box-shadow: 0 8px 20px rgba(30, 64, 175, 0.45);
    }

    [data-theme="light"] .detail-comment-btn {
        background: #e0f2fe;
        border-color: rgba(59, 130, 246, 0.55);
        color: #1e3a8a;
        box-shadow: 0 10px 18px rgba(30, 64, 175, 0.12);
    }

    [data-theme="light"] .detail-comment-btn:hover,
    [data-theme="light"] .detail-comment-btn:focus-visible {
        background: #bfdbfe;
        color: #1d4ed8;
    }

    .detail-subtasks {
        border-top: 1px solid rgba(148, 163, 184, 0.35);
        margin-top: 0.75rem;
        padding-top: 0.75rem;
    }

    .detail-subtask {
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.4rem 0;
        border-bottom: 1px dashed rgba(148, 163, 184, 0.25);
    }

    .detail-subtask:last-child {
        border-bottom: none;
    }

    .detail-subtask__title {
        font-size: 0.9rem;
        font-weight: 500;
    }

    .detail-subtask__meta {
        font-size: 0.8rem;
        color: rgba(226, 232, 240, 0.65);
    }

    .detail-empty {
        text-align: center;
        padding: 1rem;
        border: 1px dashed rgba(148, 163, 184, 0.4);
        border-radius: 12px;
        color: rgba(226, 232, 240, 0.65);
        font-size: 0.9rem;
    }

    body.detail-sheet-open {
        overflow: hidden;
    }

    [data-theme="light"] .detail-sheet__overlay {
        background: rgba(15, 23, 42, 0.35);
    }

    [data-theme="light"] .detail-sheet__panel {
        background: #ffffff;
        border-color: rgba(148, 163, 184, 0.3);
        color: #0f172a;
        box-shadow: 0 -20px 45px rgba(15, 23, 42, 0.12);
    }

    [data-theme="light"] .detail-board__title {
        color: #0f172a;
    }

    [data-theme="light"] .detail-sheet__title {
        color: #0f172a;
    }

    [data-theme="light"] .detail-sheet__subtitle {
        color: #1f2937;
    }

    [data-theme="light"] .detail-card {
        background: #f8fafc;
        border-color: rgba(148, 163, 184, 0.4);
    }

    [data-theme="light"] .detail-card__meta,
    [data-theme="light"] .detail-subtask__meta {
        color: #475569;
    }

    [data-theme="light"] .detail-empty {
        border-color: rgba(148, 163, 184, 0.5);
        color: #475569;
    }

    .member-sheet {
        position: fixed;
        inset: 0;
        display: grid;
        align-items: flex-end;
        justify-items: center;
        z-index: 1500;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .member-sheet.hidden {
        display: none;
    }

    .member-sheet.member-sheet--active {
        opacity: 1;
        pointer-events: auto;
    }

    .member-sheet__overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(10px);
    }

    [data-theme="light"] .member-sheet__overlay {
        background: rgba(15, 23, 42, 0.35);
    }

    .member-sheet__panel {
        position: relative;
        width: min(480px, 100% - 1.5rem);
        max-height: 82vh;
        background: rgba(17, 24, 39, 0.96);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 28px 28px 0 0;
        padding: 1.5rem;
        transform: translateY(24px);
        transition: transform 0.25s ease;
    }

    .member-sheet.member-sheet--active .member-sheet__panel {
        transform: translateY(0);
    }

    [data-theme="light"] .member-sheet__panel {
        background: #ffffff;
        border-color: rgba(15, 23, 42, 0.08);
        box-shadow: 0 -20px 40px rgba(15, 23, 42, 0.1);
    }

    .member-sheet__handle {
        width: 48px;
        height: 4px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.45);
        margin: 0 auto 1rem;
    }

    .member-sheet__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .member-sheet__title {
        font-size: 1.25rem;
        color: #f8fafc;
        font-weight: 600;
        margin-bottom: 0;
    }

    .member-sheet__subtitle {
        font-size: 0.9rem;
        color: rgba(226, 232, 240, 0.75);
        margin-bottom: 0.25rem;
    }

    [data-theme="light"] .member-sheet__title {
        color: #0f172a;
    }

    [data-theme="light"] .member-sheet__subtitle {
        color: #475569;
    }

    .member-sheet__close {
        border: none;
        background: rgba(148, 163, 184, 0.18);
        color: rgba(248, 250, 252, 0.9);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .member-sheet__close:hover {
        background: rgba(99, 102, 241, 0.35);
        color: #ffffff;
    }

    [data-theme="light"] .member-sheet__close {
        background: rgba(15, 23, 42, 0.08);
        color: #0f172a;
    }

    .member-sheet__empty {
        border: 1px dashed rgba(148, 163, 184, 0.4);
        border-radius: 16px;
        padding: 1rem;
        color: rgba(226, 232, 240, 0.8);
        font-size: 0.9rem;
    }

    [data-theme="light"] .member-sheet__empty {
        border-color: rgba(71, 85, 105, 0.35);
        color: #475569;
    }

    .member-sheet__form-note {
        font-size: 0.8rem;
        color: rgba(226, 232, 240, 0.65);
    }

    [data-theme="light"] .member-sheet__form-note {
        color: #6366f1;
    }

    .member-sheet-open {
        overflow: hidden;
    }
</style>

<div class="monitoring-page container py-4">
    @forelse ($projects as $project)
        @php
            $memberNames = $project->members->pluck('user.full_name')->filter()->implode(' ');
            $searchKeywords = Str::lower(trim(
                ($project->project_name ?? '') . ' ' .
                ($project->description ?? '') . ' ' .
                $memberNames
            ));
        @endphp
        <div class="monitoring-card card mb-4 border-0 shadow-sm"
             data-project-card
             data-project-keywords="{{ $searchKeywords }}">
            <div class="card-body">
                <div class="d-flex justify-content-between flex-wrap gap-3">
                    <div>
                        <h2 class="h5 mb-1">{{ $project->project_name }}</h2>
                        <p class="mb-2 text-muted">{{ $project->description ?: 'Belum ada deskripsi singkat.' }}</p>
                        <span class="badge-deadline">
                            Deadline: {{ optional($project->deadline)->format('d M Y') ?? '-' }}
                        </span>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold">{{ $project->progress }}% selesai</div>
                        <small class="text-muted">{{ $project->cards_done }} dari {{ $project->cards_total }} card selesai</small>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="progress track">
                        <div class="progress-bar" role="progressbar" style="width: {{ $project->progress }}%"></div>
                    </div>
                </div>

                <div class="row mt-3 text-center g-2">
                    @foreach (['To Do' => 'todo_count', 'In Progress' => 'in_progress_count', 'Review' => 'review_count', 'Done' => 'done_count'] as $label => $field)
                        <div class="col-6 col-md-3">
                            <div class="status-card h-100">
                                <div class="fw-semibold">{{ $project->$field }}</div>
                                <small class="text-muted">{{ $label }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-start">
                        <div class="d-flex flex-wrap gap-2 align-items-center flex-grow-1">
                            <span class="text-muted small">Anggota aktif:</span>
                            @forelse ($project->members->take(6) as $member)
                                <span class="participant-badge">
                                    {{ optional($member->user)->full_name ?? '-' }}
                                    <small class="text-muted">({{ optional($member->user)->role ?? $member->role }})</small>
                                </span>
                            @empty
                                <span class="text-muted small">Belum ada anggota terdaftar.</span>
                            @endforelse
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button"
                                    class="btn-detail flex-shrink-0"
                                    data-detail-sheet-trigger="true"
                                    data-project-id="{{ $project->project_id }}"
                                    data-project-name="{{ $project->project_name }}">
                                <i class="bi bi-eye"></i>
                                <span>Detail</span>
                            </button>
                            <button type="button"
                                    class="btn-add-member flex-shrink-0"
                                    data-add-member-trigger="true"
                                    data-project-id="{{ $project->project_id }}"
                                    data-project-name="{{ $project->project_name }}"
                                    data-project-action="{{ route('admin.projects.members.add', $project->project_id) }}">
                                <i class="bi bi-person-plus"></i>
                                <span>Tambah Member</span>
                            </button>
                            <a href="{{ route('admin.reports.project.pdf', $project) }}" target="_blank" rel="noopener" class="btn-add-member flex-shrink-0">
                                <i class="bi bi-printer"></i>
                                <span>Cetak PDF</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <p class="text-muted mb-1">Belum ada proyek yang dapat dimonitor.</p>
        </div>
    @endforelse
    @if($projects->count())
        <div id="monitoringSearchEmpty" class="text-center py-5 d-none">
            <p class="text-muted mb-1">Tidak ada proyek yang cocok dengan pencarian.</p>
        </div>
    @endif
</div>
<div id="monitoringMemberSheet" class="member-sheet hidden" aria-hidden="true" hidden>
    <div class="member-sheet__overlay" data-member-sheet-dismiss></div>
    <div class="member-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="memberSheetTitle">
        <div class="member-sheet__handle"></div>
        <div class="member-sheet__header">
            <div>
                <p class="member-sheet__subtitle mb-1" id="memberSheetSubtitle">Pilih proyek</p>
                <h3 class="member-sheet__title mb-0" id="memberSheetTitle">Tambah Member</h3>
            </div>
            <button type="button" class="member-sheet__close" data-member-sheet-dismiss aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="memberSheetForm" method="POST">
            @csrf
            <input type="hidden" name="role" id="memberSheetRoleInput">
            <div class="mb-3" id="memberSheetUserWrapper">
                <label for="memberSheetUserSelect" class="form-label">Pilih anggota idle</label>
                <select class="form-select" id="memberSheetUserSelect" name="user_id" required></select>
                <p class="member-sheet__form-note mt-2 mb-0">
                    Hanya developer dan designer dengan status idle yang dapat ditambahkan.
                </p>
            </div>
            <div class="member-sheet__empty text-center d-none" id="memberSheetEmpty">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <span>Belum ada developer/designer dengan status idle.</span>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-outline-secondary" data-member-sheet-dismiss>Batal</button>
                <button type="submit" class="btn btn-primary" data-member-sheet-submit>Tambahkan</button>
            </div>
        </form>
    </div>
</div>
<div id="monitoringDetailSheet" class="detail-sheet hidden" aria-hidden="true" hidden>
    <div class="detail-sheet__overlay" data-detail-sheet-dismiss></div>
    <div class="detail-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="detailSheetTitle">
        <div class="detail-sheet__handle"></div>
        <div class="detail-sheet__header">
            <div>
                <p class="detail-sheet__subtitle mb-1" id="detailSheetSubtitle">Project</p>
                <h3 class="detail-sheet__title mb-0" id="detailSheetTitle">Detail Proyek</h3>
            </div>
            <button type="button" class="detail-sheet__close" data-detail-sheet-dismiss aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="detail-sheet__body" id="detailSheetContent">
            <div class="detail-empty">
                <i class="bi bi-eye"></i>
                <p class="mb-0">Pilih proyek untuk melihat detail card dan subtask.</p>
            </div>
        </div>
    </div>
</div>
@include('components.comment-bottom-sheet')
@endsection
