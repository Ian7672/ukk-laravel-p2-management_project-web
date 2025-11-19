@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Laporkan Blocker
                    </h3>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('blocker.store') }}" method="POST">
                        @csrf

                        @if($cards->isEmpty())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Tidak ada card aktif (status selain Done) dengan subtask yang belum selesai untuk Anda saat ini.
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label">Pilih Card yang Terblokir</label>
                                <select name="card_id" id="cardSelect" class="form-select @error('card_id') is-invalid @enderror" required>
                                    <option value="">Pilih Card</option>
                                    @foreach($cards as $card)
                                        <option 
  value="{{ $card->card_id }}"
  data-subtasks='@json($card->encoded_subtasks)'
>
  {{ $card->card_title }} — {{ $card->board->project->project_name }}
</option>

                                    @endforeach
                                </select>
                                @error('card_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Pilih Subtask yang Terblokir</label>
                                <select name="subtask_id" id="subtaskSelect" class="form-select @error('subtask_id') is-invalid @enderror" required disabled>
                                    <option value="">Pilih Card terlebih dahulu</option>
                                </select>
                                @error('subtask_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text" id="subtaskHelper">Subtask yang tampil hanya yang belum selesai.</div>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Setelah Anda memilih card dan subtask, laporan akan langsung dikirim ke Team Lead dengan status <strong>pending</strong>.
                            Team Lead akan menandai blocker sebagai selesai setelah memberikan bantuan.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('blocker.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary" {{ $cards->isEmpty() ? 'disabled' : '' }}>
                                <i class="fas fa-paper-plane me-1"></i>Laporkan Blocker
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function() {
        const cardSelect = document.getElementById('cardSelect');
        const subtaskSelect = document.getElementById('subtaskSelect');
        const helper = document.getElementById('subtaskHelper');
        if (!cardSelect || !subtaskSelect) {
            return;
        }

        const previousSelection = {
            card: @json(old('card_id')),
            subtask: @json(old('subtask_id'))
        };

        function renderSubtasks() {
            const selectedOption = cardSelect.options[cardSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                subtaskSelect.innerHTML = '<option value="">Pilih Card terlebih dahulu</option>';
                subtaskSelect.disabled = true;
                if (helper) helper.textContent = 'Subtask yang tampil hanya yang belum selesai.';
                return;
            }

            const subtasks = JSON.parse(selectedOption.dataset.subtasks || '[]');
            subtaskSelect.innerHTML = '';

            if (subtasks.length === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Tidak ada subtask aktif pada card ini';
                subtaskSelect.appendChild(option);
                subtaskSelect.disabled = true;
                if (helper) helper.textContent = 'Semua subtask untuk card ini sudah selesai.';
                return;
            }

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Pilih Subtask';
            subtaskSelect.appendChild(placeholder);

            subtasks.forEach(subtask => {
                const option = document.createElement('option');
                option.value = subtask.id;
                option.textContent = subtask.title;
                if (previousSelection.subtask && previousSelection.subtask === String(subtask.id)) {
                    option.selected = true;
                }
                subtaskSelect.appendChild(option);
            });

            subtaskSelect.disabled = false;
            if (helper) helper.textContent = 'Subtask yang tampil hanya yang belum selesai.';
        }

        cardSelect.addEventListener('change', renderSubtasks);

        if (previousSelection.card) {
            renderSubtasks();
        }
    })();
</script>
@endsection
