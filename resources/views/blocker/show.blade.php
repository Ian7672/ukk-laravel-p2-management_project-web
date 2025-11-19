@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Detail Blocker
                    </h3>
                    <a href="{{ route('blocker.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <div class="card-body">
                    @php
                        $supports = $supportsSubtask ?? true;
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

                    <div class="blocker-detail">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <h4 class="mb-1 text-white">{{ $title }}</h4>
                                @if($subtitle)
                                    <small class="text-muted">{{ $subtitle }}</small>
                                @endif
                            </div>
                            <span class="status-chip {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>

                        <div class="detail-grid mt-4">
                            <div>
                                <div class="detail-label">Dilaporkan Oleh</div>
                                <div class="detail-value">{{ optional($blocker->user)->full_name ?? optional($blocker->user)->username }}</div>
                            </div>
                            <div>
                                <div class="detail-label">Tanggal Laporan</div>
                                <div class="detail-value">{{ $blocker->created_at?->format('d M Y H:i') }}</div>
                            </div>
                        </div>

                        @if($blocker->status === 'selesai')
                            <div class="alert alert-success mt-4 mb-0">
                                <i class="fas fa-check me-2"></i>
                                Blocker ini sudah ditandai selesai oleh Team Lead.
                            </div>
                        @else
                            <div class="alert alert-warning mt-4 mb-0">
                                <i class="fas fa-clock me-2"></i>
                                Blocker masih menunggu bantuan dari Team Lead.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.blocker-detail {
    border-radius: 20px;
    padding: 2rem;
    background: rgba(15, 23, 42, 0.75);
    border: 1px solid rgba(148, 163, 184, 0.2);
}

.status-chip {
    padding: 0.35rem 1rem;
    border-radius: 999px;
    font-size: 0.9rem;
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

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.detail-label {
    font-size: 0.8rem;
    text-transform: uppercase;
    color: rgba(226, 232, 240, 0.7);
    letter-spacing: 0.05em;
}

.detail-value {
    font-size: 1rem;
    color: #f8fafc;
    font-weight: 600;
}

.text-white {
    color: #f8fafc;
}
</style>
@endsection
