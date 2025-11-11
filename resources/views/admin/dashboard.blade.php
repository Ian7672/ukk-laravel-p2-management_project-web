@extends('layouts.app')

@section('title', 'Dashboard - Manajemen Proyek')
@section('page-title', 'Dashboard')

@section('page-toolbar')
<div class="dashboard-toolbar">
    <span class="badge-modern badge-primary">
        {{ $projects->count() }} Proyek
    </span>
    @if($projects->count() > 0)
        <div class="search-input-wrapper mb-0">
            <i class="bi bi-search"></i>
            <input type="search"
                   id="projectSearchInput"
                   placeholder="Cari proyek, deskripsi, atau status...">
        </div>
    @endif
    <button type="button" class="btn-modern" onclick="showCreateProject()">
        <i class="bi bi-plus-circle me-2"></i>Buat Proyek Baru
    </button>
</div>
@endsection

@section('content')
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

      :root {
        --dashboard-topbar-height: 72px;
      }

      /* Main Layout with Fixed Sidebar */
      .layout-wrapper {
        display: flex;
        min-height: 100vh;
        position: relative;
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

      /* Text Muted Light dengan background berdasarkan status */
.text-muted-light {
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 4px;
    padding: 4px 8px;
    border-radius: 6px;
    backdrop-filter: blur(10px);
    border: 1px solid;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.3s ease;
    white-space: nowrap;
}

/* Status Idle - Background hijau transparan */
.status-idle {
    background: rgba(16, 185, 129, 0.15) !important;
    border-color: rgba(16, 185, 129, 0.25) !important;
    color: #6ee7b7 !important;
}

/* Status Working - Background merah transparan */
.status-working {
    background: rgba(239, 68, 68, 0.15) !important;
    border-color: rgba(239, 68, 68, 0.25) !important;
    color: #fca5a5 !important;
}

/* Hover effects */
.status-idle:hover {
    background: rgba(16, 185, 129, 0.25) !important;
    border-color: rgba(16, 185, 129, 0.35) !important;
    color: #a7f3d0 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
}

.status-working:hover {
    background: rgba(239, 68, 68, 0.25) !important;
    border-color: rgba(239, 68, 68, 0.35) !important;
    color: #fecaca !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
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
        position: absolute;
        bottom: 1.5rem;
        left: 1.5rem;
        right: 1.5rem;
        display: flex;
        justify-content: center;
      }

      .sidebar-footer form {
        width: 100%;
        display: flex;
        justify-content: center;
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

      /* Main Content */
      .main-content {
        flex-grow: 1;
        margin-left: 250px;
        padding: 20px;
        min-height: 100vh;
        transition: margin-left 0.3s ease;
      }

      .mobile-nav-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
      }

      .dashboard-toolbar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        justify-content: flex-end;
        width: 100%;
        flex-wrap: nowrap;
      }

      .dashboard-toolbar > * {
        flex-shrink: 0;
      }

      @media (max-width: 575.98px) {
        .dashboard-toolbar {
          justify-content: flex-start;
          overflow-x: auto;
          padding-bottom: 0.25rem;
        }
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

      /* Responsif */
      @media (max-width: 992px) {
        .acrylic-sidebar-fixed {
          position: relative;
          width: 100%;
          height: auto;
          box-shadow: none;
        }
        .main-content {
          margin-left: 0;
          padding: 20px;
        }
      }

      /* Glass Card */
      .dashboard-card {
        background: rgba(31, 41, 55, 0.6);
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 1.5rem;
        color: white;
        box-shadow: 
          0 8px 32px rgba(0, 0, 0, 0.3),
          inset 0 1px 0 rgba(255, 255, 255, 0.1);
      }


      /* Button Styling */
      .btn-modern {
        background: rgba(139, 92, 246, 0.25);
        border: 1px solid rgba(139, 92, 246, 0.4);
        color: #c4b5fd;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        text-decoration: none;
        display: inline-block;
        border: none;
        cursor: pointer;
      }

      .btn-modern:hover {
        background: rgba(139, 92, 246, 0.4);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
      }

      .btn-modern.btn-sm {
        padding: 6px 12px;
        font-size: 0.875rem;
      }

      /* Selected Items */
      .selected-items {
        background: rgba(31, 41, 55, 0.5);
        border-radius: 8px;
        padding: 10px;
        margin-top: 8px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
      }

      .selected-item {
        display: inline-flex;
        align-items: center;
        background: rgba(59, 130, 246, 0.3);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        margin: 3px;
        font-size: 0.85rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(59, 130, 246, 0.4);
      }

      .remove-selection {
        background: none;
        border: none;
        color: white;
        margin-left: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: color 0.3s ease;
      }

      .remove-selection:hover {
        color: #fca5a5;
      }

      /* Form Styling */
      .form-control, .form-select {
        background: rgba(31, 41, 55, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #f3f4f6;
        border-radius: 8px;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
      }

      .form-control:focus, .form-select:focus {
        background: rgba(31, 41, 55, 0.95);
        border-color: rgba(139, 92, 246, 0.6);
        color: white;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.25);
      }

      .form-control::placeholder {
        color: rgba(255, 255, 255, 0.4);
      }

      .form-select.form-select-sm {
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        font-size: 0.875rem;
      }

      /* Comment Styles */
      .comment {
        background: rgba(31, 41, 55, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        backdrop-filter: blur(10px);
      }

      .comment strong {
        color: #c4b5fd;
      }

      .comment small {
        color: rgba(255, 255, 255, 0.5);
      }

      .comment p {
        color: #e5e7eb;
        margin: 0.5rem 0;
      }

      /* Badge Styling */
      .badge {
        backdrop-filter: blur(10px);
        border: 1px solid;
      }

      .badge.bg-danger {
        background: rgba(239, 68, 68, 0.25) !important;
        border-color: rgba(239, 68, 68, 0.4);
        color: #fca5a5;
      }

      .badge.bg-success {
        background: rgba(16, 185, 129, 0.25) !important;
        border-color: rgba(16, 185, 129, 0.4);
        color: #6ee7b7;
      }

      /* Hidden state */
      .hidden {
        display: none !important;
      }

      /* Scrollbar */
      ::-webkit-scrollbar {
        width: 8px;
      }

      ::-webkit-scrollbar-track {
        background: rgba(17, 24, 39, 0.3);
        border-radius: 4px;
      }

      ::-webkit-scrollbar-thumb {
        background: rgba(139, 92, 246, 0.4);
        border-radius: 4px;
      }

      ::-webkit-scrollbar-thumb:hover {
        background: rgba(139, 92, 246, 0.6);
      }

      /* Project Card Styles */
      .project-card-acrylic {
        background: rgba(31, 41, 55, 0.6);
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 1.5rem;
        color: #e5e7eb;
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .project-card-acrylic:hover {
      transform: translateY(-5px);
      box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.4),
        0 0 25px rgba(139, 92, 246, 0.1);
      border-color: rgba(139, 92, 246, 0.3);
    }

    .project-toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
    }

      .search-input-wrapper {
        position: relative;
        width: min(360px, 100%);
        flex: 1 1 240px;
        margin-bottom: 0;
      }

      .dashboard-toolbar .btn-modern {
        flex: 0 0 auto;
        white-space: nowrap;
      }

    .search-input-wrapper input {
      width: 100%;
      background: rgba(17, 24, 39, 0.65);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 12px;
      padding: 0.65rem 0.75rem 0.65rem 2.5rem;
      color: #e5e7eb;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }

    .search-input-wrapper input::placeholder {
      color: rgba(255, 255, 255, 0.4);
    }

    .search-input-wrapper input:focus {
      outline: none;
      border-color: rgba(139, 92, 246, 0.5);
      box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.12);
      background: rgba(17, 24, 39, 0.85);
      color: #fff;
    }

    .search-input-wrapper .bi {
      position: absolute;
      top: 50%;
      left: 0.9rem;
      transform: translateY(-50%);
      color: rgba(148, 163, 184, 0.8);
      font-size: 1rem;
      pointer-events: none;
    }

    .hidden-by-search {
      display: none !important;
    }

    .search-empty {
      background: rgba(17, 24, 39, 0.45);
      border: 1px dashed rgba(139, 92, 246, 0.4);
      border-radius: 14px;
      padding: 1.5rem;
      color: rgba(255, 255, 255, 0.65);
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .search-empty i {
      font-size: 1.75rem;
      margin-bottom: 0.5rem;
      color: rgba(139, 92, 246, 0.7);
    }

    .project-card-header {
      margin-bottom: 1rem;
    }

      .project-title {
        color: #ffffff;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        line-height: 1.4;
      }

      .project-description {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 0;
      }

      .project-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
        border: 1px solid;
        white-space: nowrap;
      }

      .badge-primary { background: rgba(59, 130, 246, 0.25); border-color: rgba(59, 130, 246, 0.4); color: #93c5fd; }
      .badge-success { background: rgba(16, 185, 129, 0.25); border-color: rgba(16, 185, 129, 0.4); color: #6ee7b7; }
      .badge-warning { background: rgba(251, 191, 36, 0.25); border-color: rgba(251, 191, 36, 0.4); color: #fde047; }
      .badge-danger { background: rgba(239, 68, 68, 0.25); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5; }
      .badge-info { background: rgba(14, 165, 233, 0.25); border-color: rgba(14, 165, 233, 0.4); color: #7dd3fc; }
      .badge-purple { background: rgba(139, 92, 246, 0.25); border-color: rgba(139, 92, 246, 0.4); color: #c4b5fd; }

      .project-meta {
        margin-bottom: 1.5rem;
      }

      .meta-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
      }

      .meta-item i {
        color: rgba(139, 92, 246, 0.8);
        margin-right: 8px;
        width: 16px;
      }

      .meta-label {
        color: rgba(255, 255, 255, 0.6);
        margin-right: 6px;
      }

      .meta-value {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
      }

      .project-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 1rem;
      }

      .stat-item {
        text-align: center;
        flex: 1;
      }

      .stat-value {
        color: #ffffff;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 4px;
      }

      .stat-label {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }

      .project-progress {
        margin-bottom: 1.5rem;
      }

      .progress-bar-container {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        height: 8px;
        overflow: hidden;
        margin-bottom: 8px;
      }

      .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #8b5cf6, #3b82f6);
        border-radius: 10px;
        transition: width 0.5s ease;
        position: relative;
      }

      .progress-bar-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, 
          transparent 0%, 
          rgba(255, 255, 255, 0.3) 50%, 
          transparent 100%);
        animation: shimmer 2s infinite;
      }

      .progress-text {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.8rem;
        text-align: center;
        font-weight: 500;
      }

      .project-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: auto;
      }

      .btn-project-action {
        flex: 1;
        background: rgba(139, 92, 246, 0.15);
        border: 1px solid rgba(139, 92, 246, 0.3);
        color: #c4b5fd;
        padding: 10px 12px;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.85rem;
        cursor: pointer;
      }

      .btn-project-action:hover {
        background: rgba(139, 92, 246, 0.25);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
      }

      .btn-detail {
        background: rgba(59, 130, 246, 0.15);
        border-color: rgba(59, 130, 246, 0.3);
        color: #93c5fd;
      }

      .btn-comment {
        background: rgba(16, 185, 129, 0.15);
        border-color: rgba(16, 185, 129, 0.3);
        color: #6ee7b7;
      }

      .empty-state {
        padding: 3rem 2rem;
      }

      @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
      }

      .badge-modern {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
        backdrop-filter: blur(10px);
        border: 1px solid;
        white-space: nowrap;
        display: inline-block;
      }
      
      /* Responsive Design */
