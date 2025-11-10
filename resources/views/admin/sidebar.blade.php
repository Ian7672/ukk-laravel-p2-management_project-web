<div class="acrylic-sidebar-fixed">
    <button type="button" class="sidebar-close-btn d-lg-none" aria-label="Tutup menu">
        <i class="bi bi-x-lg"></i>
    </button>

    <div class="text-center mb-4">
        <div class="sidebar-icon-wrapper mb-3" role="button" tabindex="0" aria-label="Profil saya" data-profile-sheet-trigger>
            <i class="bi bi-kanban-fill text-gradient" style="font-size: 2rem;"></i>
        </div>
        <h4 class="text-gradient mb-0 fw-bold">{{ '@'. Auth::user()->username }}</h4>

        @if(Auth::user()->role !== 'admin')
            @php
                $isWorking = Auth::user()->current_task_status === 'working';
                $statusClass = $isWorking ? 'status-pill-working' : 'status-pill-idle';
                $statusLabel = $isWorking ? 'Working' : 'Idle';
            @endphp
            <div class="status-pill {{ $statusClass }}" data-status="{{ $statusLabel }}">
                <span class="status-indicator"></span>
                <span class="status-label">{{ $statusLabel }}</span>
            </div>
        @endif
    </div>

    <hr class="divider-glow">

    @php
        $sidebarProject = $sidebarProject ?? ($project ?? null);
        $isDashboard = request()->routeIs('dashboard') || request()->routeIs('admin.projects.*');
        $isMonitoring = request()->routeIs('admin.monitoring.*');
        $isUserManagement = request()->routeIs('admin.users.index') || request()->routeIs('admin.users.store') || request()->routeIs('admin.users.edit') || request()->routeIs('admin.users.update') || request()->routeIs('admin.users.approve') || request()->routeIs('admin.users.reject') || request()->routeIs('admin.users.updateRole') || request()->routeIs('admin.users.getUserProjects');
        $isReports = request()->routeIs('admin.reports.*');

        $activeBoardId = null;
        if (request()->routeIs('admin.cards.index')) {
            $routeBoard = request()->route('board');
            if (is_object($routeBoard)) {
                $activeBoardId = $routeBoard->board_id ?? $routeBoard->id ?? null;
            } else {
                $activeBoardId = $routeBoard;
            }
        }
    @endphp

    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a href="{{ route('dashboard') }}" class="nav-link-acrylic {{ $isDashboard ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-3"></i> Dashboard
            </a>
        </li>

        @if(!$isMonitoring && isset($sidebarProject) && $sidebarProject && $sidebarProject->boards->count() && Auth::user()->role !== 'admin')
            <li class="nav-item mb-2">
                <span class="text-muted-light small fw-bold d-block mb-2">Cards</span>
            </li>
            @foreach($sidebarProject->boards as $board)
                @php
                    $isBoardActive = request()->routeIs('admin.cards.index') && (string) $activeBoardId === (string) $board->board_id;
                @endphp
                <li class="nav-item mb-2">
                    <a href="{{ route('admin.cards.index', $board->board_id) }}" class="nav-link-acrylic ps-4 {{ $isBoardActive ? 'active' : '' }}">
                        <i class="bi bi-card-checklist me-3"></i> {{ $board->board_name }}
                    </a>
                </li>
            @endforeach
        @endif

        <li class="nav-item mb-2">
            <a href="{{ route('admin.monitoring.index') }}" class="nav-link-acrylic {{ $isMonitoring ? 'active' : '' }}">
                <i class="bi bi-graph-up me-3"></i> Monitoring
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('admin.users.index') }}" class="nav-link-acrylic {{ $isUserManagement ? 'active' : '' }}">
                <i class="bi bi-person-plus me-3"></i> User
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('admin.reports.index') }}" class="nav-link-acrylic {{ $isReports ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text me-3"></i>Laporan
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

.sidebar-close-btn {
    border: none;
    background: rgba(15, 23, 42, 0.7);
    color: rgba(255, 255, 255, 0.8);
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

.acrylic-sidebar-fixed .nav {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    flex: 1 1 auto;
    margin-bottom: 1.5rem;
}

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

.sidebar-footer {
    position: relative;
    margin-top: 2.5rem;
    padding-bottom: 1.25rem;
    display: flex;
    justify-content: center;
}

.sidebar-footer form {
    width: 100%;
    display: flex;
    justify-content: center;
}

.sidebar-footer .btn-logout-acrylic {
    width: 100%;
    max-width: 220px;
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
}

.btn-logout-acrylic:hover {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(220, 38, 38, 0.3));
    color: white;
    box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
}

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

@media (max-width: 991.98px) {
    body.sidebar-open {
        overflow: hidden;
    }

    .acrylic-sidebar-fixed {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        width: min(85vw, 320px);
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
        display: flex;
        justify-content: center;
    }

    .sidebar-footer form {
        justify-content: center;
    }

    }
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

@include('components.theme-toggle')
