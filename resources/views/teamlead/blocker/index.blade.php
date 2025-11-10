@extends('layouts.app')

@section('title', 'Solver - Manajemen Blocker')
@section('page-title', 'Solver')

@section('page-toolbar')
<div class="topbar-search-wrapper">
    <div class="search-input-wrapper">
        <i class="bi bi-search"></i>
        <input type="search"
               id="searchInput"
               class="form-control"
               placeholder="Cari berdasarkan {{ ($supportsSubtask ?? true) ? 'subtask' : 'card' }}, user, atau deskripsi..."
               autocomplete="off">
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Manajemen Blocker
                    </h3>
                </div>
                
                <div class="card-body">
                    @php $supports = $supportsSubtask ?? true; @endphp

                    @if(!$supports)
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Struktur blocker belum dimigrasikan ke level subtask. Jalankan <code>php artisan migrate</code> agar tim dapat memilih subtask ketika melaporkan blocker.
                        </div>
                    @endif
                    <!-- Filter -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <label for="statusFilter" class="form-label text-uppercase small fw-semibold text-muted mb-2">
                                Filter Status
                            </label>
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>

                    @if($blockers->count() > 0)
                        <div id="blockerCardList" class="blocker-card-grid">
                            @foreach($blockers as $index => $blocker)
                                @php
                                    $statusLabel = ucfirst(str_replace('_', ' ', $blocker->status));
                                    $reporter = $blocker->user;
                                    $reporterName = optional($reporter)->full_name ?? optional($reporter)->username ?? 'User';
                                    $reporterRole = $reporter ? ucfirst(str_replace('_', ' ', $reporter->role)) : 'Tidak diketahui';
                                    $assigneeName = optional($blocker->assignedTo)->full_name;
                                    $title = 'Data tidak tersedia';
                                    $subtitle = null;
                                    if ($supports && $blocker->subtask) {
                                        $title = $blocker->subtask->subtask_title;
                                        $card = $blocker->subtask->card;
                                        if ($card) {
                                            $projectName = optional(optional($card->board)->project)->project_name;
                                            $subtitle = $card->card_title;
                                            if ($projectName) {
                                                $subtitle .= ' | ' . $projectName;
                                            }
                                        }
                                    } elseif (!$supports && isset($blocker->legacy_card)) {
                                        $title = $blocker->legacy_card->card_title;
                                        $legacyProject = optional(optional($blocker->legacy_card->board)->project)->project_name;
                                        if ($legacyProject) {
                                            $subtitle = $legacyProject;
                                        }
                                    }
                                @endphp
                                <div class="blocker-card" data-status="{{ $blocker->status }}">
                                    <div class="blocker-card-header d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="blocker-card-index">#{{ $index + 1 }}</span>
                                            </div>
                                            <h5 class="blocker-card-title text-grey-custom mb-1">{{ $title }}</h5>
                                            @if($subtitle)
                                                <small class="blocker-card-subtitle text-gresy-custom">{{ $subtitle }}</small>
                                            @endif
                                        </div>
                                        <span class="badge-chip status-{{ $blocker->status }}">{{ $statusLabel }}</span>
                                    </div>
                                    <p class="blocker-card-description text-grey-custom">
                                        {{ \Illuminate\Support\Str::limit($blocker->description, 160) }}
                                    </p>
                                    <div class="blocker-card-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-user-circle"></i>
                                            <div>
                                                <span class="meta-label">Pelapor</span>
                                                <span class="meta-value">{{ $reporterName }}</span>
                                                <span class="meta-subtext">{{ $reporterRole }}</span>
                                            </div>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-user-check"></i>
                                            <div>
                                                <span class="meta-label">Assigned To</span>
                                                <span class="meta-value">
                                                    {{ $assigneeName ?? 'Belum ditugaskan' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <div>
                                                <span class="meta-label">Dibuat</span>
                                                <span class="meta-value">{{ $blocker->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="blocker-card-actions d-flex flex-wrap gap-2">
                                        <a href="{{ route('teamlead.blocker.edit', $blocker) }}" class="btn-blocker-action">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        @if($blocker->status == 'pending')
                                            <button type="button" class="btn-blocker-action btn-blocker-success" onclick="assignBlocker({{ $blocker->blocker_id }})">
                                                <i class="fas fa-user-plus me-1"></i> Assign
                                            </button>
                                        @endif
                                        @if($blocker->status == 'in_progress')
                                            <button type="button" class="btn-blocker-action btn-blocker-info" onclick="resolveBlocker({{ $blocker->blocker_id }})">
                                                <i class="fas fa-check me-1"></i> Resolve
                                            </button>
                                            <button type="button" class="btn-blocker-action btn-blocker-danger" onclick="rejectBlocker({{ $blocker->blocker_id }})">
                                                <i class="fas fa-times me-1"></i> Reject
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div id="blockerNoResult" class="alert alert-purple-soft text-center d-none">
                            <i class="fas fa-search mb-2"></i>
                            <div>Blocker tidak ditemukan dengan filter saat ini.</div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                <h4 class="text-grey-custom">Belum ada blocker yang dilaporkan</h4>
                                <p class="text-grey-custom">Tim Anda belum melaporkan blocker apapun.</p>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Bottom Sheet -->
<div class="assign-sheet" id="assignSheet" aria-hidden="true">
    <div class="assign-sheet__overlay" data-assign-close></div>
    <div class="assign-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="assignSheetTitle">
        <div class="assign-sheet__handle"></div>
        <div class="assign-sheet__header">
            <div>
                <h5 class="assign-sheet__title" id="assignSheetTitle">
                    <i class="fas fa-user-plus me-2"></i>Assign Blocker
                </h5>
                <p class="assign-sheet__subtitle">Pilih team lead yang akan menangani blocker ini</p>
            </div>
            <button type="button" class="assign-sheet__close" data-assign-close aria-label="Tutup lembar assign">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="assignForm" method="POST" class="assign-sheet__form">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Assign ke Team Lead</label>
                <select name="assigned_to" class="form-select" required>
                    <option value="">Pilih Team Lead</option>
                    @foreach(\App\Models\User::where('role', 'team_lead')->get() as $teamLead)
                        <option value="{{ $teamLead->user_id }}">{{ $teamLead->full_name }}</option>
                    @endforeach
                </select>
                <div class="form-text">Pilih team lead yang akan menangani blocker ini</div>
            </div>
            <div class="assign-sheet__actions">
                <button type="button" class="btn btn-light" data-assign-close>
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus me-1"></i>Assign
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Resolve Blocker -->
<div class="modal fade" id="resolveModal" tabindex="-1" aria-labelledby="resolveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="resolveModalLabel">
                    <i class="fas fa-check me-2"></i>Resolve Blocker
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="resolveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Solusi</label>
                        <textarea name="solution" class="form-control" rows="5" 
                                  placeholder="Jelaskan solusi yang diberikan untuk mengatasi blocker ini..." required></textarea>
                        <div class="form-text">Deskripsikan bagaimana blocker ini diselesaikan</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Resolve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reject Blocker -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="fas fa-times me-2"></i>Reject Blocker
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Penolakan</label>
                        <textarea name="reason" class="form-control" rows="5" 
                                  placeholder="Jelaskan alasan penolakan blocker ini..." required></textarea>
                        <div class="form-text">Berikan alasan yang jelas mengapa blocker ini ditolak</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Bottom sheet Assign handling
const assignSheet = document.getElementById('assignSheet');
const assignCloseControls = document.querySelectorAll('[data-assign-close]');

function openAssignSheet() {
    if (!assignSheet) {
        return;
    }
    assignSheet.classList.add('assign-sheet--open');
    assignSheet.setAttribute('aria-hidden', 'false');
    document.body.classList.add('assign-sheet-open');
}

function closeAssignSheet() {
    if (!assignSheet) {
        return;
    }
    assignSheet.classList.remove('assign-sheet--open');
    assignSheet.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('assign-sheet-open');
}

assignCloseControls.forEach(trigger => {
    trigger.addEventListener('click', closeAssignSheet);
});

document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && assignSheet?.classList.contains('assign-sheet--open')) {
        closeAssignSheet();
    }
});

function assignBlocker(blockerId) {
    const form = document.getElementById('assignForm');
    if (!form) {
        return;
    }
    form.action = `/teamlead/blocker/${blockerId}/assign`;
    form.reset();
    openAssignSheet();
}

function resolveBlocker(blockerId) {
    const form = document.getElementById('resolveForm');
    form.action = `/teamlead/blocker/${blockerId}/resolve`;
    form.reset();
    
    const modal = new bootstrap.Modal(document.getElementById('resolveModal'));
    modal.show();
}

function rejectBlocker(blockerId) {
    const form = document.getElementById('rejectForm');
    form.action = `/teamlead/blocker/${blockerId}/reject`;
    form.reset();
    
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');

    if (statusFilter) statusFilter.addEventListener('change', filterBlockers);
    if (searchInput) searchInput.addEventListener('input', filterBlockers);

    filterBlockers();
    
    // Handle form submissions dengan feedback
    const forms = ['assignForm', 'resolveForm', 'rejectForm'];
    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
                submitBtn.disabled = true;
                
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 2000);
            });
        }
    });
});