@media (max-width: 1200px) {
  .project-card-acrylic {
    padding: 1.25rem;
  }
  
  .project-stats {
    padding: 0.75rem;
  }
  
  .stat-value {
    font-size: 1rem;
  }
}

@media (max-width: 991.98px) {
  .layout-wrapper {
    flex-direction: row !important;
    min-height: auto;
  }

  .layout-wrapper > .acrylic-sidebar-fixed {
    position: fixed;
  }

  .main-content-area {
    margin-left: 0;
  }

  .acrylic-sidebar-fixed {
    top: var(--dashboard-topbar-height, 72px);
    height: calc(100vh - var(--dashboard-topbar-height, 72px));
  }

  .sidebar-backdrop {
    top: var(--dashboard-topbar-height, 72px);
  }

  .project-item-6,
  .project-item-3 {
    margin-bottom: 1rem;
  }

  .project-actions {
    flex-direction: column;
    gap: 0.5rem;
  }

  .btn-project-action {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .main-content {
    padding: 10px;
  }

  .dashboard-toolbar .badge-modern {
    display: none !important;
  }
  
  .d-flex.justify-content-between.align-items-center.mb-4 {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .mobile-nav-wrapper {
    width: 100%;
    justify-content: space-between;
  }
  
  .mobile-nav-wrapper h1 {
    font-size: 1.6rem;
  }
  
  .dashboard-card {
    padding: 1rem;
    border-radius: 12px;
  }
  
  .project-card-acrylic {
    padding: 1rem;
    border-radius: 12px;
  }
  
  .project-stats {
    flex-direction: column;
    gap: 0.75rem;
    padding: 0.75rem;
  }
  
  .stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  /* Form responsive */
  .row {
    margin-left: -8px;
    margin-right: -8px;
  }
  
  .row > [class*="col-"] {
    padding-left: 8px;
    padding-right: 8px;
  }
  
  .add-member-form .col-md-6,
  .add-member-form .col-md-4,
  .add-member-form .col-md-2 {
    margin-bottom: 10px;
  }
  
  #createProjectView .row > .col-md-6 {
    margin-bottom: 1rem;
  }
}

@media (max-width: 576px) {
  body {
    font-size: 14px;
  }
  
  h1.text-gradient {
    font-size: 1.75rem;
  }
  
  h3.text-gradient {
    font-size: 1.5rem;
  }
  
  .project-title {
    font-size: 1rem;
  }
  
  .project-description {
    font-size: 0.85rem;
  }
  
  .meta-item {
    font-size: 0.8rem;
  }
  
  .btn-modern, .btn-project-action {
    font-size: 0.8rem;
    padding: 8px 12px;
  }
  
  .project-meta {
    margin-bottom: 1rem;
  }
  
  .project-progress {
    margin-bottom: 1rem;
  }
  
  .empty-state {
    padding: 2rem 1rem;
  }
  
  .empty-state .bi-folder-x {
    font-size: 2.5rem !important;
  }
  
  .comment {
    padding: 0.75rem;
  }
  
  .selected-item {
    font-size: 0.75rem;
    padding: 3px 8px;
  }
}

/* Ensure proper spacing on all devices */
.project-card-acrylic {
  margin-bottom: 1rem;
}

/* Fix for form elements on mobile */
.form-control, .form-select {
  min-height: 42px;
}

/* Improve touch targets for mobile */
.btn-modern, .btn-project-action, .nav-link-acrylic {
  min-height: 44px;
  display: flex;
  align-items: center;
}

/* Ensure images and media are responsive */
img, video, iframe {
  max-width: 100%;
  height: auto;
}

/* Improve readability on small screens */
@media (max-width: 768px) {
  body {
    line-height: 1.5;
  }
  
  p, li, .project-description, .meta-value {
    line-height: 1.5;
  }
}

/* Fix for very small screens */
@media (max-width: 360px) {
  .main-content {
    padding: 8px;
  }
  
  .dashboard-card, .project-card-acrylic {
    padding: 0.75rem;
  }
  
  .btn-modern, .btn-project-action {
    padding: 6px 10px;
    font-size: 0.75rem;
  }
  
}

/* Light Mode Styles */
[data-theme="light"] {
  background: linear-gradient(135deg, #f0f4ff 0%, #e6f0ff 50%, #f8faff 100%);
  color: #374151;
}

[data-theme="light"] .dashboard-card {
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.9);
  color: #374151;
  box-shadow: 
    0 8px 32px rgba(0, 0, 0, 0.1),
    inset 0 1px 0 rgba(255, 255, 255, 0.9);
}

[data-theme="light"] .project-card-acrylic {
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid rgba(255, 255, 255, 0.9);
  color: #374151;
  box-shadow: 
    0 8px 32px rgba(0, 0, 0, 0.08),
    inset 0 1px 0 rgba(255, 255, 255, 0.9);
}

[data-theme="light"] .project-card-acrylic:hover {
  border-color: rgba(139, 92, 246, 0.4);
  box-shadow: 
    0 12px 40px rgba(0, 0, 0, 0.12),
    0 0 25px rgba(139, 92, 246, 0.1);
}

[data-theme="light"] .project-title {
  color: #1f2937;
}

[data-theme="light"] .project-description {
  color: #6b7280;
}

[data-theme="light"] .meta-label {
  color: #6b7280;
}

[data-theme="light"] .meta-value {
  color: #374151;
}

[data-theme="light"] .progress-bar-container {
  background: rgba(0, 0, 0, 0.1);
}

[data-theme="light"] .progress-text {
  color: #6b7280;
}

[data-theme="light"] .search-input-wrapper input {
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid rgba(209, 213, 219, 0.8);
  color: #374151;
}

[data-theme="light"] .search-input-wrapper input::placeholder {
  color: #9ca3af;
}

[data-theme="light"] .search-input-wrapper input:focus {
  background: rgba(255, 255, 255, 0.95);
  border-color: rgba(139, 92, 246, 0.5);
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
  color: #1f2937;
}

[data-theme="light"] .search-input-wrapper .bi {
  color: #6b7280;
}

[data-theme="light"] .search-empty {
  background: rgba(255, 255, 255, 0.7);
  border: 1px dashed rgba(139, 92, 246, 0.3);
  color: #6b7280;
}

[data-theme="light"] .empty-state p {
  color: #6b7280;
}

[data-theme="light"] .form-control,
[data-theme="light"] .form-select {
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid rgba(209, 213, 219, 0.8);
  color: #374151;
}

[data-theme="light"] .form-control:focus,
[data-theme="light"] .form-select:focus {
  background: rgba(255, 255, 255, 0.95);
  border-color: rgba(139, 92, 246, 0.6);
  color: #1f2937;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
}

[data-theme="light"] .form-control::placeholder {
  color: #9ca3af;
}

[data-theme="light"] .selected-items {
  background: rgba(255, 255, 255, 0.7);
  border: 1px solid rgba(209, 213, 219, 0.6);
}

[data-theme="light"] .selected-item {
  background: rgba(59, 130, 246, 0.15);
  color: #1e40af;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

[data-theme="light"] .comment {
  background: rgba(255, 255, 255, 0.7);
  border: 1px solid rgba(209, 213, 219, 0.6);
}

[data-theme="light"] .comment strong {
  color: #7c3aed;
}

[data-theme="light"] .comment small {
  color: #6b7280;
}

[data-theme="light"] .comment p {
  color: #374151;
}

/* Badge colors untuk light mode */
[data-theme="light"] .badge-primary { 
  background: rgba(59, 130, 246, 0.15); 
  border-color: rgba(59, 130, 246, 0.3); 
  color: #1e40af; 
}

[data-theme="light"] .badge-success { 
  background: rgba(16, 185, 129, 0.15); 
  border-color: rgba(16, 185, 129, 0.3); 
  color: #065f46; 
}

[data-theme="light"] .badge-warning { 
  background: rgba(251, 191, 36, 0.15); 
  border-color: rgba(251, 191, 36, 0.3); 
  color: #92400e; 
}

[data-theme="light"] .badge-danger { 
  background: rgba(239, 68, 68, 0.15); 
  border-color: rgba(239, 68, 68, 0.3); 
  color: #991b1b; 
}

[data-theme="light"] .badge-info { 
  background: rgba(14, 165, 233, 0.15); 
  border-color: rgba(14, 165, 233, 0.3); 
  color: #0c4a6e; 
}

[data-theme="light"] .badge-purple { 
  background: rgba(139, 92, 246, 0.15); 
  border-color: rgba(139, 92, 246, 0.3); 
  color: #5b21b6; 
}

[data-theme="light"] .project-stats {
  background: rgba(0, 0, 0, 0.05);
}

[data-theme="light"] .stat-value {
  color: #1f2937;
}

[data-theme="light"] .stat-label {
  color: #6b7280;
}


[data-theme="light"] ::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
}

[data-theme="light"] ::-webkit-scrollbar-thumb {
  background: rgba(139, 92, 246, 0.3);
}

[data-theme="light"] ::-webkit-scrollbar-thumb:hover {
  background: rgba(139, 92, 246, 0.5);
}

[data-theme="light"] .acrylic-sidebar-fixed::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
}

