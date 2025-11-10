@extends('layouts.app')
@php
    use Illuminate\Support\Str;
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

                <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-muted small">Anggota aktif:</span>
                    @forelse ($project->members->take(6) as $member)
                        <span class="participant-badge">
                            {{ optional($member->user)->full_name ?? '-' }}
                            <small class="text-muted">({{ $member->role }})</small>
                        </span>
                    @empty
                        <span class="text-muted small">Belum ada anggota terdaftar.</span>
                    @endforelse
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
@endsection