function filterBlockers() {
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const cards = document.querySelectorAll('#blockerCardList .blocker-card');
    const emptyState = document.getElementById('blockerNoResult');

    if (!cards.length) {
        if (emptyState) emptyState.classList.add('d-none');
        return;
    }

    const statusValue = statusFilter ? statusFilter.value : '';
    const searchValue = searchInput ? searchInput.value.toLowerCase() : '';
    
    let visibleCount = 0;

    cards.forEach(card => {
        const status = card.getAttribute('data-status');
        const text = card.textContent.toLowerCase();

        const statusMatch = !statusValue || status === statusValue;
        const searchMatch = !searchValue || text.includes(searchValue);

        if (statusMatch && searchMatch) {
            card.classList.remove('d-none');
            visibleCount++;
        } else {
            card.classList.add('d-none');
        }
    });

    if (emptyState) {
        emptyState.classList.toggle('d-none', visibleCount !== 0);
    }
}
</script>

<style>
.topbar-search-wrapper {
    width: min(360px, 100%);
}

.topbar-search-wrapper .search-input-wrapper {
    position: relative;
}

.topbar-search-wrapper .search-input-wrapper input {
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.35);
    padding: 0.55rem 1rem 0.55rem 2.5rem;
    color: #e5e7eb;
    font-size: 0.9rem;
}