[data-theme="light"] .acrylic-sidebar-fixed::-webkit-scrollbar-thumb {
  background: rgba(139, 92, 246, 0.3);
}

[data-theme="light"] .acrylic-sidebar-fixed::-webkit-scrollbar-thumb:hover {
  background: rgba(139, 92, 246, 0.5);
}
    </style>

<div class="dashboard-page">
  <!-- Main Dashboard View -->
  <div id="mainDashboardView">
    
      
      
      @if($projects->count() > 0)
        <div class="search-empty hidden" id="projectSearchEmpty">
          <i class="bi bi-search"></i>
          <p class="mb-0">Proyek tidak ditemukan untuk kata kunci tersebut.</p>
        </div>

        <!-- Grid View 6 projects per row -->
        <div id="gridView6">
          <div class="row g-4" id="projectsGrid6">
            @foreach($projects as $project)
              @php
                // Hitung progress berdasarkan subtask (prioritas) atau card
                $cards = $project->boards->flatMap->cards;
                $subtasks = $cards->flatMap->subtasks;
                
                $cards_total = $cards->count();
                $cards_done = $cards->where('status', 'done')->count();
                $subtasks_total = $subtasks->count();
                $subtasks_done = $subtasks->where('status', 'done')->count();
                
                // Logika progress sama seperti di monitoring
                if ($subtasks_total > 0) {
                    $progress = round(($subtasks_done / $subtasks_total) * 100);
                } elseif ($cards_total > 0) {
                    $progress = round(($cards_done / $cards_total) * 100);
                } else {
                    $progress = 0;
                }
                
                // Status dan warna berdasarkan progress
                if ($progress < 30) {
                    $status_color = 'danger';
                    $status = 'Low Progress';
                } elseif ($progress < 70) {
                    $status_color = 'warning';
                    $status = 'In Progress';
                } elseif ($progress < 100) {
                    $status_color = 'info';
                    $status = 'Almost Done';
                } else {
                    $status_color = 'success';
                    $status = 'Completed';
                }
              @endphp
              
              @php
                $searchKeywords = Str::lower(
                  trim(($project->project_name ?? '') . ' ' . ($project->description ?? '') . ' ' . $status)
                );
              @endphp

              <div class="col-xl-4 col-lg-6 col-md-6 project-item-6"
                   data-project-item
                   data-project-keywords="{{ $searchKeywords }}">
                <div class="project-card-acrylic">
                  <div class="project-card-header">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <h5 class="project-title">{{ $project->project_name }}</h5>
                      <span class="project-badge badge-{{ $status_color }}">
                        {{ $status }}
                      </span>
                    </div>
                    
                    @if($project->description)
                      <p class="project-description">{{ Str::limit($project->description, 120) }}</p>
                    @endif
                  </div>

                  <div class="project-meta">
                    <div class="meta-item">
                      <i class="bi bi-calendar-event"></i>
                      <span class="meta-label">Deadline:</span>
                      <span class="meta-value">{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : 'Tidak ditentukan' }}</span>
                    </div>
                    <div class="meta-item">
                      <i class="bi bi-clock"></i>
                      <span class="meta-label">Dibuat:</span>
                      <span class="meta-value">{{ $project->created_at ? $project->created_at->format('d M Y') : '-' }}</span>
                    </div>
                  </div>

                
                  <div class="project-progress">
                    <div class="progress-bar-container">
                      <div class="progress-bar-fill" style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="progress-text">{{ $progress }}% Complete</div>
                  </div>

                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Grid View 3 projects per row -->
        <div id="gridView3" class="hidden">
          <div class="row g-4" id="projectsGrid3">
            @foreach($projects as $project)
              @php
                // Hitung progress berdasarkan subtask (prioritas) atau card
                $cards = $project->boards->flatMap->cards;
                $subtasks = $cards->flatMap->subtasks;
                
                $cards_total = $cards->count();
                $cards_done = $cards->where('status', 'done')->count();
                $subtasks_total = $subtasks->count();
                $subtasks_done = $subtasks->where('status', 'done')->count();
                
                // Logika progress sama seperti di monitoring
                if ($subtasks_total > 0) {
                    $progress = round(($subtasks_done / $subtasks_total) * 100);
                } elseif ($cards_total > 0) {
                    $progress = round(($cards_done / $cards_total) * 100);
                } else {
                    $progress = 0;
                }
                
                // Status dan warna berdasarkan progress
                if ($progress < 30) {
                    $status_color = 'danger';
                    $status = 'Low Progress';
                } elseif ($progress < 70) {
                    $status_color = 'warning';
                    $status = 'In Progress';
                } elseif ($progress < 100) {
                    $status_color = 'info';
                    $status = 'Almost Done';
                } else {
                    $status_color = 'success';
                    $status = 'Completed';
                }
              @endphp
              
              @php
                $searchKeywords = Str::lower(
                  trim(($project->project_name ?? '') . ' ' . ($project->description ?? '') . ' ' . $status)
                );
              @endphp

              <div class="col-md-6 col-lg-4 project-item-3"
                   data-project-item
                   data-project-keywords="{{ $searchKeywords }}">
                <div class="project-card-acrylic">
                  <div class="project-card-header">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <h5 class="project-title">{{ $project->project_name }}</h5>
                      <span class="project-badge badge-{{ $status_color }}">
                        {{ $status }}
                      </span>
                    </div>
                    
                    @if($project->description)
                      <p class="project-description">{{ Str::limit($project->description, 120) }}</p>
                    @endif
                  </div>

                  <div class="project-meta">
                    <div class="meta-item">
                      <i class="bi bi-calendar-event"></i>
                      <span class="meta-label">Deadline:</span>
                      <span class="meta-value">{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : 'Tidak ditentukan' }}</span>
                    </div>
                    <div class="meta-item">
                      <i class="bi bi-clock"></i>
                      <span class="meta-label">Dibuat:</span>
                      <span class="meta-value">{{ $project->created_at ? $project->created_at->format('d M Y') : '-' }}</span>
                    </div>
                  </div>

              
                  <div class="project-progress">
                    <div class="progress-bar-container">
                      <div class="progress-bar-fill" style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="progress-text">{{ $progress }}% Complete</div>
                  </div>

                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- List View (All projects) -->
        <div id="listView" class="hidden">
          <div class="row g-4">
            @foreach($projects as $project)
              @php
                // Hitung progress berdasarkan subtask (prioritas) atau card
                $cards = $project->boards->flatMap->cards;
                $subtasks = $cards->flatMap->subtasks;
                
                $cards_total = $cards->count();
                $cards_done = $cards->where('status', 'done')->count();
                $subtasks_total = $subtasks->count();
                $subtasks_done = $subtasks->where('status', 'done')->count();
                
                // Logika progress sama seperti di monitoring
                if ($subtasks_total > 0) {
                    $progress = round(($subtasks_done / $subtasks_total) * 100);
                } elseif ($cards_total > 0) {
                    $progress = round(($cards_done / $cards_total) * 100);
                } else {
                    $progress = 0;
                }
                
                // Status dan warna berdasarkan progress
                if ($progress < 30) {
                    $status_color = 'danger';
                    $status = 'Low Progress';
                } elseif ($progress < 70) {
                    $status_color = 'warning';
                    $status = 'In Progress';
                } elseif ($progress < 100) {
                    $status_color = 'info';
                    $status = 'Almost Done';
                } else {
                    $status_color = 'success';
                    $status = 'Completed';
                }
              @endphp
              
              @php
                $searchKeywords = Str::lower(
                  trim(($project->project_name ?? '') . ' ' . ($project->description ?? '') . ' ' . $status)
                );
              @endphp

              <div class="col-12 project-item-list"
                   data-project-item
                   data-project-keywords="{{ $searchKeywords }}">
                <div class="project-card-acrylic">
                  <div class="project-card-header">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <h5 class="project-title">{{ $project->project_name }}</h5>
                      <span class="project-badge badge-{{ $status_color }}">
                        {{ $status }}
                      </span>
                    </div>
                    
                    @if($project->description)
                      <p class="project-description">{{ Str::limit($project->description, 120) }}</p>
                    @endif
                  </div>

                  <div class="project-meta">
                    <div class="meta-item">
                      <i class="bi bi-calendar-event"></i>
                      <span class="meta-label">Deadline:</span>
                      <span class="meta-value">{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : 'Tidak ditentukan' }}</span>
                    </div>
                    <div class="meta-item">
                      <i class="bi bi-clock"></i>
                      <span class="meta-label">Dibuat:</span>
                      <span class="meta-value">{{ $project->created_at ? $project->created_at->format('d M Y') : '-' }}</span>
                    </div>
                  </div>

              
                  <div class="project-progress">
                    <div class="progress-bar-container">
                      <div class="progress-bar-fill" style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="progress-text">{{ $progress }}% Complete</div>
                  </div>

                </div>
              </div>
            @endforeach
          </div>
        </div>
      @else
        <div class="empty-state text-center py-5">
          <i class="bi bi-folder-x fs-1" style="color: rgba(139, 92, 246, 0.3);"></i>
          <h5 class="mt-3" style="color: #c4b5fd;">Belum Ada Proyek</h5>
          <p style="color: rgba(255, 255, 255, 0.5);">Mulai dengan membuat proyek pertama Anda</p>
          <button class="btn-modern mt-3" onclick="showCreateProject()">
            <i class="bi bi-plus-circle me-2"></i>Buat Proyek Baru
          </button>
        </div>
      @endif
    
  </div>

      <!-- PROJECT DETAIL VIEW -->
      <div id="projectDetailView" class="hidden">
        <div class="dashboard-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-gradient mb-0" id="detailProjectName"></h3>
            <button class="btn-modern btn-sm" onclick="showMainView()">
              <i class="bi bi-arrow-left me-2"></i>Kembali
            </button>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-4">
                <h5 class="text-info mb-3">📋 Informasi Proyek</h5>
                <div class="dashboard-card p-3">
                  <p><strong>Deskripsi:</strong> <span id="detailProjectDescription"></span></p>
                  <p><strong>📅 Deadline:</strong> <span id="detailProjectDeadline"></span></p>
                  <p><strong>🕒 Dibuat:</strong> <span id="detailProjectCreated"></span></p>
                </div>
              </div>

              <!-- Boards -->
              <div class="mb-4">
                <h5 class="text-info mb-3">📊 Boards</h5>
                <div class="dashboard-card p-3" id="detailBoardsList">
                  <!-- Boards akan diisi oleh JavaScript -->
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <!-- Anggota Tim -->
              <div class="mb-4">
                <h5 class="text-info mb-3">👥 Anggota Tim</h5>
                <div class="dashboard-card p-3" id="detailMembersList">
                  <!-- Members akan diisi oleh JavaScript -->
                </div>
              </div>

              <!-- Tambah Anggota -->
              <div class="mb-4">
                <h5 class="text-info mb-3">➕ Tambah Anggota</h5>
                <div class="dashboard-card p-3">
                  <form class="row g-2 align-items-end add-member-form" method="POST"
                        action="{{ route('admin.projects.members.add', $project->project_id ?? '') }}">
                    @csrf
                    <div class="col-md-6">
                      <select name="user_id" class="form-select form-select-sm user-select" required>
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $user)
                          @if($user->role !== 'admin')
                            <option value="{{ $user->user_id }}" data-role="{{ $user->role }}">
                              {{ $user->username }} ({{ ucfirst($user->role) }})
                            </option>
                          @endif
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-4">
                      <select name="role" class="form-select form-select-sm role-select" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="team_lead">Team Lead</option>
                        <option value="developer">Developer</option>
                        <option value="designer">Designer</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <button type="submit" class="btn-modern btn-sm w-100">Tambah</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CREATE PROJECT VIEW -->
      <div id="createProjectView" class="hidden">
        <div class="dashboard-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-gradient mb-0">🚀 Buat Proyek Baru</h3>
            <button class="btn-modern btn-sm" onclick="showMainView()">
              <i class="bi bi-arrow-left me-2"></i>Kembali
            </button>
          </div>

          <form method="POST" action="{{ route('admin.projects.store') }}" id="createProjectForm">
            @csrf

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="project_name" class="form-label">Nama Proyek</label>
                  <input type="text" class="form-control" id="project_name" name="project_name" placeholder="Masukkan nama proyek" required>
                </div>

                <div class="mb-3">
                  <label for="description" class="form-label">Deskripsi</label>
                  <textarea class="form-control" id="description" name="description" rows="3" placeholder="Tulis deskripsi proyek..." required></textarea>
                </div>

                <div class="mb-3">
                  <label for="deadline" class="form-label">Deadline</label>
                  <input type="date" class="form-control" id="deadline" name="deadline" required>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="team_lead_id" class="form-label fw-semibold">Pilih Team Lead</label>
                  <input type="text" class="form-control mb-2" id="teamLeadSearch" placeholder="Cari Team Lead...">
                  <select name="team_lead_id" id="team_lead_id" class="form-select" required>
                    <option value="">-- Pilih Team Lead (Idle) --</option>
                    @foreach($users as $user)
                      @if($user->role === 'team_lead' && $user->current_task_status === 'idle')
                        <option value="{{ $user->user_id }}">{{ $user->username }}</option>
                      @endif
                    @endforeach
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold">💻 Developer (opsional)</label>
                  <input type="text" class="form-control mb-2" id="developerSearch" placeholder="Cari Developer...">
                  <select name="developers[]" id="developers" class="form-select" multiple>
                    @foreach($users as $user)
                      @if($user->role === 'developer' && $user->current_task_status === 'idle')
                        <option value="{{ $user->user_id }}">{{ $user->username }}</option>
                      @endif
                    @endforeach
                  </select>
                  
                  <div id="selected-developers" class="selected-items mt-2 d-none">
                    <small class="text-muted d-block mb-2">Developer terpilih:</small>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold">🎨 Designer (opsional)</label>
                  <input type="text" class="form-control mb-2" id="designerSearch" placeholder="Cari Designer...">
                  <select name="designers[]" id="designers" class="form-select" multiple>
                    @foreach($users as $user)
                      @if($user->role === 'designer' && $user->current_task_status === 'idle')
                        <option value="{{ $user->user_id }}">{{ $user->username }}</option>
                      @endif
                    @endforeach
                  </select>
                  
                  <div id="selected-designers" class="selected-items mt-2 d-none">
                    <small class="text-muted d-block mb-2">Designer terpilih:</small>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-grid mt-4">
              <button type="submit" class="btn-modern">
                <i class="bi bi-save me-2"></i>Simpan Proyek
              </button>
            </div>
          </form>
        </div>
      </div>
  </div>
