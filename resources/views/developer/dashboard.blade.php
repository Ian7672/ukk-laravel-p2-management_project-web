<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Developer Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
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

    .text-surface-strong {
      color: #f9fafb;
    }

    .text-surface-soft {
      color: rgba(255, 255, 255, 0.7);
    }

    .text-surface-muted {
      color: rgba(255, 255, 255, 0.5);
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
      display: flex;
      flex-direction: column;
    }

    .mobile-nav-wrapper {
      display: flex;
      align-items: center;
      gap: 0.75rem;
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
      width: 100%;
      flex-shrink: 0;
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
      max-width: 1600px;
      margin: 0 auto;
      flex: 1 1 auto;
      width: 100%;
    }

    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      flex-wrap: wrap;
      gap: 1.5rem;
    }

    .dashboard-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      justify-content: flex-end;
    }

    .filter-trigger-btn {
      border-radius: 999px;
      border: 1px solid rgba(148, 163, 184, 0.45);
      background: rgba(17, 24, 39, 0.45);
      color: #e5e7eb;
      padding: 0.35rem 0.9rem;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.85rem;
      transition: all 0.2s ease;
    }

    .filter-trigger-btn:hover,
    .filter-trigger-btn:focus-visible {
      border-color: rgba(129, 140, 248, 0.75);
      color: #ffffff;
      background: rgba(79, 70, 229, 0.35);
      box-shadow: 0 12px 24px rgba(79, 70, 229, 0.35);
    }

    [data-theme="light"] .filter-trigger-btn {
      background: #e2e8f0;
      border-color: #cbd5f5;
      color: #1f2937;
    }

    [data-theme="light"] .filter-trigger-btn:hover,
    [data-theme="light"] .filter-trigger-btn:focus-visible {
      background: #c7d2fe;
      color: #1d4ed8;
      border-color: #a5b4fc;
    }

    .status-filter-sheet {
      position: fixed;
      inset: 0;
      display: grid;
      align-items: flex-end;
      justify-items: center;
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.25s ease;
      z-index: 1500;
    }

    .status-filter-sheet.hidden {
      display: none;
    }

    .status-filter-sheet.status-filter-sheet--active {
      opacity: 1;
      pointer-events: auto;
    }

    .status-filter-sheet__overlay {
      position: absolute;
      inset: 0;
      background: rgba(15, 23, 42, 0.65);
      backdrop-filter: blur(10px);
    }

    .status-filter-sheet__panel {
      position: relative;
      width: min(420px, 100% - 1.5rem);
      background: rgba(17, 24, 39, 0.96);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 28px 28px 0 0;
      padding: 1.5rem;
      transform: translateY(24px);
      transition: transform 0.25s ease;
    }

    .status-filter-sheet.status-filter-sheet--active .status-filter-sheet__panel {
      transform: translateY(0);
    }

    .status-filter-sheet__handle {
      width: 48px;
      height: 4px;
      border-radius: 999px;
      background: rgba(148, 163, 184, 0.45);
      margin: 0 auto 1rem;
    }

    .status-filter-sheet__header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .status-filter-sheet__close {
      border: none;
      background: rgba(148, 163, 184, 0.18);
      color: rgba(248, 250, 252, 0.9);
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
    }

    .status-filter-sheet__close:hover {
      background: rgba(99, 102, 241, 0.35);
      color: #ffffff;
    }

    .status-filter-options {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 0.75rem;
    }

    .status-filter-option {
      border-radius: 14px;
      border: 1px solid rgba(148, 163, 184, 0.4);
      background: rgba(30, 41, 59, 0.65);
      color: #e2e8f0;
      padding: 0.6rem 0.8rem;
      font-size: 0.9rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.4rem;
      transition: all 0.2s ease;
    }

    .status-filter-option.active {
      border-color: rgba(79, 70, 229, 0.9);
      background: rgba(79, 70, 229, 0.25);
      color: #ffffff;
      box-shadow: 0 12px 26px rgba(79, 70, 229, 0.35);
    }

    [data-theme="light"] .status-filter-sheet__overlay {
      background: rgba(15, 23, 42, 0.35);
    }

    [data-theme="light"] .status-filter-sheet__panel {
      background: #ffffff;
      border-color: rgba(148, 163, 184, 0.25);
      color: #0f172a;
      box-shadow: 0 -24px 50px rgba(15, 23, 42, 0.1);
    }

    [data-theme="light"] .status-filter-option {
      background: #f1f5f9;
      border-color: #cbd5f5;
      color: #1f2937;
    }

    [data-theme="light"] .status-filter-option.active {
      background: #c7d2fe;
      border-color: #818cf8;
      color: #111827;
    }

    body.status-filter-sheet-open {
      overflow: hidden;
    }

    .dashboard-actions .btn-modern {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.65rem 1rem;
      border-radius: 10px;
      font-size: 0.9rem;
      white-space: nowrap;
    }

    .dashboard-actions .dropdown-menu {
      background: rgba(17, 24, 39, 0.92);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      padding: 0.5rem;
      box-shadow: 0 18px 40px rgba(15, 23, 42, 0.45);
      min-width: 220px;
    }

    .dashboard-actions .dropdown-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #e5e7eb;
      border-radius: 10px;
      padding: 0.5rem 0.75rem;
      transition: all 0.2s ease;
    }

    .dashboard-actions .dropdown-item:hover {
      background: rgba(129, 140, 248, 0.2);
      color: #ffffff;
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

    /* Task Card Layout */
    .task-card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 1.5rem;
    }

    .task-card {
      background: rgba(17, 24, 39, 0.55);
      backdrop-filter: blur(20px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 18px;
      padding: 1.75rem;
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
      position: relative;
      box-shadow: 0 18px 45px rgba(15, 23, 42, 0.35);
    }

    .task-card--project-finished {
      border-color: rgba(34, 197, 94, 0.35);
      box-shadow: inset 0 0 0 1px rgba(34, 197, 94, 0.2);
    }

    .project-finished-note {
      font-size: 0.85rem;
      color: #facc15;
    }

    .task-toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .topbar-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 1rem;
      flex: 1 1 auto;
      min-width: 240px;
    }

    .topbar-actions .search-input-wrapper {
      width: min(360px, 100%);
      margin-bottom: 0;
    }

    .search-input-wrapper {
      position: relative;
      width: min(320px, 100%);
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

    .btn-solver {
      background: linear-gradient(135deg, rgba(244, 114, 182, 0.35), rgba(99, 102, 241, 0.3));
      border: 1px solid rgba(244, 114, 182, 0.45);
      color: #fdf2f8;
    }

    .btn-solver:hover {
      background: linear-gradient(135deg, rgba(244, 114, 182, 0.55), rgba(99, 102, 241, 0.5));
      color: #fff;
      box-shadow: 0 8px 20px rgba(244, 114, 182, 0.25);
    }

    .task-card-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 1rem;
    }

    .task-card-project {
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.55);
    }

    .task-card-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: #ffffff;
      margin-top: 0.35rem;
    }

    .task-card-meta {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 0.75rem;
    }

    .task-card-meta-item {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      padding: 0.75rem;
      display: flex;
      flex-direction: column;
      gap: 0.2rem;
      color: rgba(255, 255, 255, 0.65);
      font-size: 0.83rem;
    }

    .task-card-meta-label {
      text-transform: uppercase;
      letter-spacing: 0.06em;
      font-size: 0.7rem;
      color: rgba(255, 255, 255, 0.45);
    }

    .task-card-meta-value {
      font-size: 0.95rem;
      font-weight: 600;
      color: #f9fafb;
    }

    .task-card-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.6rem;
      align-items: center;
    }

    .task-card .subtask-content {
      margin-top: 0.5rem;
    }

    .subtask-list-wrapper {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .subtask-list-wrapper.hidden {
      display: none !important;
    }

    .subtask-item {
      background: rgba(17, 24, 39, 0.55);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 14px;
      padding: 1rem;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      transition: border-color 0.2s ease, transform 0.2s ease;
    }

    .subtask-item-header strong {
      color: #f8fafc;
    }

    .subtask-item:hover {
      border-color: rgba(139, 92, 246, 0.4);
      transform: translateY(-2px);
    }

    .subtask-item-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 0.75rem;
    }

    .subtask-item-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.6);
    }

    .subtask-item-meta span {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
    }

    .subtask-item-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      align-items: center;
    }

    .empty-subtasks {
      text-align: center;
      color: rgba(255, 255, 255, 0.45);
      padding: 1rem;
      border: 1px dashed rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      font-size: 0.9rem;
    }

    .task-card .comment-section {
      margin-top: 0.5rem;
    }

    .task-card-divider {
      height: 1px;
      background: linear-gradient(90deg, rgba(139, 92, 246, 0), rgba(139, 92, 246, 0.35), rgba(139, 92, 246, 0));
      border: none;
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
    }

    .empty-state i {
      font-size: 5rem;
      color: rgba(139, 92, 246, 0.3);
      margin-bottom: 1.5rem;
    }

    .empty-state h4 {
      color: #c4b5fd;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    /* Table Modern - FIXED BACKGROUND */
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
    }

    .btn-modern:hover {
      background: linear-gradient(135deg, rgba(139, 92, 246, 0.4), rgba(59, 130, 246, 0.4));
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);
    }

    /* Action buttons stacked with small vertical gap */
    .action-buttons {
      display: flex;
      flex-direction: column;
      gap: 6px;
      align-items: flex-start;
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

    /* Subtask Row */
    .subtask-row td {
      background: rgba(17, 24, 39, 0.4) !important;
      padding: 1.5rem !important;
    }

    .subtask-card {
      background: rgba(31, 41, 55, 0.5);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      padding: 1.5rem;
    }

    .subtask-card h6 {
      color: #c4b5fd !important;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    /* Subtask Table Acrylic - FIXED BACKGROUND */
    .subtask-table {
      background: transparent !important;
      border-radius: 12px;
      overflow: hidden;
      border: none !important;
    }

    .subtask-table thead {
      background: rgba(139, 92, 246, 0.1) !important;
      backdrop-filter: blur(10px);
    }

    .subtask-table thead th {
      color: #c4b5fd !important;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.7rem;
      letter-spacing: 0.05em;
      padding: 0.75rem 0.5rem;
      border: none !important;
      white-space: nowrap;
      background: transparent !important;
    }

    .subtask-table tbody {
      background: transparent !important;
    }

    .subtask-table tbody td {
      color: #f3f4f6 !important;
      padding: 0.75rem 0.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
      font-size: 0.85rem;
      background: transparent !important;
      border: none !important;
    }

    .subtask-table tbody tr {
      transition: all 0.3s ease;
      background: transparent !important;
    }

    .subtask-table tbody tr:hover {
      background: rgba(139, 92, 246, 0.06) !important;
    }

    /* Pagination Styles */
    /* Comment Section Styles */
    .comment-section {
      background: rgba(31, 41, 55, 0.5);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      padding: 1.5rem;
      margin-top: 1rem;
    }

    .comment-section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .comment-form-section {
      background: rgba(17, 24, 39, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1.5rem;
    }

    /* Loading Animation Styles */
    .loading-skeleton {
      background: rgba(17, 24, 39, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1rem;
      position: relative;
      overflow: hidden;
    }

    .skeleton-line {
      height: 12px;
      background: linear-gradient(90deg, 
        rgba(255, 255, 255, 0.1) 25%, 
        rgba(255, 255, 255, 0.2) 50%, 
        rgba(255, 255, 255, 0.1) 75%);
      background-size: 200% 100%;
      border-radius: 4px;
      margin-bottom: 8px;
      animation: loading 1.5s infinite;
    }

    .skeleton-line.short {
      width: 60%;
    }

    .skeleton-line.medium {
      width: 80%;
    }

    .skeleton-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      margin-right: 10px;
      animation: loading 1.5s infinite;
    }

    .skeleton-header {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    @keyframes loading {
      0% {
        background-position: 200% 0;
      }
      100% {
        background-position: -200% 0;
      }
    }

    /* Loading Spinner */
    .loading-spinner {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: #8b5cf6;
      animation: spin 1s ease-in-out infinite;
      margin-right: 8px;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Comment Loading State */
    .comments-loading {
      opacity: 0.6;
      pointer-events: none;
    }

    /* Empty State */
    .empty-state-comments {
      text-align: center;
      padding: 2rem;
      color: rgba(255, 255, 255, 0.5);
    }

    .empty-state-comments i {
      font-size: 2rem;
      margin-bottom: 1rem;
    }

    /* Toggle Comment Button */
    .toggle-comment-btn {
      background: rgba(59, 130, 246, 0.2);
      border: 1px solid rgba(59, 130, 246, 0.4);
      color: #93c5fd;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 0.875rem;
      transition: all 0.3s ease;
      white-space: nowrap;
      border: none;
      cursor: pointer;
    }

    .toggle-comment-btn:hover {
      background: rgba(59, 130, 246, 0.3);
      color: white;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    /* Reply Button Styles */
    .btn-reply {
      background: rgba(16, 185, 129, 0.2);
      border: 1px solid rgba(16, 185, 129, 0.3);
      color: #6ee7b7;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 0.8rem;
      transition: all 0.3s ease;
      cursor: pointer;
      backdrop-filter: blur(5px);
      border: none;
    }

    .btn-reply:hover {
      background: rgba(16, 185, 129, 0.3);
      color: white;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }

    /* Cancel Button Styles */
    .btn-cancel {
      background: rgba(107, 114, 128, 0.2);
      border: 1px solid rgba(107, 114, 128, 0.3);
      color: #d1d5db;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 0.8rem;
      transition: all 0.3s ease;
      cursor: pointer;
      backdrop-filter: blur(5px);
      border: none;
    }

    .btn-cancel:hover {
      background: rgba(107, 114, 128, 0.3);
      color: white;
      transform: translateY(-1px);
    }

    /* Reply Form Styles */
    .reply-form {
      background: rgba(17, 24, 39, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      padding: 1rem;
      margin-top: 1rem;
      backdrop-filter: blur(10px);
    }

    .reply-form-buttons {
      display: flex;
      gap: 0.5rem;
      margin-top: 0.5rem;
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
      color: #c4b5fd !important;
    }

    .comment small {
      color: rgba(255, 255, 255, 0.5) !important;
    }

    .comment p {
      color: #e5e7eb !important;
      margin: 0.5rem 0;
    }

    /* Nested Comments Styles */
    .nested-comment {
      background: rgba(17, 24, 39, 0.3) !important;
      border: 1px solid rgba(255, 255, 255, 0.05) !important;
      margin-bottom: 0.75rem;
    }

    .nested-comment .nested-comment {
      background: rgba(17, 24, 39, 0.2) !important;
      border: 1px solid rgba(255, 255, 255, 0.03) !important;
    }

    .replies {
      border-left: 2px solid rgba(139, 92, 246, 0.3);
      padding-left: 1rem;
      margin-top: 1rem;
    }

    .comment[data-level="1"] {
      margin-left: 0.5rem;
    }

    .comment[data-level="2"] {
      margin-left: 1rem;
    }

    .comment[data-level="3"] {
      margin-left: 1.5rem;
    }

    .comment[data-level="4"] {
      margin-left: 2rem;
    }

    /* Batasi maksimal nesting level untuk readability */
    .comment[data-level="5"] {
      margin-left: 2rem;
      opacity: 0.9;
    }

    .comment[data-level="6"] {
      margin-left: 2rem;
      opacity: 0.8;
    }

    .comment[data-level="7"] {
      margin-left: 2rem;
      opacity: 0.7;
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

    /* Scrollbar */
    ::-webkit-scrollbar {
      width: 10px;
      height: 10px;
    }

    ::-webkit-scrollbar-track {
      background: rgba(17, 24, 39, 0.5);
    }

    ::-webkit-scrollbar-thumb {
      background: rgba(139, 92, 246, 0.3);
      border-radius: 5px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: rgba(139, 92, 246, 0.5);
    }

    /* Light Mode Overrides */
    [data-theme="light"] body {
      background: linear-gradient(135deg, #f9fafb 0%, #e0e7ff 45%, #ffffff 100%);
      color: #1f2937;
    }

    [data-theme="light"] .text-gradient {
      background: linear-gradient(135deg, #4c1d95, #1d4ed8);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    [data-theme="light"] .text-surface-strong {
      color: #0f172a;
    }

    [data-theme="light"] .text-surface-soft {
      color: #475569;
    }

    [data-theme="light"] .text-surface-muted {
      color: #64748b;
    }

    [data-theme="light"] .navbar-acrylic {
      background: rgba(255, 255, 255, 0.95);
      border-bottom: 1px solid rgba(203, 213, 225, 0.7);
      box-shadow: 0 14px 36px rgba(148, 163, 184, 0.26);
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

    [data-theme="light"] .badge-acrylic {
      background: rgba(129, 140, 248, 0.18);
      border: 1px solid rgba(99, 102, 241, 0.35);
      color: #1e3a8a;
    }

    [data-theme="light"] .glass-card {
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid rgba(203, 213, 225, 0.7);
      box-shadow: 0 24px 50px rgba(148, 163, 184, 0.25);
      color: #1f2937;
    }

    [data-theme="light"] .project-card {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(203, 213, 225, 0.7);
      box-shadow: 0 14px 30px rgba(148, 163, 184, 0.22);
      color: #1f2937;
    }

    [data-theme="light"] .project-card:hover {
      background: rgba(255, 255, 255, 0.98);
      box-shadow: 0 20px 40px rgba(148, 163, 184, 0.26);
    }

    [data-theme="light"] .project-stats {
      background: rgba(248, 250, 252, 0.9);
      border: 1px solid rgba(203, 213, 225, 0.65);
    }

    [data-theme="light"] .project-stat-title {
      color: #1f2937;
    }

    [data-theme="light"] .project-stat-value {
      color: #4338ca;
    }

    [data-theme="light"] .task-list-title {
      color: #1f2937;
    }

    [data-theme="light"] .task-item {
      background: rgba(255, 255, 255, 0.88);
      border: 1px solid rgba(203, 213, 225, 0.65);
    }

    [data-theme="light"] .task-meta {
      color: #64748b;
    }

    [data-theme="light"] .task-status,
    [data-theme="light"] .task-priority {
      border: 1px solid rgba(203, 213, 225, 0.6);
      background: rgba(226, 232, 240, 0.45);
      color: #1f2937;
    }

    [data-theme="light"] .priority-high {
      background: rgba(248, 113, 113, 0.2);
      border-color: rgba(248, 113, 113, 0.35);
      color: #b91c1c;
    }

    [data-theme="light"] .priority-medium {
      background: rgba(251, 191, 36, 0.2);
      border-color: rgba(251, 191, 36, 0.35);
      color: #92400e;
    }

    [data-theme="light"] .priority-low {
      background: rgba(59, 130, 246, 0.18);
      border-color: rgba(59, 130, 246, 0.3);
      color: #1d4ed8;
    }

    [data-theme="light"] .progress-container {
      background: rgba(226, 232, 240, 0.6);
      border: 1px solid rgba(203, 213, 225, 0.6);
    }

    [data-theme="light"] .progress-container .progress {
      background: rgba(248, 250, 252, 0.9);
    }

    [data-theme="light"] .progress-container .progress-bar {
      color: #111827;
    }

    [data-theme="light"] .badge-modern {
      border-color: rgba(203, 213, 225, 0.6);
      color: #1f2937;
    }

    [data-theme="light"] .badge-status-todo {
      background: rgba(148, 163, 184, 0.2);
      color: #475569;
    }

    [data-theme="light"] .badge-status-in-progress {
      background: rgba(59, 130, 246, 0.18);
      color: #1d4ed8;
    }

    [data-theme="light"] .badge-status-review {
      background: rgba(14, 165, 233, 0.18);
      color: #0c4a6e;
    }

    [data-theme="light"] .badge-status-done {
      background: rgba(16, 185, 129, 0.18);
      color: #047857;
    }

    [data-theme="light"] .task-card {
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid rgba(203, 213, 225, 0.7);
      box-shadow: 0 18px 40px rgba(148, 163, 184, 0.22);
      color: #1f2937;
    }

    [data-theme="light"] .task-card--project-finished {
      border-color: rgba(34, 197, 94, 0.45);
    }

    [data-theme="light"] .project-finished-note {
      color: #b45309;
    }

    [data-theme="light"] .task-card:hover {
      box-shadow: 0 24px 50px rgba(148, 163, 184, 0.25);
    }

    [data-theme="light"] .task-card-project {
      color: #4338ca;
    }

    [data-theme="light"] .task-card-title {
      color: #0f172a;
    }

    [data-theme="light"] .task-card-meta-item {
      background: rgba(248, 250, 252, 0.9);
      border: 1px solid rgba(203, 213, 225, 0.6);
    }

    [data-theme="light"] .task-card-meta-label {
      color: #94a3b8;
    }

    [data-theme="light"] .task-card-meta-value {
      color: #1f2937;
    }

    [data-theme="light"] .task-card-divider {
      background: linear-gradient(90deg, rgba(129, 140, 248, 0), rgba(129, 140, 248, 0.35), rgba(129, 140, 248, 0));
    }

    [data-theme="light"] .subtask-card {
      background: rgba(248, 250, 252, 0.95);
      border: 1px solid rgba(203, 213, 225, 0.7);
      color: #1f2937;
    }

    [data-theme="light"] .subtask-item {
      background: rgba(255, 255, 255, 0.96);
      border: 1px solid rgba(203, 213, 225, 0.7);
      box-shadow: 0 16px 36px rgba(148, 163, 184, 0.2);
      color: #1f2937;
    }

    [data-theme="light"] .subtask-item:hover {
      border-color: rgba(99, 102, 241, 0.35);
      box-shadow: 0 22px 40px rgba(148, 163, 184, 0.24);
    }

    [data-theme="light"] .subtask-item-header strong {
      color: #0f172a;
    }

    [data-theme="light"] .subtask-item-meta {
      color: #475569;
    }

    [data-theme="light"] .subtask-item-meta span i {
      color: #6366f1;
    }

    [data-theme="light"] .subtask-card h6 {
      color: #4338ca !important;
    }

    [data-theme="light"] .subtask-card p {
      color: #475569;
    }

    [data-theme="light"] .btn-comment {
      background: rgba(59, 130, 246, 0.16);
      border: 1px solid rgba(59, 130, 246, 0.32);
      color: #1d4ed8;
    }

    [data-theme="light"] .btn-comment:hover {
      background: rgba(59, 130, 246, 0.24);
      color: #1e3a8a;
    }

    [data-theme="light"] .comment-section {
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid rgba(203, 213, 225, 0.7);
      color: #1f2937;
    }

    [data-theme="light"] .comment {
      background: rgba(248, 250, 252, 0.9);
      border: 1px solid rgba(203, 213, 225, 0.6);
      color: #1f2937;
    }

    [data-theme="light"] .comment strong {
      color: #4c1d95 !important;
    }

    [data-theme="light"] .comment small {
      color: #64748b !important;
    }

    [data-theme="light"] .comment p {
      color: #1f2937 !important;
    }



    [data-theme="light"] .search-input-wrapper input {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(203, 213, 225, 0.65);
      color: #1f2937;
    }

    [data-theme="light"] .search-input-wrapper input::placeholder {
      color: #94a3b8;
    }

    [data-theme="light"] .search-input-wrapper input:focus {
      background: rgba(255, 255, 255, 0.98);
      border-color: rgba(99, 102, 241, 0.5);
      box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2);
      color: #111827;
    }

    [data-theme="light"] .search-input-wrapper .bi {
      color: #6366f1;
    }

    [data-theme="light"] .dashboard-actions .dropdown-menu {
      background: rgba(255, 255, 255, 0.96);
      border: 1px solid rgba(203, 213, 225, 0.7);
      box-shadow: 0 20px 45px rgba(148, 163, 184, 0.22);
    }

    [data-theme="light"] .dashboard-actions .btn-modern {
      color: #1d4ed8;
    }

    [data-theme="light"] .dashboard-actions .btn-modern:hover {
      color: #1e3a8a;
    }

    [data-theme="light"] .dashboard-actions .dropdown-item {
      color: #1f2937;
    }

    [data-theme="light"] .dashboard-actions .dropdown-item:hover {
      background: rgba(129, 140, 248, 0.18);
      color: #1d4ed8;
    }

    [data-theme="light"] .btn-modern {
      background: linear-gradient(135deg, rgba(129, 140, 248, 0.22), rgba(59, 130, 246, 0.22));
      border: 1px solid rgba(99, 102, 241, 0.35);
      color: #1e3a8a;
    }

    [data-theme="light"] .btn-modern:hover {
      box-shadow: 0 12px 28px rgba(99, 102, 241, 0.2);
      color: #1d4ed8;
    }

    [data-theme="light"] .btn-outline {
      border: 1px solid rgba(99, 102, 241, 0.35);
      color: #1d4ed8;
    }

    [data-theme="light"] .btn-outline:hover {
      background: rgba(129, 140, 248, 0.18);
    }

    [data-theme="light"] .empty-state {
      background: rgba(248, 250, 252, 0.95);
      border: 1px solid rgba(203, 213, 225, 0.7);
      color: #64748b;
    }

    [data-theme="light"] .empty-state i {
      color: rgba(129, 140, 248, 0.35);
    }

    [data-theme="light"] .empty-subtasks {
      background: rgba(248, 250, 252, 0.95);
      border: 1px dashed rgba(148, 163, 184, 0.45);
      color: #64748b;
    }

    [data-theme="light"] .modal-content {
      background: rgba(255, 255, 255, 0.96);
      border: 1px solid rgba(203, 213, 225, 0.7);
      color: #1f2937;
    }

    [data-theme="light"] .modal-header,
    [data-theme="light"] .modal-footer {
      border-color: rgba(203, 213, 225, 0.6);
    }

    [data-theme="light"] .form-control,
    [data-theme="light"] .form-select {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(203, 213, 225, 0.65);
      color: #1f2937;
    }

    [data-theme="light"] .form-control::placeholder {
      color: #94a3b8;
    }

    [data-theme="light"] .form-control:focus,
    [data-theme="light"] .form-select:focus {
      background: rgba(255, 255, 255, 0.98);
      border-color: rgba(99, 102, 241, 0.5);
      color: #111827;
      box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.25);
    }

    [data-theme="light"] .search-empty {
      border: 1px dashed rgba(129, 140, 248, 0.4);
      background: rgba(248, 250, 252, 0.95);
      color: #475569;
    }

    [data-theme="light"] .search-empty i {
      color: rgba(129, 140, 248, 0.35);
    }

    [data-theme="light"] ::-webkit-scrollbar-track {
      background: rgba(226, 232, 240, 0.65);
    }

    [data-theme="light"] ::-webkit-scrollbar-thumb {
      background: rgba(129, 140, 248, 0.38);
    }

    [data-theme="light"] ::-webkit-scrollbar-thumb:hover {
      background: rgba(99, 102, 241, 0.48);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .main-content-area {
        margin-left: 0;
        display: flex;
        flex-direction: column;
      }
      
      .acrylic-sidebar-fixed {
        transform: translateX(-100%);
      }

      .topbar-actions {
        width: 100%;
        justify-content: stretch;
      }

      .topbar-actions .search-input-wrapper {
        width: 100%;
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

    /* Tambahan CSS untuk toggle content */
    .subtask-content {
      transition: all 0.3s ease;
    }

    .subtask-content.hidden {
      display: none !important;
    }

    .comment-section.hidden {
      display: none !important;
    }

    /* Hide class untuk filter dan search */
    .row-hidden {
      display: none !important;
    }

    .text-grey-custom {
      color: rgba(255, 255, 255, 0.6) !important;
    }

    @media (max-width: 1199.98px) {
      .content-wrapper {
        padding: 1.75rem;
      }
    }

    @media (max-width: 991.98px) {
      .layout-wrapper {
        flex-direction: column;
      }

      .main-content-area {
        margin-left: 0;
        min-height: auto;
        display: flex;
        flex-direction: column;
      }

      .navbar-acrylic {
        position: relative;
        padding: 0.85rem 1.25rem;
      }

      .navbar-title {
        font-size: 1.35rem;
      }

      .badge-acrylic {
        font-size: 0.85rem;
        padding: 6px 12px;
      }

      .content-wrapper {
        padding: 1.5rem 1rem 4rem;
        width: 100%;
      }

      .dashboard-header {
        align-items: flex-start;
      }

      .dashboard-actions {
        width: 100%;
        justify-content: flex-start;
      }

      .dashboard-actions .btn-modern {
        flex: 1 1 45%;
      }

      .glass-card {
        padding: 1.5rem;
      }

      .filter-toolbar,
      .filters-row {
        flex-direction: column;
        align-items: stretch !important;
      }

      .filter-toolbar .btn-modern,
      .filters-row .btn-modern {
        width: 100%;
      }

      .stats-grid {
        grid-template-columns: 1fr;
      }

      .activity-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
      }
    }

    @media (max-width: 767.98px) {
      .navbar-acrylic .container-fluid {
        flex-wrap: wrap;
        justify-content: space-between;
      }

      .badge-acrylic {
        order: 3;
        width: 100%;
        text-align: center;
      }

      .mobile-nav-wrapper {
        width: auto;
        justify-content: flex-start;
        gap: 0.75rem;
      }

      .glass-card,
      .empty-state {
        padding: 1.25rem;
      }

      .table-responsive {
        margin-bottom: 1.5rem;
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

      .badge-acrylic {
        font-size: 0.8rem;
      }

      .content-wrapper {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
      }

      .dashboard-actions .btn-modern {
        flex: 1 1 100%;
      }

      .stats-summary {
        flex-direction: column;
        gap: 1.25rem;
      }
    }
  </style>
</head>
<body>

<div class="layout-wrapper">
  @include('components.app-sidebar')

  <!-- Main Content Area -->
  <div class="main-content-area">
    <!-- Topbar -->
    <nav class="navbar-acrylic">
      <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="mobile-nav-wrapper">
          <button type="button"
                  class="sidebar-toggle-btn d-lg-none"
                  data-sidebar-toggle
                  aria-label="Buka menu navigasi">
            <i class="bi bi-list"></i>
          </button>
          <span class="navbar-title">
            <i class="bi me-2"></i>Dashboard
          </span>
        </div>
                <div class="topbar-actions ms-auto">
                  <div class="search-input-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="search"
                   id="taskSearchInput"
                   placeholder="Search tasks or subtasks...">
          </div>
          <button type="button" class="filter-trigger-btn" id="statusFilterTrigger">
            <i class="bi bi-funnel"></i>
            <span id="statusFilterLabel">Filter: All</span>
          </button>
        </div>
        <div id="developerStatusFilterSheet" class="status-filter-sheet hidden" aria-hidden="true">
          <div class="status-filter-sheet__overlay" data-status-filter-dismiss></div>
          <div class="status-filter-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="developerStatusFilterTitle">
            <div class="status-filter-sheet__handle"></div>
            <div class="status-filter-sheet__header">
              <div>
                <p class="text-surface-muted mb-1" id="developerStatusFilterSubtitle">Tampilkan berdasarkan status card</p>
                <h5 class="mb-0" id="developerStatusFilterTitle">Filter Status</h5>
              </div>
              <button type="button" class="status-filter-sheet__close" data-status-filter-dismiss aria-label="Tutup filter">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
            <div class="status-filter-options">
              <button type="button" class="status-filter-option active" data-status-option="all">
                <i class="bi bi-app-indicator"></i> All
              </button>
              <button type="button" class="status-filter-option" data-status-option="todo">
                <i class="bi bi-list-task"></i> To Do
              </button>
              <button type="button" class="status-filter-option" data-status-option="in_progress">
                <i class="bi bi-lightning-charge"></i> In Progress
              </button>
              <button type="button" class="status-filter-option" data-status-option="review">
                <i class="bi bi-eye"></i> Review
              </button>
              <button type="button" class="status-filter-option" data-status-option="done">
                <i class="bi bi-check2-circle"></i> Done
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <div class="content-wrapper">


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

      @if($cards->isEmpty())
        <div class="glass-card empty-state">
          <i class="bi bi-palette"></i>
          <h4>No Dev Tasks Assigned</h4>
          <p class="text-surface-muted">You don't have any development tasks assigned yet.</p>
        </div>
      @else
        
          <div class="task-card-grid" id="cardsContainer">
          @foreach($cards as $card)
          @php
            $projectStatus = strtolower($card->board->project->status ?? 'proses');
            $projectFinished = $projectStatus === 'selesai';
            $subtasks = $card->subtasks ?? collect();
            $totalSubtasks = $subtasks->count();
            $completedSubtasks = $subtasks->where('status', 'done')->count();
          @endphp
          <div class="task-card {{ $projectFinished ? 'task-card--project-finished' : '' }}"
               data-project="{{ strtolower($card->board->project->project_name ?? '') }}"
               data-project-status="{{ $projectStatus }}"
               data-card-status="{{ strtolower($card->status ?? 'todo') }}"
               data-board="{{ strtolower($card->board->board_name ?? '') }}"
               data-card-title="{{ strtolower($card->card_title ?? '') }}">
              <div>
                <div class="task-card-header">
                  <div>
                    <span class="task-card-project">{{ $card->board->project->project_name }}</span>
                    <h5 class="task-card-title mb-0">{{ $card->card_title }}</h5>
                  </div>
                  <div>
                    @if($card->status == 'todo')
                      <span class="badge-modern badge-primary">To Do</span>
                    @elseif($card->status == 'in_progress')
                      <span class="badge-modern badge-warning">In Progress</span>
                    @elseif($card->status == 'review')
                      <span class="badge-modern badge-info">Review</span>
                    @else
                      <span class="badge-modern badge-success">Done</span>
                    @endif
                  </div>
                </div>
                <div class="task-card-meta">
                  <div class="task-card-meta-item">
                    <span class="task-card-meta-label">Progress</span>
                    <span class="task-card-meta-value">{{ $completedSubtasks }}/{{ $totalSubtasks }}</span>
                  </div>
                  <div class="task-card-meta-item">
                    <span class="task-card-meta-label">Priority</span>
                    <span class="task-card-meta-value">
                      @if($card->priority == 'high')
                        <span class="badge-modern badge-danger">High</span>
                      @elseif($card->priority == 'medium')
                        <span class="badge-modern badge-warning">Medium</span>
                      @else
                        <span class="badge-modern badge-primary">Low</span>
                      @endif
                    </span>
                  </div>
                  <div class="task-card-meta-item">
                    <span class="task-card-meta-label">Est. Hours</span>
                    <span class="task-card-meta-value">{{ $card->estimated_hours ?? '-' }}</span>
                  </div>
                  <div class="task-card-meta-item">
                    <span class="task-card-meta-label">Deadline</span>
                    <span class="task-card-meta-value">{{ $card->due_date ? \Carbon\Carbon::parse($card->due_date)->format('d M Y') : '-' }}</span>
                  </div>
                </div>
              </div>

              <hr class="task-card-divider">

              <div class="task-card-actions">
                @if(!$projectFinished)
                  <button class="btn-modern btn-sm"
                          data-subtask-sheet-trigger="true"
                          data-subtask-url="{{ route('subtasks.store', $card->card_id) }}"
                          data-subtask-heading="Tambah Dev Subtask"
                          data-subtask-subtitle="{{ $card->board->project->project_name }} - {{ $card->card_title }}"
                          data-subtask-title-label="Judul Dev Subtask"
                          data-subtask-title-placeholder="Masukkan judul dev subtask"
                          data-subtask-estimate-label="Estimasi Waktu (Jam)"
                          data-subtask-estimate-placeholder="Contoh: 2.5"
                          data-subtask-description-label="Deskripsi Dev"
                          data-subtask-description-placeholder="Tuliskan deskripsi dev subtask (opsional)"
                          data-subtask-submit-label="Simpan Dev Subtask"
                          data-subtask-loading-label="Menyimpan...">
                    <i class="bi bi-plus-circle me-1"></i> Add Subtask
                  </button>
                @endif
              </div>

              @if($projectFinished)
                <div class="project-finished-note mt-2">
                  <i class="bi bi-lock-fill me-1"></i>Proyek selesai. Penambahan subtask dan aksi pengerjaan dinonaktifkan.
                </div>
              @endif

              <!-- Subtasks Content (Default View) -->
              <div class="subtask-content" id="subtaskContent{{ $card->card_id }}">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                  <h6 class="mb-0">💻 Dev Subtasks</h6>
                  
                </div>
                <!-- Subtasks List -->
                <div id="subtasksList{{ $card->card_id }}" class="subtask-list-wrapper">
                  @forelse($card->subtasks as $st)
                  <div class="subtask-item" data-subtask-title="{{ strtolower($st->subtask_title ?? '') }}">
                    <div class="subtask-item-header">
                      <strong>{{ $st->subtask_title }}</strong>
                      <span class="badge-modern 
                        @if($st->status=='todo') badge-primary
                        @elseif($st->status=='in_progress') badge-warning
                        @elseif($st->status=='review') badge-info
                        @else badge-success @endif">
                        {{ ucfirst(str_replace('_',' ', $st->status)) }}
                      </span>
                    </div>
                    <div class="subtask-item-meta">
                      <span><i class="bi bi-hourglass-split"></i>{{ $st->estimated_hours }}h Est.</span>
                      <span><i class="bi bi-clock-history"></i>{{ $st->actual_hours }}h Actual</span>
                      <span><i class="bi bi-calendar-event"></i>{{ $st->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="subtask-item-actions">
                      @php
                        $activeSolver = $st->blockers->first();
                      @endphp
                      @if(!$projectFinished)
                        @if($st->status == 'todo')
                          <form id="startSubtaskForm{{ $st->subtask_id }}" action="{{ route('subtasks.start', $st->subtask_id) }}" method="POST" class="d-none">
                            @csrf
                          </form>
                          <button class="btn-modern btn-sm"
                                  data-action-sheet-trigger="true"
                                  data-action-form="#startSubtaskForm{{ $st->subtask_id }}"
                                  data-action-title="Mulai Subtask"
                                  data-action-subtitle="{{ $st->subtask_title }}"
                                  data-action-message="Mulai mengerjakan subtask &quot;{{ $st->subtask_title }}&quot; sekarang?"
                                  data-action-confirm-label="Mulai"
                                  data-action-loading-label="Memproses...">
                            Mulai
                          </button>
                        @elseif($st->status == 'in_progress')
                          <form id="completeSubtaskForm{{ $st->subtask_id }}" action="{{ route('subtasks.complete', $st->subtask_id) }}" method="POST" class="d-none">
                            @csrf
                          </form>
                          <button class="btn-modern btn-sm"
                                  data-action-sheet-trigger="true"
                                  data-action-form="#completeSubtaskForm{{ $st->subtask_id }}"
                                  data-action-title="Selesaikan Subtask"
                                  data-action-subtitle="{{ $st->subtask_title }}"
                                  data-action-message="Kirim subtask &quot;{{ $st->subtask_title }}&quot; untuk direview Team Lead?"
                                  data-action-confirm-label="Selesaikan"
                                  data-action-loading-label="Mengirim...">
                            Selesaikan
                          </button>
                        @endif
                      @endif
                      <button class="toggle-comment-btn btn-sm"
                              data-comment-sheet-trigger="true"
                              data-comment-type="subtask"
                              data-comment-id="{{ $st->subtask_id }}"
                              data-comment-title="{{ $st->subtask_title }}"
                              data-comment-subtitle="{{ $card->card_title }} - {{ $card->board->project->project_name }}">
                        <i class="bi bi-chat-dots me-1"></i>Comments
                      </button>
                      @if(!$projectFinished)
                        @if(!$activeSolver && $st->status !== 'done')
                          <button class="btn-modern btn-sm btn-solver"
                                  data-action-sheet-trigger="true"
                                  data-action-url="{{ route('blocker.subtask.store', $st->subtask_id) }}"
                                  data-action-method="POST"
                                  data-action-title="Kirim Solver"
                                  data-action-subtitle="{{ $st->subtask_title }}"
                                  data-action-message="Kirim permintaan bantuan kepada Team Lead untuk subtask &quot;{{ $st->subtask_title }}&quot;?"
                                  data-action-confirm-label="Kirim Solver"
                                  data-action-loading-label="Mengirim..."
                                  data-action-success="reload">
                            Solver
                          </button>
                        @elseif($activeSolver)
                          <span class="badge-modern badge-warning">
                            Solver {{ $activeSolver->status === 'selesai' ? 'selesai' : 'menunggu' }}
                          </span>
                        @endif
                      @elseif($activeSolver)
                        <span class="badge-modern badge-warning">
                          Solver {{ $activeSolver->status === 'selesai' ? 'selesai' : 'menunggu' }}
                        </span>
                      @elseif($st->status !== 'done')
                        <span class="badge-modern badge-success">Tindakan terkunci</span>
                      @endif
                    </div>
                  </div>

                  <div class="comment-section hidden mt-2" id="commentSectionSubtask{{ $st->subtask_id }}">
                    {{-- Comment section handled via bottom sheet --}}
                  </div>

                  @empty
                  <div class="empty-subtasks">No subtasks yet</div>
                  @endforelse
                </div>
              </div>
            </div>
            @endforeach
          </div>
          <div id="developerTaskSearchEmpty" class="glass-card empty-state text-center d-none">
            <i class="bi bi-search"></i>
            <h4 class="mt-3 mb-1">Task tidak ditemukan</h4>
            <p class="text-surface-muted mb-0">Coba gunakan kata kunci lain atau bersihkan pencarian.</p>
          </div>
        
      @endif
</div>
</div>
</div>

@include('components.comment-bottom-sheet')
@include('components.action-bottom-sheet')
@include('components.subtask-bottom-sheet')
@include('components.theme-toggle')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@include('partials.profile-quick-sheet')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const taskSearchInput = document.getElementById('taskSearchInput');
  const taskSearchEmpty = document.getElementById('developerTaskSearchEmpty');
  const statusFilterButton = document.getElementById('statusFilterTrigger');
  const statusFilterLabel = document.getElementById('statusFilterLabel');
  const statusFilterSheet = document.getElementById('developerStatusFilterSheet');
  const statusFilterOptions = statusFilterSheet ? statusFilterSheet.querySelectorAll('[data-status-option]') : [];
  const statusFilterDismiss = statusFilterSheet ? statusFilterSheet.querySelectorAll('[data-status-filter-dismiss]') : [];
  const cards = Array.from(document.querySelectorAll('.task-card'));
  let currentStatusFilter = 'all';

  if (statusFilterSheet && statusFilterSheet.parentElement !== document.body) {
    document.body.appendChild(statusFilterSheet);
  }

  const matchesSearchTerm = (card, term) => {
    if (!term) return true;
    const dataMatches = ['project', 'board', 'cardTitle'].some(key => {
      return (card.dataset[key] || '').includes(term);
    });

    const subtaskMatch = Array.from(card.querySelectorAll('[data-subtask-title]')).some(subtask => {
      return (subtask.dataset.subtaskTitle || '').includes(term);
    });

    const cardText = card.textContent.toLowerCase();
    return dataMatches || subtaskMatch || cardText.indexOf(term) !== -1;
  };

  const applyFilters = () => {
    const term = (taskSearchInput?.value || '').toLowerCase();
    let visibleCount = 0;

    cards.forEach(card => {
      const status = (card.dataset.cardStatus || '').toLowerCase();
      const matchesStatus = currentStatusFilter === 'all' || status === currentStatusFilter;
      const matchesSearch = matchesSearchTerm(card, term);
      const shouldShow = matchesStatus && matchesSearch;

      card.classList.toggle('row-hidden', !shouldShow);
      if (shouldShow) {
        visibleCount++;
      }
    });

    if (taskSearchEmpty) {
      taskSearchEmpty.classList.toggle('d-none', visibleCount !== 0);
    }
  };

  const openStatusSheet = () => {
    if (!statusFilterSheet) return;
    statusFilterSheet.classList.remove('hidden');
    requestAnimationFrame(() => statusFilterSheet.classList.add('status-filter-sheet--active'));
    document.body.classList.add('status-filter-sheet-open');
  };

  const closeStatusSheet = () => {
    if (!statusFilterSheet) return;
    statusFilterSheet.classList.remove('status-filter-sheet--active');
    document.body.classList.remove('status-filter-sheet-open');
    setTimeout(() => statusFilterSheet.classList.add('hidden'), 220);
  };

  if (taskSearchInput) {
    taskSearchInput.addEventListener('input', applyFilters);
  }

  if (statusFilterButton) {
    statusFilterButton.addEventListener('click', openStatusSheet);
  }

  statusFilterOptions.forEach(option => {
    option.addEventListener('click', () => {
      if (option.classList.contains('active')) {
        closeStatusSheet();
        return;
      }
      statusFilterOptions.forEach(o => o.classList.remove('active'));
      option.classList.add('active');
      currentStatusFilter = option.dataset.statusOption || 'all';
      if (statusFilterLabel) {
        statusFilterLabel.textContent = `Filter: ${option.textContent.trim()}`;
      }
      closeStatusSheet();
      applyFilters();
    });
  });

  statusFilterDismiss.forEach(trigger => {
    trigger.addEventListener('click', closeStatusSheet);
  });

  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && statusFilterSheet && !statusFilterSheet.classList.contains('hidden')) {
      closeStatusSheet();
    }
  });

  if (statusFilterSheet) {
    statusFilterSheet.addEventListener('click', event => {
      if (event.target === statusFilterSheet) {
        closeStatusSheet();
      }
    });
  }

  applyFilters();
});
</script>

</body>
</html>


