@once
<style>
    .btn-theme-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        background: linear-gradient(135deg, rgba(148, 163, 184, 0.12), rgba(71, 85, 105, 0.12));
        color: rgba(226, 232, 240, 0.95);
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        backdrop-filter: blur(10px);
    }

    .btn-theme-toggle:hover,
    .btn-theme-toggle:focus {
        color: #ffffff;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.35), rgba(139, 92, 246, 0.35));
        border-color: rgba(59, 130, 246, 0.45);
        box-shadow: 0 10px 24px rgba(59, 130, 246, 0.25);
        outline: none;
    }

    .btn-theme-toggle .theme-toggle-icon {
        font-size: 1rem;
    }

    .btn-theme-toggle .theme-toggle-label {
        font-size: 0.875rem;
        letter-spacing: 0.02em;
    }

    .btn-theme-toggle--compact {
        width: auto;
        padding: 0.5rem 0.85rem;
    }

    .btn-theme-toggle--compact .theme-toggle-label {
        display: none;
    }

    @media (min-width: 768px) {
        .btn-theme-toggle--compact .theme-toggle-label {
            display: inline;
        }
    }

    .floating-theme-toggle {
        position: fixed;
        bottom: clamp(24px, 5vw, 48px);
        right: clamp(20px, 5vw, 36px);
        width: 52px;
        height: 52px;
        border-radius: 50%;
        border: 1px solid rgba(148, 163, 184, 0.35);
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.35), rgba(139, 92, 246, 0.35));
        color: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 12px 32px rgba(59, 130, 246, 0.3);
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease, border 0.3s ease;
        z-index: 1300;
        backdrop-filter: blur(12px);
    }

    .floating-theme-toggle:hover,
    .floating-theme-toggle:focus {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 16px 36px rgba(59, 130, 246, 0.4);
        border-color: rgba(139, 92, 246, 0.5);
        outline: none;
    }

    .floating-theme-toggle .theme-toggle-icon {
        font-size: 1.15rem;
    }

    .floating-theme-toggle .theme-toggle-label {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    body.light-mode {
        background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 50%, #f1f5f9 100%);
        color: #0f172a;
    }

    body.light-mode .acrylic-sidebar-fixed {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.94) 0%, rgba(241, 245, 249, 0.95) 50%, rgba(248, 250, 252, 0.96) 100%);
        border-right: 1px solid rgba(148, 163, 184, 0.3);
        box-shadow: 4px 0 24px rgba(148, 163, 184, 0.25);
        color: #0f172a;
    }

    body.light-mode .acrylic-sidebar-fixed::before {
        background:
            radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.12) 0%, transparent 55%),
            radial-gradient(circle at 80% 80%, rgba(37, 99, 235, 0.12) 0%, transparent 55%);
    }

    body.light-mode .nav-link-acrylic {
        color: rgba(15, 23, 42, 0.7);
    }

    body.light-mode .nav-link-acrylic::before {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(139, 92, 246, 0.12));
    }

    body.light-mode .nav-link-acrylic:hover,
    body.light-mode .nav-link-acrylic:focus {
        color: #0f172a;
        border-color: rgba(59, 130, 246, 0.25);
        box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.12), 0 10px 24px rgba(59, 130, 246, 0.18);
    }

    body.light-mode .nav-link-acrylic.active {
        color: #0f172a;
        box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.3), 0 12px 28px rgba(59, 130, 246, 0.2);
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.18), rgba(139, 92, 246, 0.18));
    }

    body.light-mode .sidebar-icon-wrapper {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(94, 234, 212, 0.15));
        border: 1px solid rgba(148, 163, 184, 0.3);
        box-shadow: 0 8px 32px rgba(59, 130, 246, 0.2);
    }

    body.light-mode .text-muted-light {
        color: rgba(15, 23, 42, 0.55) !important;
    }

    body.light-mode .divider-glow {
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.4), transparent);
    }

    body.light-mode .sidebar-backdrop {
        background: rgba(15, 23, 42, 0.35);
    }

    body.light-mode .btn-logout-acrylic {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.12), rgba(220, 38, 38, 0.12));
        border: 1px solid rgba(220, 38, 38, 0.22);
        color: #b91c1c;
        box-shadow: none;
    }

    body.light-mode .btn-logout-acrylic:hover {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.18), rgba(220, 38, 38, 0.18));
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.18);
        color: #7f1d1d;
    }

    body.light-mode .btn-theme-toggle {
        background: linear-gradient(135deg, rgba(226, 232, 240, 0.8), rgba(203, 213, 225, 0.75));
        border-color: rgba(148, 163, 184, 0.45);
        color: #0f172a;
    }

    body.light-mode .btn-theme-toggle:hover,
    body.light-mode .btn-theme-toggle:focus {
        color: #082f49;
        background: linear-gradient(135deg, rgba(148, 163, 184, 0.45), rgba(226, 232, 240, 0.85));
        border-color: rgba(59, 130, 246, 0.35);
        box-shadow: 0 12px 26px rgba(59, 130, 246, 0.2);
    }

    body.light-mode .floating-theme-toggle {
        background: linear-gradient(135deg, rgba(226, 232, 240, 0.9), rgba(203, 213, 225, 0.9));
        color: #0f172a;
        border-color: rgba(148, 163, 184, 0.4);
        box-shadow: 0 12px 32px rgba(148, 163, 184, 0.25);
    }

    body.light-mode .floating-theme-toggle:hover,
    body.light-mode .floating-theme-toggle:focus {
        box-shadow: 0 16px 36px rgba(148, 163, 184, 0.3);
        border-color: rgba(59, 130, 246, 0.45);
    }

    @media (max-width: 768px) {
        .floating-theme-toggle {
            bottom: clamp(18px, 6vw, 32px);
            right: clamp(18px, 6vw, 32px);
            width: 48px;
            height: 48px;
        }
    }

    body.light-mode .navbar-acrylic {
        background: rgba(255, 255, 255, 0.88);
        border-bottom: 1px solid rgba(148, 163, 184, 0.35);
        color: #0f172a;
        box-shadow: 0 8px 24px rgba(148, 163, 184, 0.25);
    }

    body.light-mode .badge-acrylic {
        background: rgba(191, 219, 254, 0.7);
        border: 1px solid rgba(59, 130, 246, 0.3);
        color: #1d4ed8;
    }

    body.light-mode .glass-card,
    body.light-mode .card,
    body.light-mode .subtask-card,
    body.light-mode .subtask-sheet__panel,
    body.light-mode .comment-section,
    body.light-mode .comment,
    body.light-mode .comment-form-section,
    body.light-mode .pagination-acrylic,
    body.light-mode .alert-acrylic,
    body.light-mode .alert-danger-acrylic,
    body.light-mode .modal-content,
    body.light-mode .modal-body,
    body.light-mode .modal-header {
        background: rgba(255, 255, 255, 0.92) !important;
        color: #0f172a !important;
        border-color: rgba(148, 163, 184, 0.25) !important;
        box-shadow: 0 12px 28px rgba(148, 163, 184, 0.22);
    }

    body.light-mode .comment strong {
        color: #1e3a8a !important;
    }

    body.light-mode .comment small {
        color: rgba(71, 85, 105, 0.75) !important;
    }

    body.light-mode .comment p {
        color: #0f172a !important;
    }

    body.light-mode .badge-modern {
        border: 1px solid rgba(148, 163, 184, 0.3);
        color: #0f172a;
    }

    body.light-mode .badge-modern.badge-danger {
        background: rgba(254, 202, 202, 0.7);
        border-color: rgba(239, 68, 68, 0.35);
        color: #b91c1c;
    }

    body.light-mode .badge-modern.badge-warning {
        background: rgba(254, 240, 138, 0.7);
        border-color: rgba(234, 179, 8, 0.35);
        color: #92400e;
    }

    body.light-mode .badge-modern.badge-primary {
        background: rgba(191, 219, 254, 0.7);
        border-color: rgba(59, 130, 246, 0.35);
        color: #1d4ed8;
    }

    body.light-mode .badge-modern.badge-success {
        background: rgba(187, 247, 208, 0.7);
        border-color: rgba(34, 197, 94, 0.35);
        color: #047857;
    }

    body.light-mode .badge-modern.badge-info {
        background: rgba(186, 230, 253, 0.7);
        border-color: rgba(14, 165, 233, 0.35);
        color: #0369a1;
    }

    body.light-mode .table {
        --bs-table-bg: transparent !important;
        --bs-table-color: #0f172a !important;
        --bs-table-border-color: rgba(148, 163, 184, 0.3) !important;
    }

    body.light-mode .table-acrylic thead,
    body.light-mode .subtask-table thead {
        background: rgba(226, 232, 240, 0.9) !important;
        color: #0f172a !important;
    }

    body.light-mode .table-acrylic tbody td,
    body.light-mode .subtask-table tbody td {
        color: #0f172a !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.28) !important;
    }

    body.light-mode .table-acrylic tbody tr:hover,
    body.light-mode .subtask-table tbody tr:hover {
        background: rgba(191, 219, 254, 0.35) !important;
    }

    body.light-mode .form-control {
        background: rgba(241, 245, 249, 0.9) !important;
        border: 1px solid rgba(148, 163, 184, 0.5);
        color: #0f172a !important;
    }

    body.light-mode .form-control::placeholder {
        color: rgba(71, 85, 105, 0.75);
    }

    body.light-mode .form-control:focus {
        background: rgba(255, 255, 255, 0.95) !important;
        border-color: rgba(59, 130, 246, 0.45);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.18);
        color: #0f172a !important;
    }

    body.light-mode .fab-tool,
    body.light-mode .fab-btn {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.62), rgba(37, 99, 235, 0.62));
        color: #ffffff;
        border-color: rgba(59, 130, 246, 0.35);
    }

    body.light-mode .page-link {
        background: rgba(191, 219, 254, 0.8);
        border: 1px solid rgba(148, 163, 184, 0.4);
        color: #1d4ed8;
    }

    body.light-mode .page-link:hover {
        background: rgba(148, 163, 184, 0.4);
        color: #0f172a;
    }

    body.light-mode .page-item.active .page-link {
        background: rgba(59, 130, 246, 0.75);
        border-color: rgba(59, 130, 246, 0.65);
        color: #ffffff;
    }

    body.light-mode .page-item.disabled .page-link {
        background: rgba(226, 232, 240, 0.8);
        border-color: rgba(203, 213, 225, 0.65);
        color: rgba(100, 116, 139, 0.7);
    }

    body.light-mode .toggle-comment-btn,
    body.light-mode .btn-modern,
    body.light-mode .btn-comment,
    body.light-mode .btn-reply,
    body.light-mode .btn-cancel,
    body.light-mode .btn-modern.btn-sm {
        background: linear-gradient(135deg, rgba(191, 219, 254, 0.85), rgba(167, 243, 208, 0.7));
        border: 1px solid rgba(59, 130, 246, 0.3);
        color: #0f172a;
    }

    body.light-mode .toggle-comment-btn:hover,
    body.light-mode .btn-modern:hover,
    body.light-mode .btn-comment:hover,
    body.light-mode .btn-reply:hover,
    body.light-mode .btn-cancel:hover {
        color: #082f49;
        box-shadow: 0 10px 24px rgba(59, 130, 246, 0.2);
    }

    body.light-mode ::-webkit-scrollbar-track {
        background: rgba(226, 232, 240, 0.8);
    }

    body.light-mode ::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.7);
    }

    body.light-mode ::-webkit-scrollbar-thumb:hover {
        background: rgba(100, 116, 139, 0.75);
    }
