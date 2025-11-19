<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Proyek - Team Lead</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

    .text-surface-strong {
      color: #f9fafb;
    }

    .text-surface-soft {
      color: rgba(255, 255, 255, 0.7);
    }

    .text-surface-emphasis {
      color: rgba(255, 255, 255, 0.8);
    }

    .text-surface-muted {
      color: rgba(255, 255, 255, 0.5);
    }

    .text-surface-hint {
      color: rgba(255, 255, 255, 0.4);
    }

    /* Smooth scroll behavior */
    html {
      scroll-behavior: smooth;
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

    /* Boards Container - VERTIKAL (KE BAWAH) */
    .boards-container {
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    .board-section {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      scroll-margin-top: 100px;
    }

    .board-header {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 0 0.5rem;
    }

    .board-title {
      color: #c4b5fd;
      font-weight: 600;
      font-size: 1.3rem;
      margin: 0;
      min-width: 150px;
    }

    .board-divider {
      flex: 1;
      height: 2px;
      background: linear-gradient(90deg, 
        rgba(139, 92, 246, 0.5) 0%, 
        rgba(139, 92, 246, 0.2) 50%, 
        transparent 100%);
      border: none;
    }

    /* Board Content - SCROLL HORIZONTAL untuk cards */
    .board-content {
      display: flex;
      overflow-x: auto;
      overflow-y: hidden;
      gap: 1rem;
      padding: 1rem 0.5rem;
      align-items: flex-start;
      scrollbar-width: thin;
    }

    .board-content::-webkit-scrollbar {
      height: 8px;
    }

    /* Project members */
    .project-members-card {
      margin-bottom: 2rem;
    }

    .project-members-card .member-item {
      display: flex;
      gap: 1rem;
      padding: 1rem;
      border-radius: 16px;
      background: rgba(15, 23, 42, 0.45);
      border: 1px solid rgba(255, 255, 255, 0.08);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
      transition: border-color 0.2s ease, transform 0.2s ease;
    }

    .project-members-card .member-item:hover {
      border-color: rgba(139, 92, 246, 0.35);
      transform: translateY(-2px);
    }

    .member-avatar {
      width: 54px;
      height: 54px;
      border-radius: 16px;
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.25), rgba(139, 92, 246, 0.25));
      border: 1px solid rgba(99, 102, 241, 0.35);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #dbeafe;
      font-size: 1.5rem;
    }

    .member-role-badge {
      background: rgba(139, 92, 246, 0.2);
      border: 1px solid rgba(139, 92, 246, 0.4);
      color: #c4b5fd;
      padding: 0.2rem 0.8rem;
      border-radius: 999px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .member-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      font-size: 0.85rem;
      color: rgba(226, 232, 240, 0.85);
    }

    .member-meta i {
      color: rgba(99, 102, 241, 0.9);
      margin-right: 0.35rem;
    }

    .bottom-sheet {
      position: fixed;
      inset: 0;
      display: grid;
      align-items: flex-end;
      justify-items: center;
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.25s ease;
      z-index: 1400;
    }

    .bottom-sheet.hidden {
      display: none;
    }

    .bottom-sheet.bottom-sheet--active {
      opacity: 1;
      pointer-events: auto;
    }

    .bottom-sheet__overlay {
      position: absolute;
      inset: 0;
      background: rgba(15, 23, 42, 0.65);
      backdrop-filter: blur(10px);
    }

    .bottom-sheet__panel {
      position: relative;
      width: min(560px, 100% - 1.5rem);
      max-height: 85vh;
      background: rgba(17, 24, 39, 0.96);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 28px 28px 0 0;
      padding: 1.5rem;
      transform: translateY(24px);
      transition: transform 0.25s ease;
    }

    .bottom-sheet.bottom-sheet--active .bottom-sheet__panel {
      transform: translateY(0);
    }

    .bottom-sheet__handle {
      width: 48px;
      height: 4px;
      border-radius: 999px;
      background: rgba(148, 163, 184, 0.45);
      margin: 0 auto 1rem;
    }

    .bottom-sheet__header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .bottom-sheet__title {
      font-size: 1.25rem;
      font-weight: 600;
      color: #f8fafc;
      margin-bottom: 0;
    }

    .bottom-sheet__subtitle {
      font-size: 0.9rem;
      color: rgba(226, 232, 240, 0.7);
      margin-bottom: 0.25rem;
    }

    .bottom-sheet__close {
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

    .bottom-sheet__close:hover {
      background: rgba(99, 102, 241, 0.35);
      color: #ffffff;
    }

    .bottom-sheet__footer {
      display: flex;
      justify-content: flex-end;
      gap: 0.75rem;
      margin-top: 1.5rem;
      flex-wrap: wrap;
    }

    .bottom-sheet__body {
      max-height: calc(85vh - 140px);
      overflow-y: auto;
    }

    .comments-scroll {
      max-height: 45vh;
      overflow-y: auto;
      padding-right: 0.25rem;
    }

    body.bottom-sheet-open {
      overflow: hidden;
    }

    [data-theme="light"] .bottom-sheet__overlay {
      background: rgba(15, 23, 42, 0.35);
    }

    [data-theme="light"] .bottom-sheet__panel {
      background: #ffffff;
      color: #0f172a;
      border-color: rgba(15, 23, 42, 0.08);
      box-shadow: 0 -20px 40px rgba(15, 23, 42, 0.1);
    }

    [data-theme="light"] .bottom-sheet__title {
      color: #0f172a;
    }

    [data-theme="light"] .bottom-sheet__subtitle {
      color: #475569;
    }

    [data-theme="light"] .bottom-sheet__close {
      background: rgba(148, 163, 184, 0.2);
      color: #0f172a;
    }

    [data-theme="light"] .bottom-sheet__close:hover {
      background: rgba(99, 102, 241, 0.2);
      color: #1e1b4b;
    }

    .board-content::-webkit-scrollbar-track {
      background: rgba(17, 24, 39, 0.2);
      border-radius: 4px;
      margin: 0 4px;
    }

    .board-content::-webkit-scrollbar-thumb {
      background: rgba(139, 92, 246, 0.3);
      border-radius: 4px;
    }

    .board-content::-webkit-scrollbar-thumb:hover {
      background: rgba(139, 92, 246, 0.5);
    }

    /* Project / card comment form */
    .comment-form-section {
      background: rgba(17, 24, 39, 0.45);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 18px;
      padding: 1.5rem;
      box-shadow: 0 18px 40px rgba(15, 23, 42, 0.35);
    }

    .comment-form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .comment-form .form-control {
      background: rgba(15, 23, 42, 0.7);
      border: 1px solid rgba(148, 163, 184, 0.35);
      border-radius: 16px;
      color: #f8fafc;
      padding: 1rem 1.25rem;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
      min-height: 120px;
      resize: vertical;
    }

    .comment-form .form-control::placeholder {
      color: rgba(226, 232, 240, 0.65);
    }

    .comment-form .form-control:focus {
      border-color: rgba(99, 102, 241, 0.65);
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
      background: rgba(15, 23, 42, 0.85);
      color: #fff;
    }

    .comment-form .btn-modern {
      align-self: flex-end;
      padding: 0.75rem 1.5rem;
      min-width: 160px;
    }

    .comment-form .btn-modern i {
      font-size: 1rem;
    }

    @media (max-width: 576px) {
      .comment-form .btn-modern {
        width: 100%;
        align-self: stretch;
      }
    }

    .card-item {
      background: rgba(17, 24, 39, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      padding: 1.25rem;
      transition: all 0.3s ease;
      min-width: 280px;
      flex-shrink: 0;
      cursor: pointer;
    }

    .card-item:hover {
      background: rgba(17, 24, 39, 0.6);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    /* Card Header dengan prioritas tidak nabrak */
    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 0.5rem;
      margin-bottom: 0.75rem;
    }

    .card-title {
      color: #ffffff;
      font-weight: 600;
      font-size: 0.95rem;
      line-height: 1.3;
      flex: 1;
      min-width: 0;
      word-wrap: break-word;
    }

    .priority-badge {
      flex-shrink: 0;
      margin-left: 0.5rem;
    }

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
      border: none;
      cursor: pointer;
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

    .btn-info-modern {
      background: linear-gradient(135deg, rgba(14, 165, 233, 0.25), rgba(56, 189, 248, 0.25));
      border: 1px solid rgba(14, 165, 233, 0.4);
      color: #7dd3fc;
    }

    .btn-info-modern:hover {
      background: linear-gradient(135deg, rgba(14, 165, 233, 0.4), rgba(56, 189, 248, 0.4));
      color: white;
      box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);
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
      display: inline-block;
    }

    .badge-danger { background: rgba(239, 68, 68, 0.25); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5; }
    .badge-warning { background: rgba(251, 191, 36, 0.25); border-color: rgba(251, 191, 36, 0.4); color: #fde047; }
    .badge-primary { background: rgba(59, 130, 246, 0.25); border-color: rgba(59, 130, 246, 0.4); color: #93c5fd; }
    .badge-success { background: rgba(16, 185, 129, 0.25); border-color: rgba(16, 185, 129, 0.4); color: #6ee7b7; }
    .badge-info { background: rgba(14, 165, 233, 0.25); border-color: rgba(14, 165, 233, 0.4); color: #7dd3fc; }

    /* Text Gradient */
    .text-gradient {
      background: linear-gradient(135deg, #8b5cf6, #3b82f6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    /* Action Buttons Container */
    .action-buttons {
      display: flex;
      gap: 1rem;
      align-items: center;
      margin-bottom: 2rem;
      flex-wrap: wrap;
    }

    /* Empty State */
    .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem 2rem;
      color: rgba(255, 255, 255, 0.5);
      min-width: 280px;
    }

    .empty-state i {
      font-size: 3rem;
      margin-bottom: 1rem;
    }

    /* Content Sections */
    .content-section {
      display: none;
      animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .content-section.active {
      display: block;
    }

    /* Subtask Table Styles */
    .subtask-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
      background: rgba(17, 24, 39, 0.4);
      border-radius: 12px;
      overflow: hidden;
    }

    .subtask-table th,
    .subtask-table td {
      padding: 0.75rem;
      text-align: left;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .subtask-table th {
      background: rgba(139, 92, 246, 0.2);
      color: #c4b5fd;
      font-weight: 600;
    }

    .subtask-table tr:hover {
      background: rgba(255, 255, 255, 0.05);
    }

    .badge-subtask {
      padding: 4px 8px;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .badge-todo { background: rgba(107, 114, 128, 0.3); color: #d1d5db; }
    .badge-in-progress { background: rgba(59, 130, 246, 0.3); color: #93c5fd; }
    .badge-review { background: rgba(14, 165, 233, 0.3); color: #7dd3fc; }
    .badge-done { background: rgba(16, 185, 129, 0.3); color: #6ee7b7; }

    /* Confirmation Button Styles */
    .btn-confirm {
      background: linear-gradient(135deg, rgba(251, 191, 36, 0.4), rgba(245, 158, 11, 0.4));
      border: 1px solid rgba(251, 191, 36, 0.6);
      color: #fde047;
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }

    /* Comment Styles */
    .comment-section {
      background: rgba(31, 41, 55, 0.5);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      padding: 1.5rem;
      margin-top: 2rem;
    }

    .comment {
      background: rgba(17, 24, 39, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1rem;
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

    /* Form Controls */
    .form-control {
      background: rgba(31, 41, 55, 0.8) !important;
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #f3f4f6 !important;
      border-radius: 12px;
      backdrop-filter: blur(10px);
      padding: 0.75rem 1rem;
      font-size: 1rem;
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.4);
    }

    .form-control:focus {
      background: rgba(31, 41, 55, 0.95) !important;
      border-color: rgba(139, 92, 246, 0.6);
      color: white !important;
      box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.25);
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

    /* Modal Custom Styles */
    .modal-content.glass-card {
      background: rgba(31, 41, 55, 0.95) !important;
      backdrop-filter: blur(30px) saturate(200%);
      border: 1px solid rgba(139, 92, 246, 0.3);
    }

    .btn-close-white {
      filter: invert(1) grayscale(100%) brightness(200%);
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
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

    /* Responsive */
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
      }

      .navbar-acrylic {
        padding: 0.85rem 1.25rem;
        position: relative;
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
      }
    }

    @media (max-width: 767.98px) {
      .navbar-acrylic .container-fluid {
        flex-wrap: wrap;
        justify-content: space-between;
      }

      .mobile-nav-wrapper {
        width: auto;
        justify-content: flex-start;
        gap: 0.75rem;
      }

      .badge-acrylic {
        order: 3;
        width: 100%;
        text-align: center;
      }

      .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
      }

      .priority-badge {
        margin-left: 0;
        align-self: flex-start;
      }

      .action-buttons {
        flex-direction: column;
        align-items: flex-start;
      }

      .board-title {
        min-width: 120px;
        font-size: 1.1rem;
      }

      .card-item {
        min-width: 250px;
      }

      .subtask-table {
        font-size: 0.85rem;
      }

      .subtask-table th,
      .subtask-table td {
        padding: 0.5rem;
      }

      .comment[data-level="1"],
      .comment[data-level="2"],
      .comment[data-level="3"],
      .comment[data-level="4"] {
        margin-left: 0.5rem;
      }

      .replies {
        padding-left: 0.5rem;
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

      .card-item {
        min-width: 230px;
      }

      .board-title {
        min-width: 110px;
        font-size: 1rem;
      }
    }

    @media (max-width: 480px) {
      .card-item {
        min-width: 210px;
      }

      .board-title {
        min-width: 100px;
        font-size: 0.95rem;
      }

      .subtask-table {
        display: block;
        overflow-x: auto;
      }
    }

    .text-grey-custom{
      color: #9ca3af !important;
    }

    /* Custom error dialog */
    .custom-dialog-overlay {
      position: fixed;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      background: rgba(15, 23, 42, 0.75);
      backdrop-filter: blur(8px);
      z-index: 2000;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.25s ease;
    }

    .custom-dialog-overlay.show {
      opacity: 1;
      pointer-events: auto;
    }

    .custom-dialog {
      width: 100%;
      max-width: 420px;
      background: rgba(31, 41, 55, 0.95);
      border: 1px solid rgba(139, 92, 246, 0.35);
      border-radius: 18px;
      padding: 2rem;
      box-shadow: 0 20px 60px rgba(15, 23, 42, 0.6);
      position: relative;
      text-align: center;
      backdrop-filter: blur(18px);
    }

    .custom-dialog::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: inherit;
      border: 1px solid rgba(139, 92, 246, 0.2);
      pointer-events: none;
    }

    .custom-dialog-icon {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.25rem;
      font-size: 1.75rem;
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(248, 113, 113, 0.2));
      border: 1px solid rgba(248, 113, 113, 0.3);
      color: #fca5a5;
    }

    .custom-dialog-actions {
      display: flex;
      justify-content: center;
      gap: 1rem;
      flex-wrap: wrap;
      margin-top: 1.75rem;
    }

    .custom-dialog-message {
      color: rgba(229, 231, 235, 0.9);
      margin: 0;
      line-height: 1.6;
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

    [data-theme="light"] .text-surface-emphasis {
      color: #1f2937;
    }

    [data-theme="light"] .text-surface-soft {
      color: #475569;
    }

    [data-theme="light"] .text-surface-muted {
      color: #64748b;
    }

    [data-theme="light"] .text-surface-hint {
      color: #94a3b8;
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
      box-shadow: 0 28px 60px rgba(148, 163, 184, 0.25);
      color: #1f2937;
    }

    [data-theme="light"] .project-members-card .member-item {
      background: rgba(248, 250, 252, 0.98);
      border-color: rgba(203, 213, 225, 0.8);
    }

    [data-theme="light"] .member-avatar {
      background: linear-gradient(135deg, rgba(129, 140, 248, 0.25), rgba(59, 130, 246, 0.25));
      border-color: rgba(129, 140, 248, 0.45);
      color: #1e293b;
    }

    [data-theme="light"] .member-role-badge {
      background: rgba(59, 130, 246, 0.15);
      border-color: rgba(59, 130, 246, 0.4);
      color: #1e40af;
    }

    [data-theme="light"] .member-meta {
      color: #475569;
    }

    [data-theme="light"] .board-title {
      color: #1f2937;
    }

    [data-theme="light"] .board-divider {
      background: linear-gradient(90deg,
        rgba(129, 140, 248, 0.55) 0%,
        rgba(129, 140, 248, 0.25) 50%,
        transparent 100%);
    }

    [data-theme="light"] .board-content::-webkit-scrollbar-track {
      background: rgba(226, 232, 240, 0.65);
    }

    [data-theme="light"] .board-content::-webkit-scrollbar-thumb {
      background: rgba(129, 140, 248, 0.36);
    }

    [data-theme="light"] .board-content::-webkit-scrollbar-thumb:hover {
      background: rgba(99, 102, 241, 0.45);
    }

    [data-theme="light"] .card-item {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(203, 213, 225, 0.72);
      color: #1f2937;
      box-shadow: 0 10px 24px rgba(148, 163, 184, 0.18);
    }

    [data-theme="light"] .card-item:hover {
      background: rgba(255, 255, 255, 0.98);
      box-shadow: 0 16px 32px rgba(148, 163, 184, 0.26);
    }

    [data-theme="light"] .card-title {
      color: #111827;
    }

    [data-theme="light"] .badge-modern {
      border-color: rgba(203, 213, 225, 0.7);
    }

    [data-theme="light"] .badge-danger {
      background: rgba(239, 68, 68, 0.14);
      border-color: rgba(239, 68, 68, 0.28);
      color: #b91c1c;
    }

    [data-theme="light"] .badge-warning {
      background: rgba(251, 191, 36, 0.18);
      border-color: rgba(251, 191, 36, 0.32);
      color: #92400e;
    }

    [data-theme="light"] .badge-primary {
      background: rgba(59, 130, 246, 0.16);
      border-color: rgba(59, 130, 246, 0.3);
      color: #1d4ed8;
    }

    [data-theme="light"] .badge-success {
      background: rgba(16, 185, 129, 0.14);
      border-color: rgba(16, 185, 129, 0.28);
      color: #047857;
    }

    [data-theme="light"] .badge-info {
      background: rgba(14, 165, 233, 0.16);
      border-color: rgba(14, 165, 233, 0.32);
      color: #075985;
    }

    [data-theme="light"] .badge-todo {
      background: rgba(148, 163, 184, 0.2);
      color: #475569;
    }

    [data-theme="light"] .badge-in-progress {
      background: rgba(59, 130, 246, 0.16);
      color: #1d4ed8;
    }

    [data-theme="light"] .badge-review {
      background: rgba(14, 165, 233, 0.16);
      color: #0c4a6e;
    }

    [data-theme="light"] .badge-done {
      background: rgba(16, 185, 129, 0.16);
      color: #047857;
    }

    [data-theme="light"] .btn-modern {
      background: linear-gradient(135deg, rgba(129, 140, 248, 0.24), rgba(59, 130, 246, 0.24));
      border: 1px solid rgba(99, 102, 241, 0.35);
      color: #1e3a8a;
    }

    [data-theme="light"] .btn-modern:hover {
      box-shadow: 0 12px 28px rgba(99, 102, 241, 0.22);
      color: #1d4ed8;
    }

    [data-theme="light"] .btn-success-modern {
      background: linear-gradient(135deg, rgba(16, 185, 129, 0.24), rgba(5, 150, 105, 0.24));
      border: 1px solid rgba(16, 185, 129, 0.32);
      color: #047857;
    }

    [data-theme="light"] .btn-warning-modern {
      background: linear-gradient(135deg, rgba(251, 191, 36, 0.24), rgba(245, 158, 11, 0.24));
      border: 1px solid rgba(251, 191, 36, 0.32);
      color: #92400e;
    }

    [data-theme="light"] .btn-danger-modern {
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.22), rgba(220, 38, 38, 0.22));
      border: 1px solid rgba(239, 68, 68, 0.32);
      color: #b91c1c;
    }

    [data-theme="light"] .btn-info-modern {
      background: linear-gradient(135deg, rgba(14, 165, 233, 0.22), rgba(56, 189, 248, 0.22));
      border: 1px solid rgba(14, 165, 233, 0.34);
      color: #075985;
    }

    [data-theme="light"] .comment-form-section {
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid rgba(203, 213, 225, 0.85);
      box-shadow: 0 18px 40px rgba(148, 163, 184, 0.25);
    }

    [data-theme="light"] .comment-form .form-control {
      background: rgba(248, 250, 252, 0.96);
      border: 1px solid rgba(203, 213, 225, 0.85);
      color: #0f172a;
    }

    [data-theme="light"] .comment-form .form-control::placeholder {
      color: #94a3b8;
    }

    [data-theme="light"] .comment-form .form-control:focus {
      border-color: rgba(79, 70, 229, 0.6);
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
      background: #ffffff;
      color: #0f172a;
    }

    [data-theme="light"] .comment-form .btn-modern {
      color: #1d4ed8;
    }

    [data-theme="light"] .empty-state {
      color: #64748b;
    }

    [data-theme="light"] .empty-state i {
      color: rgba(129, 140, 248, 0.32);
    }

    [data-theme="light"] .subtask-table {
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid rgba(203, 213, 225, 0.7);
    }

    [data-theme="light"] .subtask-table th {
      background: rgba(129, 140, 248, 0.18);
      color: #312e81;
    }

    [data-theme="light"] .subtask-table td {
      border-bottom: 1px solid rgba(203, 213, 225, 0.7);
      color: #1f2937;
    }

    [data-theme="light"] .comment-section {
      background: rgba(248, 250, 252, 0.92);
      border: 1px solid rgba(203, 213, 225, 0.7);
      color: #1f2937;
    }

    [data-theme="light"] .comment {
      background: rgba(255, 255, 255, 0.9);
      border: 1px solid rgba(203, 213, 225, 0.65);
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

    [data-theme="light"] .nested-comment {
      background: rgba(241, 245, 249, 0.92) !important;
      border: 1px solid rgba(203, 213, 225, 0.6) !important;
    }

    [data-theme="light"] .nested-comment .nested-comment {
      background: rgba(229, 231, 235, 0.9) !important;
      border: 1px solid rgba(203, 213, 225, 0.5) !important;
    }

    [data-theme="light"] .replies {
      border-left-color: rgba(129, 140, 248, 0.3);
    }

    [data-theme="light"] .form-control {
      background: rgba(255, 255, 255, 0.92) !important;
      border: 1px solid rgba(203, 213, 225, 0.65);
      color: #1f2937 !important;
    }

    [data-theme="light"] .form-control::placeholder {
      color: #94a3b8;
    }

    [data-theme="light"] .form-control:focus {
      background: rgba(255, 255, 255, 0.98) !important;
      border-color: rgba(99, 102, 241, 0.5);
      color: #111827 !important;
      box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.25);
    }

    [data-theme="light"] .modal-content.glass-card {
      background: rgba(255, 255, 255, 0.96) !important;
      border: 1px solid rgba(203, 213, 225, 0.7);
    }

    [data-theme="light"] .btn-close-white {
      filter: none;
    }

    [data-theme="light"] .loading-skeleton {
      background: rgba(226, 232, 240, 0.6);
      border: 1px solid rgba(203, 213, 225, 0.6);
    }

    [data-theme="light"] .skeleton-line {
      background: linear-gradient(90deg,
        rgba(203, 213, 225, 0.45) 25%,
        rgba(226, 232, 240, 0.75) 50%,
        rgba(203, 213, 225, 0.45) 75%);
    }

    [data-theme="light"] .skeleton-avatar {
      background: rgba(203, 213, 225, 0.65);
    }

    [data-theme="light"] .custom-dialog-overlay {
      background: rgba(30, 41, 59, 0.35);
      backdrop-filter: blur(6px);
    }

    [data-theme="light"] .custom-dialog {
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid rgba(203, 213, 225, 0.65);
      box-shadow: 0 24px 60px rgba(148, 163, 184, 0.25);
    }

    [data-theme="light"] .custom-dialog::before {
      border: 1px solid rgba(129, 140, 248, 0.18);
    }

    [data-theme="light"] .custom-dialog-icon {
      background: linear-gradient(135deg, rgba(248, 113, 113, 0.22), rgba(239, 68, 68, 0.22));
      border: 1px solid rgba(239, 68, 68, 0.32);
      color: #b91c1c;
    }

    [data-theme="light"] .custom-dialog-message {
      color: #334155;
    }

    [data-theme="light"] .text-grey-custom {
      color: #475569 !important;
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
  </style>
</head>
<body>

<div class="layout-wrapper">
  <!-- Sidebar -->
  @include('components.app-sidebar', ['active' => 'dashboard'])

  <!-- Main Content Area -->
  <div class="main-content-area">
    <!-- Topbar -->
    <nav class="navbar-acrylic">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="mobile-nav-wrapper">
          <button type="button"
                  class="sidebar-toggle-btn d-lg-none"
                  data-sidebar-toggle
                  aria-label="Buka navigasi">
            <i class="bi bi-list"></i>
          </button>
          <span class="navbar-title">
            <i class="bi me-2"></i>
            <span id="page-title">Detail Proyek (Boards)</span>
          </span>
        </div>

      </div>
    </nav>

    <!-- Content -->
    <div class="content-wrapper">
      <!-- Boards Content (Default View) -->
      <div id="boards-content" class="content-section active">
        <!-- Header -->
        <div class="mb-4">
          <h1 class="text-gradient mb-2" style="font-size: 2.5rem; font-weight: 700;">{{ $project->project_name }}</h1>
          <p class="mb-2 text-surface-emphasis">{{ $project->description }}</p>
          <p class="mb-0 text-surface-soft">
            <i class="bi bi-calendar-event me-2"></i><strong>Deadline:</strong> {{ $project->deadline }}
          </p>
        </div>

        @php
          $projectFinished = ($project->status === 'selesai');
        @endphp

        @if(session('success'))
          <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif
        @if(session('info'))
          <div class="alert alert-info mt-3">{{ session('info') }}</div>
        @endif

        <!-- Project Members -->
        <div class="glass-card project-members-card">
          @php
            $memberList = $project->members->filter(function ($member) {
              return !is_null($member->user) && $member->user->role !== 'admin';
            });
          @endphp
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
              <h2 class="text-gradient mb-1">Anggota Proyek</h2>
              <p class="text-surface-soft mb-0">Daftar developer/designer dan perannya dalam proyek ini.</p>
            </div>
            <span class="badge-acrylic">{{ $memberList->count() }} Anggota</span>
          </div>

          @if($memberList->isEmpty())
            <div class="empty-state py-4">
              <i class="bi bi-people"></i>
              <p class="mt-2 mb-0">Belum ada anggota tim yang ditambahkan.</p>
              <small class="text-muted">Tambahkan anggota melalui menu manajemen proyek.</small>
            </div>
          @else
            <div class="row g-3">
              @foreach($memberList as $projectMember)
                @php
                  $user = $projectMember->user;
                  $displayName = $user->full_name ?: $user->username;
                  $systemRoleLabel = ucwords(str_replace('_', ' ', $user->role));
                  $projectRoleLabel = ucwords(str_replace('_', ' ', $projectMember->role));
                  $statusLabel = $user->current_task_status
                    ? ucwords(str_replace('_', ' ', $user->current_task_status))
                    : 'Belum diatur';
                  $joinedAt = $projectMember->joined_at
                    ? \Carbon\Carbon::parse($projectMember->joined_at)->format('d M Y')
                    : 'Belum tercatat';
                @endphp
                <div class="col-md-6 col-xl-4">
                  <div class="member-item">
                    <div class="member-avatar">
                      <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                          <h5 class="mb-0 text-surface-strong">{{ $displayName }}</h5>
                          <small class="text-surface-muted">{{ '@'. $user->username }}</small>
                        </div>
                        <span class="member-role-badge">{{ $systemRoleLabel }}</span>
                      </div>
                      <div class="member-meta mt-2">
                        <span><i class="bi bi-diagram-3"></i>Role Proyek: {{ $projectRoleLabel }}</span>
                        <span><i class="bi bi-lightning-charge"></i>Status: {{ $statusLabel }}</span>
                        <span><i class="bi bi-calendar-check"></i>Gabung: {{ $joinedAt }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>

        <!-- Action Buttons - Komentar dan Tambah Tugas -->
        <div class="action-buttons">
          <a href="{{ route('teamlead.dashboard') }}" class="btn-modern btn-sm">
            <i class="bi bi-speedometer2 me-2"></i>Kembali ke Dashboard
          </a>
          @if(auth()->user()->role == 'team_lead')
            @php
              $todoBoard = $project->boards->where('board_name', 'To Do')->first();
              // Ambil data members untuk form create
              $members = \App\Models\User::whereIn('role', ['developer', 'designer'])
                  ->whereIn('user_id', function($q) use ($project) {
                      $q->select('user_id')
                        ->from('project_members')
                        ->where('project_id', $project->project_id)
                        ->where('role', 'member');
                  })
                  ->orderBy('username')
                  ->get();
            @endphp
            @if($todoBoard && !$projectFinished)
              <button class="btn-modern btn-success-modern" id="create-card-btn">
                <i class="bi bi-plus-circle me-2"></i>Tambah Tugas Baru
              </button>
            @endif
            @if($projectFinished)
              <span class="badge-acrylic">Proyek selesai</span>
            @elseif($canComplete)
              <button class="btn-modern btn-danger-modern"
                      id="finish-project-btn"
                      data-action-sheet-trigger="true"
                      data-action-form="#finishProjectForm"
                      data-action-title="Selesaikan Proyek"
                      data-action-subtitle="{{ $project->project_name }}"
                      data-action-message="Tandai proyek ini selesai? Semua developer/designer akan otomatis berstatus idle."
                      data-action-confirm-label="Tandai Selesai"
                      data-action-loading-label="Memproses...">
                <i class="bi bi-flag-fill me-2"></i>Selesaikan Proyek
              </button>
            @endif
          @endif
        </div>

        <form id="finishProjectForm" action="{{ route('teamlead.projects.complete', $project->project_id) }}" method="POST" class="d-none">
          @csrf
        </form>

        <!-- Boards Container - VERTIKAL KE BAWAH -->
        <div class="boards-container">
          @foreach($project->boards as $board)
            <div class="board-section" id="board-{{ $board->board_id }}">
              <!-- Board Header dengan judul dan garis -->
              <div class="board-header">
                <h3 class="board-title">{{ $board->board_name }}</h3>
                <hr class="board-divider">
              </div>

              <!-- Board Content dengan Scroll Horizontal -->
              <div class="board-content">
                @forelse($board->cards as $card)
                  <div class="card-item" 
                       data-card-id="{{ $card->card_id }}" 
                       data-board-name="{{ strtolower($board->board_name) }}"
                       data-card-title="{{ $card->card_title }}"
                       data-card-description="{{ $card->description ?? 'Tidak ada deskripsi' }}"
                       data-card-priority="{{ $card->priority }}"
                       data-card-status="{{ $card->status }}"
                       data-card-estimation="{{ $card->estimated_hours ?? $card->estimation ?? 'Belum diatur' }}"
                       data-card-deadline="{{ $card->due_date ?? '-' }}"
                       data-card-assignments="{{ $card->assignments->map(function($a){ 
                         $user = $a->user ?? \App\Models\User::find($a->user_id);
                         return [
                         'assignment_id' => $a->assignment_id,
                         'user' => $user ? [
                           'user_id' => $user->user_id,
                           'username' => $user->username,
                           'full_name' => $user->full_name
                         ] : null,
                         'assignment_status' => $a->assignment_status
                       ]; })->toJson() }}"
                       data-card-subtasks="{{ $card->subtasks->toJson() }}">
                    <!-- Card Header dengan prioritas tidak nabrak -->
                    <div class="card-header">
                      <strong class="card-title">{{ $card->card_title }}</strong>
                      <span class="badge-modern priority-badge
                        @if($card->priority == 'high') badge-danger
                        @elseif($card->priority == 'medium') badge-warning
                        @else badge-primary @endif">
                        {{ ucfirst($card->priority) }}
                      </span>
                    </div>
                    
                    <div class="mb-3">
                      <small class="text-surface-soft">
                        Estimasi: {{ $card->estimated_hours ?? $card->estimation ?? '-' }} jam
                      </small>
                    </div>

                    <div class="btn-modern btn-sm w-100 view-card-btn">
                      <i class="bi bi-info-circle me-1"></i>
                      @if(strtolower($board->board_name) == 'review')
                        Review
                      @else
                        Detail
                      @endif
                    </div>
                  </div>
                @empty
                  <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p class="mt-2 mb-0">Belum ada tugas</p>
                  </div>
                @endforelse
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Detail/Review Content -->
      <div id="detail-content" class="content-section">
        <!-- Content will be dynamically loaded here -->
        <div id="detail-content-body"></div>
      </div>

      <!-- Tambah Card Content -->
      <div id="create-card-content" class="content-section">
        <div class="glass-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-gradient mb-0">Buat Card Baru</h2>
            <button class="btn-modern" id="back-to-boards-from-create">
              <i class="bi bi-arrow-left me-2"></i>Kembali ke Boards
            </button>
          </div>

          <form id="create-card-form" data-board-id="{{ $todoBoard->board_id ?? '' }}">
            @csrf
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label text-white">Judul Card</label>
                  <input type="text" name="card_title" class="form-control" required>
                </div>

                <div class="mb-3">
                  <label class="form-label text-white">Deskripsi</label>
                  <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                  <label class="form-label text-white">Prioritas</label>
                  <select name="priority" class="form-control" required>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label text-white">Estimasi Jam</label>
                  <input type="number" name="estimated_hours" class="form-control" min="0" step="0.5">
                </div>

                <div class="mb-3">
                  <label class="form-label text-white">Deadline</label>
                  <input type="date" name="due_date" class="form-control">
                </div>

                <div class="mb-3">
                  <label class="form-label text-white">Assign ke Anggota Proyek</label>
                  <input type="text" class="form-control mb-2" id="assignMemberSearch" placeholder="Cari anggota...">
                  <select name="username" id="assignMemberSelect" class="form-control" required>
                    <option value="">-- Pilih Anggota --</option>
                    @foreach ($members as $member)
                      <option value="{{ $member->username }}" data-status="{{ trim(strtolower($member->current_task_status)) }}"
                              data-role="{{ trim(strtolower($member->role)) }}">
                        {{ $member->username }} ({{ ucfirst(trim($member->role)) }}) - {{ ucfirst(trim($member->current_task_status)) }}
                      </option>
                    @endforeach
                  </select>
                  <small class="text-muted text-grey-custom">Menampilkan developer/designer berstatus member pada proyek ini.</small>
                  <div class="alert alert-info mt-2 {{ $members->isEmpty() ? '' : 'd-none' }}" id="assignMemberEmptyState">
                    Belum ada developer/designer yang ditambahkan sebagai member proyek ini.
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn-modern btn-success-modern">
                <i class="bi bi-plus-circle me-2"></i>Buat Card
              </button>
              <button type="button" class="btn-modern" id="cancel-create-card">
                <i class="bi bi-x-circle me-2"></i>Batal
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Custom Error Dialog -->
<div class="custom-dialog-overlay d-none" id="error-dialog">
  <div class="custom-dialog">
    <div class="custom-dialog-icon">
      <i class="bi bi-exclamation-octagon"></i>
    </div>
    <h4 class="text-gradient mb-3" id="error-dialog-title">Terjadi Kesalahan</h4>
    <p class="custom-dialog-message" id="error-dialog-message"></p>
    <div class="custom-dialog-actions">
      <button type="button" class="btn-modern btn-danger-modern" id="error-dialog-close">
        <i class="bi bi-x-circle me-2"></i>Mengerti
      </button>
    </div>
  </div>
</div>

<div id="subtaskActionSheet" class="bottom-sheet hidden" aria-hidden="true">
  <div class="bottom-sheet__overlay" data-sheet-dismiss="action"></div>
  <div class="bottom-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="subtaskActionTitle">
    <div class="bottom-sheet__handle"></div>
    <div class="bottom-sheet__header">
      <div>
        <p class="bottom-sheet__subtitle mb-1" id="subtaskActionSubtitle">Subtask</p>
        <h3 class="bottom-sheet__title mb-0" id="subtaskActionTitle">Setujui Subtask</h3>
      </div>
      <button type="button" class="bottom-sheet__close" data-sheet-dismiss="action" aria-label="Tutup">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <form id="subtaskActionForm">
      <div class="mb-3 d-none" id="subtaskActionReasonGroup">
        <label class="form-label text-white" for="subtaskActionReason">Alasan Ulangi</label>
        <textarea id="subtaskActionReason" class="form-control" rows="3" placeholder="Jelaskan alasan ulangi..." name="reason"></textarea>
      </div>
      <div class="bottom-sheet__footer">
        <button type="button" class="btn btn-outline-secondary" data-sheet-dismiss="action">Batal</button>
        <button type="submit" class="btn-modern btn-success-modern" id="subtaskActionSubmit">Setujui</button>
      </div>
    </form>
  </div>
</div>

<div id="subtaskCommentSheet" class="bottom-sheet hidden" aria-hidden="true">
  <div class="bottom-sheet__overlay" data-sheet-dismiss="comment"></div>
  <div class="bottom-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="subtaskCommentTitle">
    <div class="bottom-sheet__handle"></div>
    <div class="bottom-sheet__header">
      <div>
        <p class="bottom-sheet__subtitle mb-1" id="subtaskCommentSubtitle">Subtask</p>
        <h3 class="bottom-sheet__title mb-0" id="subtaskCommentTitle">Komentar Subtask</h3>
      </div>
      <button type="button" class="bottom-sheet__close" data-sheet-dismiss="comment" aria-label="Tutup">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="bottom-sheet__body">
      <form id="subtaskCommentForm" class="mb-3">
        <textarea class="form-control mb-3" rows="3" placeholder="Tulis komentar tentang subtask ini..." required id="subtaskCommentInput" name="comment_text"></textarea>
        <div class="bottom-sheet__footer justify-content-end p-0">
          <button type="submit" class="btn-modern btn-success-modern" id="subtaskCommentSubmit">
            <i class="bi bi-send me-1"></i>Kirim Komentar
          </button>
        </div>
      </form>
      <div class="comments-scroll" id="subtaskCommentList">
        <div class="text-center text-surface-muted">
          <span class="loading-spinner"></span> Memuat komentar...
        </div>
      </div>
    </div>
  </div>
</div>

@include('components.action-bottom-sheet')
@include('partials.profile-quick-sheet')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Elements
  const boardsContent = document.getElementById('boards-content');
  const detailContent = document.getElementById('detail-content');
  const createCardContent = document.getElementById('create-card-content');
  const detailContentBody = document.getElementById('detail-content-body');
  const createCardBtn = document.getElementById('create-card-btn');
  const backToBoardsFromCreateBtn = document.getElementById('back-to-boards-from-create');
  const cancelCreateCardBtn = document.getElementById('cancel-create-card');
  const createCardForm = document.getElementById('create-card-form');
  const assignMemberSelect = document.getElementById('assignMemberSelect');
  const assignMemberSearch = document.getElementById('assignMemberSearch');
  const pageTitle = document.getElementById('page-title');
  const assignMemberEmptyState = document.getElementById('assignMemberEmptyState');
  const errorDialog = document.getElementById('error-dialog');
  const errorDialogTitle = document.getElementById('error-dialog-title');
  const errorDialogMessage = document.getElementById('error-dialog-message');
  const errorDialogClose = document.getElementById('error-dialog-close');
  const body = document.body;
  const subtaskActionSheet = document.getElementById('subtaskActionSheet');
  const subtaskActionForm = document.getElementById('subtaskActionForm');
  const subtaskActionTitle = document.getElementById('subtaskActionTitle');
  const subtaskActionSubtitle = document.getElementById('subtaskActionSubtitle');
  const subtaskActionReasonGroup = document.getElementById('subtaskActionReasonGroup');
  const subtaskActionReasonInput = document.getElementById('subtaskActionReason');
  const subtaskActionSubmit = document.getElementById('subtaskActionSubmit');
  const subtaskCommentSheet = document.getElementById('subtaskCommentSheet');
  const subtaskCommentTitle = document.getElementById('subtaskCommentTitle');
  const subtaskCommentSubtitle = document.getElementById('subtaskCommentSubtitle');
  const subtaskCommentForm = document.getElementById('subtaskCommentForm');
  const subtaskCommentTextarea = document.getElementById('subtaskCommentInput');
  const subtaskCommentSubmit = document.getElementById('subtaskCommentSubmit');
  const subtaskCommentList = document.getElementById('subtaskCommentList');
  let errorDialogHideTimeout = null;
  
  let currentView = 'boards';
  
  // Error dialog helpers
  function showErrorDialogOverlay() {
    if (!errorDialog) return;
    if (errorDialogHideTimeout) {
      clearTimeout(errorDialogHideTimeout);
      errorDialogHideTimeout = null;
    }
    errorDialog.classList.remove('d-none');
    requestAnimationFrame(() => {
      errorDialog.classList.add('show');
      if (errorDialogClose) {
        errorDialogClose.focus();
      }
    });
  }

  function hideErrorDialogOverlay() {
    if (!errorDialog) return;
    errorDialog.classList.remove('show');
    errorDialogHideTimeout = setTimeout(() => {
      errorDialog.classList.add('d-none');
      errorDialogHideTimeout = null;
    }, 250);
  }

  function displayError(message) {
    const cleaned = (message ?? 'Terjadi kesalahan.').toString().replace(/^(?:❌\s*)+/g, '').trim();
    if (!errorDialog || !errorDialogMessage) {
      alert('❌ ' + cleaned);
      return;
    }

    const busyMessageRegex = /sedang ada tugas lain/i;
    if (errorDialogTitle) {
      errorDialogTitle.textContent = busyMessageRegex.test(cleaned)
        ? 'Anggota Sedang Ada Tugas Lain'
        : 'Terjadi Kesalahan';
    }

    errorDialogMessage.textContent = cleaned;
    showErrorDialogOverlay();
  }

  if (errorDialogClose) {
    errorDialogClose.addEventListener('click', hideErrorDialogOverlay);
  }

  if (errorDialog) {
    errorDialog.addEventListener('click', function(e) {
      if (e.target === errorDialog) {
        hideErrorDialogOverlay();
      }
    });
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && errorDialog && errorDialog.classList.contains('show')) {
      hideErrorDialogOverlay();
    }
  });

  // Bottom sheet helpers
  const activeSheetSelector = '.bottom-sheet.bottom-sheet--active';

  function openBottomSheet(sheet) {
    if (!sheet) return;
    sheet.classList.remove('hidden');
    requestAnimationFrame(() => sheet.classList.add('bottom-sheet--active'));
    body.classList.add('bottom-sheet-open');
  }

  function closeBottomSheet(sheet) {
    if (!sheet || sheet.classList.contains('hidden')) return;
    sheet.classList.remove('bottom-sheet--active');
    setTimeout(() => {
      sheet.classList.add('hidden');
      if (!document.querySelector(activeSheetSelector)) {
        body.classList.remove('bottom-sheet-open');
      }
    }, 220);
  }

  function openSubtaskActionSheet({ subtaskId, title = 'Subtask', mode = 'approve' }) {
    if (!subtaskActionSheet || !subtaskActionForm || !subtaskActionSubmit) return;
    const isReject = mode === 'reject';

    subtaskActionSheet.dataset.subtaskId = subtaskId;
    subtaskActionSheet.dataset.mode = mode;
    subtaskActionForm.dataset.subtaskId = subtaskId;
    subtaskActionForm.dataset.mode = mode;
    subtaskActionTitle.textContent = isReject ? 'Ulangi Subtask' : 'Setujui Subtask';
    subtaskActionSubtitle.textContent = title;

    if (subtaskActionReasonGroup) {
      subtaskActionReasonGroup.classList.toggle('d-none', !isReject);
    }

    if (subtaskActionReasonInput) {
      subtaskActionReasonInput.value = '';
      subtaskActionReasonInput.required = isReject;
    }

    subtaskActionSubmit.classList.remove('btn-success-modern', 'btn-danger-modern');
    subtaskActionSubmit.classList.add(isReject ? 'btn-danger-modern' : 'btn-success-modern');
    subtaskActionSubmit.innerHTML = isReject ? 'Ulangi' : 'Setujui';
    subtaskActionSubmit.disabled = false;

    openBottomSheet(subtaskActionSheet);
  }

  function closeSubtaskActionSheet() {
    closeBottomSheet(subtaskActionSheet);
    if (subtaskActionReasonInput) {
      subtaskActionReasonInput.value = '';
    }
  }

  function openSubtaskCommentSheet(subtaskId, title = 'Subtask') {
    if (!subtaskCommentSheet || !subtaskCommentForm || !subtaskCommentList) return;
    subtaskCommentSheet.dataset.subtaskId = subtaskId;
    subtaskCommentForm.dataset.subtaskId = subtaskId;
    subtaskCommentTitle.textContent = 'Komentar Subtask';
    subtaskCommentSubtitle.textContent = title;
    if (subtaskCommentTextarea) {
      subtaskCommentTextarea.value = '';
      subtaskCommentTextarea.focus();
    }
    openBottomSheet(subtaskCommentSheet);
    loadSubtaskComments(subtaskId);
  }

  function closeSubtaskCommentSheet() {
    closeBottomSheet(subtaskCommentSheet);
  }

  document.querySelectorAll('[data-sheet-dismiss="action"]').forEach(trigger => {
    trigger.addEventListener('click', closeSubtaskActionSheet);
  });

  document.querySelectorAll('[data-sheet-dismiss="comment"]').forEach(trigger => {
    trigger.addEventListener('click', closeSubtaskCommentSheet);
  });

  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
      if (subtaskCommentSheet && subtaskCommentSheet.classList.contains('bottom-sheet--active')) {
        closeSubtaskCommentSheet();
      } else if (subtaskActionSheet && subtaskActionSheet.classList.contains('bottom-sheet--active')) {
        closeSubtaskActionSheet();
      }
    }
  });

  if (subtaskActionForm) {
    subtaskActionForm.addEventListener('submit', handleSubtaskActionSubmit);
  }

  if (subtaskCommentForm) {
    subtaskCommentForm.addEventListener('submit', handleSubtaskCommentSubmit);
  }

  if (assignMemberSelect && assignMemberSearch) {
    const originalAssignOptions = Array.from(assignMemberSelect.options);
    const allowedAssignRoles = ['developer', 'designer'];
    const filterAssignOptions = () => {
      const term = assignMemberSearch.value.trim().toLowerCase();
      let visibleCount = 0;
      originalAssignOptions.forEach(option => {
        if (option.value === '') {
          option.hidden = false;
          option.disabled = false;
          return;
        }
        const role = (option.dataset.role || '').trim().toLowerCase();
        const matchesRequirement = allowedAssignRoles.includes(role);
        const matchesSearch = option.text.toLowerCase().includes(term);

        option.disabled = !matchesRequirement;
        option.hidden = !matchesRequirement || (term.length > 0 && !matchesSearch);

        if (!option.hidden && !option.disabled) {
          visibleCount++;
        }
      });

      if (assignMemberEmptyState) {
        assignMemberEmptyState.classList.toggle('d-none', visibleCount > 0);
      }
    };

    assignMemberSearch.addEventListener('input', filterAssignOptions);
    filterAssignOptions();
  }
  
  // Show boards view
  function showBoardsView() {
    console.log('Showing boards view');
    hideAllViews();
    boardsContent.classList.add('active');
    pageTitle.textContent = 'Detail Proyek (Boards)';
    currentView = 'boards';
  }
  
  // Show detail view
  function showDetailView(isReview = false) {
    console.log('Showing detail view');
    hideAllViews();
    detailContent.classList.add('active');
    pageTitle.textContent = isReview ? 'Review Card' : 'Detail Card';
    window.scrollTo({ top: 0, behavior: 'auto' });
    currentView = 'detail';
  }
  
  // Show create card view
  function showCreateCardView() {
    console.log('Showing create card view');
    hideAllViews();
    createCardContent.classList.add('active');
    pageTitle.textContent = 'Buat Card Baru';
    currentView = 'create';
    
  // Reset form
  createCardForm.reset();
  if (assignMemberSearch) {
    assignMemberSearch.value = '';
    assignMemberSearch.dispatchEvent(new Event('input'));
  }
}
  
  // Hide all views
  function hideAllViews() {
    boardsContent.classList.remove('active');
    detailContent.classList.remove('active');
    createCardContent.classList.remove('active');
  }
  
  // Generate loading skeleton untuk komentar
  function generateCommentSkeletons(count = 3) {
    let skeletons = '';
    for (let i = 0; i < count; i++) {
      skeletons += `
        <div class="loading-skeleton">
          <div class="skeleton-header">
            <div class="skeleton-avatar"></div>
            <div style="flex: 1;">
              <div class="skeleton-line short"></div>
              <div class="skeleton-line medium" style="width: 40%;"></div>
            </div>
          </div>
          <div class="skeleton-line"></div>
          <div class="skeleton-line"></div>
          <div class="skeleton-line" style="width: 70%;"></div>
        </div>
      `;
    }
    return skeletons;
  }

  // Show loading state
  function showCommentsLoading(containerId, count = 3) {
    const container = document.getElementById(containerId);
    if (container) {
      container.innerHTML = generateCommentSkeletons(count);
      container.classList.add('comments-loading');
    }
  }

  // Hide loading state
  function hideCommentsLoading(containerId) {
    const container = document.getElementById(containerId);
    if (container) {
      container.classList.remove('comments-loading');
    }
  }

  // Event listener untuk tombol create card
  if (createCardBtn) {
    createCardBtn.addEventListener('click', function(e) {
      e.preventDefault();
      console.log('Create card button clicked');
      showCreateCardView();
    });
  }

  // Event listener untuk kembali dari create view
  backToBoardsFromCreateBtn.addEventListener('click', function(e) {
    e.preventDefault();
    console.log('Back from create clicked');
    showBoardsView();
  });

  // Event listener untuk batal create
  cancelCreateCardBtn.addEventListener('click', function(e) {
    e.preventDefault();
    console.log('Cancel create clicked');
    showBoardsView();
  });
  
  // FIXED: Event delegation untuk card items dan buttons
  document.addEventListener('click', function(e) {
    // Handle view-card-btn clicks
    if (e.target.closest('.view-card-btn')) {
      e.preventDefault();
      const cardItem = e.target.closest('.card-item');
      if (!cardItem) return;
      
      const cardId = cardItem.dataset.cardId;
      const boardName = cardItem.dataset.boardName;
      const isReview = boardName === 'review';
      
      console.log('Card clicked:', cardId, 'Review mode:', isReview);
      
      showDetailView(isReview);
      loadCardContent(cardItem, isReview);
      return;
    }
    
    // Handle direct card item clicks (jika user klik area card)
    if (e.target.closest('.card-item') && !e.target.closest('.view-card-btn')) {
      e.preventDefault();
      const cardItem = e.target.closest('.card-item');
      if (!cardItem) return;
      
      const cardId = cardItem.dataset.cardId;
      const boardName = cardItem.dataset.boardName;
      const isReview = boardName === 'review';
      
      console.log('Card item clicked:', cardId, 'Review mode:', isReview);
      
      showDetailView(isReview);
      loadCardContent(cardItem, isReview);
      return;
    }
  });
  
  // Handle form submission create card
  createCardForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    console.log('Create card form submitted');
    
    const formData = new FormData(createCardForm);
    const boardId = createCardForm.dataset.boardId;
    
    const submitBtn = createCardForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading-spinner"></span> Membuat Card...';
    submitBtn.disabled = true;
    
    try {
      const response = await fetch(`/teamlead/boards/${boardId}/cards`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: formData
      });
      
      const result = await response.json();
      
      if (response.ok) {
        // Success
        submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Berhasil!';
        setTimeout(() => {
          showBoardsView();
          // Refresh halaman untuk menampilkan card baru
          location.reload();
        }, 1500);
      } else {
        // Error
        console.error('Error creating card:', result);
        let errorMessage = 'Gagal membuat card';
        if (result.errors) {
          errorMessage = Object.values(result.errors).flat().join(', ');
        } else if (result.message) {
          errorMessage = result.message;
        }
        displayError(errorMessage);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    } catch (error) {
      console.error('Network error:', error);
      displayError('Terjadi kesalahan jaringan. Silakan coba lagi.');
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }
  });

  // Show warning when selecting user yang sedang working
  // Load card content function
  function loadCardContent(cardItem, isReview) {
    console.log('Loading card content, isReview:', isReview);
    
    const cardData = {
      id: cardItem.dataset.cardId,
      title: cardItem.dataset.cardTitle,
      description: cardItem.dataset.cardDescription,
      priority: cardItem.dataset.cardPriority,
      status: cardItem.dataset.cardStatus,
      estimation: cardItem.dataset.cardEstimation,
      deadline: cardItem.dataset.cardDeadline,
      assignments: JSON.parse(cardItem.dataset.cardAssignments || '[]'),
      subtasks: JSON.parse(cardItem.dataset.cardSubtasks || '[]')
    };
    
    console.log('Card data:', cardData);
    
    let content = '';
    
    if (isReview) {
      content = generateReviewContent(cardData);
    } else {
      content = generateDetailContent(cardData);
    }
    
    detailContentBody.innerHTML = content;
    // Ensure the detail view stays scrolled to top when opened
    try {
      // blur any active element so a textarea won't steal focus and cause scrolling
      if (document.activeElement && document.activeElement !== document.body) {
        document.activeElement.blur();
      }
      // scroll the detail content container to top
      if (detailContentBody.scrollTop !== undefined) detailContentBody.scrollTop = 0;
    } catch (err) {
      console.warn('Could not reset scroll/focus for detail view', err);
    }
    
    // Load comments untuk card ini dengan loading animation
    loadCardComments(cardData.id);
    
    // Attach event listeners for dynamic elements
    attachDynamicEventListeners();
  }
  
  // Load card comments dengan loading
  async function loadCardComments(cardId) {
    const containerId = `comments-container-card-${cardId}`;
    
    // Tampilkan loading skeleton
    showCommentsLoading(containerId, 2);
    
    try {
      console.log('Loading comments for card:', cardId);
      
      // Simulasi loading delay untuk demo
      await new Promise(resolve => setTimeout(resolve, 800));
      
      const response = await fetch(`/comments/card/${cardId}`);
      if (response.ok) {
        const comments = await response.json();
        console.log('Comments loaded:', comments);
        
        const container = document.getElementById(containerId);
        if (container) {
          if (comments && comments.length > 0) {
            container.innerHTML = generateCommentsHTML(comments, cardId, 'card');
          } else {
            container.innerHTML = generateEmptyState();
          }
        }
      } else {
        console.error('Failed to load comments, status:', response.status);
        throw new Error('Failed to fetch comments');
      }
    } catch (error) {
      console.error('Error loading comments:', error);
      const container = document.getElementById(containerId);
      if (container) {
        container.innerHTML = `
          <div class="empty-state">
            <i class="bi bi-exclamation-triangle"></i>
            <p class="mt-2 mb-0">Gagal memuat komentar</p>
            <small class="text-muted">Silakan refresh halaman</small>
          </div>
        `;
      }
    } finally {
      hideCommentsLoading(containerId);
    }
  }
  
  // Generate empty state
  function generateEmptyState() {
    return `
      <div class="empty-state">
        <i class="bi bi-chat"></i>
        <p class="mt-2 mb-0">Belum ada komentar</p>
        <small class="text-muted text-grey-custom">Jadilah yang pertama memberikan komentar</small>
      </div>
    `;
  }
  
  // Generate HTML untuk komentar dengan nested replies
  function generateCommentsHTML(comments, entityId, type = 'project', level = 0) {
    if (!comments || comments.length === 0) return level === 0 ? generateEmptyState() : '';
    
    return comments.map(comment => {
      const nestedClass = level > 0 ? 'nested-comment' : '';
      const repliesHTML = comment.replies && comment.replies.length > 0 
        ? `<div class="replies mt-3 ms-4" id="replies-${comment.comment_id}">
             ${generateCommentsHTML(comment.replies, entityId, type, level + 1)}
           </div>`
        : `<div class="replies mt-3 ms-4" id="replies-${comment.comment_id}"></div>`;
      
      return `
        <div class="comment ${nestedClass}" id="comment-${comment.comment_id}" data-level="${level}">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <strong>${comment.user?.full_name || 'Unknown'}</strong>
            <small>${new Date(comment.created_at).toLocaleDateString('id-ID', { 
              year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
            })}</small>
          </div>
          <p class="mb-2">${escapeHtml(comment.comment_text)}</p>
          
          <button class="btn-modern btn-sm reply-toggle" data-parent="${comment.comment_id}">
            <i class="bi bi-reply me-1"></i>Balas
          </button>

          <form class="reply-form mt-2 d-none" 
                data-${type}-id="${entityId}" 
                data-parent="${comment.comment_id}">
            @csrf
            <div class="mb-2">
              <textarea name="comment_text" class="form-control" rows="2" placeholder="Tulis balasan..." required></textarea>
            </div>
            <div class="d-flex align-items-center">
              <button type="submit" class="btn-modern btn-sm">
                <i class="bi bi-send me-1"></i> Kirim Balasan
              </button>
              <button type="button" class="btn-modern btn-sm ms-2 reply-cancel">Batal</button>
            </div>
          </form>

          ${repliesHTML}
        </div>
      `;
    }).join('');
  }
  
  // Generate review content
  function generateReviewContent(card) {
    const subtasksHTML = card.subtasks.length > 0 ? generateSubtasksTable(card.subtasks, true) : '<p class="text-muted">Belum ada subtask</p>';
    
    return `
      <div class="glass-card">
        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <h2 class="text-gradient mb-0">${escapeHtml(card.title)}</h2>
            <button type="button" class="btn-modern btn-sm detail-back-btn">
              <i class="bi bi-kanban me-2"></i>Kembali ke Boards
            </button>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <p><strong>Deskripsi:</strong> ${escapeHtml(card.description)}</p>
              <p><strong>Prioritas:</strong> ${escapeHtml(card.priority.charAt(0).toUpperCase() + card.priority.slice(1))}</p>
              <p><strong>Status:</strong> ${escapeHtml(card.status.charAt(0).toUpperCase() + card.status.slice(1))}</p>
            </div>
            <div class="col-md-6">
              <p><strong>Estimasi Waktu:</strong> ${escapeHtml(card.estimation)} jam</p>
              <p><strong>Deadline:</strong> ${escapeHtml(card.deadline)}</p>
              <p><strong>Penanggung Jawab:</strong><br>
                ${(() => {
                  // Tampilkan hanya assignee yang masih aktif (bukan completed)
                  const active = (card.assignments || []).filter(a => a && a.assignment_status && a.assignment_status !== 'completed');
                  if (active.length > 0) {
                    return active.map(a => `<span class="badge-modern badge-info">${escapeHtml(a.user?.username || a.user?.full_name || 'Unknown')}</span>`).join(' ');
                  }
                  return '<span class="text-surface-muted">Belum ada</span>';
                })()}
              </p>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-gradient mb-0">Subtasks</h4>
          </div>
          ${subtasksHTML}
        </div>

        <div class="comment-section">
          <h5 class="text-gradient mb-3">Komentar Card</h5>
          <form class="comment-form mb-3" data-card-id="${card.id}">
            @csrf
            <textarea name="comment_text" class="form-control mb-2" rows="2" placeholder="Tulis komentar..." required></textarea>
            <button type="submit" class="btn-modern btn-sm"><i class="bi bi-send me-1"></i> Kirim</button>
          </form>
          <div class="comments-container" id="comments-container-card-${card.id}"></div>
        </div>
      </div>
    `;
  }
  
  // Generate detail content
  function generateDetailContent(card) {
    const subtasksHTML = card.subtasks.length > 0 ? generateSubtasksTable(card.subtasks, false) : '<p class="text-muted">Belum ada subtask</p>';
    
    return `
      <div class="glass-card">
        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <h2 class="text-gradient mb-0">${escapeHtml(card.title)}</h2>
            <button type="button" class="btn-modern btn-sm detail-back-btn">
              <i class="bi bi-kanban me-2"></i>Kembali ke Boards
            </button>
          </div>
          <div class="row">
            <div class="col-md-6">
              <p><strong>Deskripsi:</strong> ${escapeHtml(card.description)}</p>
              <p><strong>Prioritas:</strong> ${escapeHtml(card.priority.charAt(0).toUpperCase() + card.priority.slice(1))}</p>
              <p><strong>Status:</strong> ${escapeHtml(card.status.charAt(0).toUpperCase() + card.status.slice(1))}</p>
            </div>
            <div class="col-md-6">
              <p><strong>Estimasi Waktu:</strong> ${escapeHtml(card.estimation)} jam</p>
              <p><strong>Deadline:</strong> ${escapeHtml(card.deadline)}</p>
              <p><strong>Penanggung Jawab:</strong><br>
                ${(() => {
                  const active = (card.assignments || []).filter(a => a && a.assignment_status && a.assignment_status !== 'completed');
                  if (active.length > 0) {
                    return active.map(a => `<span class="badge-modern badge-info">${escapeHtml(a.user?.username || a.user?.full_name || 'Unknown')}</span>`).join(' ');
                  }
                  return '<span class="text-surface-muted">Belum ada</span>';
                })()}
              </p>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-gradient mb-0">Subtasks</h4>
          </div>
          ${subtasksHTML}
        </div>

        <div class="comment-section">
          <h5 class="text-gradient mb-3">Komentar Card</h5>
          <form class="comment-form mb-3" data-card-id="${card.id}">
            @csrf
            <textarea name="comment_text" class="form-control mb-2" rows="2" placeholder="Tulis komentar..." required></textarea>
            <button type="submit" class="btn-modern btn-sm"><i class="bi bi-send me-1"></i> Kirim</button>
          </form>
          <div class="comments-container" id="comments-container-card-${card.id}"></div>
        </div>
      </div>
    `;
  }
  
  // Generate subtasks table
  function generateSubtasksTable(subtasks, showApproval = false) {
    const headers = showApproval 
      ? '<tr><th>Subtask</th><th>Deskripsi</th><th>Estimasi</th><th>Aktual</th><th>Status</th><th>Aksi</th></tr>'
      : '<tr><th>Subtask</th><th>Deskripsi</th><th>Estimasi</th><th>Aktual</th><th>Status</th><th>Aksi</th></tr>';
    
    const rows = subtasks.map(subtask => {
        const statusBadge = getStatusBadge(subtask.status);
        const rejectReason = subtask.reject_reason ? `<div><small class="text-danger">❌ ${escapeHtml(subtask.reject_reason)}</small></div>` : '';
        
        // Untuk detail (non-review), selalu tampilkan tombol komentar
        const actionColumn = showApproval 
            ? generateReviewActionColumn(subtask)
            : generateDetailActionColumn(subtask);
        
        return `
            <tr>
                <td>${escapeHtml(subtask.subtask_title)}</td>
                <td>${escapeHtml(subtask.description || '-')}</td>
                <td>${escapeHtml(subtask.estimated_hours || '-')}</td>
                <td>${escapeHtml(subtask.actual_hours || '-')}</td>
                <td>${statusBadge}${rejectReason}</td>
                <td>${actionColumn}</td>
            </tr>
        `;
    }).join('');
    
    return `
        <div class="table-responsive">
            <table class="subtask-table">
                <thead>${headers}</thead>
                <tbody>${rows}</tbody>
            </table>
        </div>
    `;
  }

  // Generate action column untuk detail (non-review)
  function generateDetailActionColumn(subtask) {
    return `
        <div class="d-flex flex-column gap-2">
            <button type="button" class="btn-modern btn-info-modern btn-sm subtask-comments-btn" 
                    data-subtask-id="${subtask.subtask_id}"
                    data-subtask-title="${escapeHtml(subtask.subtask_title)}">
                💬 Komentar
            </button>
        </div>
    `;
  }

  // Generate action column for review
  function generateReviewActionColumn(subtask) {
    if (subtask.status !== 'review') {
        // Jika bukan status review, tetap tampilkan tombol komentar
        return generateDetailActionColumn(subtask);
    }
    
    return `
        <div class="d-flex flex-column gap-2">
            <button type="button" class="btn-modern btn-success-modern btn-sm subtask-approve-btn" 
                    data-subtask-id="${subtask.subtask_id}"
                    data-subtask-title="${escapeHtml(subtask.subtask_title)}">
                ✅ Approve
            </button>
            <button type="button" class="btn-modern btn-danger-modern btn-sm subtask-reject-btn" 
                    data-subtask-id="${subtask.subtask_id}"
                    data-subtask-title="${escapeHtml(subtask.subtask_title)}">
                ❌ Ulangi
            </button>
            <button type="button" class="btn-modern btn-info-modern btn-sm subtask-comments-btn" 
                    data-subtask-id="${subtask.subtask_id}"
                    data-subtask-title="${escapeHtml(subtask.subtask_title)}">
                💬 Komentar
            </button>
        </div>
    `;
  }

  // Generate action column for review (alias untuk backward compatibility)
  function generateActionColumn(subtask) {
    return generateReviewActionColumn(subtask);
  }
  
  // Helper function for status badge
  function getStatusBadge(status) {
    const badges = {
      'todo': '<span class="badge-subtask badge-todo">To Do</span>',
      'in_progress': '<span class="badge-subtask badge-in-progress">In Progress</span>',
      'review': '<span class="badge-subtask badge-review">Review</span>',
      'done': '<span class="badge-subtask badge-done">Done</span>'
    };
    return badges[status] || '<span class="badge-subtask badge-todo">To Do</span>';
  }
  
  // Helper function to escape HTML
  function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return unsafe
      .toString()
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function resolveSubtaskTitle(element) {
    const row = element?.closest('tr');
    if (!row) return 'Subtask';
    const firstCell = row.querySelector('td:first-child');
    return firstCell ? firstCell.textContent.trim() : 'Subtask';
  }

  // ==================== KOMENTAR SUBTASK ====================

  async function loadSubtaskComments(subtaskId) {
    if (!subtaskCommentList) {
      return;
    }

    if (String(subtaskId) !== String(subtaskCommentSheet?.dataset?.subtaskId || '')) {
      return;
    }

    subtaskCommentList.innerHTML = `
      <div class="text-center text-surface-muted">
        <span class="loading-spinner"></span> Memuat komentar...
      </div>
    `;

    try {
      const response = await fetch(`/comments/subtask/${subtaskId}`);
      if (!response.ok) {
        throw new Error('Failed to load comments');
      }

      const comments = await response.json();
      if (comments && comments.length > 0) {
        subtaskCommentList.innerHTML = generateCommentsHTML(comments, subtaskId, 'subtask');
      } else {
        subtaskCommentList.innerHTML = generateEmptyState();
      }
    } catch (error) {
      console.error('Error loading subtask comments:', error);
      subtaskCommentList.innerHTML = `
        <div class="empty-state">
          <i class="bi bi-exclamation-triangle"></i>
          <p class="mt-2 mb-0">Gagal memuat komentar</p>
        </div>
      `;
    }
  }

  async function handleSubtaskCommentSubmit(event) {
    event.preventDefault();
    if (!subtaskCommentForm || !subtaskCommentTextarea) return;

    const subtaskId = subtaskCommentForm.dataset.subtaskId;
    const text = subtaskCommentTextarea.value.trim();

    if (!text) {
      alert('Komentar tidak boleh kosong');
      return;
    }

    const originalText = subtaskCommentSubmit.innerHTML;
    subtaskCommentSubmit.innerHTML = '<span class="loading-spinner"></span> Mengirim...';
    subtaskCommentSubmit.disabled = true;

    try {
      const response = await fetch(`/comments/ajax-subtask/${subtaskId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json',
          'Accept': 'text/html'
        },
        body: JSON.stringify({
          comment_text: text,
          _token: '{{ csrf_token() }}'
        })
      });

      if (!response.ok) {
        throw new Error('Failed to send comment');
      }

      const html = await response.text();
      subtaskCommentTextarea.value = '';
      subtaskCommentSubmit.innerHTML = '<i class="bi bi-check-circle"></i> Terkirim!';

      if (subtaskCommentList.querySelector('.empty-state')) {
        subtaskCommentList.innerHTML = html;
      } else {
        subtaskCommentList.insertAdjacentHTML('afterbegin', html);
      }

      setTimeout(() => {
        subtaskCommentSubmit.innerHTML = originalText;
        subtaskCommentSubmit.disabled = false;
      }, 1500);
    } catch (error) {
      console.error('Error:', error);
      alert('Gagal mengirim komentar');
      subtaskCommentSubmit.innerHTML = originalText;
      subtaskCommentSubmit.disabled = false;
    }
  }

  async function handleSubtaskActionSubmit(event) {
    event.preventDefault();
    if (!subtaskActionForm || !subtaskActionSubmit) {
      return;
    }

    const subtaskId = subtaskActionForm.dataset.subtaskId;
    const mode = subtaskActionForm.dataset.mode || 'approve';

    if (!subtaskId) {
      return;
    }

    const reason = subtaskActionReasonInput ? subtaskActionReasonInput.value.trim() : '';
    if (mode === 'reject' && !reason) {
      alert('Harap masukkan alasan ulangi');
      if (subtaskActionReasonInput) {
        subtaskActionReasonInput.focus();
      }
      return;
    }

    const originalText = subtaskActionSubmit.innerHTML;
    subtaskActionSubmit.innerHTML = '<span class="loading-spinner"></span> Memproses...';
    subtaskActionSubmit.disabled = true;

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    if (mode === 'reject') {
      formData.append('reason', reason);
    }

    const url = mode === 'approve'
      ? `/teamlead/subtasks/${subtaskId}/approve`
      : `/teamlead/subtasks/${subtaskId}/reject`;

    try {
      const response = await fetch(url, {
        method: 'POST',
        body: formData
      });

      if (!response.ok) {
        throw new Error('Failed to process subtask');
      }

      subtaskActionSubmit.innerHTML = 'Berhasil!';
      setTimeout(() => {
        closeSubtaskActionSheet();
        location.reload();
      }, 800);
    } catch (error) {
      console.error('Error:', error);
      subtaskActionSubmit.innerHTML = 'Coba Lagi';
      setTimeout(() => {
        subtaskActionSubmit.innerHTML = originalText;
        subtaskActionSubmit.disabled = false;
      }, 1500);
    }
  }

  // ==================== ATTACH DYNAMIC EVENT LISTENERS ====================
  
  function attachDynamicEventListeners() {
    console.log('Attaching dynamic event listeners');
    
    // Event listener untuk tombol approve/reject dengan bottom sheet
    document.body.addEventListener('click', e => {
      const backBtn = e.target.closest('.detail-back-btn');
      if (backBtn) {
        e.preventDefault();
        showBoardsView();
        return;
      }
      
      // Tombol Approve
      if (e.target.classList.contains('subtask-approve-btn')) {
        e.preventDefault();
        const subtaskId = e.target.dataset.subtaskId;
        if (!subtaskId) return;
        const subtaskTitle = e.target.dataset.subtaskTitle || resolveSubtaskTitle(e.target);
        openSubtaskActionSheet({ subtaskId, title: subtaskTitle, mode: 'approve' });
        return;
      }
      
      // Tombol Reject
      if (e.target.classList.contains('subtask-reject-btn')) {
        e.preventDefault();
        const subtaskId = e.target.dataset.subtaskId;
        if (!subtaskId) return;
        const subtaskTitle = e.target.dataset.subtaskTitle || resolveSubtaskTitle(e.target);
        openSubtaskActionSheet({ subtaskId, title: subtaskTitle, mode: 'reject' });
        return;
      }
      
      // Tombol komentar subtask
      if (e.target.classList.contains('subtask-comments-btn')) {
        e.preventDefault();
        const subtaskId = e.target.dataset.subtaskId;
        if (!subtaskId) return;
        const subtaskTitle = e.target.dataset.subtaskTitle || resolveSubtaskTitle(e.target);
        openSubtaskCommentSheet(subtaskId, subtaskTitle);
        return;
      }
    });
  }
  
  // === KOMENTAR DAN REPLY HANDLERS ===
  
  // Komentar utama
  document.body.addEventListener('submit', async e => {
    if (e.target.classList.contains('comment-form')) {
      e.preventDefault();
      console.log('Comment form submitted');
      await handleCommentSubmit(e.target);
    }
  });

  // Reply komentar
  document.body.addEventListener('submit', async e => {
    if (e.target.classList.contains('reply-form')) {
      e.preventDefault();
      console.log('Reply form submitted');
      await handleReplySubmit(e.target);
    }
  });

  // Handle comment submit
  async function handleCommentSubmit(form) {
    const cardId = form.dataset.cardId;
    const textarea = form.querySelector('[name="comment_text"]');
    const text = textarea.value.trim();
    
    if (!text) {
      alert('Komentar tidak boleh kosong');
      return;
    }

    if (!cardId) {
      alert('Invalid comment target');
      return;
    }

    const url = `/comments/ajax-card/${cardId}`;
    const containerSelector = `#comments-container-card-${cardId}`;
    const type = 'card';
    const entityId = cardId;
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading-spinner"></span> Mengirim...';
    submitBtn.disabled = true;

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json',
          'Accept': 'text/html'
        },
        body: JSON.stringify({ 
          comment_text: text,
          _token: '{{ csrf_token() }}'
        })
      });

      if (response.ok) {
        const html = await response.text();
        const container = document.querySelector(containerSelector);
        
        if (container) {
          // Jika container kosong (empty state), ganti isinya
          if (container.querySelector('.empty-state')) {
            container.innerHTML = html;
          } else {
            container.insertAdjacentHTML('afterbegin', html);
          }
          textarea.value = '';
          submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Terkirim!';
          
          setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
          }, 1500);
        }
      } else {
        const errorData = await response.json().catch(() => ({ error: 'Unknown error' }));
        console.error('❌ Gagal kirim komentar:', errorData);
        alert('Gagal mengirim komentar: ' + (errorData.error || 'Silakan coba lagi'));
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    } catch (error) {
      console.error('❌ Error:', error);
      alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }
  }

  // Handle reply submit
  async function handleReplySubmit(form) {
    const cardId = form.dataset.cardId;
    const subtaskId = form.dataset.subtaskId;
    const parentId = form.dataset.parent;
    const textarea = form.querySelector('[name="comment_text"]');
    const text = textarea.value.trim();
    
    if (!text) {
      alert('Balasan tidak boleh kosong');
      return;
    }

    let url, type, entityId;
    
    if (cardId) {
      url = `/comments/ajax-card/${cardId}`;
      type = 'card';
      entityId = cardId;
    } else if (subtaskId) {
      url = `/comments/ajax-subtask/${subtaskId}`;
      type = 'subtask';
      entityId = subtaskId;
    } else {
      alert('Invalid reply target');
      return;
    }
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading-spinner"></span> Mengirim...';
    submitBtn.disabled = true;

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json',
          'Accept': 'text/html'
        },
        body: JSON.stringify({
          comment_text: text,
          parent_id: parentId,
          _token: '{{ csrf_token() }}'
        })
      });

      if (response.ok) {
        const html = await response.text();
        const parentComment = document.querySelector(`#comment-${parentId}`);
        
        if (parentComment) {
          let repliesDiv = parentComment.querySelector('.replies');
          
          if (!repliesDiv) {
            repliesDiv = document.createElement('div');
            repliesDiv.className = 'replies mt-3 ms-4';
            repliesDiv.id = `replies-${parentId}`;
            parentComment.appendChild(repliesDiv);
          }
          
          repliesDiv.insertAdjacentHTML('beforeend', html);
          
          textarea.value = '';
          form.classList.add('d-none');
          submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Terkirim!';
          
          setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
          }, 1500);
          
          const newReply = repliesDiv.lastElementChild;
          if (newReply) newReply.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      } else {
        const errorData = await response.json().catch(() => ({ error: 'Unknown error' }));
        console.error('❌ Gagal kirim reply:', errorData);
        alert('Gagal mengirim balasan: ' + (errorData.error || 'Silakan coba lagi'));
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    } catch (error) {
      console.error('❌ Error:', error);
      alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }
  }

  // Toggle form reply
  document.body.addEventListener('click', e => {
    if (e.target.classList.contains('reply-toggle')) {
      e.preventDefault();
      console.log('Reply toggle clicked');
      
      const parentId = e.target.dataset.parent;
      const form = document.querySelector(`.reply-form[data-parent="${parentId}"]`);
      if (form) {
        // Sembunyikan semua form reply lainnya
        document.querySelectorAll('.reply-form').forEach(f => {
          if (f !== form) f.classList.add('d-none');
        });
        
        form.classList.toggle('d-none');
        const textarea = form.querySelector('[name="comment_text"]');
        if (textarea && !form.classList.contains('d-none')) {
          textarea.focus();
        }
      }
    }
    // Reply cancel button
    if (e.target.classList.contains('reply-cancel')) {
      e.preventDefault();
      const btn = e.target;
      const form = btn.closest('.reply-form');
      if (form) {
        const textarea = form.querySelector('[name="comment_text"]');
        if (textarea) textarea.value = '';
        form.classList.add('d-none');
      }
    }
  });

  // Auto-resize textarea
  document.body.addEventListener('input', e => {
    if (e.target.tagName === 'TEXTAREA' && e.target.name === 'comment_text') {
      const textarea = e.target;
      textarea.style.height = 'auto';
      textarea.style.height = Math.min(textarea.scrollHeight, 150) + 'px';
    }
  });

  // Handle modal events
  document.body.addEventListener('hidden.bs.modal', e => {
    const modal = e.target;
    const form = modal.querySelector('form');
    if (form) form.reset();
  });

  // ==================== SIDEBAR SCROLL FUNCTIONALITY ====================
  
  // Scroll to board function
  function scrollToBoard(boardId) {
    const targetElement = document.getElementById(`board-${boardId}`);
    if (targetElement) {
      // Update active state di sidebar
      document.querySelectorAll('.board-nav-link').forEach(link => {
        link.classList.remove('active');
      });
      const activeLink = document.querySelector(`.board-nav-link[data-board-id="${boardId}"]`);
      if (activeLink) {
        activeLink.classList.add('active');
      }
      
      // Smooth scroll ke board
      targetElement.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start'
      });
    }
  }

  // Initialize Intersection Observer untuk active state saat scroll
  function initBoardObserver() {
    const boardSections = document.querySelectorAll('.board-section');
    const boardLinks = document.querySelectorAll('.board-nav-link');
    
    if (boardSections.length === 0) return;
    
    // Buat observer untuk mendeteksi section yang visible
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const boardId = entry.target.id.replace('board-', '');
          
          // Update active state di sidebar
          boardLinks.forEach(link => {
            link.classList.remove('active');
            if (link.dataset.boardId === boardId) {
              link.classList.add('active');
            }
          });
        }
      });
    }, {
      threshold: 0.5,
      rootMargin: '-100px 0px -100px 0px' // Adjust untuk navbar fixed
    });
    
    // Observe setiap board section
    boardSections.forEach(section => {
      observer.observe(section);
    });
  }

  // Initial setup
  console.log('Initializing application...');
  attachDynamicEventListeners();
  initBoardObserver();
  
  // Test: Check if elements are properly loaded
  console.log('Boards content:', boardsContent);
  console.log('Detail content:', detailContent);
  console.log('Create card content:', createCardContent);
  console.log('Create card button:', createCardBtn);
});
</script>

</body>
</html>