.topbar-search-wrapper .search-input-wrapper input::placeholder {
    color: rgba(226, 232, 240, 0.7);
}

.topbar-search-wrapper .search-input-wrapper i {
    position: absolute;
    top: 50%;
    left: 0.9rem;
    transform: translateY(-50%);
    color: rgba(148, 163, 184, 0.7);
    font-size: 1rem;
}

@media (max-width: 576px) {
    .topbar-search-wrapper {
        width: 100%;
    }
}

/* Card layout */
.blocker-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
}

.blocker-card {
    background: rgba(31, 41, 55, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    padding: 1.6rem;
    backdrop-filter: blur(16px);
    box-shadow: 0 24px 48px rgba(15, 23, 42, 0.35);
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    position: relative;
    overflow: hidden;
}

.blocker-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top right, rgba(139, 92, 246, 0.18), transparent 55%);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.blocker-card:hover {
    transform: translateY(-6px);
    border-color: rgba(139, 92, 246, 0.45);
    box-shadow: 0 28px 60px rgba(59, 130, 246, 0.25);
}

.blocker-card:hover::before {
    opacity: 1;
}

.blocker-card-header {
    margin-bottom: 1.1rem;
}

.blocker-card-subtitle {
    font-size: 0.8rem;
    letter-spacing: 0.01em;
}

.blocker-card-index {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(59, 130, 246, 0.15);
    border: 1px solid rgba(59, 130, 246, 0.35);
    font-weight: 600;
    color: #bfdbfe;
    font-size: 0.8rem;
}

.blocker-card-description {
    margin: 1.1rem 0;
    line-height: 1.6;
    min-height: 72px;
}

.blocker-card-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 1rem;
    margin-bottom: 1.35rem;
}

.meta-item {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    padding: 0.9rem 1rem;
    border-radius: 12px;
    background: rgba(17, 24, 39, 0.55);
    border: 1px solid rgba(255, 255, 255, 0.06);
}

.meta-item i {
    font-size: 1rem;
    margin-top: 0.15rem;
    color: rgba(139, 92, 246, 0.65);
}

.meta-label {
    display: block;
    font-size: 0.72rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: rgba(226, 232, 240, 0.7);
    margin-bottom: 0.25rem;
}

.meta-value {
    display: block;
    font-weight: 600;
    color: #f9fafb;
}

.meta-subtext {
    display: block;
    font-size: 0.75rem;
    color: rgba(203, 213, 225, 0.8);
}

.blocker-card-actions {
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    padding-top: 1rem;
}

.btn-blocker-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 0.95rem;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    background: rgba(79, 70, 229, 0.18);
    border: 1px solid rgba(99, 102, 241, 0.35);
    color: #c7d2fe;
    transition: all 0.25s ease;
    text-decoration: none;
}

