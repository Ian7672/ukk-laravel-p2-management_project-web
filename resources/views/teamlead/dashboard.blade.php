<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Lead Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
            color: #e5e7eb;
            overflow-x: hidden;
        }

        .text-surface-strong {
            color: #f9fafb;
        }

        .text-surface-soft {
            color: rgba(255, 255, 255, 0.6);
        }

        .text-surface-muted {
            color: rgba(255, 255, 255, 0.5);
        }

        /* Main Layout with Fixed Sidebar */
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
            margin-left: auto;
        }

        .topbar-actions .search-input-wrapper {
            position: relative;
            width: clamp(220px, 30vw, 360px);
            margin-left: auto;
            margin-right: 0;
        }

        .topbar-actions .search-input-wrapper input {
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(148, 163, 184, 0.4);
            color: #e5e7eb;
            padding: 0.55rem 1rem 0.55rem 2.5rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .topbar-actions .search-input-wrapper input::placeholder {
            color: rgba(226, 232, 240, 0.75);
        }

        .topbar-actions .search-input-wrapper input:focus {
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
            background: rgba(15, 23, 42, 0.85);
            color: #fff;
        }

        .topbar-actions .search-input-wrapper i {
            position: absolute;
            top: 50%;
            left: 0.9rem;
            color: rgba(148, 163, 184, 0.65);
            transform: translateY(-50%);
        }

        /* Topbar Acrylic */
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
            gap: 1rem;
        }

        .navbar-title {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #8b5cf6, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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

        .badge-acrylic {
            background: rgba(139, 92, 246, 0.2);
            border: 1px solid rgba(139, 92, 246, 0.3);
            color: #c4b5fd;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        /* Content Area */
        .content-wrapper {
            padding: 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Glass Card */
        .glass-card {
            background: rgba(31, 41, 55, 0.6);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        /* Table Modern */
        .table-acrylic {
            background: transparent !important;
            border-radius: 16px;
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
            letter-spacing: 0.05em;
            padding: 1rem;
            border: none !important;
            white-space: nowrap;
            background: transparent !important;
        }

        .table-acrylic tbody {
            background: transparent !important;
        }

        .table-acrylic tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
            color: #f3f4f6 !important;
            font-size: 0.9rem;
            background: transparent !important;
            border: none !important;
        }

        .table-acrylic tbody td strong {
            color: #ffffff !important;
            font-weight: 600;
        }

        .table-acrylic tbody tr {
            transition: all 0.3s ease;
            background: transparent !important;
        }

        .table-acrylic tbody tr:hover {
            background: rgba(139, 92, 246, 0.08) !important;
        }

        /* Progress Bar Styling */
        .progress-acrylic {
            background: rgba(31, 41, 55, 0.7);
            border-radius: 10px;
            height: 24px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .progress-bar-acrylic {
            border-radius: 8px;
            height: 100%;
            background: linear-gradient(90deg, 
                rgba(139, 92, 246, 0.8) 0%, 
                rgba(59, 130, 246, 0.8) 100%);
            box-shadow: 
                0 0 10px rgba(139, 92, 246, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
            transition: all 0.5s ease;
        }

        .progress-bar-acrylic::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.2) 50%, 
                transparent 100%);
            animation: shimmer 2s infinite;
        }

        .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            z-index: 2;
        }

        .progress-details {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        /* Project Cards */
        .project-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .project-card {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .project-index {
            display: inline-block;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(148, 163, 184, 0.75);
            margin-bottom: 0.25rem;
        }

        .project-name {
            font-size: 1.2rem;
            color: #f8fafc;
        }

        .project-deadline {
            font-size: 0.9rem;
            color: rgba(226, 232, 240, 0.75);
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            background: rgba(148, 163, 184, 0.15);
            border: 1px solid rgba(148, 163, 184, 0.25);
            color: rgba(226, 232, 240, 0.85);
        }

        .status-chip-danger {
            background: rgba(239, 68, 68, 0.12);
            border-color: rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }

        .status-chip-warning {
            background: rgba(251, 191, 36, 0.15);
            border-color: rgba(251, 191, 36, 0.35);
            color: #fcd34d;
        }

        .status-chip-info {
            background: rgba(59, 130, 246, 0.15);
            border-color: rgba(59, 130, 246, 0.35);
            color: #93c5fd;
        }

        .status-chip-success {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.35);
            color: #6ee7b7;
        }

        .project-progress-meta {
            font-size: 0.85rem;
            color: rgba(226, 232, 240, 0.7);
        }

        .project-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .project-stats {
            color: rgba(226, 232, 240, 0.7);
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .project-stats span {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
        }

        body.light-mode .project-name {
            color: #0f172a;
        }

        body.light-mode .project-deadline {
            color: #4b5563;
        }

        body.light-mode .project-index {
            color: #94a3b8;
        }

        body.light-mode .project-progress-meta,
        body.light-mode .project-stats {
            color: #4b5563;
        }

        body.light-mode .status-chip {
            background: rgba(148, 163, 184, 0.15);
            border-color: rgba(148, 163, 184, 0.3);
            color: #0f172a;
        }

        body.light-mode .status-chip-danger {
            background: rgba(239, 68, 68, 0.12);
            border-color: rgba(239, 68, 68, 0.3);
            color: #b91c1c;
        }

        body.light-mode .status-chip-warning {
            background: rgba(251, 191, 36, 0.12);
            border-color: rgba(251, 191, 36, 0.3);
            color: #b45309;
        }

        body.light-mode .status-chip-info {
            background: rgba(59, 130, 246, 0.12);
            border-color: rgba(59, 130, 246, 0.3);
            color: #1d4ed8;
        }

        body.light-mode .status-chip-success {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.3);
            color: #047857;
        }

        /* Progress Bar Color Variants */
        .progress-bar-danger {
            background: linear-gradient(90deg, 
                rgba(239, 68, 68, 0.8) 0%, 
                rgba(220, 38, 38, 0.8) 100%) !important;
            box-shadow: 
                0 0 10px rgba(239, 68, 68, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
        }

        .progress-bar-warning {
            background: linear-gradient(90deg, 
                rgba(251, 191, 36, 0.8) 0%, 
                rgba(245, 158, 11, 0.8) 100%) !important;
            box-shadow: 
                0 0 10px rgba(251, 191, 36, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
        }

        .progress-bar-info {
            background: linear-gradient(90deg, 
                rgba(96, 165, 250, 0.8) 0%, 
                rgba(59, 130, 246, 0.85) 100%) !important;
            box-shadow: 
                0 0 10px rgba(59, 130, 246, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
        }

        .progress-bar-success {
            background: linear-gradient(90deg, 
                rgba(16, 185, 129, 0.8) 0%, 
                rgba(5, 150, 105, 0.8) 100%) !important;
            box-shadow: 
                0 0 10px rgba(16, 185, 129, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Buttons */
        .btn-modern {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.25), rgba(59, 130, 246, 0.25));
            border: 1px solid rgba(139, 92, 246, 0.4);
            color: #c4b5fd;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-modern:hover {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.4), rgba(59, 130, 246, 0.4));
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);
        }

        /* Alerts */
        .alert-acrylic {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            color: #6ee7b7;
            padding: 1rem;
            backdrop-filter: blur(10px);
        }

        .alert-danger-acrylic {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            font-size: 5rem;
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

        /* Responsive */
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

            .navbar-title {
                font-size: 1.35rem;
            }

            .badge-acrylic {
                font-size: 0.85rem;
                padding: 6px 12px;
            }

            .content-wrapper {
                padding: 1.5rem 1rem 4rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .team-overview-card .card-body {
                flex-direction: column;
                align-items: stretch;
                gap: 1.25rem;
            }
        }

        @media (max-width: 767.98px) {
            .navbar-acrylic .container-fluid {
                flex-wrap: wrap;
                justify-content: space-between;
            }

            .topbar-actions {
                width: 100%;
                flex: 1 1 100%;
                margin-left: 0;
                justify-content: center;
            }

            .topbar-actions .search-input-wrapper {
                width: 100%;
                margin: 0;
            }

            .mobile-nav-wrapper {
                width: auto;
                justify-content: flex-start;
                gap: 0.75rem;
            }

            .badge-acrylic {
                order: 3;
                width: 100%;
                text-align: center;
            }

            .content-wrapper {
                padding: 1.5rem 1rem 3rem;
            }

            .glass-card {
                padding: 1.5rem;
            }

            .project-grid {
                grid-template-columns: 1fr;
            }

            .activity-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .activity-new-assignments {
                flex-direction: column;
                align-items: stretch;
            }
        }

        @media (max-width: 575.98px) {
            .navbar-title {
                font-size: 1.2rem;
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

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .team-overview-card .btn-modern {
                width: 100%;
            }

            .topbar-actions .search-input-wrapper {
                width: 100%;
                margin: 0;
            }
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

        /* Text Gradient */
        .text-gradient {
            background: linear-gradient(135deg, #8b5cf6, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Light Mode Overrides */
        [data-theme="light"] body {
            background: linear-gradient(135deg, #f9fafb 0%, #e0e7ff 50%, #ffffff 100%);
            color: #1f2937;
        }

        [data-theme="light"] .layout-wrapper {
            color: #1f2937;
        }

        [data-theme="light"] .text-gradient {
            background: linear-gradient(135deg, #4c1d95, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        [data-theme="light"] .text-surface-strong {
            color: #111827;
        }

        [data-theme="light"] .text-surface-soft {
            color: #4b5563;
        }

        [data-theme="light"] .text-surface-muted {
            color: #6b7280;
        }

        [data-theme="light"] .topbar-actions .search-input-wrapper input {
            background: rgba(255, 255, 255, 0.96);
            border-color: rgba(203, 213, 225, 0.85);
            color: #0f172a;
        }

        [data-theme="light"] .topbar-actions .search-input-wrapper input::placeholder {
            color: #94a3b8;
        }

        [data-theme="light"] .navbar-acrylic {
            background: rgba(255, 255, 255, 0.92);
            border-bottom: 1px solid rgba(203, 213, 225, 0.75);
            box-shadow: 0 12px 30px rgba(148, 163, 184, 0.26);
        }

        [data-theme="light"] .sidebar-toggle-btn {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.7);
            color: #1f2937;
        }

        [data-theme="light"] .sidebar-toggle-btn:hover,
        [data-theme="light"] .sidebar-toggle-btn:focus {
            color: #111827;
            background: rgba(129, 140, 248, 0.2);
            border-color: rgba(99, 102, 241, 0.45);
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.18);
        }

        [data-theme="light"] .badge-acrylic {
            background: rgba(129, 140, 248, 0.18);
            border: 1px solid rgba(99, 102, 241, 0.35);
            color: #3730a3;
        }

        [data-theme="light"] .glass-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(203, 213, 225, 0.7);
            box-shadow: 0 24px 50px rgba(148, 163, 184, 0.25);
            color: #1f2937;
        }

        [data-theme="light"] .glass-card.empty-state {
            color: #1f2937;
        }

        [data-theme="light"] .table {
            --bs-table-color: #1f2937 !important;
            --bs-table-border-color: rgba(203, 213, 225, 0.7) !important;
        }

        [data-theme="light"] .table-acrylic thead {
            background: rgba(129, 140, 248, 0.18) !important;
        }

        [data-theme="light"] .table-acrylic thead th {
            color: #3730a3 !important;
        }

        [data-theme="light"] .table-acrylic tbody td {
            color: #1f2937 !important;
            border-bottom: 1px solid rgba(203, 213, 225, 0.7) !important;
        }

        [data-theme="light"] .table-acrylic tbody td strong {
            color: #111827 !important;
        }

        [data-theme="light"] .table-acrylic tbody tr:hover {
            background: rgba(129, 140, 248, 0.12) !important;
        }

        [data-theme="light"] .progress-acrylic {
            background: rgba(229, 231, 235, 0.95);
            border: 1px solid rgba(203, 213, 225, 0.7);
            box-shadow: inset 0 2px 4px rgba(148, 163, 184, 0.25);
        }

        /* AFTER: progress text putih di mode light */
body.light-mode .progress-text,
[data-theme="light"] .progress-text {
    color: #ffffff !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25); /* biar tetap kebaca */
}


        [data-theme="light"] .progress-details {
            color: #4b5563;
        }

        [data-theme="light"] .btn-modern {
            background: linear-gradient(135deg, rgba(129, 140, 248, 0.24), rgba(59, 130, 246, 0.24));
            border: 1px solid rgba(99, 102, 241, 0.35);
            color: #1d4ed8;
        }

        [data-theme="light"] .btn-modern:hover {
            color: #1e3a8a;
            background: linear-gradient(135deg, rgba(129, 140, 248, 0.4), rgba(59, 130, 246, 0.4));
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.2);
        }

        [data-theme="light"] .alert-acrylic {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #047857;
        }

        [data-theme="light"] .alert-danger-acrylic {
            background: rgba(239, 68, 68, 0.12);
            border-color: rgba(239, 68, 68, 0.3);
            color: #b91c1c;
        }

        [data-theme="light"] .empty-state i {
            color: rgba(129, 140, 248, 0.35);
        }

        [data-theme="light"] ::-webkit-scrollbar-track {
            background: rgba(209, 213, 219, 0.4);
        }

        [data-theme="light"] ::-webkit-scrollbar-thumb {
            background: rgba(129, 140, 248, 0.35);
        }

        [data-theme="light"] ::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.45);
        }
    </style>
</head>
<body>

<div class="layout-wrapper">
    <!-- Fixed Floating Sidebar -->
    @include('teamlead.sidebar')

    <!-- Main Content Area -->
    <div class="main-content-area">
        <!-- Topbar -->
        <nav class="navbar-acrylic">
            <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="mobile-nav-wrapper">
                    <button type="button"
                            class="sidebar-toggle-btn d-lg-none"
                            data-sidebar-toggle
                            aria-label="Buka navigasi">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="navbar-title">
                        <i class="bi me-2"></i>Dashboard
                    </span>
                </div>
                <div class="topbar-actions ms-auto">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="search"
                               id="projectSearchInput"
                               placeholder="Cari proyek, tim, atau status...">
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="content-wrapper">


            @if(session('success'))
                <div class="alert-acrylic mb-4">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-acrylic alert-danger-acrylic mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if($projects->isEmpty())
                <div class="glass-card empty-state">
                    <i class="bi bi-kanban"></i>
                    <h4>Belum Ada Proyek</h4>
                    <p class="text-surface-muted">Anda belum memimpin proyek apapun.</p>
                </div>
            @else
                <div class="project-grid">
                    @foreach($projects as $i => $project)
                        @php
                            $totalSubtasks = 0;
                            $doneSubtasks = 0;
                            $cardCount = 0;

                            foreach ($project->boards as $board) {
                                $cardCount += $board->cards->count();
                                foreach ($board->cards as $card) {
                                    $totalSubtasks += $card->subtasks->count();
                                    $doneSubtasks += $card->subtasks->where('status', 'done')->count();
                                }
                            }

                            $progress = $totalSubtasks > 0 ? round(($doneSubtasks / $totalSubtasks) * 100) : 0;

                            if ($progress < 30) {
                                $progressClass = 'progress-bar-danger';
                                $statusClass = 'status-chip-danger';
                                $statusLabel = 'Low Progress';
                            } elseif ($progress < 70) {
                                $progressClass = 'progress-bar-warning';
                                $statusClass = 'status-chip-warning';
                                $statusLabel = 'On Track';
                            } elseif ($progress < 100) {
                                $progressClass = 'progress-bar-info';
                                $statusClass = 'status-chip-info';
                                $statusLabel = 'Almost Done';
                            } else {
                                $progressClass = 'progress-bar-success';
                                $statusClass = 'status-chip-success';
                                $statusLabel = 'Completed';
                            }

                            $deadlineText = $project->deadline
                                ? \Carbon\Carbon::parse($project->deadline)->format('d M Y')
                                : 'Tidak ada deadline';
                            $boardCount = $project->boards->count();
                            $memberCollection = $project->members ?? collect();
                            $memberTokens = $memberCollection->map(function ($member) {
                                $user = $member->user ?? $member;
                                return trim(($user->full_name ?? '') . ' ' . ($user->username ?? ''));
                            })->implode(' ');
                            $searchTokens = strtolower(
                                $project->project_name . ' ' .
                                ($project->description ?? '') . ' ' .
                                $deadlineText . ' ' .
                                $statusLabel . ' ' .
                                $memberTokens
                            );
                        @endphp

                        <div class="project-card glass-card teamlead-project-card" data-search="{{ $searchTokens }}">
                            <div class="project-card-header d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <span class="project-index">#{{ sprintf('%02d', $i + 1) }}</span>
                                    <h4 class="project-name mb-1">{{ $project->project_name }}</h4>
                                    <p class="project-deadline text-surface-soft mb-0">
                                        <i class="bi bi-calendar-event me-2"></i>{{ $deadlineText }}
                                    </p>
                                </div>
                                <span class="status-chip {{ $statusClass }}">{{ $statusLabel }}</span>
                            </div>

                            <div class="project-progress mt-4">
                                <div class="progress-acrylic" data-bs-toggle="tooltip" title="{{ $doneSubtasks }}/{{ $totalSubtasks }} Subtasks selesai">
                                    <div class="progress-bar-acrylic {{ $progressClass }}" style="width: {{ $progress }}%;">
                                        <span class="progress-text">{{ $progress }}%</span>
                                    </div>
                                </div>
                                <div class="project-progress-meta mt-2">
                                    <span><i class="bi bi-check2-circle me-1"></i>{{ $doneSubtasks }}/{{ $totalSubtasks }} Subtasks</span>
                                </div>
                            </div>

                            <div class="project-card-footer d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">
                                <div class="project-stats d-flex flex-wrap gap-3 text-surface-soft">
                                    <span><i class="bi bi-kanban me-1"></i>{{ $boardCount }} Boards</span>
                                    <span><i class="bi bi-layout-text-window-reverse me-1"></i>{{ $cardCount }} Cards</span>
                                </div>
                                <a href="{{ route('teamlead.projects.show', $project->project_id) }}" class="btn-modern btn-sm">
                                    <i class="bi bi-eye me-1"></i>Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="teamleadProjectSearchEmpty" class="glass-card empty-state text-center d-none">
                    <i class="bi bi-search"></i>
                    <h4 class="mt-3">Proyek tidak ditemukan</h4>
                    <p class="text-surface-muted mb-0">Coba gunakan kata kunci lain.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Sidebar Styles -->
<style>
/* Fixed Floating Sidebar */
.acrylic-sidebar-fixed {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    background: linear-gradient(135deg, 
        rgba(13, 17, 23, 0.95) 0%, 
        rgba(22, 27, 34, 0.98) 50%, 
        rgba(13, 17, 23, 0.95) 100%
    );
    backdrop-filter: blur(20px) saturate(180%);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 4px 0 30px rgba(0, 0, 0, 0.5);
    z-index: 1000;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 1.5rem;
}

.acrylic-sidebar-fixed::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 50%);
    pointer-events: none;
    z-index: -1;
}

.sidebar-icon-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(59, 130, 246, 0.2));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 32px rgba(139, 92, 246, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.text-gradient {
    background: linear-gradient(135deg, #8b5cf6, #3b82f6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.text-muted-light {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.85rem;
}

.divider-glow {
    border: none;
    height: 1px;
    background: linear-gradient(90deg, 
        transparent, 
        rgba(139, 92, 246, 0.5), 
        transparent
    );
    margin: 1.5rem 0;
}

.nav-link-acrylic {
    color: rgba(255, 255, 255, 0.8);
    padding: 12px 16px;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    font-weight: 500;
    position: relative;
    overflow: hidden;
    text-decoration: none;
}

.nav-link-acrylic::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(59, 130, 246, 0.1));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.nav-link-acrylic:hover {
    color: white;
    background: rgba(139, 92, 246, 0.15);
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2);
}

.nav-link-acrylic:hover::before {
    opacity: 1;
}

.nav-link-acrylic.active {
    color: white;
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.3), rgba(59, 130, 246, 0.3));
    box-shadow: 
        0 4px 20px rgba(139, 92, 246, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(139, 92, 246, 0.5);
}

.sidebar-footer {
    position: static;
    margin-top: auto;
    padding-top: 1.25rem;
    padding-bottom: 1.25rem;
}

.btn-logout-acrylic {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2));
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #fca5a5;
    padding: 12px;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-logout-acrylic:hover {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(220, 38, 38, 0.3));
    color: white;
    box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
}

