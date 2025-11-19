@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-life-ring me-2"></i>Permintaan Blocker
                    </h3>
                </div>

                <div class="card-body">
                    @php $supports = $supportsSubtask ?? true; @endphp

                    @if(!$supports)
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Struktur blocker masih menggunakan card langsung. Jalankan <code>php artisan migrate</code> untuk mengaktifkan pelaporan berdasarkan subtask.
                        </div>
                    @endif

                    @if($blockers->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <p class="mb-0">Belum ada blocker yang dilaporkan tim Anda.</p>
                        </div>
                    @else
                        <div class="blocker-grid">
                            @foreach($blockers as $blocker)
                                @php
                                    $statusClass = $blocker->status === 'selesai' ? 'status-chip-success' : 'status-chip-warning';
                                    $statusLabel = $blocker->status === 'selesai' ? 'Selesai' : 'Pending';
                                    $title = 'Data tidak tersedia';
                                    $subtitle = null;
                                    if ($supports && $blocker->subtask) {
                                        $title = $blocker->subtask->subtask_title;
                                        $card = $blocker->subtask->card;
                                        if ($card) {
                                            $projectName = optional(optional($card->board)->project)->project_name;
                                            $subtitle = $card->card_title;
                                            if ($projectName) {
                                                $subtitle .= ' · ' . $projectName;
                                            }
                                        }
                                    } elseif (!$supports && isset($blocker->legacy_card)) {
                                        $title = $blocker->legacy_card->card_title;
                                        $subtitle = optional(optional($blocker->legacy_card->board)->project)->project_name;
                                    }
                                @endphp
                                <div class="blocker-card">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <div class="text-muted small">Pelapor: {{ optional($blocker->user)->full_name ?? optional($blocker->user)->username }}</div>
                                            <h5 class="mt-1 mb-1 blocker-card-title">{{ $title }}</h5>
                                            @if($subtitle)
                                                <small class="text-muted">{{ $subtitle }}</small>
                                            @endif
                                        </div>
                                        <span class="status-chip {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                        <span class="text-muted small">
                                            Dilaporkan {{ $blocker->created_at?->diffForHumans() ?? '-' }}
                                        </span>
                                        @if($blocker->status !== 'selesai')
                                            <form id="completeBlockerForm{{ $blocker->blocker_id }}" action="{{ route('teamlead.blocker.complete', $blocker) }}" method="POST" class="d-none">
                                                @csrf
                                            </form>
                                            <button type="button"
                                                    class="btn btn-success btn-sm"
                                                    data-action-sheet-trigger="true"
                                                    data-action-form="#completeBlockerForm{{ $blocker->blocker_id }}"
                                                    data-action-title="Tandai Blocker Selesai"
                                                    data-action-subtitle="{{ $title }}"
                                                    data-action-message="Pastikan blocker ini sudah benar-benar selesai sebelum menandainya selesai."
                                                    data-action-confirm-label="Selesai"
                                                    data-action-loading-label="Menandai...">
                                                <i class="fas fa-check me-1"></i>Tandai Selesai
                                            </button>
                                        @else
                                            <span class="badge bg-success-soft">
                                                <i class="fas fa-check me-1"></i>Ditandai selesai
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.blocker-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
}

.blocker-card {
    padding: 1.5rem;
    border-radius: 18px;
    background: rgba(15, 23, 42, 0.75);
    border: 1px solid rgba(148, 163, 184, 0.2);
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.35);
}

.status-chip {
    padding: 0.35rem 1rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid transparent;
}

.status-chip-success {
    background: rgba(34, 197, 94, 0.15);
    color: #bbf7d0;
    border-color: rgba(34, 197, 94, 0.35);
}

.status-chip-warning {
    background: rgba(250, 204, 21, 0.15);
    color: #fde68a;
    border-color: rgba(250, 204, 21, 0.35);
}

.bg-success-soft {
    background: rgba(34, 197, 94, 0.12);
    color: #86efac;
    border-radius: 999px;
    padding: 0.35rem 0.9rem;
    border: 1px solid rgba(34, 197, 94, 0.35);
}

.text-white {
    color: #f8fafc;
}

.blocker-card-title {
    color: #f8fafc;
}

[data-theme="light"] .blocker-card-title {
    color: #0f172a;
}

[data-theme="light"] .blocker-card {
    background: #ffffff;
    border-color: rgba(148, 163, 184, 0.35);
    box-shadow: 0 12px 24px rgba(148, 163, 184, 0.25);
    color: #0f172a;
}

[data-theme="light"] .blocker-card .text-white {
    color: #0f172a;
}

[data-theme="light"] .blocker-card .text-muted {
    color: rgba(71, 85, 105, 0.85) !important;
}

[data-theme="light"] .badge.bg-success-soft {
    background: rgba(16, 185, 129, 0.2);
    color: #15803d;
    border-color: rgba(16, 185, 129, 0.35);
}

[data-theme="light"] .status-chip {
    color: #0f172a;
}

[data-theme="light"] .status-chip-success {
    background: rgba(34, 197, 94, 0.22);
    border-color: rgba(34, 197, 94, 0.45);
}

[data-theme="light"] .status-chip-warning {
    background: rgba(250, 204, 21, 0.22);
    border-color: rgba(250, 204, 21, 0.45);
}
</style>
@include('components.action-bottom-sheet')
@endsection