.btn-blocker-action:hover {
    color: #fff;
    background: rgba(99, 102, 241, 0.35);
    border-color: rgba(129, 140, 248, 0.6);
    box-shadow: 0 12px 25px rgba(79, 70, 229, 0.25);
}

button.btn-blocker-action {
    border: none;
    cursor: pointer;
}

.btn-blocker-success {
    background: rgba(16, 185, 129, 0.18);
    border-color: rgba(16, 185, 129, 0.4);
    color: #a7f3d0;
}

.btn-blocker-success:hover {
    background: rgba(16, 185, 129, 0.35);
    border-color: rgba(34, 197, 94, 0.55);
    box-shadow: 0 12px 25px rgba(16, 185, 129, 0.25);
}

.btn-blocker-info {
    background: rgba(59, 130, 246, 0.2);
    border-color: rgba(59, 130, 246, 0.45);
    color: #bfdbfe;
}

.btn-blocker-info:hover {
    background: rgba(59, 130, 246, 0.38);
    border-color: rgba(96, 165, 250, 0.65);
    box-shadow: 0 12px 25px rgba(59, 130, 246, 0.25);
}

.btn-blocker-danger {
    background: rgba(239, 68, 68, 0.18);
    border-color: rgba(239, 68, 68, 0.4);
    color: #fecaca;
}

.btn-blocker-danger:hover {
    background: rgba(239, 68, 68, 0.38);
    border-color: rgba(248, 113, 113, 0.6);
    box-shadow: 0 12px 25px rgba(239, 68, 68, 0.25);
}

.badge-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    font-size: 0.72rem;
    letter-spacing: 0.04em;
    font-weight: 600;
    text-transform: uppercase;
    border: 1px solid transparent;
}

.status-pending {
    background: rgba(251, 191, 36, 0.22);
    border-color: rgba(234, 179, 8, 0.45);
    color: #facc15;
}

.status-in_progress {
    background: rgba(59, 130, 246, 0.22);
    border-color: rgba(59, 130, 246, 0.5);
    color: #93c5fd;
}

.status-resolved {
    background: rgba(16, 185, 129, 0.22);
    border-color: rgba(34, 197, 94, 0.45);
    color: #6ee7b7;
}

.status-rejected {
    background: rgba(239, 68, 68, 0.25);
    border-color: rgba(248, 113, 113, 0.5);
    color: #fca5a5;
}

.alert-purple-soft {
    background: rgba(76, 29, 149, 0.18);
    border: 1px solid rgba(139, 92, 246, 0.35);
    color: #e9d5ff;
    border-radius: 14px;
    padding: 1.25rem;
}

/* Assign bottom sheet */
.assign-sheet {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease;
    z-index: 1500;
}

.assign-sheet.assign-sheet--open {
    opacity: 1;
    pointer-events: auto;
}

.assign-sheet__overlay {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(12px);
}

.assign-sheet__panel {
    position: relative;
    width: min(520px, 100% - 1.5rem);
    background: rgba(17, 24, 39, 0.96);
    border-radius: 24px 24px 0 0;
    border: 1px solid rgba(255, 255, 255, 0.08);
    padding: 1.75rem 2rem;
    box-shadow: 0 -30px 60px rgba(15, 23, 42, 0.45);
    transform: translateY(24px);
    transition: transform 0.25s ease;
}

.assign-sheet.assign-sheet--open .assign-sheet__panel {
    transform: translateY(0);
}

.assign-sheet__handle {
    width: 44px;
    height: 5px;
    border-radius: 999px;
    background: rgba(148, 163, 184, 0.4);
    margin: 0 auto 1rem;
}

.assign-sheet__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}

.assign-sheet__title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #f8fafc;
    margin-bottom: 0.2rem;
}

.assign-sheet__subtitle {
    font-size: 0.9rem;
    color: rgba(226, 232, 240, 0.8);
    margin: 0;
}

.assign-sheet__close {
    border: none;
    background: rgba(148, 163, 184, 0.15);
    color: rgba(226, 232, 240, 0.85);
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.assign-sheet__close:hover {
    background: rgba(99, 102, 241, 0.25);
    color: #fff;
}

.assign-sheet__actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1.25rem;
}

body.assign-sheet-open {
    overflow: hidden;
}

@media (max-width: 576px) {
    .assign-sheet__panel {
        padding: 1.25rem 1.25rem 1.5rem;
    }
}

/* Modal layering */
.modal {
    z-index: 1060 !important;
}

