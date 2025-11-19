@php
    use App\Models\Blocker;

    $user = auth()->user();
    $role = $user->role ?? 'guest';
    $activeKey = $active ?? null;
    $sidebarProject = $sidebarProject ?? ($project ?? null);
    $activeBoardId = null;

    if (request()->routeIs('admin.cards.index')) {
        $routeBoard = request()->route('board');
        if (is_object($routeBoard)) {
            $activeBoardId = $routeBoard->board_id ?? $routeBoard->id ?? null;
        } else {
            $activeBoardId = $routeBoard;
        }
    }

    $iconMap = [
        'admin' => 'bi-kanban-fill',
        'team_lead' => 'bi-person-badge',
        'developer' => 'bi-code-slash',
        'designer' => 'bi-brush',
    ];

    $menuItems = [];

    switch ($role) {
        case 'admin':
            $menuItems[] = [
                'key' => 'dashboard',
                'label' => 'Dashboard',
                'icon' => 'bi bi-speedometer2',
                'url' => route('dashboard'),
                'active' => request()->routeIs('dashboard') || request()->routeIs('admin.projects.*'),
            ];
            $menuItems[] = [
                'key' => 'monitoring',
                'label' => 'Monitoring',
                'icon' => 'bi bi-graph-up',
                'url' => route('admin.monitoring.index'),
                'active' => request()->routeIs('admin.monitoring.*'),
            ];
            $menuItems[] = [
                'key' => 'users',
                'label' => 'User',
                'icon' => 'bi bi-person-plus',
                'url' => route('admin.users.index'),
                'active' => request()->routeIs('admin.users.*'),
            ];
            $menuItems[] = [
                'key' => 'reports',
                'label' => 'Laporan',
                'icon' => 'bi bi-file-earmark-text',
                'url' => route('admin.reports.index'),
                'active' => request()->routeIs('admin.reports.*'),
            ];

            break;

        case 'team_lead':
            $pendingBlockers = Blocker::where('status', 'pending')->count();
            $menuItems[] = [
                'key' => 'dashboard',
                'label' => 'Dashboard',
                'icon' => 'bi bi-speedometer2',
                'url' => route('teamlead.dashboard'),
                'active' => request()->routeIs('teamlead.dashboard') || request()->routeIs('teamlead.projects.*'),
            ];
            $menuItems[] = [
                'key' => 'blocker',
                'label' => 'Solver',
                'icon' => 'bi bi-exclamation-triangle',
                'url' => route('teamlead.blocker.index'),
                'active' => request()->routeIs('teamlead.blocker.*'),
                'badge' => $pendingBlockers,
            ];
            break;

        case 'designer':
            $menuItems[] = [
                'key' => 'dashboard',
                'label' => 'Dashboard',
                'icon' => 'bi bi-speedometer2',
                'url' => route('designer.dashboard'),
                'active' => request()->routeIs('designer.dashboard'),
            ];
            $menuItems[] = [
                'key' => 'myteam',
                'label' => 'My Team',
                'icon' => 'bi bi-people',
                'url' => route('designer.myteam'),
                'active' => request()->routeIs('designer.myteam'),
            ];
            break;

        case 'developer':
        default:
            $menuItems[] = [
                'key' => 'dashboard',
                'label' => 'Dashboard',
                'icon' => 'bi bi-speedometer2',
                'url' => route('developer.dashboard'),
                'active' => request()->routeIs('developer.dashboard'),
            ];
            $menuItems[] = [
                'key' => 'myteam',
                'label' => 'My Team',
                'icon' => 'bi bi-people',
                'url' => route('developer.myteam'),
                'active' => request()->routeIs('developer.myteam'),
            ];
            break;
    }

    foreach ($menuItems as $index => $item) {
        if (($item['type'] ?? 'link') === 'section') {
            if (!empty($item['children'])) {
                foreach ($item['children'] as $childIndex => $child) {
                    $menuItems[$index]['children'][$childIndex]['is_active'] =
                        (($child['key'] ?? null) && $activeKey === $child['key']) || ($child['active'] ?? false);
                }
            }
            continue;
        }

        $menuItems[$index]['is_active'] =
            (($item['key'] ?? null) && $activeKey === $item['key']) || ($item['active'] ?? false);
    }

    $iconClass = $iconMap[$role] ?? 'bi-kanban-fill';
    $showStatus = $role !== 'admin';

    $isWorking = $user->current_task_status === 'working';
    $statusClass = $isWorking ? 'status-pill-working' : 'status-pill-idle';
    $statusLabel = $isWorking ? 'Working' : 'Idle';
@endphp

