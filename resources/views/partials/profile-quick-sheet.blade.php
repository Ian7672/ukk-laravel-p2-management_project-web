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
        $profileRoleLabel = $profileRoleLabels[$profileSheetUser->role] ?? ucwords(str_replace('_', ' ', $profileSheetUser->role));
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
        <div class="profile-quick-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="profileQuickSheetTitle">
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
                <button type="button" class="profile-quick-sheet__close" data-profile-close aria-label="Tutup ringkasan profil">
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
                    <strong class="profile-quick-sheet__status profile-quick-sheet__status--{{ $profileStatusWorking ? 'working' : 'idle' }}">
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