.modal-backdrop {
    z-index: 1050 !important;
    opacity: 0.5 !important;
}

.modal .modal-dialog {
    position: relative;
    z-index: 1070;
}

.modal-content {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.25);
    border: none;
    border-radius: 18px;
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.modal-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.text-grey-custom {
    color: #edededff !important;
}

.text-gresy-custom {
    color: grey !important;
}

body.light-mode .blocker-card,
[data-theme="light"] .blocker-card {
    background: rgba(255, 255, 255, 0.95);
    border-color: rgba(15, 23, 42, 0.08);
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.15);
    color: #1f2937;
}

body.light-mode .blocker-card::before,
[data-theme="light"] .blocker-card::before {
    opacity: 1;
    background: radial-gradient(circle at top right, rgba(129, 140, 248, 0.18), transparent 55%);
}

body.light-mode .blocker-card:hover,
[data-theme="light"] .blocker-card:hover {
    border-color: rgba(59, 130, 246, 0.25);
    box-shadow: 0 28px 60px rgba(15, 23, 42, 0.2);
}

body.light-mode .blocker-card-actions,
[data-theme="light"] .blocker-card-actions {
    border-top: 1px solid rgba(15, 23, 42, 0.08);
}

body.light-mode .blocker-card-index,
[data-theme="light"] .blocker-card-index {
    background: rgba(226, 232, 240, 0.9);
    border-color: rgba(148, 163, 184, 0.5);
    color: #1d4ed8;
}

body.light-mode .blocker-card-subtitle,
[data-theme="light"] .blocker-card-subtitle {
    color: #475569;
}

body.light-mode .text-grey-custom,
[data-theme="light"] .text-grey-custom {
    color: #1f2937 !important;
}

body.light-mode .text-gresy-custom,
[data-theme="light"] .text-gresy-custom {
    color: #475569 !important;
}

body.light-mode .topbar-search-wrapper .search-input-wrapper input,
[data-theme="light"] .topbar-search-wrapper .search-input-wrapper input {
    background: rgba(255, 255, 255, 0.95);
    border-color: rgba(203, 213, 225, 0.8);
    color: #0f172a;
}

body.light-mode .topbar-search-wrapper .search-input-wrapper input::placeholder,
[data-theme="light"] .topbar-search-wrapper .search-input-wrapper input::placeholder {
    color: #94a3b8;
}

body.light-mode .blocker-card-meta .meta-item,
[data-theme="light"] .blocker-card-meta .meta-item {
    background: rgba(248, 250, 252, 0.95);
    border-color: rgba(203, 213, 225, 0.7);
}

body.light-mode .meta-label,
[data-theme="light"] .meta-label {
    color: #475569;
}

body.light-mode .meta-value,
[data-theme="light"] .meta-value {
    color: #0f172a;
}

body.light-mode .meta-subtext,
[data-theme="light"] .meta-subtext {
    color: #64748b;
}

body.light-mode .meta-item i,
[data-theme="light"] .meta-item i {
    color: #4f46e5;
}

body.light-mode .badge-chip,
[data-theme="light"] .badge-chip {
    border-color: rgba(203, 213, 225, 0.7);
    color: #0f172a;
}

body.light-mode .badge-chip.status-pending,
[data-theme="light"] .badge-chip.status-pending {
    background: rgba(250, 204, 21, 0.25);
    border-color: rgba(234, 179, 8, 0.45);
    color: #92400e;
}

body.light-mode .badge-chip.status-in_progress,
[data-theme="light"] .badge-chip.status-in_progress {
    background: rgba(59, 130, 246, 0.18);
    border-color: rgba(59, 130, 246, 0.35);
    color: #1d4ed8;
}

body.light-mode .badge-chip.status-resolved,
[data-theme="light"] .badge-chip.status-resolved {
    background: rgba(16, 185, 129, 0.18);
    border-color: rgba(34, 197, 94, 0.35);
    color: #166534;
}

body.light-mode .badge-chip.status-rejected,
[data-theme="light"] .badge-chip.status-rejected {
    background: rgba(248, 113, 113, 0.18);
    border-color: rgba(239, 68, 68, 0.4);
    color: #b91c1c;
}

body.light-mode .btn-blocker-action,
[data-theme="light"] .btn-blocker-action {
    color: #1f2937;
    background: rgba(99, 102, 241, 0.12);
    border-color: rgba(99, 102, 241, 0.25);
}