<div class="acrylic-sidebar-fixed">
    <button type="button" class="sidebar-close-btn d-lg-none" aria-label="Tutup menu">
        <i class="bi bi-x-lg"></i>
    </button>

    <div class="text-center mb-4">
        <div class="sidebar-icon-wrapper mb-3" role="button" tabindex="0" aria-label="Profil saya" data-profile-sheet-trigger>
            <i class="{{ $iconClass }} fs-1 text-gradient"></i>
        </div>
        <h4 class="text-gradient mb-0 fw-bold">{{ '@'. $user->username }}</h4>

        @if($showStatus)
            <div class="status-pill {{ $statusClass }}" data-status="{{ $statusLabel }}">
                <span class="status-indicator"></span>
                <span class="status-label">{{ $statusLabel }}</span>
            </div>
        @endif
    </div>

    <hr class="divider-glow">

    <ul class="nav flex-column flex-grow-1">
        @foreach($menuItems as $item)
            @if(($item['type'] ?? 'link') === 'section')
                <li class="nav-item mb-2">
                    <span class="text-muted-light small fw-bold d-block mb-2">{{ $item['label'] }}</span>
                </li>
                @foreach($item['children'] ?? [] as $child)
                    <li class="nav-item mb-2">
                        <a href="{{ $child['url'] ?? '#' }}" class="nav-link-acrylic ps-4 {{ !empty($child['is_active']) ? 'active' : '' }}">
                            @if(!empty($child['icon']))
                                <i class="{{ $child['icon'] }} me-2"></i>
                            @endif
                            {{ $child['label'] }}
                        </a>
                    </li>
                @endforeach
                @continue
            @endif

            <li class="nav-item mb-2">
                <a href="{{ $item['url'] ?? '#' }}" class="nav-link-acrylic {{ !empty($item['is_active']) ? 'active' : '' }}">
                    @if(!empty($item['icon']))
                        <i class="{{ $item['icon'] }} me-2"></i>
                    @endif
                    {{ $item['label'] }}
                    @if(!empty($item['badge']))
                        <span class="nav-link-badge">{{ $item['badge'] }}</span>
                    @endif
                </a>
            </li>
        @endforeach
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

<div class="sidebar-backdrop d-lg-none" id="sidebarBackdrop"></div>

@once
    <style>
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
            border: 1px solid transparent;
            gap: 0.5rem;
        }

        .nav-link-badge {
            margin-left: auto;
            background: rgba(248, 113, 113, 0.2);
            color: #fecaca;
            border: 1px solid rgba(248, 113, 113, 0.5);
            padding: 0.1rem 0.55rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .nav-link-acrylic:hover {
            color: #fff;
            border-color: rgba(139, 92, 246, 0.4);
            background: rgba(139, 92, 246, 0.2);
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.18);
        }

        .nav-link-acrylic.active {
            color: #fff;
            border-color: rgba(139, 92, 246, 0.65);
            background: linear-gradient(135deg,
                rgba(129, 140, 248, 0.35),
                rgba(59, 130, 246, 0.35)
            );
            box-shadow:
                0 12px 32px rgba(79, 70, 229, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }

        .sidebar-footer .btn-logout-acrylic {
            border-radius: 999px;
            padding: 0.65rem 1rem;
            font-weight: 600;
            border: 1px solid rgba(248, 113, 113, 0.35);
            background: rgba(248, 113, 113, 0.2);
            color: #fecaca;
            transition: all 0.2s ease;
        }

        .sidebar-footer .btn-logout-acrylic:hover {
            background: rgba(248, 113, 113, 0.3);
            border-color: rgba(248, 113, 113, 0.5);
            color: #fff;
            box-shadow: 0 10px 25px rgba(248, 113, 113, 0.25);
        }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            z-index: 1300;
        }

        body.sidebar-open .sidebar-backdrop {
            opacity: 1;
            visibility: visible;
        }

        body.sidebar-open .acrylic-sidebar-fixed {
            transform: translateX(0);
        }

        @media (max-width: 991.98px) {
            .acrylic-sidebar-fixed {
                transform: translateX(-110%);
                transition: transform 0.3s ease;
            }

            body.sidebar-open {
                overflow: hidden;
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

        [data-theme="light"] .nav-link-acrylic {
            color: #1f2937;
        }

        [data-theme="light"] .nav-link-acrylic.active {
            color: #1e3a8a;
        }

        [data-theme="light"] .nav-link-badge {
            background: rgba(248, 113, 113, 0.22);
            color: #b91c1c;
            border-color: rgba(248, 113, 113, 0.45);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.__appSidebarInitialized) {
                return;
            }

            const toggles = document.querySelectorAll('[data-sidebar-toggle]');
            const body = document.body;
            const closeBtn = document.querySelector('.sidebar-close-btn');
            const backdrop = document.getElementById('sidebarBackdrop');

            const closeSidebar = () => body.classList.remove('sidebar-open');
            const toggleSidebar = () => body.classList.toggle('sidebar-open');

            toggles.forEach(toggle => {
                toggle.addEventListener('click', toggleSidebar);
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

            window.__appSidebarInitialized = true;
        });
    </script>
@endonce