/* Scrollbar Sidebar */
.acrylic-sidebar-fixed::-webkit-scrollbar {
    width: 6px;
}

.acrylic-sidebar-fixed::-webkit-scrollbar-track {
    background: rgba(17, 24, 39, 0.3);
}

.acrylic-sidebar-fixed::-webkit-scrollbar-thumb {
    background: rgba(139, 92, 246, 0.3);
    border-radius: 3px;
}

.acrylic-sidebar-fixed::-webkit-scrollbar-thumb:hover {
    background: rgba(139, 92, 246, 0.5);
}

[data-theme="light"] .acrylic-sidebar-fixed {
    background: linear-gradient(135deg,
        rgba(248, 250, 252, 0.96) 0%,
        rgba(237, 242, 255, 0.98) 50%,
        rgba(248, 250, 252, 0.96) 100%
    );
    border-right: 1px solid rgba(203, 213, 225, 0.75);
    box-shadow: 6px 0 28px rgba(148, 163, 184, 0.22);
}

[data-theme="light"] .acrylic-sidebar-fixed::before {
    background:
        radial-gradient(circle at 20% 50%, rgba(129, 140, 248, 0.18) 0%, transparent 55%),
        radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.18) 0%, transparent 55%);
}

[data-theme="light"] .sidebar-icon-wrapper {
    background: linear-gradient(135deg, rgba(129, 140, 248, 0.22), rgba(59, 130, 246, 0.22));
    border: 1px solid rgba(99, 102, 241, 0.35);
    box-shadow: 0 10px 28px rgba(99, 102, 241, 0.25);
}

