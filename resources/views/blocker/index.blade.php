@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Solve Blocker
                    </h3>
                    <a href="{{ route('blocker.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Laporkan Blocker
                    </a>
                </div>
                
                <div class="card-body">
                    @php $supports = $supportsSubtask ?? true; @endphp

                    @if(!$supports)
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Struktur blocker masih menggunakan card langsung. Jalankan <code>php artisan migrate</code> untuk mengaktifkan pelaporan berdasarkan subtask.
                        </div>
                    @endif

                    @if($blockers->count() > 0)
                        <div class="blocker-card-grid">
                            @foreach($blockers as $index => $blocker)
                                @php
                                    $statusLabel = ucfirst(str_replace('_', ' ', $blocker->status));
                                    $priorityLabel = ucfirst($blocker->priority);
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
                                <div class="blocker-card" data-status="{{ $blocker->status }}" data-priority="{{ $blocker->priority }}">
                                    <div class="blocker-card-header d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="blocker-card-index">#{{ $index + 1 }}</span>
                                                <span class="badge-chip priority-{{ $blocker->priority }}">{{ $priorityLabel }}</span>
                                            </div>
                                            <h5 class="blocker-card-title text-grey-custom mb-1">{{ $title }}</h5>
                                            @if($subtitle)
                                                <small class="blocker-card-subtitle text-gresy-custom">{{ $subtitle }}</small>
                                            @endif
                                        </div>
                                        <span class="badge-chip status-{{ $blocker->status }}">{{ $statusLabel }}</span>
                                    </div>
                                    <p class="blocker-card-description text-grey-custom">
                                        {{ \Illuminate\Support\Str::limit($blocker->description, 180) }}
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
                                        <a href="{{ route('blocker.show', $blocker) }}" class="btn-blocker-action btn-blocker-info">
                                            <i class="fas fa-eye"></i>
                                            <span>Detail Blocker</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                <h4 class="text-grey-custom">Belum ada blocker yang dilaporkan</h4>
                                <p  class="text-grey-custom">Jika Anda mengalami kesulitan dalam menyelesaikan task, silakan laporkan blocker untuk mendapatkan bantuan dari team lead.</p>
                                <a href="{{ route('blocker.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Laporkan Blocker Pertama
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<style>
.blocker-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.card-header.d-flex {
    gap: 0.75rem;
}

.card-header .card-title {
    margin-bottom: 0;
}

.card-header .btn {
    flex-shrink: 0;
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
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
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

.priority-urgent {
    background: rgba(239, 68, 68, 0.25);
    border-color: rgba(248, 113, 113, 0.5);
    color: #fecaca;
}

.priority-high {
    background: rgba(251, 191, 36, 0.2);
    border-color: rgba(252, 211, 77, 0.45);
    color: #fcd34d;
}

.priority-medium {
    background: rgba(59, 130, 246, 0.18);
    border-color: rgba(96, 165, 250, 0.45);
    color: #bfdbfe;
}

.priority-low {
    background: rgba(75, 85, 99, 0.35);
    border-color: rgba(148, 163, 184, 0.35);
    color: #e5e7eb;
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

.text-grey-custom {
    color: #edededff !important;
}

.text-gresy-custom {
    color: grey !important;
}

@media (max-width: 992px) {
    .card-header.d-flex {
        flex-wrap: wrap;
    }

    .card-header .btn {
        margin-left: auto;
    }

    .blocker-card-grid {
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.25rem;
    }

    .blocker-card {
        padding: 1.4rem;
    }

    .blocker-card-meta {
        gap: 0.85rem;
    }
}

@media (max-width: 576px) {
    .card-header.d-flex {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.65rem;
    }

    .card-header .btn {
        width: 100%;
    }

    .blocker-card-header {
        gap: 0.6rem;
    }

    .blocker-card-header > span {
        margin-left: auto;
    }

    .blocker-card {
        padding: 1.25rem;
    }
    
    .blocker-card-meta {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .btn-blocker-action {
        width: 100%;
        justify-content: center;
    }

    .blocker-card-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .blocker-card-description {
        min-height: auto;
    }
}
</style>
@endsection
