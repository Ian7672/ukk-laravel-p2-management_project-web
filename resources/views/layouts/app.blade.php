<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Solver')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

        /* Main Layout */
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

        /* Topbar */
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

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .page-toolbar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .page-toolbar .toolbar-text {
            color: rgba(226, 232, 240, 0.85);
            font-size: 0.95rem;
            margin: 0;
        }

        .page-toolbar form {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .page-toolbar form .form-control {
            min-width: 220px;
        }

        /* Content Area */
        .content-wrapper {
            padding: 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Cards */
        .card {
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            background: rgba(17, 24, 39, 0.8);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px 16px 0 0 !important;
        }

        .card-title {
            color: #e5e7eb;
            font-weight: 600;
        }

        /* Tables */
        .table {
            color: #e5e7eb;
        }

        .table-dark {
            background: rgba(17, 24, 39, 0.8);
        }

        .table-striped>tbody>tr:nth-of-type(odd)>td {
            background: rgba(17, 24, 39, 0.3);
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #8b5cf6, #3b82f6);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #7c3aed, #2563eb);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
        }

        /* Forms */
        .form-control,
        .form-select {
            background: rgba(17, 24, 39, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e5e7eb;
            border-radius: 8px;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(17, 24, 39, 0.8);
            border-color: #8b5cf6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
            color: #e5e7eb;
        }

        .form-label {
            color: #e5e7eb;
            font-weight: 500;
        }

        /* Badges */
        .badge {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
        }

        /* Avatar */
        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content-area {
                margin-left: 0;
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
                position: relative;
                padding: 0.85rem 1.25rem;
            }

            .navbar-title {
                font-size: 1.35rem;
            }

            .content-wrapper {
                padding: 1.75rem 1rem 4rem;
            }
        }

        @media (max-width: 767.98px) {
            .navbar-acrylic .container-fluid {
                flex-wrap: wrap;
                justify-content: space-between;
            }

            .mobile-nav-wrapper {
                width: 100%;
                justify-content: space-between;
            }

            .topbar-actions {
                width: 100%;
                justify-content: space-between;
            }

            .page-toolbar {
                width: 100%;
                justify-content: space-between;
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
        }

        [data-theme="light"] body {
            background: linear-gradient(135deg, #f9fafb 0%, #e0e7ff 50%, #ffffff 100%);
            color: #1f2937;
        }

        [data-theme="light"] .navbar-acrylic {
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(203, 213, 225, 0.7);
            box-shadow: 0 14px 36px rgba(148, 163, 184, 0.26);
        }

        [data-theme="light"] .navbar-title {
            background: linear-gradient(135deg, #4c1d95, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        [data-theme="light"] .page-toolbar .toolbar-text {
            color: #475569;
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
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.2);
        }


        [data-theme="light"] .card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(203, 213, 225, 0.7);
            box-shadow: 0 22px 48px rgba(148, 163, 184, 0.24);
            color: #1f2937;
        }

        [data-theme="light"] .card-header {
            background: rgba(248, 250, 252, 0.9);
            border-bottom: 1px solid rgba(203, 213, 225, 0.7);
        }

        [data-theme="light"] .card-title {
            color: #1f2937;
        }

        [data-theme="light"] .table {
            color: #1f2937;
        }

        [data-theme="light"] .table-dark {
            background: rgba(241, 245, 249, 0.95);
            color: #1f2937;
        }

        [data-theme="light"] .table-striped>tbody>tr:nth-of-type(odd)>td {
            background: rgba(226, 232, 240, 0.35);
        }

        [data-theme="light"] .btn-primary {
            box-shadow: 0 12px 28px rgba(129, 140, 248, 0.28);
        }

        [data-theme="light"] .btn-primary:hover {
            box-shadow: 0 16px 36px rgba(99, 102, 241, 0.3);
        }

        [data-theme="light"] .form-control,
        [data-theme="light"] .form-select {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(203, 213, 225, 0.65);
            color: #1f2937;
        }

        [data-theme="light"] .form-control:focus,
        [data-theme="light"] .form-select:focus {
            background: rgba(255, 255, 255, 0.98);
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 0 0 0.2rem rgba(129, 140, 248, 0.25);
            color: #111827;
        }

        [data-theme="light"] .form-label {
            color: #475569;
        }

        [data-theme="light"] .badge {
            background: rgba(226, 232, 240, 0.6);
            color: #1f2937;
        }
    </style>
</head>

<body>
    <div class="layout-wrapper">
        @include('partials.sidebar')

        <div class="main-content-area">
            <!-- Topbar -->
            <nav class="navbar-acrylic">
                <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="mobile-nav-wrapper">
                        <button type="button" class="sidebar-toggle-btn d-lg-none" data-sidebar-toggle
                            aria-label="Buka navigasi">
                            <i class="bi bi-list"></i>
                        </button>
                        <span class="navbar-title">
                            @yield('page-title', 'Solver')
                        </span>
                    </div>
                    <div class="topbar-actions">
                        @hasSection('page-toolbar')
                            <div class="page-toolbar">
                                @yield('page-toolbar')
                            </div>
                        @endif

                    </div>
                </div>
            </nav>

            <!-- Content -->
            <div class="content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.__sidebarToggleInitialized) {
                return;
            }

            const body = document.body;
            const toggles = document.querySelectorAll('[data-sidebar-toggle]');
            const closeBtn = document.querySelector('.sidebar-close-btn');
            const backdrop = document.querySelector('.sidebar-backdrop');
            const sidebar = document.querySelector('.acrylic-sidebar-fixed');

            if (!sidebar || !toggles.length) {
                return;
            }

            const closeSidebar = () => body.classList.remove('sidebar-open');
            const toggleSidebar = () => body.classList.toggle('sidebar-open');

            toggles.forEach(btn => btn.addEventListener('click', toggleSidebar));

            if (closeBtn) {
                closeBtn.addEventListener('click', closeSidebar);
            }

            if (backdrop) {
                backdrop.addEventListener('click', closeSidebar);
            }

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 992) {
                    closeSidebar();
                }
            });

            window.__sidebarToggleInitialized = true;
        });
    </script>
    @php
        $profileSheetUser = auth()->user();
        $profileRoleLabels = [
            'admin' => 'Administrator',
            'team_lead' => 'Team Lead',
            'developer' => 'Developer',
            'designer' => 'Designer',
        ];
    @endphp
    @if ($profileSheetUser)
        @php
            $profileDisplayName = $profileSheetUser->full_name ?: $profileSheetUser->username;
            $profileRoleLabel =
                $profileRoleLabels[$profileSheetUser->role] ?? ucwords(str_replace('_', ' ', $profileSheetUser->role));
            $profileEmail = $profileSheetUser->email ?? 'Belum diatur';
            $profileStatusWorking = $profileSheetUser->current_task_status === 'working';
            $profileStatusLabel = $profileStatusWorking ? 'Sedang bekerja' : 'Idle';
            $profileAvatar =
                $profileSheetUser->profile_photo_url ??
                'https://ui-avatars.com/api/?name=' .
                    urlencode($profileDisplayName) .
                    '&background=0D8ABC&color=fff&size=256';
        @endphp
        <div class="profile-quick-sheet" id="profileQuickSheet" aria-hidden="true">
            <div class="profile-quick-sheet__overlay" data-profile-close></div>
            <div class="profile-quick-sheet__panel" role="dialog" aria-modal="true"
                aria-labelledby="profileQuickSheetTitle">
                <div class="profile-quick-sheet__handle"></div>
                <div class="profile-quick-sheet__header">
                    <div class="profile-quick-sheet__avatar">
                        <img src="{{ $profileAvatar }}" alt="{{ $profileDisplayName }}">
                    </div>
                    <div class="profile-quick-sheet__header-text">
                        <h5 id="profileQuickSheetTitle" class="mb-1">{{ $profileDisplayName }}</h5>
                        <p class="mb-1 text-muted">{{ '@' . $profileSheetUser->username }}</p>
                        <span class="profile-quick-sheet__role">{{ $profileRoleLabel }}</span>
                    </div>
                    <button type="button" class="profile-quick-sheet__close" data-profile-close
                        aria-label="Tutup ringkasan profil">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="profile-quick-sheet__body">
                    <div class="profile-quick-sheet__info">
                        <span>Email</span>
                        <strong>{{ $profileEmail }}</strong>
                    </div>
                    <div class="profile-quick-sheet__info">
                        <span>Status</span>
                        <strong
                            class="profile-quick-sheet__status profile-quick-sheet__status--{{ $profileStatusWorking ? 'working' : 'idle' }}">
                            {{ $profileStatusLabel }}
                        </strong>
                    </div>
                </div>
                <div class="profile-quick-sheet__actions">
                    <button type="button" class="profile-quick-sheet__logout" data-profile-logout>
                        <i class="bi bi-box-arrow-right me-2"></i>Keluar
                    </button>
                </div>
                <form id="profileQuickLogoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
        <style>
            .profile-quick-sheet {
                position: fixed;
                inset: 0;
                display: flex;
                align-items: flex-end;
                justify-content: center;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.25s ease;
                z-index: 1600;
            }

            .profile-quick-sheet--open {
                opacity: 1;
                pointer-events: auto;
            }

            .profile-quick-sheet__overlay {
                position: absolute;
                inset: 0;
                background: rgba(15, 23, 42, 0.65);
                backdrop-filter: blur(12px);
            }

            .profile-quick-sheet__panel {
                position: relative;
                width: min(520px, 100% - 1.5rem);
                background: rgba(17, 24, 39, 0.96);
                border-radius: 28px 28px 0 0;
                border: 1px solid rgba(255, 255, 255, 0.08);
                padding: 1.75rem 1.75rem 1.5rem;
                box-shadow: 0 -30px 60px rgba(15, 23, 42, 0.5);
                transform: translateY(32px);
                transition: transform 0.25s ease;
            }

            .profile-quick-sheet--open .profile-quick-sheet__panel {
                transform: translateY(0);
            }

            .profile-quick-sheet__handle {
                width: 48px;
                height: 5px;
                border-radius: 999px;
                background: rgba(148, 163, 184, 0.4);
                margin: 0 auto 1rem;
            }

            .profile-quick-sheet__header {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .profile-quick-sheet__avatar {
                width: 64px;
                height: 64px;
                border-radius: 18px;
                overflow: hidden;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .profile-quick-sheet__avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .profile-quick-sheet__header-text h5 {
                font-weight: 700;
                color: #f8fafc;
            }

            .profile-quick-sheet__header-text p {
                color: rgba(226, 232, 240, 0.75);
                font-size: 0.9rem;
            }

            .profile-quick-sheet__role {
                display: inline-block;
                padding: 0.15rem 0.75rem;
                border-radius: 999px;
                font-size: 0.75rem;
                letter-spacing: 0.08em;
                background: rgba(129, 140, 248, 0.18);
                border: 1px solid rgba(99, 102, 241, 0.35);
                color: #c7d2fe;
            }

            .profile-quick-sheet__close {
                border: none;
                background: rgba(226, 232, 240, 0.22);
                color: #f8fafc;
                width: 38px;
                height: 38px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: inset 0 0 10px rgba(15, 23, 42, 0.35);
            }

            .profile-quick-sheet__body {
                display: grid;
                gap: 1rem;
                margin-bottom: 1.5rem;
            }

            .profile-quick-sheet__info {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: rgba(23, 37, 84, 0.35);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 16px;
                padding: 0.85rem 1rem;
            }

            .profile-quick-sheet__info span {
                color: rgba(226, 232, 240, 0.65);
                font-size: 0.85rem;
            }

            .profile-quick-sheet__info strong {
                color: #f8fafc;
            }

            .profile-quick-sheet__status {
                font-weight: 600;
            }

            .profile-quick-sheet__status--working {
                color: #f97316;
            }

            .profile-quick-sheet__status--idle {
                color: #10b981;
            }

            .profile-quick-sheet__actions {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .profile-quick-sheet__logout {
                border-radius: 16px;
                padding: 0.85rem 1.25rem;
                font-weight: 600;
                border: 1px solid rgba(99, 102, 241, 0.4);
                background: rgba(99, 102, 241, 0.15);
                color: #c7d2fe;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                text-decoration: none;
            }

            .profile-quick-sheet__logout {
                border-color: rgba(239, 68, 68, 0.5);
                background: rgba(239, 68, 68, 0.2);
                color: #fecaca;
            }

            body.profile-quick-open {
                overflow: hidden;
            }

            @media (max-width: 575.98px) {
                .profile-quick-sheet__panel {
                    padding: 1.25rem 1.25rem 1.5rem;
                }

                .profile-quick-sheet__header {
                    align-items: flex-start;
                }
            }

            [data-theme="light"] .profile-quick-sheet__panel {
                background: rgba(255, 255, 255, 0.98);
                border-color: rgba(148, 163, 184, 0.2);
            }

            [data-theme="light"] .profile-quick-sheet__header-text h5 {
                color: #0f172a;
            }

            [data-theme="light"] .profile-quick-sheet__header-text p {
                color: #475569;
            }

            [data-theme="light"] .profile-quick-sheet__info {
                background: rgba(248, 250, 252, 0.95);
                border-color: rgba(203, 213, 225, 0.8);
            }

            [data-theme="light"] .profile-quick-sheet__info span {
                color: #64748b;
            }

            [data-theme="light"] .profile-quick-sheet__info strong {
                color: #0f172a;
            }

            [data-theme="light"] .profile-quick-sheet__role {
                color: #1e1b4b;
                background: rgba(191, 219, 254, 0.75);
                border-color: rgba(59, 130, 246, 0.4);
            }

            [data-theme="light"] .profile-quick-sheet__close {
                background: rgba(226, 232, 240, 0.92);
                color: #0f172a;
            }

            [data-theme="light"] .profile-quick-sheet__logout {
                border-color: rgba(239, 68, 68, 0.4);
                background: rgba(239, 68, 68, 0.15);
                color: #991b1b;
            }

            html:not([data-theme="light"]) .text-muted,
            [data-theme="dark"] .text-muted {
                color: #ffffff !important;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sheet = document.getElementById('profileQuickSheet');
                if (!sheet) {
                    return;
                }

                const triggers = document.querySelectorAll('[data-profile-sheet-trigger]');
                const overlay = sheet.querySelector('.profile-quick-sheet__overlay');
                const closeButtons = sheet.querySelectorAll('[data-profile-close]');
                const logoutButton = sheet.querySelector('[data-profile-logout]');
                const logoutForm = document.getElementById('profileQuickLogoutForm');
                const body = document.body;

                const openSheet = () => {
                    sheet.classList.add('profile-quick-sheet--open');
                    sheet.setAttribute('aria-hidden', 'false');
                    body.classList.add('profile-quick-open');
                };

                const closeSheet = () => {
                    sheet.classList.remove('profile-quick-sheet--open');
                    sheet.setAttribute('aria-hidden', 'true');
                    body.classList.remove('profile-quick-open');
                };

                triggers.forEach(trigger => {
                    trigger.addEventListener('click', openSheet);
                    trigger.addEventListener('keydown', event => {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            openSheet();
                        }
                    });
                });

                closeButtons.forEach(btn => btn.addEventListener('click', closeSheet));
                if (overlay) {
                    overlay.addEventListener('click', closeSheet);
                }

                document.addEventListener('keydown', event => {
                    if (event.key === 'Escape' && sheet.classList.contains('profile-quick-sheet--open')) {
                        closeSheet();
                    }
                });

                if (logoutButton && logoutForm) {
                    logoutButton.addEventListener('click', () => logoutForm.submit());
                }
            });
        </script>
    @endif
    @yield('scripts')
</body>

</html>