body.light-mode .btn-blocker-action:hover,
[data-theme="light"] .btn-blocker-action:hover {
    color: #111827;
    background: rgba(99, 102, 241, 0.2);
    border-color: rgba(99, 102, 241, 0.4);
}

body.light-mode .btn-blocker-success,
[data-theme="light"] .btn-blocker-success {
    background: rgba(34, 197, 94, 0.15);
    border-color: rgba(34, 197, 94, 0.35);
    color: #166534;
}

body.light-mode .btn-blocker-info,
[data-theme="light"] .btn-blocker-info {
    background: rgba(59, 130, 246, 0.15);
    border-color: rgba(59, 130, 246, 0.35);
    color: #1d4ed8;
}

body.light-mode .btn-blocker-danger,
[data-theme="light"] .btn-blocker-danger {
    background: rgba(248, 113, 113, 0.15);
    border-color: rgba(248, 113, 113, 0.35);
    color: #b91c1c;
}

body.light-mode .assign-sheet__panel,
[data-theme="light"] .assign-sheet__panel {
    background: rgba(255, 255, 255, 0.98);
    border-color: rgba(15, 23, 42, 0.05);
}

body.light-mode .assign-sheet__title,
[data-theme="light"] .assign-sheet__title {
    color: #0f172a;
}

body.light-mode .assign-sheet__subtitle,
[data-theme="light"] .assign-sheet__subtitle {
    color: #475569;
}

body.light-mode .assign-sheet__close,
[data-theme="light"] .assign-sheet__close {
    background: rgba(226, 232, 240, 0.9);
    color: #475569;
}

@media (max-width: 992px) {
    .blocker-card-grid {
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    }
}

@media (max-width: 576px) {
    .blocker-card {
        padding: 1.25rem;
    }
    
    .blocker-card-meta {
        grid-template-columns: 1fr;
    }
    
    .btn-blocker-action {
        width: 100%;
        justify-content: center;
    }
}
/* 🔹 1. Perbaikan alert ungu agar lebih kontras */
.alert-purple-soft {
    background: rgba(139, 92, 246, 0.2);
    border: 1px solid rgba(168, 85, 247, 0.4);
    color: #f3e8ff;
}

body.light-mode .alert-purple-soft,
[data-theme="light"] .alert-purple-soft {
    background: rgba(196, 181, 253, 0.25);
    border: 1px solid rgba(167, 139, 250, 0.4);
    color: #4c1d95;
}

/* 🔹 2. Security text — hitam saat light mode, putih saat dark */
.security-text {
    color: #f8fafc; /* default untuk dark mode */
}
body.light-mode .security-text,
[data-theme="light"] .security-text {
    color: #111827; /* hitam untuk light mode */
}

/* 🔹 3. Tombol keluar kanan bawah — warna lebih jelas & kontras */
.btn-logout {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    z-index: 999;
    border-radius: 50px;
    font-weight: 600;
    padding: 0.6rem 1.4rem;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
    transition: all 0.25s ease;
}

.btn-logout.btn-outline-light {
    color: #f9fafb;
    border-color: rgba(255, 255, 255, 0.6);
    background: rgba(255, 255, 255, 0.05);
}

.btn-logout.btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: #fff;
}

body.light-mode .btn-logout.btn-outline-light,
[data-theme="light"] .btn-logout.btn-outline-light {
    color: #1f2937;
    border-color: rgba(15, 23, 42, 0.3);
    background: rgba(255, 255, 255, 0.8);
}

body.light-mode .btn-logout.btn-outline-light:hover,
[data-theme="light"] .btn-logout.btn-outline-light:hover {
    background: rgba(15, 23, 42, 0.1);
    border-color: rgba(15, 23, 42, 0.5);
}

/* 🔹 4. Badge bg-primary-subtle text-uppercase mb-1 — buat lebih kontras */
.badge.bg-primary-subtle.text-uppercase.mb-1 {
    background-color: rgba(99, 102, 241, 0.25) !important;
    color: #c7d2fe !important;
    border: 1px solid rgba(129, 140, 248, 0.5);
    font-weight: 600;
    padding: 0.35rem 0.7rem;
    border-radius: 0.5rem;
}

body.light-mode .badge.bg-primary-subtle.text-uppercase.mb-1,
[data-theme="light"] .badge.bg-primary-subtle.text-uppercase.mb-1 {
    background-color: rgba(99, 102, 241, 0.15) !important;
    color: #1e3a8a !important;
    border: 1px solid rgba(99, 102, 241, 0.4);
}

</style>
@endsection
