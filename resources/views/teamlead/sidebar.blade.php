<!-- Fixed Floating Sidebar -->
<div class="acrylic-sidebar-fixed">
    <button type="button" class="sidebar-close-btn d-lg-none" aria-label="Tutup menu">
        <i class="bi bi-x-lg"></i>
    </button>
    <div class="text-center mb-4">
        <div class="sidebar-icon-wrapper mb-3" role="button" tabindex="0" aria-label="Profil saya" data-profile-sheet-trigger>
            <i class="bi bi-person-badge fs-1 text-gradient"></i>
        </div>
        <h4 class="text-gradient mb-0 fw-bold">{{ '@'. Auth::user()->username }}</h4>
        @php
            $isWorking = Auth::user()->current_task_status === 'working';
            $statusClass = $isWorking ? 'status-pill-working' : 'status-pill-idle';
            $statusLabel = $isWorking ? 'Working' : 'Idle';
        @endphp
        <div class="status-pill {{ $statusClass }}" data-status="{{ $statusLabel }}">
            <span class="status-indicator"></span>
            <span class="status-label">{{ $statusLabel }}</span>
        </div>
    </div>
    <hr class="divider-glow">

    @php
        $isDashboard = request()->routeIs('teamlead.dashboard') || request()->routeIs('teamlead.projects.*');
        $isBlocker = request()->routeIs('teamlead.blocker.*');

        if (!$isDashboard && !$isBlocker) {
            $isDashboard = true;
        }
    @endphp

    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a href="{{ route('teamlead.dashboard') }}" 
               class="nav-link-acrylic {{ $isDashboard ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('teamlead.blocker.index') }}" 
               class="nav-link-acrylic {{ $isBlocker ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle me-2"></i> Solver
            </a>
        </li>
    </ul>

    <div class="sidebar-footer mt-4 pt-2">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout-acrylic w-100">
                <i class="bi bi-box-arrow-right me-2"></i>Keluar
            </button>
        </form>
    </div>
</div>

<div class="sidebar-backdrop d-lg-none"></div>

<style>
/* Fixed Floating Sidebar */
.acrylic-sidebar-fixed {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    display: flex;
    flex-direction: column;
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

.sidebar-close-btn {
    border: none;
    background: rgba(15, 23, 42, 0.7);
    color: rgba(255, 255, 255, 0.85);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 1rem;
    right: 1rem;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.35);
}

.sidebar-close-btn:hover {
    color: #fff;
    background: rgba(139, 92, 246, 0.4);
    transform: rotate(90deg);
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
    cursor: pointer;
}

.text-gradient {
    background: linear-gradient(135deg, #8b5cf6, #3b82f6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Status badge */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: center;
    padding: 0.45rem 0.95rem;
    border-radius: 999px;
    border: 1px solid transparent;
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    backdrop-filter: blur(16px);
    transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    white-space: nowrap;
    margin: 0 auto;
}

.status-pill .status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: currentColor;
    box-shadow: 0 0 6px currentColor;
}

.status-pill-working {
    color: #ef4444;
    background: rgba(239, 68, 68, 0.14);
    border-color: rgba(239, 68, 68, 0.28);
    box-shadow: 0 6px 18px rgba(239, 68, 68, 0.15);
}

.status-pill-idle {
    color: #22c55e;
    background: rgba(34, 197, 94, 0.14);
    border-color: rgba(34, 197, 94, 0.28);
    box-shadow: 0 6px 18px rgba(34, 197, 94, 0.15);
}

.status-pill-unknown {
    color: #94a3b8;
    background: rgba(148, 163, 184, 0.14);
    border-color: rgba(148, 163, 184, 0.28);
    box-shadow: 0 6px 18px rgba(148, 163, 184, 0.15);
}

.status-pill:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.22);
}

.text-muted-light {
    color: rgba(226, 232, 240, 0.7);
}

[data-theme="light"] .status-pill-working {
    color: #dc2626;
    background: rgba(248, 113, 113, 0.18);
    border-color: rgba(248, 113, 113, 0.35);
}

[data-theme="light"] .status-pill-idle {
    color: #15803d;
    background: rgba(134, 239, 172, 0.2);
    border-color: rgba(134, 239, 172, 0.35);
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.18);
}

[data-theme="light"] .status-pill-unknown {
    color: #475569;
    background: rgba(203, 213, 225, 0.2);
    border-color: rgba(203, 213, 225, 0.35);
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
    margin-top: auto;
    padding-top: 1.25rem;
    padding-bottom: 1.25rem;
}

.sidebar-footer .btn-logout-acrylic {
    width: 100%;
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
    width: 100%;
    text-decoration: none;
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

.sidebar-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 900;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

/* Board section styling */
.nav-item .text-muted-light {
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
    display: block;
}

@media (max-width: 991.98px) {
    body.sidebar-open {
        overflow: hidden;
    }

    .acrylic-sidebar-fixed {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        width: min(78vw, 260px);
    }

    body.sidebar-open .acrylic-sidebar-fixed {
        transform: translateX(0);
        box-shadow: 12px 0 40px rgba(15, 23, 42, 0.65);
    }

    .sidebar-close-btn {
        display: inline-flex;
    }

    body.sidebar-open .sidebar-backdrop {
        opacity: 1;
        visibility: visible;
    }

    .sidebar-footer {
        margin-top: 2rem;
        padding-bottom: 1rem;
    }
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.__sidebarToggleInitialized) {
        return;
    }

    const body = document.body;
    const toggles = document.querySelectorAll('[data-sidebar-toggle]');
    const closeBtn = document.querySelector('.sidebar-close-btn');
    const backdrop = document.querySelector('.sidebar-backdrop');

    if (!toggles.length) {
        return;
    }

    const closeSidebar = () => body.classList.remove('sidebar-open');
    const toggleSidebar = () => body.classList.toggle('sidebar-open');

    toggles.forEach(btn => {
        btn.addEventListener('click', toggleSidebar);
    });

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

@include('components.theme-toggle')