[data-theme="light"] .text-muted-light {
    color: #475569;
}

[data-theme="light"] .nav-link-acrylic {
    color: #1f2937;
    background: rgba(255, 255, 255, 0.4);
    border: 1px solid transparent;
}

[data-theme="light"] .nav-link-acrylic:hover {
    color: #1d4ed8;
    background: rgba(129, 140, 248, 0.18);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.18);
}

[data-theme="light"] .nav-link-acrylic.active {
    color: #1e3a8a;
    background: linear-gradient(135deg, rgba(129, 140, 248, 0.32), rgba(59, 130, 246, 0.32));
    border: 1px solid rgba(99, 102, 241, 0.4);
    box-shadow:
        0 8px 28px rgba(99, 102, 241, 0.25),
        inset 0 1px 0 rgba(255, 255, 255, 0.6);
}

[data-theme="light"] .btn-logout-acrylic {
    background: linear-gradient(135deg, rgba(248, 113, 113, 0.18), rgba(239, 68, 68, 0.18));
    border: 1px solid rgba(239, 68, 68, 0.32);
    color: #b91c1c;
}

[data-theme="light"] .btn-logout-acrylic:hover {
    background: linear-gradient(135deg, rgba(248, 113, 113, 0.28), rgba(239, 68, 68, 0.28));
    color: #7f1d1d;
    box-shadow: 0 10px 28px rgba(239, 68, 68, 0.28);
}

