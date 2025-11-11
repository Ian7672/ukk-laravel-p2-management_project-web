@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('page-toolbar')
<div class="users-toolbar">
    
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn btn-primary" type="button" id="openAddUserSheet">
            <i class="bi bi-plus-circle me-1"></i> Tambah User
        </button>
        <button class="btn btn-primary" onclick="refreshUsers()">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
        </button>
    </div>
</div>
@endsection

@section('content')
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-purple: #8b5cf6;
            --primary-blue: #3b82f6;
            --dark-bg: #0f0c29;
            --glass-bg: rgba(31, 41, 55, 0.7);
            --glass-border: rgba(255, 255, 255, 0.12);
            --text-primary: #e5e7eb;
            --text-muted: rgba(255, 255, 255, 0.6);
            
            /* Tambahan untuk mode terang */
            --light-bg: #f8fafc;
            --light-glass-bg: rgba(255, 255, 255, 0.9);
            --light-glass-border: rgba(0, 0, 0, 0.1);
            --light-text-primary: #1f2937;
            --light-text-muted: #6b7280;
            --light-shadow: rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
            color: #e5e7eb;
            overflow-x: hidden;
        }

        .users-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .users-toolbar .toolbar-text {
            color: rgba(229, 231, 235, 0.75);
            margin: 0;
        }

        /* ==================== LIGHT MODE STYLES ==================== */
        body.light-mode {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%);
            color: var(--light-text-primary);
        }

        body.light-mode .text-gradient {
            background: linear-gradient(135deg, #7c3aed, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        body.light-mode .acrylic-sidebar-fixed {
            background: linear-gradient(135deg,
                rgba(255, 255, 255, 0.95) 0%,
                rgba(248, 250, 252, 0.98) 50%,
                rgba(255, 255, 255, 0.95) 100%
            );
            border-right: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.1);
        }

        body.light-mode .users-shell {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.08);
            color: var(--light-text-primary);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        body.light-mode .nav-link-acrylic {
            color: var(--light-text-muted);
        }

        body.light-mode .nav-link-acrylic:hover {
            color: var(--light-text-primary);
            background: rgba(139, 92, 246, 0.08);
        }

        body.light-mode .nav-link-acrylic.active {
            color: var(--primary-purple);
            background: rgba(139, 92, 246, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.2);
        }

        body.light-mode .form-control,
        body.light-mode .form-select {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.15);
            color: var(--light-text-primary);
        }

        body.light-mode .form-control:focus,
        body.light-mode .form-select:focus {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(139, 92, 246, 0.6);
            color: var(--light-text-primary);
        }

        body.light-mode .form-control::placeholder {
            color: #9ca3af !important;
        }

        body.light-mode .user-card {
            background: linear-gradient(135deg,
                rgba(255, 255, 255, 0.9) 0%,
                rgba(248, 250, 252, 0.95) 100%
            );
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 
                0 18px 42px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        body.light-mode .user-name {
            color: var(--light-text-primary);
        }

        body.light-mode .user-username {
            color: #6b7280 !important;
        }

        body.light-mode .user-number {
            color: #6b7280 !important;
        }

        body.light-mode .user-info {
            color: var(--light-text-primary);
        }

        body.light-mode .text-muted-light {
            color: var(--light-text-muted) !important;
        }

        body.light-mode .users-toolbar .toolbar-text {
            color: var(--light-text-muted);
        }

        body.light-mode .modal-content {
            background: linear-gradient(135deg,
                rgba(255, 255, 255, 0.98) 0%,
                rgba(248, 250, 252, 0.98) 100%
            );
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: var(--light-text-primary);
        }

        body.light-mode .modal-header,
        body.light-mode .modal-footer {
            border-color: rgba(0, 0, 0, 0.1);
        }

        /* Status Pill Styles - Konsisten dengan Sidebar */
        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.55rem;
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            font-weight: 600;
            letter-spacing: 0.02em;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.08);
            color: rgba(229, 231, 235, 0.95);
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.25);
            transition: all 0.3s ease;
        }

        .status-pill .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
            box-shadow: 0 0 6px currentColor;
        }

        .status-pill .status-label {
            font-size: 0.82rem;
            font-weight: 600;
        }

        /* Dark Mode */
        .status-pill-idle {
            background: rgba(34, 197, 94, 0.14);
            border-color: rgba(34, 197, 94, 0.28);
            color: #22c55e;
            box-shadow: 0 6px 18px rgba(34, 197, 94, 0.18);
        }

        .status-pill-working {
            background: rgba(239, 68, 68, 0.14);
            border-color: rgba(239, 68, 68, 0.28);
            color: #ef4444;
            box-shadow: 0 6px 18px rgba(239, 68, 68, 0.2);
        }

        /* Light Mode */
        

        body.light-mode .status-pill-idle {
            background: rgba(134, 239, 172, 0.2);
            border-color: rgba(134, 239, 172, 0.35);
            color: #15803d;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.18);
        }

        body.light-mode .status-pill-working {
            background: rgba(248, 113, 113, 0.18);
            border-color: rgba(248, 113, 113, 0.35);
            color: #dc2626;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.18);
        }

        /* Perbaikan tombol untuk mode terang */
        body.light-mode .btn-primary {
            color: #7c3aed !important;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(59, 130, 246, 0.15));
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        body.light-mode .btn-primary:hover {
            color: #ffffff !important;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.7), rgba(59, 130, 246, 0.7));
        }

        body.light-mode .btn-danger {
            color: #dc2626 !important;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.15));
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        body.light-mode .btn-danger:hover {
            color: #ffffff !important;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.7), rgba(220, 38, 38, 0.7));
        }

        body.light-mode .btn-secondary {
            color: #4b5563 !important;
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.15), rgba(75, 85, 99, 0.15));
            border: 1px solid rgba(107, 114, 128, 0.3);
        }

        body.light-mode .btn-secondary:hover {
            color: #ffffff !important;
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.6), rgba(75, 85, 99, 0.6));
        }

        body.light-mode .btn-outline-primary {
            color: #7c3aed !important;
            border-color: rgba(139, 92, 246, 0.45);
        }

        body.light-mode .btn-outline-primary:hover {
            color: #ffffff !important;
            background: rgba(139, 92, 246, 0.3);
        }

        body.light-mode .btn-outline-danger {
            color: #dc2626 !important;
            border-color: rgba(239, 68, 68, 0.4);
        }

        body.light-mode .btn-outline-danger:hover {
            color: #ffffff !important;
            background: rgba(239, 68, 68, 0.35);
        }

        body.light-mode .btn-info {
            color: #0369a1 !important;
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid rgba(14, 165, 233, 0.3);
        }

        body.light-mode .btn-info:hover {
            color: #ffffff !important;
            background: rgba(14, 165, 233, 0.4);
        }

        /* Perbaikan ikon dalam tombol */
        body.light-mode .btn i {
            color: inherit !important;
        }

        /* Perbaikan label form */
        body.light-mode .form-label {
            color: #374151 !important;
            font-weight: 500;
        }

        body.light-mode .empty-state {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #6b7280;
        }

        body.light-mode .empty-state i {
            color: rgba(139, 92, 246, 0.5) !important;
        }

        /* Perbaikan badge role */
        body.light-mode .badge.bg-success {
            background: rgba(16, 185, 129, 0.15) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
            color: #065f46 !important;
        }

        body.light-mode .badge.bg-info {
            background: rgba(14, 165, 233, 0.15) !important;
            border-color: rgba(14, 165, 233, 0.3) !important;
            color: #155e75 !important;
        }

        body.light-mode .badge.bg-warning {
            background: rgba(251, 191, 36, 0.15) !important;
            border-color: rgba(251, 191, 36, 0.3) !important;
            color: #92400e !important;
        }

        body.light-mode .badge.bg-danger {
            background: rgba(239, 68, 68, 0.15) !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
            color: #991b1b !important;
        }

        body.light-mode .badge.bg-secondary {
            background: rgba(107, 114, 128, 0.15) !important;
            border-color: rgba(107, 114, 128, 0.3) !important;
            color: #374151 !important;
        }

        /* Project list styles */
        .user-projects-container {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-out, opacity 0.3s ease;
            opacity: 0;
        }

        .user-projects-container.expanded {
            max-height: 500px;
            opacity: 1;
        }

        .user-projects-list {
            margin-top: 1rem;
            padding: 1.5rem;
            background: rgba(31, 41, 55, 0.4);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        body.light-mode .user-projects-list {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .action-sheet__body .user-projects-list {
            max-height: 55vh;
            overflow-y: auto;
        }

        .sheet-loading p {
            color: rgba(148, 163, 184, 0.85);
        }

        body.light-mode .sheet-loading p {
            color: rgba(75, 85, 99, 0.85);
        }

        .project-item {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }

        body.light-mode .project-item {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.08);
        }

        .project-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2);
        }

        .project-info {
            width: 100%;
        }

        .project-name {
            font-weight: 600;
            color: inherit;
            margin-bottom: 0.25rem;
        }

        .project-meta {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }

        body.light-mode .project-meta {
            color: #6b7280;
        }

        .project-role {
            background: rgba(139, 92, 246, 0.2);
            color: #c4b5fd;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        body.light-mode .project-role {
            background: rgba(139, 92, 246, 0.15);
            color: #7c3aed;
        }

        .project-progress {
            width: 100%;
            text-align: left;
        }

        .project-progress-status {
            margin-top: 0.4rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .project-metrics {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.75rem;
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.7);
        }

        body.light-mode .project-metrics {
            color: #6b7280;
        }

        .progress {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 0.35rem;
        }

        body.light-mode .progress {
            background: rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .progress-text {
            font-size: 0.8rem;
            font-weight: 600;
            color: inherit;
            text-align: left;
        }

        .no-projects {
            text-align: center;
            padding: 2rem;
            color: rgba(255, 255, 255, 0.6);
        }

        body.light-mode .no-projects {
            color: #6b7280;
        }

        /* Scrollbar untuk mode terang */
        body.light-mode ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }

        body.light-mode ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.4), rgba(59, 130, 246, 0.4));
        }

        body.light-mode ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.6), rgba(59, 130, 246, 0.6));
        }

        /* ==================== DARK MODE STYLES ==================== */

        /* Dark mode: bikin subtitle di Add User Sheet jadi putih biar kebaca */
