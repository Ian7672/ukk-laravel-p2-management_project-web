@extends('layouts.app')

@section('title', 'Generate Laporan')
@section('page-title', 'Generate Laporan')

@section('page-toolbar')
<div class="reports-toolbar">
    
</div>
@endsection

@section('content')
<style>
    .reports-toolbar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .reports-toolbar .toolbar-text {
        color: rgba(226, 232, 240, 0.85);
        font-size: 0.95rem;
    }

    [data-theme="light"] .reports-toolbar .toolbar-text {
        color: #475569;
    }

    .reports-page {
        width: 100%;
    }

    .reports-page .alert {
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .reports-page .alert ul {
        margin: 0;
    }

    .text-gradient {
        background: linear-gradient(135deg, #8b5cf6, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .text-muted-light {
        color: rgba(226, 232, 240, 0.75) !important;
        font-size: 0.9rem;
    }

    [data-theme="light"] .text-muted-light {
        color: #475569 !important;
    }

    .report-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: clamp(1rem, 3vw, 1.5rem);
    }

    .report-card {
        border-radius: 22px;
        padding: clamp(1.5rem, 3vw, 2rem);
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(17, 24, 39, 0.75);
        backdrop-filter: blur(18px);
        color: #e5e7eb;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.35);
    }

    [data-theme="light"] .report-card {
        background: rgba(255, 255, 255, 0.96);
        border-color: rgba(226, 232, 240, 0.8);
        color: #0f172a;
        box-shadow: 0 22px 48px rgba(15, 23, 42, 0.08);
    }

    .report-card-title {
        font-size: 1.25rem;
        margin-bottom: 0.35rem;
    }

    .report-card-subtitle {
        margin-bottom: 0;
        color: rgba(226, 232, 240, 0.8);
        font-size: 0.95rem;
    }

    [data-theme="light"] .report-card-subtitle {
        color: #475569;
    }

    .report-card-body {
        margin-top: 1.25rem;
    }

    .form-grid.two-columns {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .form-field .form-control,
    .form-field .form-select {
        background: rgba(31, 41, 55, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #f3f4f6;
        border-radius: 14px;
        padding: 0.75rem 1rem;
    }

    .form-field .form-control:focus,
    .form-field .form-select:focus {
        background: rgba(31, 41, 55, 0.95);
        border-color: rgba(139, 92, 246, 0.6);
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
        color: #fff;
    }

    [data-theme="light"] .form-field .form-control,
    [data-theme="light"] .form-field .form-select {
        background: rgba(248, 250, 252, 0.95);
        border-color: rgba(203, 213, 225, 0.8);
        color: #0f172a;
    }

    .quick-stats-section {
        margin-top: clamp(2rem, 4vw, 3rem);
    }

    .quick-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: clamp(1rem, 3vw, 1.5rem);
    }

    @media (min-width: 1200px) {
        .quick-stats-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    .quick-stat-card {
        border-radius: 18px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(16px);
    }

    .quick-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .quick-stat-value {
        font-size: clamp(1.4rem, 1rem + 1vw, 1.9rem);
        font-weight: 700;
        color: #fff;
    }

    .quick-stat-label {
        color: rgba(226, 232, 240, 0.75);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.85rem;
    }

    .accent-general {
        border-color: rgba(59, 130, 246, 0.35);
        box-shadow: 0 20px 45px rgba(59, 130, 246, 0.2);
    }

    .accent-primary .quick-stat-icon {
        background: rgba(59, 130, 246, 0.2);
        color: #93c5fd;
    }

    .accent-success .quick-stat-icon {
        background: rgba(16, 185, 129, 0.2);
        color: #6ee7b7;
    }

    .accent-info .quick-stat-icon {
        background: rgba(14, 165, 233, 0.2);
        color: #7dd3fc;
    }

    .accent-warning .quick-stat-icon {
        background: rgba(251, 191, 36, 0.2);
        color: #fde047;
    }

    [data-theme="light"] .quick-stat-card {
        background: rgba(255, 255, 255, 0.95);
        border-color: rgba(226, 232, 240, 0.75);
        color: #0f172a;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    [data-theme="light"] .quick-stat-value {
        color: #0f172a;
    }

    [data-theme="light"] .quick-stat-label {
        color: #475569;
    }

    @media (max-width: 575.98px) {
        .reports-toolbar form {
            width: 100%;
        }

        .form-grid.two-columns {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="reports-page">
    @if(session('success'))
        <div class="alert alert-success bg-success bg-opacity-25 border-0 text-white">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-warning bg-warning bg-opacity-25 border-0">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="report-grid">
        <div class="report-card accent-general">
            <div class="report-card-header">
                <h4 class="report-card-title text-gradient">Laporan Umum</h4>
                <p class="report-card-subtitle">Generate laporan statistik umum sistem dan performa keseluruhan.</p>
            </div>
            <div class="report-card-body">
                <form action="{{ route('admin.reports.general') }}" method="POST" class="d-flex flex-column gap-3">
                    @csrf
                    <div class="form-grid two-columns">
                        <div class="form-field">
                            <label class="form-label text-muted-light">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="form-field">
                            <label class="form-label text-muted-light">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="form-label text-muted-light">Format Laporan</label>
                        <select name="format" class="form-select" required>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-info w-100">
                        <i class="bi bi-download me-2"></i>Generate Laporan Umum
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="quick-stats-section">
        <h4 class="text-gradient mb-3">Statistik Cepat</h4>
        <div class="quick-stats-grid">
            <div class="quick-stat-card accent-primary">
                <div class="quick-stat-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <div class="quick-stat-value">{{ \App\Models\Project::count() }}</div>
                <div class="quick-stat-label">Total Proyek</div>
            </div>
            <div class="quick-stat-card accent-success">
                <div class="quick-stat-icon">
                    <i class="bi bi-lightning-charge"></i>
                </div>
                <div class="quick-stat-value">{{ \App\Models\Card::where('status', '!=', 'completed')->count() }}</div>
                <div class="quick-stat-label">Proyek Aktif</div>
            </div>
            <div class="quick-stat-card accent-info">
                <div class="quick-stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="quick-stat-value">{{ \App\Models\User::whereIn('role', ['team_lead', 'developer', 'designer'])->count() }}</div>
                <div class="quick-stat-label">Total Tim</div>
            </div>
            <div class="quick-stat-card accent-warning">
                <div class="quick-stat-icon">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="quick-stat-value">{{ \App\Models\Card::where('status', 'done')->count() }}</div>
                <div class="quick-stat-label">Task Selesai</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const lastWeek = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            const formatDate = (date) => date.toISOString().split('T')[0];
            const startDateInput = document.querySelector('input[name="start_date"]');
            const endDateInput = document.querySelector('input[name="end_date"]');
            if (startDateInput) {
                startDateInput.value = formatDate(lastWeek);
            }
            if (endDateInput) {
                endDateInput.value = formatDate(today);
            }
        });
    </script>
@endsection
