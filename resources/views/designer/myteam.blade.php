<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Team - Designer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
            color: #e5e7eb;
            overflow-x: hidden;
        }

        .layout-wrapper {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        .main-content-area {
            flex: 1;
            margin-left: 250px;
            min-height: 100vh;
            position: relative;
            z-index: 1;
            transition: margin-left 0.3s ease;
        }

        .mobile-nav-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
            flex: 1 1 auto;
            min-width: 240px;
        }

        .topbar-actions .search-input-wrapper {
            position: relative;
            width: min(320px, 100%);
        }

        .topbar-actions .search-input-wrapper input {
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(148, 163, 184, 0.4);
            color: #e5e7eb;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
        }

        .topbar-actions .search-input-wrapper input::placeholder {
            color: rgba(226, 232, 240, 0.7);
        }

        .topbar-actions .search-input-wrapper i {
            position: absolute;
            top: 50%;
            left: 0.85rem;
            transform: translateY(-50%);
            color: rgba(148, 163, 184, 0.65);
        }

        .sidebar-toggle-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.4);
            background: rgba(15, 23, 42, 0.65);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #e5e7eb;
            transition: all 0.3s ease;
        }

        .sidebar-toggle-btn:hover,
        .sidebar-toggle-btn:focus {
            color: #fff;
            border-color: rgba(139, 92, 246, 0.5);
            background: rgba(139, 92, 246, 0.4);
            outline: none;
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.25);
        }

        .navbar-acrylic {
            background: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 2rem;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-acrylic .container-fluid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .content-wrapper {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .glass-card {
            background: rgba(31, 41, 55, 0.6);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            margin-bottom: 2rem;
        }

        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .project-title {
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .badge-modern {
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            backdrop-filter: blur(10px);
            border: 1px solid;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            line-height: 1;
            white-space: nowrap;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.25);
            border-color: rgba(16, 185, 129, 0.4);
            color: #6ee7b7;
        }

        .badge-warning {
            background: rgba(251, 191, 36, 0.25);
            border-color: rgba(251, 191, 36, 0.4);
            color: #fde047;
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.25);
            border-color: rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }

        .badge-primary {
            background: rgba(59, 130, 246, 0.25);
            border-color: rgba(59, 130, 246, 0.4);
            color: #93c5fd;
        }

        .badge-secondary {
            background: rgba(107, 114, 128, 0.25);
            border-color: rgba(107, 114, 128, 0.4);
            color: #d1d5db;
        }

        .badge-info {
            background: rgba(14, 165, 233, 0.25);
            border-color: rgba(14, 165, 233, 0.4);
            color: #7dd3fc;
        }

        .project-description {
            color: #d1d5db;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .page-subtitle {
            color: rgba(226, 232, 240, 0.75);
            margin-bottom: 1.5rem;
        }

        .empty-project-text {
            color: rgba(226, 232, 240, 0.65);
        }

        .team-progress-track {
            height: 8px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            overflow: hidden;
        }

        .team-progress-track .progress-bar {
            transition: width 0.5s ease;
        }

        .progress-caption {
            color: #c4b5fd;
            font-weight: 600;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            background: rgba(139, 92, 246, 0.1);
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid rgba(139, 92, 246, 0.2);
            backdrop-filter: blur(10px);
        }

        .info-label {
            color: #c4b5fd;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .info-value {
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .section-title {
            color: #c4b5fd;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* Table Acrylic - FIXED BACKGROUND */
        .table-acrylic {
            background: transparent !important;
            border-radius: 12px;
            overflow: hidden;
            border: none !important;
        }

        .table-acrylic thead {
            background: rgba(139, 92, 246, 0.15) !important;
            backdrop-filter: blur(10px);
        }

        .table-acrylic thead th {
            color: #c4b5fd !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 1rem;
            border: none !important;
            white-space: nowrap;
            background: transparent !important;
        }

        .table-acrylic tbody {
            background: transparent !important;
        }

        .table-acrylic tbody td {
            color: #f3f4f6 !important;
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
            background: transparent !important;
            border: none !important;
        }

        .table-acrylic tbody td strong {
            color: #ffffff !important;
        }

        .table-acrylic tbody tr {
            transition: all 0.3s ease;
            background: transparent !important;
        }

        .table-acrylic tbody tr:hover {
            background: rgba(139, 92, 246, 0.08) !important;
        }

        .alert-acrylic {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 12px;
            color: #93c5fd;
            padding: 1rem;
            backdrop-filter: blur(10px);
        }

        .alert-warning-acrylic {
            background: rgba(251, 191, 36, 0.15);
            border-color: rgba(251, 191, 36, 0.3);
            color: #fde047;
        }

        .text-gradient {
            background: linear-gradient(135deg, #8b5cf6, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Project Status Badge */
        .project-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            backdrop-filter: blur(10px);
            border: 1px solid;
        }

        .status-todo {
            background: rgba(59, 130, 246, 0.25);
            border-color: rgba(59, 130, 246, 0.4);
            color: #93c5fd;
        }

        .status-in-progress {
            background: rgba(251, 191, 36, 0.25);
            border-color: rgba(251, 191, 36, 0.4);
            color: #fde047;
        }

        .status-review {
            background: rgba(14, 165, 233, 0.25);
            border-color: rgba(14, 165, 233, 0.4);
            color: #7dd3fc;
        }

        .status-done {
            background: rgba(16, 185, 129, 0.25);
            border-color: rgba(16, 185, 129, 0.4);
            color: #6ee7b7;
        }

        /* Progress Cards */
        .progress-card {
            background: rgba(139, 92, 246, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .progress-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: rgba(31, 41, 55, 0.5);
            border-radius: 10px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #c4b5fd;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: rgba(139, 92, 246, 0.3);
            margin-bottom: 1.5rem;
        }

        .empty-state h4 {
            color: #c4b5fd;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(17, 24, 39, 0.5);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(139, 92, 246, 0.3);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(139, 92, 246, 0.5);
        }

        /* Light Mode Overrides */
        [data-theme="light"] body {
            background: linear-gradient(135deg, #f9fafb 0%, #e0f2fe 45%, #ffffff 100%);
            color: #1f2937;
        }

        [data-theme="light"] .navbar-acrylic {
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(203, 213, 225, 0.7);
            box-shadow: 0 14px 36px rgba(148, 163, 184, 0.22);
            color: #1f2937;
        }

        [data-theme="light"] .sidebar-toggle-btn {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.65);
            color: #1f2937;
        }

        [data-theme="light"] .sidebar-toggle-btn:hover,
        [data-theme="light"] .sidebar-toggle-btn:focus {
            color: #1f2937;
            background: rgba(129, 140, 248, 0.2);
            border-color: rgba(99, 102, 241, 0.45);
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.18);
        }

        [data-theme="light"] .glass-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(203, 213, 225, 0.7);
            box-shadow: 0 24px 45px rgba(148, 163, 184, 0.2);
            color: #1f2937;
        }

        [data-theme="light"] .project-header {
            border-bottom: 1px solid rgba(203, 213, 225, 0.6);
        }

        [data-theme="light"] .project-title {
            color: #111827;
        }

        [data-theme="light"] .project-description {
            color: #475569;
        }

        [data-theme="light"] .page-subtitle {
            color: #475569;
        }

        [data-theme="light"] .empty-project-text {
            color: #64748b;
        }

        [data-theme="light"] .team-progress-track {
            background: rgba(226, 232, 240, 0.85);
        }

        [data-theme="light"] .progress-caption {
            color: #475569;
        }

        [data-theme="light"] .info-item {
            background: rgba(248, 250, 252, 0.92);
            border: 1px solid rgba(203, 213, 225, 0.7);
        }

        [data-theme="light"] .info-label {
            color: #4c1d95;
        }

        [data-theme="light"] .info-value {
            color: #111827;
        }

        [data-theme="light"] .badge-modern {
            border-color: rgba(203, 213, 225, 0.6);
        }

        [data-theme="light"] .badge-success {
            background: rgba(16, 185, 129, 0.16);
            border-color: rgba(16, 185, 129, 0.28);
            color: #047857;
        }

        [data-theme="light"] .badge-warning {
            background: rgba(251, 191, 36, 0.18);
            border-color: rgba(251, 191, 36, 0.3);
            color: #92400e;
        }

        [data-theme="light"] .badge-danger {
            background: rgba(239, 68, 68, 0.18);
            border-color: rgba(239, 68, 68, 0.3);
            color: #b91c1c;
        }

        [data-theme="light"] .topbar-actions .search-input-wrapper input {
            background: rgba(255, 255, 255, 0.96);
            border-color: rgba(203, 213, 225, 0.85);
            color: #0f172a;
        }

        [data-theme="light"] .topbar-actions .search-input-wrapper input::placeholder {
            color: #94a3b8;
        }

        [data-theme="light"] .badge-primary {
            background: rgba(59, 130, 246, 0.18);
            border-color: rgba(59, 130, 246, 0.3);
            color: #1d4ed8;
        }

        [data-theme="light"] .badge-secondary {
            background: rgba(148, 163, 184, 0.18);
            border-color: rgba(148, 163, 184, 0.32);
            color: #334155;
        }

        [data-theme="light"] .badge-info {
            background: rgba(14, 165, 233, 0.18);
            border-color: rgba(14, 165, 233, 0.3);
            color: #0369a1;
        }

        [data-theme="light"] .info-value span {
            color: inherit;
        }

        [data-theme="light"] .stat-item {
            background: rgba(248, 250, 252, 0.94);
            border: 1px solid rgba(203, 213, 225, 0.7);
        }

        [data-theme="light"] .stat-value {
            color: #111827;
        }

        [data-theme="light"] .stat-label {
            color: #64748b;
        }

        [data-theme="light"] .table-acrylic thead {
            background: rgba(129, 140, 248, 0.15) !important;
        }

        [data-theme="light"] .table-acrylic thead th {
            color: #1d4ed8 !important;
        }

        [data-theme="light"] .table-acrylic tbody td {
            color: #1f2937 !important;
            border-bottom: 1px solid rgba(148, 163, 184, 0.25) !important;
        }

        [data-theme="light"] .table-acrylic tbody td strong {
            color: #0f172a !important;
        }

        [data-theme="light"] .table-acrylic tbody tr:hover {
            background: rgba(129, 140, 248, 0.12) !important;
        }

        [data-theme="light"] .empty-state {
            background: rgba(248, 250, 252, 0.95);
            border: 1px solid rgba(203, 213, 225, 0.7);
            color: #475569;
        }

        [data-theme="light"] .empty-state i {
            color: rgba(129, 140, 248, 0.35);
        }

        [data-theme="light"] .empty-state h4 {
            color: #1e3a8a;
        }

        [data-theme="light"] .alert-acrylic {
            background: rgba(254, 240, 138, 0.2);
            border: 1px solid rgba(234, 179, 8, 0.4);
            color: #854d0e;
        }

        [data-theme="light"] .alert-danger-acrylic {
            background: rgba(254, 226, 226, 0.6);
            border-color: rgba(248, 113, 113, 0.6);
            color: #b91c1c;
        }

        [data-theme="light"] ::-webkit-scrollbar-track {
            background: rgba(226, 232, 240, 0.65);
        }

        [data-theme="light"] ::-webkit-scrollbar-thumb {
            background: rgba(129, 140, 248, 0.38);
        }

        [data-theme="light"] ::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.48);
        }

        /* OVERRIDE BOOTSTRAP DEFAULT TABLE STYLES */
        .table {
            --bs-table-bg: transparent !important;
            --bs-table-color: #e5e7eb !important;
            --bs-table-border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .table > :not(caption) > * > * {
            background-color: transparent !important;
        }

        @media (max-width: 1199.98px) {
            .content-wrapper {
                padding: 1.75rem;
            }
        }

        @media (max-width: 991.98px) {
            .layout-wrapper {
                flex-direction: column;
            }

            .main-content-area {
                margin-left: 0;
                min-height: auto;
            }

            .navbar-acrylic {
                padding: 0.85rem 1.25rem;
                position: relative;
            }

            .content-wrapper {
                padding: 1.5rem 1rem 3.5rem;
            }

            .glass-card {
                padding: 1.5rem;
            }

            .project-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .info-grid,
            .progress-stats {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .navbar-acrylic .container-fluid {
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .mobile-nav-wrapper {
                width: auto;
                justify-content: flex-start;
                gap: 0.75rem;
            }

            .text-gradient.mb-0 {
                font-size: 1.35rem;
            }

            .sidebar-toggle-btn {
                width: 38px;
                height: 38px;
                border-radius: 10px;
            }

            .content-wrapper {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
        }
    </style>
</head>
<body>
<div class="layout-wrapper">
    @include('components.app-sidebar')

    <div class="main-content-area">
        <nav class="navbar-acrylic">
            <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="mobile-nav-wrapper">
                    <button type="button"
                            class="sidebar-toggle-btn d-lg-none"
                            data-sidebar-toggle
                            aria-label="Buka menu navigasi">
                        <i class="bi bi-list"></i>
                    </button>
                    <h4 class="text-gradient mb-0">My Team</h4>
                </div>
                <div class="topbar-actions ms-auto">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="search"
                               id="designerTeamSearch"
                               placeholder="Cari anggota atau proyek...">
                    </div>
                </div>
            </div>
        </nav>

        <div class="content-wrapper">

            
            @if($projects->count() > 0)
                @foreach($projects as $project)
                    @php
                        $memberTokens = ($project->members ?? collect())->map(function($member) {
                            return trim(($member->user->full_name ?? '') . ' ' . ($member->user->username ?? ''));
                        })->implode(' ');
                        $projectSearchTokens = strtolower(
                            $project->project_name . ' ' .
                            ($project->description ?? '') . ' ' .
                            ($project->deadline ?? '') . ' ' .
                            $memberTokens
                        );
                    @endphp
                    <div class="glass-card team-project-card" data-search="{{ $projectSearchTokens }}">
                        <div class="project-header">
                            <div>
                                <h5 class="project-title">{{ $project->project_name }}</h5>
                                <!-- Project Status Badge -->
                                @php
                                    // Hitung progress berdasarkan subtasks
                                    $totalSubtasks = 0;
                                    $completedSubtasks = 0;
                                    
                                    foreach($project->boards as $board) {
                                        foreach($board->cards as $card) {
                                            $totalSubtasks += $card->subtasks->count();
                                            $completedSubtasks += $card->subtasks->where('status', 'done')->count();
                                        }
                                    }
                                    
                                    // Tentukan status project berdasarkan progress
                                    $progressPercentage = $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100) : 0;
                                    
                                    if ($progressPercentage == 0) {
                                        $projectStatus = 'todo';
                                        $statusText = 'To Do';
                                    } elseif ($progressPercentage > 0 && $progressPercentage < 100) {
                                        $projectStatus = 'in_progress';
                                        $statusText = 'In Progress';
                                    } else {
                                        $projectStatus = 'done';
                                        $statusText = 'Done';
                                    }
                                    $projectState = strtolower($project->status ?? 'proses');
                                    $projectMarkedDone = $projectState === 'selesai';

                                    if ($projectMarkedDone) {
                                        $projectStatus = 'done';
                                        $statusText = 'Completed';
                                    } elseif ($progressPercentage == 0) {
                                        $projectStatus = 'todo';
                                        $statusText = 'To Do';
                                    } elseif ($progressPercentage > 0 && $progressPercentage < 100) {
                                        $projectStatus = 'in_progress';
                                        $statusText = 'In Progress';
                                    } else {
                                        $projectStatus = 'in_progress';
                                        $statusText = 'Awaiting Completion';
                                    }

                                    $statusClasses = [
                                        'todo' => 'status-todo',
                                        'in_progress' => 'status-in-progress',
                                        'review' => 'status-review',
                                        'done' => 'status-done',
                                    ];
                                    $statusBadgeClass = $statusClasses[$projectStatus] ?? 'badge-secondary';
                                @endphp

                            </div>
                            <span class="project-status-badge {{ $statusBadgeClass }}">
                                {{ $statusText }}
                            </span>
                        </div>

                        <p class="project-description">{{ $project->description }}</p>

                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">📅 Deadline</div>
                                <div class="info-value">{{ $project->deadline ?? 'Not set' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">🎭 Your Role</div>
                                <div class="info-value">
                                    {{ ucfirst($project->members->where('user_id', $user->user_id)->first()->role ?? 'Member') }}
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">👥 Team Members</div>
                                <div class="info-value">{{ $project->members->count() }} Members</div>
                            </div>

                        </div>

                        <h6 class="section-title">Team Members</h6>
                        <div class="table-responsive">
                            <table class="table table-acrylic mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->members as $member)
                                        <tr>
                                            <td>
                                                <strong>{{ $member->user->full_name }}</strong>
                                                @if($member->user_id == $user->user_id)
                                                    <span class="badge-modern badge-primary ms-2">You</span>
                                                @endif
                                            </td>
                                            <td>{{ $member->user->username }}</td>
                                            <td>
                                                @php
                                                    $displayRole = strtolower($member->user->role ?? $member->role ?? 'member');
                                                    $roleClass = $displayRole === 'admin'
                                                        ? 'badge-danger'
                                                        : ($displayRole === 'team_lead' ? 'badge-primary' : 'badge-success');
                                                @endphp
                                                <span class="badge-modern {{ $roleClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $displayRole)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-modern 
                                                    {{ $member->user->current_task_status == 'working' ? 'badge-warning' : 
                                                       ($member->user->current_task_status == 'busy' ? 'badge-danger' : 'badge-secondary') }}">
                                                    {{ ucfirst($member->user->current_task_status ?? 'idle') }}
                                                </span>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
                <div id="designerTeamSearchEmpty" class="glass-card empty-state text-center d-none">
                    <i class="bi bi-search"></i>
                    <p class="mt-3 mb-0">Tidak ada proyek yang cocok dengan pencarian.</p>
                </div>
            @else
                <div class="glass-card empty-state">
                    <i class="bi bi-people"></i>
                    <h4 class="text-gradient mt-3">No Design Projects Yet</h4>
                    <p class="empty-project-text">You haven't joined any design projects yet.</p>
                </div>
            @endif

            @if(isset($hasCompletedOnly) && $hasCompletedOnly)
                <div class="alert-acrylic alert-warning-acrylic">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    All your design projects are completed. Please contact admin for new projects.
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('designerTeamSearch');
        const projectCards = document.querySelectorAll('.team-project-card');
        const emptyNotice = document.getElementById('designerTeamSearchEmpty');

        if (!searchInput || !projectCards.length) {
            return;
        }

        const filterCards = () => {
            const term = searchInput.value.trim().toLowerCase();
            let visible = 0;

            projectCards.forEach(card => {
                const haystack = (card.dataset.search || card.textContent || '').toLowerCase();
                const match = !term || haystack.includes(term);
                card.classList.toggle('d-none', !match);
                if (match) {
                    visible++;
                }
            });

            if (emptyNotice) {
                emptyNotice.classList.toggle('d-none', visible !== 0);
            }
        };

        searchInput.addEventListener('input', filterCards);
        filterCards();
    });
</script>
@include('components.theme-toggle')
@include('partials.profile-quick-sheet')
</body>
</html>