.add-user-sheet .add-user-sheet__header .text-muted {
  color: #ffffff !important;
  opacity: 0.92; /* tetap sedikit lembut, tapi jelas */
}

/* Light mode: kembalikan ke muted versi terang */
body.light-mode .add-user-sheet .add-user-sheet__header .text-muted,
[data-theme="light"] .add-user-sheet .add-user-sheet__header .text-muted {
  color: var(--light-text-muted) !important; /* kamu sudah define di :root */
  opacity: 1;
}

        .layout-wrapper {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

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
            z-index: 1400;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem;
        }

        .acrylic-sidebar-fixed::before {
            content: '';
            position: absolute;
            inset: 0;
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
            color: rgba(255, 255, 255, 0.6) !important;
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
            inset: 0;
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
            width: 100%;
        }

        .btn-logout-acrylic:hover {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(220, 38, 38, 0.3));
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
        }

        .main-content {
            flex-grow: 1;
            margin-left: 250px;
            padding: clamp(2rem, 4vw, 3rem);
            min-height: 100vh;
            position: relative;
            z-index: 1;
            transition: margin-left 0.3s ease, padding 0.3s ease;
        }

        .mobile-nav-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
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

        @media (max-width: 991.98px) {
            .layout-wrapper {
                flex-direction: column;
            }
            .main-content {
                margin-left: 0;
                padding: clamp(1.75rem, 6vw, 2.25rem);
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .mobile-nav-wrapper {
                width: 100%;
                justify-content: space-between;
            }
        }

        .users-shell {
            background: rgba(31, 41, 55, 0.6);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: clamp(1.5rem, 3vw, 2.5rem);
            color: white;
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: clamp(1rem, 2vw, 1.75rem);
            flex-wrap: wrap;
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
        }

        .page-header h3 {
            font-size: clamp(1.6rem, 1.1rem + 1.2vw, 2.1rem);
            margin: 0;
        }

        .action-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .action-buttons .btn {
            min-width: 160px;
            border-radius: 12px;
            flex: 0 0 auto;
        }

        .filters-row .form-select,
        .filters-row .form-control {
            min-height: 48px;
        }

        .form-control,
        .form-select {
            background: rgba(31, 41, 55, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #f3f4f6;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            padding: 12px 16px;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(31, 41, 55, 0.95);
            border-color: rgba(139, 92, 246, 0.6);
            color: white;
            box-shadow:
                0 0 0 4px rgba(139, 92, 246, 0.15),
                0 8px 18px rgba(139, 92, 246, 0.2);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .users-grid-wrapper {
            width: 100%;
        }

        .users-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: clamp(1rem, 3vw, 1.75rem);
            width: 100%;
        }

        .user-card-container {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .user-card {
            background: linear-gradient(135deg,
                rgba(31, 41, 55, 0.72) 0%,
                rgba(17, 24, 39, 0.82) 100%
            );
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: clamp(1.25rem, 3vw, 1.75rem);
            position: relative;
            overflow: hidden;
            box-shadow:
                0 18px 42px rgba(0, 0, 0, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            backdrop-filter: blur(18px);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .user-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg,
                rgba(139, 92, 246, 0.12),
                transparent 45%,
                rgba(59, 130, 246, 0.08) 100%
            );
            opacity: 0.7;
            pointer-events: none;
        }

        .user-card:hover {
            transform: translateY(-6px);
            box-shadow:
                0 24px 60px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(139, 92, 246, 0.18);
        }

        .user-card-content {
            position: relative;
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            gap: 1.25rem;
        }

        .user-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }

        .user-meta {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .user-number {
            font-size: 0.78rem;
            letter-spacing: 0.1em;
            color: rgba(255, 255, 255, 0.55);
            text-transform: uppercase;
        }

        .user-name {
            font-size: clamp(1.1rem, 0.9rem + 0.5vw, 1.35rem);
            color: #ffffff;
            margin: 0;
            line-height: 1.2;
        }

        .user-username {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.55);
            letter-spacing: 0.02em;
        }

        .role-badge {
            border-radius: 999px;
            padding: 0.45rem 0.9rem;
            font-weight: 600;
            font-size: 0.85rem;
            backdrop-filter: blur(12px);
        }

        .user-card-body {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
            flex: 1 1 auto;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: rgba(229, 231, 235, 0.92);
            font-size: 0.95rem;
        }

        .user-info i {
            font-size: 1.1rem;
            color: rgba(139, 92, 246, 0.65);
        }

        .user-card-footer {
            display: flex;
            justify-content: flex-start;
            align-items: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: auto;
        }

        .status-left {
            margin-right: auto;
            align-self: flex-end;
        }

        .action-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
            margin-left: auto;
        }

        .action-group .btn {
            min-width: 42px;
            border-radius: 10px;
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 991.98px) {
            .user-card-footer {
                justify-content: space-between;
                align-items: center;
            }

            .user-card-footer .action-group {
                margin-left: 0;
            }

            .user-card-footer .status-pill {
                align-self: auto;
            }
        }

        @media (max-width: 767.98px) {
            .users-shell,
            .users-shell .filters-row .form-control,
            .users-shell .filters-row .form-select,
            .users-shell .search-form .form-control,
            .user-card,
            .empty-state,
            .role-badge,
            .badge,
            .btn-primary,
            .btn-outline-primary,
            .btn-outline-danger,
            .btn-info,
            .btn-group .btn,
            .status-pill,
            .modal-content,
            .add-user-sheet .add-user-sheet__overlay,
            .add-user-sheet .add-user-sheet__content {
                backdrop-filter: none !important;
            }

            .user-card::before {
                display: none;
            }
        }

        .status-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .empty-state {
            margin-top: 1.5rem;
            border-radius: 18px;
            padding: 2.5rem;
            background: rgba(31, 41, 55, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            color: rgba(229, 231, 235, 0.85);
            backdrop-filter: blur(16px);
        }

        .empty-state i {
            font-size: 2.6rem;
            margin-bottom: 1rem;
            color: rgba(139, 92, 246, 0.6);
        }

        .empty-state p {
            margin: 0;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.3), rgba(59, 130, 246, 0.3));
            border: 1px solid rgba(139, 92, 246, 0.5);
            color: #c4b5fd;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.22), transparent);
            transition: left 0.45s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.5), rgba(59, 130, 246, 0.5));
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(139, 92, 246, 0.4);
            border-color: rgba(139, 92, 246, 0.7);
        }

        .btn-secondary {
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.25), rgba(75, 85, 99, 0.25));
            border: 1px solid rgba(107, 114, 128, 0.4);
            color: #d1d5db;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.4), rgba(75, 85, 99, 0.4));
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(107, 114, 128, 0.25);
        }

        .btn-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(220, 38, 38, 0.3));
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #fca5a5;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.45), rgba(220, 38, 38, 0.45));
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(239, 68, 68, 0.35);
        }

        .btn-outline-primary,
        .btn-outline-danger,
        .btn-info {
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .btn-outline-primary {
            border-color: rgba(139, 92, 246, 0.45);
            color: #c4b5fd;
        }

        .btn-outline-primary:hover {
            background: rgba(139, 92, 246, 0.3);
            color: #fff;
            border-color: rgba(139, 92, 246, 0.6);
        }

        .btn-outline-danger {
            border-color: rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }

        .btn-outline-danger:hover {
            background: rgba(239, 68, 68, 0.35);
            color: white;
            border-color: rgba(239, 68, 68, 0.6);
        }

        .btn-info {
            background: rgba(14, 165, 233, 0.25);
            border: 1px solid rgba(14, 165, 233, 0.45);
            color: #7dd3fc;
        }

        .btn-info:hover {
            background: rgba(14, 165, 233, 0.4);
            color: white;
            border-color: rgba(14, 165, 233, 0.6);
            box-shadow: 0 8px 18px rgba(14, 165, 233, 0.35);
        }

        .badge {
            padding: 6px 14px;
            border-radius: 9px;
            font-weight: 600;
            font-size: 0.8rem;
            backdrop-filter: blur(10px);
            border: 1px solid;
        }

        .bg-danger { background: rgba(239, 68, 68, 0.25) !important; color: #fca5a5 !important; border-color: rgba(239, 68, 68, 0.4) !important; }
        .bg-success { background: rgba(16, 185, 129, 0.25) !important; color: #6ee7b7 !important; border-color: rgba(16, 185, 129, 0.4) !important; }
        .bg-info { background: rgba(59, 130, 246, 0.25) !important; color: #93c5fd !important; border-color: rgba(59, 130, 246, 0.4) !important; }
        .bg-warning { background: rgba(245, 158, 11, 0.25) !important; color: #fcd34d !important; border-color: rgba(245, 158, 11, 0.4) !important; }

        .modal-content {
            background: linear-gradient(135deg,
                rgba(31, 41, 55, 0.98) 0%,
                rgba(17, 24, 39, 0.98) 100%
            );
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.55);
            color: white;
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
        }

        .modal-body {
            padding: 2rem 1.75rem;
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
        }

        .btn-group .btn {
            background: rgba(31, 41, 55, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .btn-group .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(139, 92, 246, 0.25);
        }

        .progress {
            background: rgba(31, 41, 55, 0.6);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .progress-bar {
            transition: width 0.6s ease;
            position: relative;
        }

        .progress-bar::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shimmerProgress 2s infinite;
        }

        @keyframes shimmerProgress {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(31, 41, 55, 0.5);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.6), rgba(59, 130, 246, 0.6));
            border-radius: 10px;
            border: 2px solid rgba(31, 41, 55, 0.5);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.78), rgba(59, 130, 246, 0.78));
        }

        @media (max-width: 991.98px) {
            .page-header {
                justify-content: flex-start;
                text-align: left;
            }
            .page-header h3 {
                width: 100%;
                white-space: normal;
            }
            .action-buttons {
                justify-content: flex-start;
                width: 100%;
                gap: 0.75rem;
            }
            .action-buttons .btn {
                flex: 1 1 160px;
                min-width: 140px;
            }
            .mobile-nav-wrapper {
                width: 100%;
                justify-content: space-between;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: clamp(1.5rem, 6vw, 2rem);
            }
            .users-shell {
                padding: clamp(1.25rem, 5vw, 1.75rem);
            }
            .users-grid {
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 1.25rem;
            }
            .user-card {
                border-radius: 18px;
                padding: clamp(1.1rem, 4.5vw, 1.5rem);
            }
            .filters-row > div {
                width: 100%;
            }
            .filters-row .form-select,
            .filters-row .form-control {
                min-height: 44px;
            }
            .action-buttons .btn {
                flex: 1 1 calc(50% - 0.75rem);
            }
            .user-card-footer {
                gap: 0.75rem;
            }
            .status-pill {
                width: 100%;
                justify-content: flex-start;
            }
            .status-pill.status-left {
                width: auto;
            }
            .action-group {
                width: 100%;
                justify-content: space-between;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: clamp(1.25rem, 8vw, 1.75rem);
            }
            .users-shell {
                padding: clamp(1rem, 6vw, 1.5rem);
                border-radius: 20px;
            }
            .action-buttons .btn {
                flex: 1 1 100%;
                min-width: 0;
            }
            .users-grid {
                grid-template-columns: 1fr;
                gap: 1.1rem;
            }
            .user-card-footer {
                flex-direction: column;
                align-items: stretch;
            }
            .action-group {
                justify-content: center;
            }
            .action-group .btn {
                flex: 1 1 auto;
            }
            .users-grid-wrapper {
                margin: 0 -0.25rem;
            }
            .status-pill.status-left {
                width: auto;
                justify-content: flex-start;
            }
        }

        @media (max-width: 480px) {
            .page-header h3 {
                font-size: clamp(1.3rem, 8vw, 1.6rem);
            }
            .filters-row {
                gap: 1rem;
            }
            .user-card {
                padding: clamp(1rem, 7vw, 1.4rem);
            }
            .action-group {
                gap: 0.4rem;
            }
            .action-group .btn {
                min-height: 36px;
                padding: 0.55rem 0.65rem;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        

        .add-user-sheet {
            position: fixed;
            inset: 0;
            display: grid;
            align-items: flex-end;
            justify-items: center;
            z-index: 1400;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .add-user-sheet.show {
            pointer-events: auto;
            opacity: 1;
        }

        .add-user-sheet.hidden {
            display: none;
        }

        .add-user-sheet__overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(8px);
        }

        .add-user-sheet__panel {
            position: relative;
            width: min(520px, 100% - 1.5rem);
            border-radius: 28px 28px 0 0;
            background: rgba(17, 24, 39, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 1.75rem 1.75rem 1.25rem;
            box-shadow: 0 -20px 45px rgba(15, 23, 42, 0.45);
            max-height: min(90vh, 640px);
            overflow-y: auto;
        }

        .add-user-sheet__handle {
            width: 48px;
            height: 5px;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.4);
            margin: 0 auto 1rem;
        }

        .add-user-sheet__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .add-user-sheet__close {
            border: none;
            background: rgba(148, 163, 184, 0.2);
            color: #f1f5f9;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .add-user-sheet__close:hover {
            background: rgba(129, 140, 248, 0.35);
        }

        body.sheet-open {
            overflow: hidden;
        }

        @media (max-width: 575.98px) {
            .add-user-sheet__panel {
                width: 100%;
                border-radius: 24px 24px 0 0;
            }
        }

        [data-theme="light"] .add-user-sheet__overlay {
            background: rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(4px);
        }

        [data-theme="light"] .add-user-sheet__panel {
            background: rgba(255, 255, 255, 0.97);
            border-color: rgba(203, 213, 225, 0.6);
            box-shadow: 0 -16px 35px rgba(148, 163, 184, 0.2);
        }

        [data-theme="light"] .add-user-sheet__close {
            background: rgba(148, 163, 184, 0.2);
            color: #0f172a;
        }

        [data-theme="light"] .add-user-sheet__close:hover {
            background: rgba(99, 102, 241, 0.25);
            color: #1e1b4b;
        }
</style>

<div class="users-page">
        <div class="users-shell">
            <!-- Filter -->
            <div class="row mb-4 g-3 filters-row">
                <div class="col-md-3">
                    <select class="form-select" id="roleFilter" onchange="filterUsers()">
                        <option value="">Semua Role</option>
                        <option value="team_lead">Team Lead</option>
                        <option value="developer">Developer</option>
                        <option value="designer">Designer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter" onchange="filterUsers()">
                        <option value="">Semua Status</option>
                        <option value="working">Working</option>
                        <option value="idle">Idle</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="searchInput" placeholder="🔍 Cari nama, username, atau email..." onkeyup="filterUsers()">
                </div>
            </div>

            <!-- Users List -->
            <div class="users-grid-wrapper">
                @if($users->isNotEmpty())
                    <div class="users-grid" id="usersGrid">
                        @foreach($users as $index => $user)
                        @php
                            $roleColor = $user->role == 'admin' ? 'danger' : ($user->role == 'team_lead' ? 'success' : ($user->role == 'developer' ? 'info' : 'warning'));
                            $status = $user->current_task_status ?? 'idle';
                            $statusClass = $status === 'working' ? 'status-pill-working' : 'status-pill-idle';
                            $statusLabel = $status === 'working' ? 'Working' : 'Idle';
                        @endphp
                        <div class="user-card-container">
                            <div class="user-card"
                                data-role="{{ $user->role }}"
                                data-status="{{ $status }}"
                                data-name="{{ strtolower($user->full_name) }}"
                                data-username="{{ strtolower($user->username) }}"
                                data-email="{{ strtolower($user->email) }}">
                                <div class="user-card-content">
                                    <div class="user-card-header">
                                        <div class="user-meta">
                                            <span class="user-number">#{{ sprintf('%02d', $index + 1) }}</span>
                                            <h5 class="user-name">{{ $user->full_name }}</h5>
                                            <span class="user-username">{{ '@'. $user->username }}</span>
                                        </div>
                                        <span class="role-badge badge bg-{{ $roleColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </div>
                                    <div class="user-card-body">
                                        <div class="user-info">
                                            <i class="bi bi-envelope"></i>
                                            <span>{{ $user->email }}</span>
                                        </div>
                                        <div class="user-info">
                                            <i class="bi bi-calendar-event"></i>
                                            <span>Bergabung: {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="user-card-footer">
                                        <div class="status-pill status-left {{ $statusClass }}">
                                            <span class="status-indicator"></span>
                                            <span class="status-label">{{ $statusLabel }}</span>
                                        </div>
                                        <div class="action-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editUser({{ $user->user_id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @if($user->role !== 'admin')
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteUser({{ $user->user_id }}, '{{ $user->full_name }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-people"></i>
                        <p>Tidak ada user yang ditemukan.</p>
                    </div>
                @endif
                <div id="noResultsState" class="empty-state d-none">
                    <i class="bi bi-search"></i>
                    <p>Hasil tidak ditemukan untuk filter saat ini.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Sheet Tambah User -->
<div class="add-user-sheet hidden" id="addUserSheet">
    <div class="add-user-sheet__overlay" data-sheet-close></div>
    <div class="add-user-sheet__panel">
        <div class="add-user-sheet__handle"></div>
        <div class="add-user-sheet__header">
            <div>
                <h5 class="text-gradient mb-1">
                    <i class="bi bi-person-plus-fill me-2"></i>Tambah User Baru
                </h5>
                <p class="text-muted mb-0">Lengkapi detail user untuk menambahkan anggota baru.</p>
            </div>
            <button type="button" class="add-user-sheet__close" data-sheet-close>
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" id="addUserForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="team_lead">Team Lead</option>
                    <option value="developer">Developer</option>
                    <option value="designer">Designer</option>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2 pt-2">
                <button type="button" class="btn btn-secondary" data-sheet-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Tambah User
                </button>
            </div>
        </form>
    </div>
</div>

@include('components.action-bottom-sheet')



<!-- Modal Konfirmasi Hapus User -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-gradient">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus user <strong id="deleteUserName"></strong>?</p>
                <p class="text-warning">⚠️ Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteUser">
                    <i class="bi bi-trash me-1"></i>Hapus User
                </button>
            </div>
        </div>
    </div>
</div>

</div>
</div>
@endsection
@section('scripts')
    @parent
    <script>
    // Sinkronisasi warna badge dengan tema aktif
    function updateBadgeColors(theme) {
        const isLightMode = theme ? theme === 'light' : document.body.classList.contains('light-mode');
        const badges = document.querySelectorAll('.badge');
        badges.forEach((badge) => {
            if (isLightMode) {
                if (badge.classList.contains('bg-success')) {
                    badge.style.backgroundColor = 'rgba(16, 185, 129, 0.15)';
                    badge.style.color = '#065f46';
                } else if (badge.classList.contains('bg-info')) {
                    badge.style.backgroundColor = 'rgba(14, 165, 233, 0.15)';
                    badge.style.color = '#155e75';
                } else if (badge.classList.contains('bg-warning')) {
                    badge.style.backgroundColor = 'rgba(251, 191, 36, 0.15)';
                    badge.style.color = '#92400e';
                } else if (badge.classList.contains('bg-danger')) {
                    badge.style.backgroundColor = 'rgba(239, 68, 68, 0.15)';
                    badge.style.color = '#991b1b';
                } else if (badge.classList.contains('bg-secondary')) {
                    badge.style.backgroundColor = 'rgba(107, 114, 128, 0.15)';
                    badge.style.color = '#374151';
                }
            } else {
                badge.style.backgroundColor = '';
                badge.style.color = '';
            }
        });
    }
    
    // Sinkronisasi status pill dengan tema
    function updateStatusPillColors(theme) {
        const isLightMode = theme ? theme === 'light' : document.body.classList.contains('light-mode');
        const statusPills = document.querySelectorAll('.status-pill');
        
        statusPills.forEach((pill) => {
            if (isLightMode) {
                if (pill.classList.contains('status-pill-idle')) {
                    pill.style.background = 'rgba(134, 239, 172, 0.2)';
                    pill.style.borderColor = 'rgba(134, 239, 172, 0.35)';
                    pill.style.color = '#15803d';
                    pill.style.boxShadow = '0 0 10px rgba(16, 185, 129, 0.18)';
                } else if (pill.classList.contains('status-pill-working')) {
                    pill.style.background = 'rgba(248, 113, 113, 0.18)';
                    pill.style.borderColor = 'rgba(248, 113, 113, 0.35)';
                    pill.style.color = '#dc2626';
                    pill.style.boxShadow = '0 0 10px rgba(239, 68, 68, 0.18)';
                }
            } else {
                // Reset ke dark mode
                pill.style.background = '';
                pill.style.borderColor = '';
                pill.style.color = '';
                pill.style.boxShadow = '';
            }
        });
    }
    
    // Listen untuk event perubahan tema
    document.addEventListener('theme:change', (event) => {
        updateBadgeColors(event.detail?.theme);
        updateStatusPillColors(event.detail?.theme);
    });
    
    // Inisialisasi awal
    const initializeColors = () => {
        updateBadgeColors();
        updateStatusPillColors();
    };
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeColors, { once: true });
    } else {
        initializeColors();
    }
    
    function filterUsers() {
        const roleFilter = document.getElementById('roleFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        
        const userCards = document.querySelectorAll('.user-card');
        const noResultsState = document.getElementById('noResultsState');
        const usersGrid = document.getElementById('usersGrid');
    
        if (!userCards.length) {
            if (noResultsState) {
                noResultsState.classList.add('d-none');
            }
            return;
        }
        
        let visibleCount = 0;
        
        userCards.forEach(card => {
            const role = card.dataset.role;
            const status = card.dataset.status;
            const name = card.dataset.name;
            const username = card.dataset.username;
            const email = card.dataset.email;
            
            let show = true;
            
            if (roleFilter && role !== roleFilter) {
                show = false;
            }
            
            if (statusFilter && status !== statusFilter) {
                show = false;
            }
            
            if (searchInput && !name.includes(searchInput) && !username.includes(searchInput) && !email.includes(searchInput)) {
                show = false;
            }
            
            const container = card.closest('.user-card-container');
            container.style.display = show ? '' : 'none';
            if (show) {
                visibleCount += 1;
            }
        });
    
        if (usersGrid) {
            usersGrid.style.display = visibleCount > 0 ? '' : 'none';
        }
    
        if (noResultsState) {
            noResultsState.classList.toggle('d-none', visibleCount > 0);
        }
    }
    
    function refreshUsers() {
        location.reload();
    }
    
    function editUser(userId) {
        window.location.href = `{{ url('admin/users') }}/${userId}/edit`;
    }
    
    let userToDelete = null;
    
    function deleteUser(userId, userName) {
        userToDelete = userId;
        document.getElementById('deleteUserName').textContent = userName;
        new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
    }
    
    document.getElementById('confirmDeleteUser').addEventListener('click', function() {
        if (userToDelete) {
            // Implementasi untuk hapus user
            alert('Fitur hapus user akan segera tersedia untuk user ID: ' + userToDelete);
            bootstrap.Modal.getInstance(document.getElementById('deleteUserModal')).hide();
            userToDelete = null;
        }
    });
    
    const addUserSheet = document.getElementById('addUserSheet');
    const openAddUserSheetBtn = document.getElementById('openAddUserSheet');
    const toggleAddUserSheet = (show = false) => {
        if (!addUserSheet) {
            return;
        }
        addUserSheet.classList.toggle('hidden', !show);
        addUserSheet.classList.toggle('show', show);
        document.body.classList.toggle('sheet-open', show);
        if (show) {
            setTimeout(() => {
                addUserSheet.querySelector('input[name=\"full_name\"]')?.focus();
            }, 150);
        }
    };

    openAddUserSheetBtn?.addEventListener('click', () => toggleAddUserSheet(true));

    addUserSheet?.querySelectorAll('[data-sheet-close]')?.forEach(button => {
        button.addEventListener('click', () => toggleAddUserSheet(false));
    });

    addUserSheet?.addEventListener('click', event => {
        if (event.target.classList.contains('add-user-sheet__overlay')) {
            toggleAddUserSheet(false);
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && addUserSheet?.classList.contains('show')) {
            toggleAddUserSheet(false);
        }
    });

    </script>
@endsection
