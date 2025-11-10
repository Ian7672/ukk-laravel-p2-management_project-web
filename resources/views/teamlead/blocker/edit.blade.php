@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-edit me-2"></i>Edit Blocker
                    </h3>
                    <a href="{{ route('teamlead.blocker.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                
                <div class="card-body">
                    @if(isset($supportsSubtask) && !$supportsSubtask)
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Data blocker masih menggunakan card langsung. Migrasikan tabel blocker agar subtask dapat dipilih saat pelaporan baru.
                        </div>
                    @endif
                    <form action="{{ route('teamlead.blocker.update', $blocker) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Informasi Blocker</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">User</label>
                                            <input type="text" class="form-control" value="{{ $blocker->user->full_name }}" readonly>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">{{ ($supportsSubtask ?? true) ? 'Subtask' : 'Card' }}</label>
                                            @if(($supportsSubtask ?? true) && $blocker->subtask)
                                                <input type="text" class="form-control" value="{{ $blocker->subtask->subtask_title }}" readonly>
                                                @if($blocker->subtask->card)
                                                    <small class="text-muted">
                                                        {{ $blocker->subtask->card->card_title }} • {{ $blocker->subtask->card->board->project->project_name }}
                                                    </small>
                                                @endif
                                            @elseif(!$supportsSubtask && isset($blocker->legacy_card))
                                                <input type="text" class="form-control" value="{{ $blocker->legacy_card->card_title }}" readonly>
                                                <small class="text-muted">
                                                    {{ $blocker->legacy_card->board->project->project_name ?? 'Proyek tidak ditemukan' }}
                                                </small>
                                            @else
                                                <input type="text" class="form-control" value="Data tidak tersedia" readonly>
                                            @endif
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi</label>
                                            <textarea class="form-control" rows="4" readonly>{{ $blocker->description }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Manajemen Blocker</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Priority</label>
                                            <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                                <option value="low" {{ old('priority', $blocker->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="medium" {{ old('priority', $blocker->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ old('priority', $blocker->priority) == 'high' ? 'selected' : '' }}>High</option>
                                                <option value="urgent" {{ old('priority', $blocker->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                            </select>
                                            @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                                <option value="pending" {{ old('status', $blocker->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ old('status', $blocker->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="resolved" {{ old('status', $blocker->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                <option value="rejected" {{ old('status', $blocker->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Assign ke Team Lead</label>
                                            <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                                                <option value="">Pilih Team Lead</option>
                                                @foreach($teamLeads as $teamLead)
                                                    <option value="{{ $teamLead->user_id }}" {{ old('assigned_to', $blocker->assigned_to) == $teamLead->user_id ? 'selected' : '' }}>
                                                        {{ $teamLead->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('assigned_to')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        @if($blocker->solution)
                                        <div class="mb-3">
                                            <label class="form-label">Solusi</label>
                                            <textarea class="form-control" rows="4" readonly>{{ $blocker->solution }}</textarea>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('teamlead.blocker.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Blocker
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
