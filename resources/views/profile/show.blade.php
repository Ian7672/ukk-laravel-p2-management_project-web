@extends('layouts.app')

@section('title', 'Area Saya')
@section('page-title', 'Area Saya')

@section('content')
@php
    $roleLabels = [
        'admin' => 'Administrator',
        'team_lead' => 'Team Lead',
        'developer' => 'Developer',
        'designer' => 'Designer',
    ];

    $displayName = $user->full_name ?: $user->username;
    $roleLabel = $roleLabels[$user->role] ?? ucwords(str_replace('_', ' ', $user->role));
    $statusWorking = $user->current_task_status === 'working';
    $statusLabel = $statusWorking ? 'Sedang Bekerja' : 'Idle';
    $statusClass = $statusWorking ? 'status-pill-working' : 'status-pill-idle';
    $initial = strtoupper(mb_substr($displayName, 0, 1));
    $isAdmin = $user->role === 'admin';
    $profilePhotoUrl = $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=0D8ABC&color=fff&size=256';
@endphp

<style>
    :root {
        --clr-bg-dark: rgba(17, 25, 40, 0.85);
        --clr-card-border: rgba(255, 255, 255, 0.1);
        --clr-accent: rgba(139, 92, 246, 0.8);
        --clr-accent-soft: rgba(139, 92, 246, 0.25);
        --clr-text-main: #f8fafc;
        --clr-text-muted: rgba(226, 232, 240, 0.75);
        --clr-light-bg: rgba(255, 255, 255, 0.9);
    }

    .profile-summary-card,
    .security-card {
        background: var(--clr-bg-dark);
        border: 1px solid var(--clr-card-border);
        border-radius: 24px;
        backdrop-filter: blur(14px);
        transition: all 0.35s ease;
        box-shadow: 0 10px 32px rgba(0, 0, 0, 0.35);
        position: relative;
    }

    .profile-summary-card:hover,
    .security-card:hover {
        border-color: rgba(139, 92, 246, 0.35);
        box-shadow: 0 15px 40px rgba(139, 92, 246, 0.25);
        transform: translateY(-2px);
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 32px;
        margin: 0 auto 1.25rem;
        background: linear-gradient(145deg, rgba(99, 102, 241, 0.3), rgba(147, 51, 234, 0.25));
        border: 1px solid rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        box-shadow: inset 0 2px 6px rgba(255, 255, 255, 0.2),
                    0 20px 45px rgba(15, 23, 42, 0.45);
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .fw-bold.text-gradient {
        background: linear-gradient(90deg, #a78bfa, #60a5fa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .divider-glow {
        border: none;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.6), transparent);
        opacity: 0.8;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border-radius: 999px;
        padding: 0.45rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-top: 1rem;
        transition: all 0.3s ease;
    }

    .status-pill-working {
        background: rgba(239, 68, 68, 0.15);
        color: #fecaca;
        border: 1px solid rgba(239, 68, 68, 0.35);
    }

    .status-pill-idle {
        background: rgba(34, 197, 94, 0.15);
        color: #bbf7d0;
        border: 1px solid rgba(34, 197, 94, 0.35);
    }

    .status-pill .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        box-shadow: 0 0 6px currentColor;
        background: currentColor;
    }

    .profile-summary-card .text-muted {
        color: rgba(248, 250, 252, 0.95) !important;
    }

    /* Badge diperjelas */
    .badge.bg-primary-subtle {
  background: rgba(30, 41, 59, 0.75); /* gelap, biru keabu-an */
  border: 1px solid rgba(96, 165, 250, 0.35);
  color: #1e3a8a; /* teks sama seperti di light mode */
  box-shadow: inset 0 0 6px rgba(59, 130, 246, 0.2);
  transition: all 0.3s ease;
}

    /* Tombol logout kanan bawah */
    .security-action {
        position: absolute;
        bottom: 1.5rem;
        right: 1.5rem;
        display: flex;
        justify-content: flex-end;
    }

    /* Tombol keluar: kontras & elegan */
    .btn-logout-sheet {
        border-radius: 999px;
        padding: 0.85rem 1.5rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #dc2626, #ef4444);
        border: 1px solid rgba(239, 68, 68, 0.6);
        color: #fff;
        transition: all 0.3s ease;
        box-shadow: 0 8px 16px rgba(239, 68, 68, 0.25);
    }

    .btn-logout-sheet:hover {
        background: linear-gradient(135deg, #b91c1c, #ef4444);
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(239, 68, 68, 0.35);
    }

    /* Warna security-text di dark mode: tidak terlalu putih */
    .security-card .security-text p,
    .security-card .security-text .text-muted {
        color: rgba(226, 232, 240, 0.78) !important;
    }

    /* text-muted dark mode jangan putih */
    [data-theme="dark"] .text-muted {
        color: rgba(226, 232, 240, 0.7) !important;
    }

    /* Light mode adjustments */
    [data-theme="light"] .profile-summary-card,
    [data-theme="light"] .security-card {
        background: var(--clr-light-bg);
        border-color: rgba(148, 163, 184, 0.25);
        box-shadow: 0 6px 24px rgba(148, 163, 184, 0.25);
    }

    [data-theme="light"] .fw-bold.text-gradient {
        background: linear-gradient(90deg, #6366f1, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    [data-theme="light"] .badge.bg-primary-subtle {
  background: rgba(191, 219, 254, 0.85); /* biru muda lembut */
  border: 1px solid rgba(59, 130, 246, 0.35);
  color: #1e3a8a;
  box-shadow: none;
}


    /* Email di light mode jadi hitam */
    [data-theme="light"] .profile-summary-card a.link-light {
        color: #111827 !important;
    }
    [data-theme="light"] .profile-summary-card a.link-light:hover {
        color: #1e40af !important;
    }

    /* Security text di light mode: hitam */
    [data-theme="light"] .security-card .security-text p,
    [data-theme="light"] .security-card .security-text .text-muted {
        color: #111827 !important;
    }

    [data-theme="light"] .profile-summary-card .text-muted {
        color: #475569 !important;
    }

    /* Tombol keluar di light mode: warna merah elegan tapi jelas */
    [data-theme="light"] .btn-logout-sheet {
        background: linear-gradient(135deg, #ef4444, #f87171);
        border: 1px solid rgba(239, 68, 68, 0.55);
        color: #fff;
        box-shadow: 0 6px 18px rgba(239, 68, 68, 0.25);
    }

    [data-theme="light"] .btn-logout-sheet:hover {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        color: #fff;
        box-shadow: 0 10px 28px rgba(239, 68, 68, 0.35);
    }

    @media (max-width: 991.98px) {
        .profile-avatar {
            width: 96px;
            height: 96px;
            font-size: 2.5rem;
        }

        .security-action {
            position: relative;
            bottom: auto;
            right: auto;
            margin-top: 1.5rem;
            justify-content: center;
        }
    }

    
</style>



<div class="row g-4 align-items-stretch">
    <div class="col-12 {{ $isAdmin ? 'col-xl-5 col-lg-6' : 'col-xl-4 col-lg-5' }}">
        <div class="card profile-summary-card h-100">
            <div class="card-body text-center">
                <div class="profile-avatar mb-3">
                    <img src="{{ $profilePhotoUrl }}" alt="{{ $displayName }}">
                </div>
                <h4 class="fw-bold text-gradient mb-1">{{ $displayName }}</h4>
                <p class="text-muted mb-3">{{ '@' . $user->username }}</p>
                <p class="text-muted mb-3">{{ $roleLabel }}</p>
                

                <div class="status-pill {{ $statusClass }}">
                    <span class="dot"></span>
                    <span>{{ $statusLabel }}</span>
                </div>

                <hr class="divider-glow my-4">

                <p class="text-muted mb-2">Email</p>
                <p class="mb-0 fw-semibold">
                    @if($user->email)
                        <a href="mailto:{{ $user->email }}" class="link-light text-decoration-none">
                            {{ $user->email }}
                        </a>
                    @else
                        <span class="text-muted">Belum diatur</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="col-12 {{ $isAdmin ? 'col-xl-7 col-lg-6' : 'col-xl-8 col-lg-7' }}">
        <div class="card security-card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill text-danger"></i>
                <h5 class="card-title mb-0">Keamanan &amp; Sesi</h5>
            </div>
            <div class="card-body">
                <div class="security-text">
                    <p class="text-muted mb-3">
                        Kelola sesi akun Anda dengan aman. Pastikan untuk keluar setiap kali selesai bekerja,
                        terutama ketika menggunakan perangkat publik atau bersama dengan tim lain.
                    </p>
                    <p class="text-muted mb-0">
                        Tindakan keluar akan mengakhiri sesi saat ini dan memastikan tidak ada pihak lain yang dapat
                        mengakses dashboard atau data proyek Anda tanpa izin.
                    </p>
                </div>

                <div class="security-action">
                    <button
                        type="button"
                        class="btn-logout-sheet"
                        data-action-sheet-trigger
                        data-action-title="Keluar dari Akun"
                        data-action-subtitle="Sesi saat ini akan diakhiri"
                        data-action-message="Apakah Anda yakin ingin keluar sebagai {{ '@' . $user->username }}?"
                        data-action-confirm-label="Keluar Sekarang"
                        data-action-cancel-label="Batal"
                        data-action-loading-label="Mengakhiri sesi..."
                        data-action-form="#logoutForm">
                        <i class="bi bi-box-arrow-right"></i>
                        Keluar
                    </button>
                </div>

                <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent
    @include('components.action-bottom-sheet')
@endsection
