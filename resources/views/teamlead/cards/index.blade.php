<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cards - {{ $board->board_name }}</title>
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
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
      min-height: 100vh;
      color: #e5e7eb;
      overflow-x: hidden;
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
      display: flex;
      flex-direction: column;
    }

    .sidebar-content {
      flex: 1;
      overflow-y: auto;
      padding-bottom: 1rem;
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
      margin: 1rem 0;
    }

    .nav-link-acrylic {
      color: rgba(255, 255, 255, 0.8);
      padding: 10px 12px;
      border-radius: 10px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      align-items: center;
      font-weight: 500;
      position: relative;
      overflow: hidden;
      text-decoration: none;
      font-size: 0.9rem;
      margin-bottom: 4px;
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

.btn-logout-acrylic {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2));
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #fca5a5;
    padding: 0.85rem 1rem;
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

    .nav-profile-entry {
      margin-top: 1.5rem;
      padding-top: 1rem;
      border-top: 1px solid rgba(255, 255, 255, 0.12);
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

    .navbar-title {
      font-size: 1.5rem;
      font-weight: 700;
      background: linear-gradient(135deg, #8b5cf6, #3b82f6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
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
      max-width: 1800px;
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

    /* Badges */
    .badge-modern {
      padding: 6px 12px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.75rem;
      backdrop-filter: blur(10px);
      border: 1px solid;
      white-space: nowrap;
    }

    .badge-danger { background: rgba(239, 68, 68, 0.25); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5; }
    .badge-warning { background: rgba(251, 191, 36, 0.25); border-color: rgba(251, 191, 36, 0.4); color: #fde047; }
    .badge-primary { background: rgba(59, 130, 246, 0.25); border-color: rgba(59, 130, 246, 0.4); color: #93c5fd; }
    .badge-success { background: rgba(16, 185, 129, 0.25); border-color: rgba(16, 185, 129, 0.4); color: #6ee7b7; }
    .badge-info { background: rgba(14, 165, 233, 0.25); border-color: rgba(14, 165, 233, 0.4); color: #7dd3fc; }
    .badge-secondary { background: rgba(107, 114, 128, 0.25); border-color: rgba(107, 114, 128, 0.4); color: #d1d5db; }

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

    .btn-success-modern {
      background: linear-gradient(135deg, rgba(16, 185, 129, 0.25), rgba(5, 150, 105, 0.25));
      border: 1px solid rgba(16, 185, 129, 0.4);
      color: #6ee7b7;
    }

    .btn-success-modern:hover {
      background: linear-gradient(135deg, rgba(16, 185, 129, 0.4), rgba(5, 150, 105, 0.4));
      color: white;
      box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }

    .btn-warning-modern {
      background: linear-gradient(135deg, rgba(251, 191, 36, 0.25), rgba(245, 158, 11, 0.25));
      border: 1px solid rgba(251, 191, 36, 0.4);
      color: #fde047;
    }

    .btn-warning-modern:hover {
      background: linear-gradient(135deg, rgba(251, 191, 36, 0.4), rgba(245, 158, 11, 0.4));
      color: white;
      box-shadow: 0 8px 20px rgba(251, 191, 36, 0.3);
    }

    .btn-danger-modern {
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(220, 38, 38, 0.25));
      border: 1px solid rgba(239, 68, 68, 0.4);
      color: #fca5a5;
    }

    .btn-danger-modern:hover {
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.4), rgba(220, 38, 38, 0.4));
      color: white;
      box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
    }

    .btn-comment {
      background: rgba(59, 130, 246, 0.2);
      border: 1px solid rgba(59, 130, 246, 0.4);
      color: #93c5fd;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 0.875rem;
      transition: all 0.3s ease;
      white-space: nowrap;
    }

    .btn-comment:hover {
      background: rgba(59, 130, 246, 0.3);
      color: white;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    /* Text Gradient */
    .text-gradient {
      background: linear-gradient(135deg, #8b5cf6, #3b82f6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    /* MODAL FIXES - SOLUSI BACKDROP */
    .modal-backdrop {
      background-color: rgba(0, 0, 0, 0.7) !important;
      backdrop-filter: blur(5px);
      z-index: 1090 !important;
    }

    .modal {
      z-index: 1100 !important;
    }

    .modal-content {
      background: rgba(17, 24, 39, 0.98) !important;
      backdrop-filter: blur(30px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 20px;
      color: #e5e7eb;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }

    .modal-header {
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      background: rgba(139, 92, 246, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 20px 20px 0 0;
      padding: 1.5rem;
    }

    .modal-title {
      color: #ffffff !important;
      font-weight: 600;
    }

    .modal-body {
      background: rgba(17, 24, 39, 0.5);
      padding: 1.5rem;
      color: #e5e7eb;
    }

    .modal-body p,
    .modal-body small,
    .modal-body div {
      color: #e5e7eb !important;
    }

    .btn-close {
      filter: invert(1) brightness(2);
      opacity: 0.8;
    }

    .btn-close:hover {
      opacity: 1;
    }

    /* Form Controls */
    .form-control {
      background: rgba(31, 41, 55, 0.7) !important;
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #f3f4f6 !important;
      border-radius: 10px;
      backdrop-filter: blur(10px);
      padding: 0.75rem;
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.4);
    }

    .form-control:focus {
      background: rgba(31, 41, 55, 0.9) !important;
      border-color: rgba(139, 92, 246, 0.5);
      color: white !important;
      box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15);
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

    /* Scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    ::-webkit-scrollbar-track {
      background: rgba(17, 24, 39, 0.5);
    }

    ::-webkit-scrollbar-thumb {
      background: rgba(139, 92, 246, 0.3);
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: rgba(139, 92, 246, 0.5);
    }

    /* Mobile Toggle Button */
    .sidebar-toggle {
      display: none;
      position: fixed;
      top: 15px;
      left: 15px;
      z-index: 1100;
      background: rgba(139, 92, 246, 0.3);
      border: 1px solid rgba(139, 92, 246, 0.5);
      color: white;
      border-radius: 8px;
      padding: 8px 12px;
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    /* Board List Styling */
    .board-list {
      max-height: 200px;
      overflow-y: auto;
      margin-bottom: 1rem;
    }

    .board-list .nav-link-acrylic {
      padding: 8px 12px;
      font-size: 0.85rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .main-content-area {
        margin-left: 0;
      }
      
      .acrylic-sidebar-fixed {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
      }
      
      .acrylic-sidebar-fixed.mobile-open {
        transform: translateX(0);
      }
      
      .sidebar-toggle {
        display: block;
      }
      
      .table-responsive {
        font-size: 0.8rem;
      }
      
      .content-wrapper {
        padding: 1rem;
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

    /* Board section styling */
    .nav-item .text-muted-light {
      font-size: 0.75rem;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      margin-bottom: 0.5rem;
      display: block;
    }

    /* Menu Kembali ke Project styling */
    .nav-link-acrylic .bi-arrow-left-circle {
      color: #93c5fd;
    }

    .nav-link-acrylic:hover .bi-arrow-left-circle {
      color: white;
    }

    .nav-link-acrylic.active .bi-arrow-left-circle {
      color: white;
    }
  </style>
</head>
<body>

<div class="layout-wrapper">
  <!-- Sidebar dengan Menu Kembali ke Project -->
  <div class="acrylic-sidebar-fixed" id="sidebar">
    <div class="sidebar-content">
      <div class="text-center mb-4">
        <div class="sidebar-icon-wrapper mb-3" role="button" tabindex="0" aria-label="Profil saya" data-profile-sheet-trigger>
          <i class="bi bi-person-badge fs-1 text-gradient"></i>
        </div>
        <h4 class="text-gradient mb-0 fw-bold">Team Lead</h4>
        <small class="text-muted-light">Project Management</small>
      </div>
      <hr class="divider-glow">
      
      <ul class="nav flex-column">
        <li class="nav-item mb-2">
          <a href="{{ route('teamlead.dashboard') }}" 
             class="nav-link-acrylic {{ request()->routeIs('teamlead.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
          </a>
        </li>

        <!-- Menu Kembali ke Project -->
        @if(isset($board) && $board->project)
          <li class="nav-item mb-2">
            <a href="{{ route('teamlead.projects.show', $board->project->project_id) }}" 
               class="nav-link-acrylic {{ request()->routeIs('teamlead.projects.show') ? 'active' : '' }}">
              <i class="bi bi-arrow-left-circle me-2"></i> Kembali ke Project
            </a>
          </li>
        @endif

        @if(isset($board) && $board->project && $board->project->boards->count())
          <li class="nav-item mt-3">
            <span class="text-muted-light small fw-bold ps-3">PROJECT BOARDS</span>
          </li>
          <div class="board-list">
            @foreach($board->project->boards as $projectBoard)
              <li class="nav-item">
                <a href="{{ route('teamlead.cards.index', $projectBoard->board_id) }}" 
                   class="nav-link-acrylic ps-4 {{ request()->routeIs('teamlead.cards.index') && request()->route('board_id') == $projectBoard->board_id ? 'active' : '' }}">
                  <i class="bi bi-kanban me-2"></i> {{ $projectBoard->board_name }}
                </a>
              </li>
            @endforeach
          </div>
        @endif

      </ul>
    </div>
  </div>

  <!-- Mobile Toggle Button -->
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list"></i>
  </button>

  <!-- Main Content Area -->
  <div class="main-content-area">
    <!-- Topbar -->
    <nav class="navbar-acrylic">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <span class="navbar-title">
          <i class="bi bi-kanban me-2"></i>Board: {{ $board->board_name }}
        </span>

      </div>
    </nav>

    <!-- Content -->
    <div class="content-wrapper">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="text-gradient mb-1" style="font-size: 2rem; font-weight: 700;">📋 {{ $board->board_name }}</h1>
          <p style="color: rgba(255, 255, 255, 0.6);">Kelola cards dan tugas dalam board ini</p>
        </div>
        <a href="{{ route('teamlead.cards.create', $board->board_id) }}" class="btn-modern btn-success-modern">
          <i class="bi bi-plus-circle me-2"></i>Tambah Card
        </a>
      </div>

      <!-- Notifikasi -->
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

      <!-- Daftar Cards -->
      <div class="glass-card">
        <div class="table-responsive">
          <table class="table table-acrylic mb-0">
            <thead>
              <tr>
                <th>Judul</th>
                <th>Prioritas</th>
                <th>Estimasi</th>
                <th>Status</th>
                <th>Anggota</th>
                <th>Posisi</th>
                <th>Subtasks</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($cards as $card)
                <tr>
                  <!-- Judul + Deskripsi -->
                  <td>
                    <strong>{{ $card->card_title }}</strong><br>
                    <small style="color: rgba(255, 255, 255, 0.6);">{{ $card->description ?: 'Tidak ada deskripsi' }}</small>
                  </td>

                  <!-- Prioritas -->
                  <td>
                    @if($card->priority == 'high')
                      <span class="badge-modern badge-danger">High</span>
                    @elseif($card->priority == 'medium')
                      <span class="badge-modern badge-warning">Medium</span>
                    @else
                      <span class="badge-modern badge-secondary">Low</span>
                    @endif
                  </td>

                  <!-- Estimasi Jam -->
                  <td>{{ $card->estimated_hours ? $card->estimated_hours . 'h' : '-' }}</td>

                  <!-- Status -->
                  <td>
                    @if($card->status == 'todo')
                      <span class="badge-modern badge-secondary">To Do</span>
                    @elseif($card->status == 'in_progress')
                      <span class="badge-modern badge-primary">In Progress</span>
                    @elseif($card->status == 'review')
                      <span class="badge-modern badge-info">Review</span>
                    @elseif($card->status == 'done')
                      <span class="badge-modern badge-success">Done</span>
                    @endif
                  </td>

                  <!-- Anggota -->
                  <td>
                    @if($card->assignments->isNotEmpty())
                      @foreach($card->assignments as $a)
                        <span class="badge-modern badge-info">{{ $a->user->username }}</span>
                      @endforeach
                    @else
                      <span style="color: rgba(255, 255, 255, 0.5);">Belum ada</span>
                    @endif
                  </td>

                  <!-- Posisi -->
                  <td>
                    {{ $card->position }}
                  </td>

                  <!-- Subtasks -->
                  <td>
                    @if($card->subtasks->isNotEmpty())
                      <span class="badge-modern badge-info">{{ $card->subtasks->count() }} Subtasks</span>
                    @else
                      <span style="color: rgba(255, 255, 255, 0.5);">Tidak ada</span>
                    @endif
                  </td>

                  <!-- Aksi -->
                  <td>
                    <div class="d-flex flex-column gap-1">
                      {{-- Edit & Hapus hanya jika card belum Done --}}
                      @if($card->status != 'done')
                        <a href="{{ route('teamlead.cards.edit', [$board->board_id, $card->card_id]) }}" 
                           class="btn-modern btn-warning-modern btn-sm">
                          <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        
                        <form method="POST" action="{{ route('teamlead.cards.destroy', [$board->board_id, $card->card_id]) }}" 
                              class="m-0">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn-modern btn-danger-modern btn-sm w-100"
                                  onclick="return confirm('Yakin mau hapus card ini?')">
                            <i class="bi bi-trash me-1"></i>Hapus
                          </button>
                        </form>
                      @endif

                      <!-- Tombol Approve & Reject langsung untuk status Review -->
                      @if($card->status == 'review')
                        <form method="POST" action="{{ route('teamlead.subtasks.approve', $card->card_id) }}" class="m-0">
                          @csrf
                          <button type="submit" class="btn-modern btn-success-modern btn-sm w-100" 
                                  onclick="return confirm('Approve card ini?')">
                            <i class="bi bi-check-circle me-1"></i>Approve
                          </button>
                        </form>
                        <form method="POST" action="{{ route('teamlead.subtasks.reject', $card->card_id) }}" class="m-0">
                          @csrf
                          <button type="submit" class="btn-modern btn-danger-modern btn-sm w-100"
                                  onclick="return confirm('Ulangi card ini?')">
                            <i class="bi bi-x-circle me-1"></i>Ulangi
                          </button>
                        </form>
                      @endif

                      <!-- Tombol Komentar -->
                      <button class="btn-comment"
                              data-comment-sheet-trigger="true"
                              data-comment-type="card"
                              data-comment-id="{{ $card->card_id }}"
                              data-comment-title="{{ $card->card_title }}"
                              data-comment-subtitle="Board: {{ $board->board_name }}">
                        Komentar
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center" style="color: rgba(255, 255, 255, 0.5);">
                    <div class="py-4">
                      <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                      <p class="mt-2 mb-0">Belum ada card di board ini</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


@include('partials.profile-quick-sheet')
@include('components.comment-bottom-sheet')

</body>
</html>
