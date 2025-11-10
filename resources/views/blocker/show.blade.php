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
                    @if(isset($supportsSubtask) && !$supportsSubtask)
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Blocker ini masih tercatat berdasarkan card. Jalankan migrasi untuk mulai menggunakan subtask.
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informasi Blocker</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong class="text-white-custom">{{ ($supportsSubtask ?? true) ? 'Subtask' : 'Card' }}:</strong>
                                            @if(($supportsSubtask ?? true) && $blocker->subtask)
                                                <p class="mb-0 text-grey-custom">{{ $blocker->subtask->subtask_title }}</p>
                                                @if($blocker->subtask->card)
                                                    <small class="text-muted text-gresy-custom">
                                                        {{ $blocker->subtask->card->card_title }} • {{ $blocker->subtask->card->board->project->project_name }}
                                                    </small>
                                                @endif
                                            @elseif(!$supportsSubtask && isset($blocker->legacy_card))
                                                <p class="mb-0 text-grey-custom">{{ $blocker->legacy_card->card_title }}</p>
                                                <small class="text-muted text-gresy-custom">
                                                    {{ $blocker->legacy_card->board->project->project_name ?? 'Proyek tidak ditemukan' }}
                                                </small>
                                            @else
                                                <p class="mb-0 text-muted text-grey-custom">Data tidak tersedia</p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <strong class="text-white-custom">Priority:</strong>
                                            <p class="mb-0">
                                                <span class="badge bg-{{ $blocker->priority == 'urgent' ? 'danger' : ($blocker->priority == 'high' ? 'warning' : ($blocker->priority == 'medium' ? 'info' : 'secondary')) }}">
                                                    {{ ucfirst($blocker->priority) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong class="text-white-custom">Status:</strong>
                                            <p class="mb-0">
                                                <span class="badge bg-{{ $blocker->status == 'resolved' ? 'success' : ($blocker->status == 'in_progress' ? 'primary' : ($blocker->status == 'rejected' ? 'danger' : 'warning')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $blocker->status)) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong class="text-white-custom">Tanggal Dibuat:</strong>
                                            <p class="mb-0 text-grey-custom">{{ $blocker->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="text-white-custom">Deskripsi Blocker:</strong>
                                        <div class="border rounded p-3 mt-2 text-grey-custom">
                                            {{ $blocker->description }}
                                        </div>
                                    </div>

                                    @if($blocker->assignedTo)
                                    <div class="mb-3">
                                        <strong>Ditugaskan ke:</strong>
                                        <p class="mb-0 text-primary">{{ $blocker->assignedTo->full_name }}</p>
                                    </div>
                                    @endif

                                    @if($blocker->solution)
                                    <div class="mb-3">
                                        <strong>Solusi:</strong>
                                        <div class="border rounded p-3 mt-2 bg-light">
                                            {{ $blocker->solution }}
                                        </div>
                                    </div>
                                    @endif

                                    @if($blocker->resolved_at)
                                    <div class="mb-3">
                                        <strong>Tanggal Diselesaikan:</strong>
                                        <p class="mb-0 text-grey-custom">{{ $blocker->resolved_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Timeline</h5>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Blocker Dilaporkan</h6>
                                                <p class="timeline-text">{{ $blocker->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($blocker->assignedTo)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-info"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Ditugaskan ke Team Lead</h6>
                                                <p class="timeline-text">{{ $blocker->assignedTo->full_name }}</p>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($blocker->status == 'resolved')
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Blocker Diselesaikan</h6>
                                                <p class="timeline-text">{{ $blocker->resolved_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                        @elseif($blocker->status == 'rejected')
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-danger"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Blocker Ditolak</h6>
                                                <p class="timeline-text">{{ $blocker->resolved_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid rgba(255, 255, 255, 0.4);
    box-shadow: 0 0 12px currentColor, 0 0 0 4px rgba(255, 255, 255, 0.08);
}

.timeline-content {
    background: rgba(31, 41, 55, 0.65);
    padding: 1rem 1.25rem;
    border-radius: 14px;
    border: 1px solid rgba(139, 92, 246, 0.25);
    backdrop-filter: blur(16px);
    box-shadow: 0 20px 48px rgba(15, 23, 42, 0.35);
    position: relative;
    overflow: hidden;
}

.timeline-content::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top left, rgba(139, 92, 246, 0.2), transparent 55%);
    opacity: 0.7;
    pointer-events: none;
}

.timeline-title {
    margin: 0 0 0.4rem 0;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    background: linear-gradient(135deg, #8b5cf6, #3b82f6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.timeline-text {
    margin: 0;
    font-size: 0.82rem;
    color: rgba(226, 232, 240, 0.85);
}

.text-white-custom {
    color:white !important;
}
.text-grey-custom {
    color: #b7b7b7ff !important;
}
.text-gresy-custom {
    color: grey !important;
}
</style>
@endsection