</style>
<script>
    (function () {
        if (window.__themeToggleInitialized) {
            return;
        }

        const STORAGE_KEY = 'app:theme-preference';

        const ensureToggleControls = () => {
            if (!document.body) {
                return;
            }

            if (!document.querySelector('[data-theme-toggle].floating-theme-toggle')) {
                const toggleButton = document.createElement('button');
                toggleButton.type = 'button';
                toggleButton.className = 'floating-theme-toggle';
                toggleButton.setAttribute('data-theme-toggle', '');
                toggleButton.setAttribute('aria-label', 'Aktifkan mode terang');
                toggleButton.innerHTML = '<i class="theme-toggle-icon bi bi-sun-fill"></i>';
                document.body.appendChild(toggleButton);
            }
        };

        const getPreferredTheme = () => {
            try {
                const stored = localStorage.getItem(STORAGE_KEY);
                if (stored === 'light' || stored === 'dark') {
                    return stored;
                }
            } catch (error) {
                console.warn('Theme preference cannot be read from storage:', error);
            }
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches
                ? 'light'
                : 'dark';
        };

        const updateToggleLabels = (theme) => {
            const isLight = theme === 'light';
            document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                const icon = button.querySelector('.theme-toggle-icon');
                const label = button.querySelector('.theme-toggle-label');
                const baseIconClass = `theme-toggle-icon bi ${isLight ? 'bi-moon-stars' : 'bi-sun-fill'}`;

                if (icon) {
                    const shouldAddMargin = Boolean(label) && !button.classList.contains('floating-theme-toggle');
                    icon.className = shouldAddMargin ? `${baseIconClass} me-2` : baseIconClass;
                }

                if (label) {
                    label.textContent = isLight ? 'Mode Gelap' : 'Mode Terang';
                }

                button.setAttribute(
                    'aria-label',
                    isLight ? 'Aktifkan mode gelap' : 'Aktifkan mode terang'
                );
            });
        };

        const applyTheme = (theme) => {
            const body = document.body;
            if (!body) {
                return;
            }

            if (theme === 'light') {
                body.classList.add('light-mode');
            } else {
                body.classList.remove('light-mode');
            }

            document.documentElement.setAttribute('data-theme', theme);

            try {
                localStorage.setItem(STORAGE_KEY, theme);
            } catch (error) {
                console.warn('Theme preference cannot be saved to storage:', error);
            }

            updateToggleLabels(theme);
            document.dispatchEvent(new CustomEvent('theme:change', { detail: { theme } }));
        };

        const toggleTheme = () => {
            const currentTheme = document.body.classList.contains('light-mode') ? 'light' : 'dark';
            applyTheme(currentTheme === 'light' ? 'dark' : 'light');
        };

        const init = () => {
            ensureToggleControls();
            applyTheme(getPreferredTheme());
        };

        document.addEventListener('click', (event) => {
            const target = event.target.closest('[data-theme-toggle]');
            if (!target) {
                return;
            }
            event.preventDefault();
            toggleTheme();
        });

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init, { once: true });
        } else {
            init();
        }

        window.__themeToggleInitialized = true;
    })();
</script>
@endonce