</div>


</div>
@endsection

@section('scripts')
    @parent
    <script>
    // Data projects dari PHP
    const projectsData = {!! json_encode($projects->keyBy('project_id')) !!};

    const initSearchableSelect = (searchId, selectId) => {
      const searchInput = document.getElementById(searchId);
      const selectElement = document.getElementById(selectId);
      if (!searchInput || !selectElement) return;

      const originalOptions = Array.from(selectElement.options);

      const filterOptions = () => {
        const term = searchInput.value.trim().toLowerCase();

        originalOptions.forEach(option => {
          if (option.value === '') {
            option.hidden = false;
            return;
          }

          const matches = option.text.toLowerCase().includes(term);
          const shouldHide = term.length > 0 && !matches && !(selectElement.multiple && option.selected);
          option.hidden = shouldHide;
        });
      };

      searchInput.addEventListener('input', filterOptions);
      filterOptions();
    };

    document.addEventListener('DOMContentLoaded', () => {
      const navbar = document.querySelector('.navbar-acrylic');
      const updateSidebarOffset = () => {
        if (!navbar) {
          return;
        }
        document.documentElement
          .style
          .setProperty('--dashboard-topbar-height', `${navbar.offsetHeight}px`);
      };

      updateSidebarOffset();
      window.addEventListener('resize', updateSidebarOffset);

      // Create Project Form Functionality
      const teamLeadSelect = document.getElementById('team_lead_id');
      const devSelect = document.getElementById('developers');
      const desSelect = document.getElementById('designers');
      const selectedDevContainer = document.getElementById('selected-developers');
      const selectedDesContainer = document.getElementById('selected-designers');
      const teamLeadSearchInput = document.getElementById('teamLeadSearch');
      const developerSearchInput = document.getElementById('developerSearch');
      const designerSearchInput = document.getElementById('designerSearch');

      initSearchableSelect('teamLeadSearch', 'team_lead_id');
      initSearchableSelect('developerSearch', 'developers');
      initSearchableSelect('designerSearch', 'designers');

      // Fungsi untuk menampilkan item yang dipilih
      const updateSelectedDisplay = (selectElement, container) => {
        const selectedOptions = Array.from(selectElement.selectedOptions);
        container.innerHTML = '';
        
        if (selectedOptions.length > 0) {
          container.classList.remove('d-none');
          selectedOptions.forEach(option => {
            const item = document.createElement('div');
            item.className = 'selected-item';
            item.innerHTML = `
              ${option.text}
              <button type="button" class="remove-selection" data-value="${option.value}">
                ×
              </button>
            `;
            container.appendChild(item);
          });
        } else {
          container.classList.add('d-none');
        }
      };

      // Event listener untuk menghapus pilihan
      document.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-selection')) {
          const value = e.target.dataset.value;
          const container = e.target.closest('.selected-items');
          
          if (container.id === 'selected-developers') {
            const option = devSelect.querySelector(`option[value="${value}"]`);
            if (option) {
              option.selected = false;
              updateSelectedDisplay(devSelect, selectedDevContainer);
              if (developerSearchInput) {
                developerSearchInput.dispatchEvent(new Event('input'));
              }
            }
          } else if (container.id === 'selected-designers') {
            const option = desSelect.querySelector(`option[value="${value}"]`);
            if (option) {
              option.selected = false;
              updateSelectedDisplay(desSelect, selectedDesContainer);
              if (designerSearchInput) {
                designerSearchInput.dispatchEvent(new Event('input'));
              }
            }
          }
        }
      });

      // Update saat developer/designer diubah
      if (devSelect) {
        devSelect.addEventListener('change', () => {
          updateSelectedDisplay(devSelect, selectedDevContainer);
          if (developerSearchInput) {
            developerSearchInput.dispatchEvent(new Event('input'));
          }
        });
      }

      if (desSelect) {
        desSelect.addEventListener('change', () => {
          updateSelectedDisplay(desSelect, selectedDesContainer);
          if (designerSearchInput) {
            designerSearchInput.dispatchEvent(new Event('input'));
          }
        });
      }

      // Inisialisasi tampilan selected items
      if (devSelect && desSelect) {
        updateSelectedDisplay(devSelect, selectedDevContainer);
        updateSelectedDisplay(desSelect, selectedDesContainer);
      }

      // Comment Form Submission
      // Add Member Form Synchronization
      document.querySelectorAll('.add-member-form').forEach(form => {
        const roleSelect = form.querySelector('.role-select');
        const userSelect = form.querySelector('.user-select');
        const allOptions = Array.from(userSelect.querySelectorAll('option')).slice(1);

        const renderUsers = (roleFilter = '') => {
          userSelect.innerHTML = '<option value="">-- Pilih User --</option>';

          allOptions.forEach(opt => {
            const optRole = opt.dataset.role;
            if (!roleFilter || optRole === roleFilter) {
              userSelect.appendChild(opt.cloneNode(true));
            }
          });
        };

        roleSelect.addEventListener('change', e => {
          const role = e.target.value;
          renderUsers(role);
        });

        userSelect.addEventListener('change', e => {
          const selected = userSelect.selectedOptions[0];
          if (!selected) return;
          const role = selected.dataset.role;
          if (roleSelect.value === '') {
            roleSelect.value = role;
          }
        });

        renderUsers('');
      });

      const projectSearchInput = document.getElementById('projectSearchInput');
      const projectCards = Array.from(document.querySelectorAll('[data-project-item]'));
      const projectEmptyState = document.getElementById('projectSearchEmpty');

      const filterProjects = (rawTerm = '') => {
        const term = (rawTerm || '').toLowerCase().trim();
        let visibleCount = 0;

        projectCards.forEach(card => {
          const keywords = (card.dataset.projectKeywords || '').toLowerCase();
          if (!term || keywords.includes(term)) {
            card.classList.remove('hidden-by-search');
          } else {
            card.classList.add('hidden-by-search');
          }

          if (!card.classList.contains('hidden-by-search') && card.offsetParent !== null) {
            visibleCount += 1;
          }
        });

        if (projectEmptyState) {
          projectEmptyState.classList.toggle('hidden', visibleCount !== 0);
        }
      };

      if (projectSearchInput && projectCards.length > 0) {
        projectSearchInput.addEventListener('input', event => {
          filterProjects(event.target.value);
        });
      }

      if (projectCards.length > 0) {
        filterProjects('');
      }
    });

    // View Management Functions
    function showView(viewName) {
      const viewIds = ['mainDashboardView', 'projectDetailView', 'createProjectView'];
      viewIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
          el.classList.add('hidden');
        }
      });

      const target = document.getElementById(viewName);
      if (target) {
        target.classList.remove('hidden');
      }
    }

    function showMainView() {
      const main = document.getElementById('mainDashboardView');
      if (main) {
        main.classList.remove('hidden');
      }

      ['projectDetailView', 'createProjectView'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
          el.classList.add('hidden');
        }
      });
    }

    function showProjectDetail(projectId) {
      // Project detail view removed: function intentionally left empty to avoid errors if referenced elsewhere
    }
    

    function showCreateProject() {
      showView('createProjectView');
    }
    </script>
@endsection
