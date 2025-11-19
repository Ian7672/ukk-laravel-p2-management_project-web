@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Status Blocker Saya
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

                    @if($blockers->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <p class="mb-0">Belum ada blocker yang Anda laporkan.</p>
                        </div>
                    @else
                        <div class="blocker-list">
                            @foreach($blockers as $blocker)
                                @php
                                    $statusClass = $blocker->status === 'selesai' ? 'status-chip-success' : 'status-chip-warning';
                                    $statusLabel = $blocker->status === 'selesai' ? 'Selesai' : 'Pending';
                                    $title = 'Data tidak tersedia';
                                    $subtitle = null;
                                    if (($supportsSubtask ?? true) && $blocker->subtask) {
                                        $title = $blocker->subtask->subtask_title;
                                        $card = $blocker->subtask->card;
                                        if ($card) {
                                            $projectName = optional(optional($card->board)->project)->project_name;
                                            $subtitle = $card->card_title;
                                            if ($projectName) {
                                                $subtitle .= ' · ' . $projectName;
                                            }
                                        }
                                    } elseif (!$supportsSubtask && isset($blocker->legacy_card)) {
                                        $title = $blocker->legacy_card->card_title;
                                        $subtitle = optional(optional($blocker->legacy_card->board)->project)->project_name;
                                    }
                                @endphp
                                <div class="blocker-item">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <h5 class="mb-1 text-white">{{ $title }}</h5>
                                            @if($subtitle)
                                                <small class="text-muted">{{ $subtitle }}</small>
                                            @endif
                                        </div>
                                        <span class="status-chip {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                        <div class="text-muted small">
                                            Dilaporkan pada {{ $blocker->created_at?->format('d M Y H:i') }}
                                        </div>
                                        @if($blocker->status === 'selesai')
                                            <span class="badge bg-success-soft">
                                                <i class="fas fa-check me-1"></i>Telah dibantu Team Lead
                                            </span>
                                        @else
                                            <span class="badge bg-warning-soft text-dark">
                                                <i class="fas fa-clock me-1"></i>Menunggu bantuan
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
.blocker-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.blocker-item {
    border-radius: 16px;
    padding: 1.5rem;
    background: rgba(15, 23, 42, 0.7);
    border: 1px solid rgba(148, 163, 184, 0.25);
}

.status-chip {
    padding: 0.25rem 0.9rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid transparent;
}

.status-chip-success {
    background: rgba(34, 197, 94, 0.18);
    color: #bbf7d0;
    border-color: rgba(34, 197, 94, 0.4);
}

.status-chip-warning {
    background: rgba(250, 204, 21, 0.18);
    color: #fde68a;
    border-color: rgba(250, 204, 21, 0.4);
}

.bg-success-soft {
    background: rgba(34, 197, 94, 0.12);
    color: #86efac;
    border-radius: 999px;
    padding: 0.35rem 0.9rem;
    border: 1px solid rgba(34, 197, 94, 0.35);
}

.bg-warning-soft {
    background: rgba(250, 204, 21, 0.15);
    border-radius: 999px;
    padding: 0.35rem 0.9rem;
    border: 1px solid rgba(250, 204, 21, 0.35);
}

.text-white {
    color: #f8fafc;
}
</style>
@endsection