[data-theme="light"] .acrylic-sidebar-fixed::-webkit-scrollbar-track {
    background: rgba(226, 232, 240, 0.6);
}

[data-theme="light"] .acrylic-sidebar-fixed::-webkit-scrollbar-thumb {
    background: rgba(129, 140, 248, 0.38);
}

[data-theme="light"] .acrylic-sidebar-fixed::-webkit-scrollbar-thumb:hover {
    background: rgba(99, 102, 241, 0.48);
}
</style>

@include('partials.profile-quick-sheet')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Inisialisasi tooltip & pencarian proyek
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });

        const projectSearchInput = document.getElementById('projectSearchInput');
        const projectCards = document.querySelectorAll('.teamlead-project-card');
        const projectSearchEmpty = document.getElementById('teamleadProjectSearchEmpty');

        if (projectSearchInput && projectCards.length) {
            const filterProjects = () => {
                const term = projectSearchInput.value.trim().toLowerCase();
                let visibleCount = 0;

                projectCards.forEach(card => {
                    const haystack = (card.dataset.search || card.textContent || '').toLowerCase();
                    const match = !term || haystack.includes(term);
                    card.classList.toggle('d-none', !match);
                    if (match) {
                        visibleCount++;
                    }
                });

                if (projectSearchEmpty) {
                    projectSearchEmpty.classList.toggle('d-none', visibleCount !== 0);
                }
            };

            projectSearchInput.addEventListener('input', filterProjects);
            filterProjects();
        }
    });
</script>
</body>
</html>
