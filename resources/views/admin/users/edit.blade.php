<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit User - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

        .layout-wrapper {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        .acrylic-sidebar-fixed {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(135deg,
                    rgba(13, 17, 23, 0.95) 0%,
                    rgba(22, 27, 34, 0.98) 50%,
                    rgba(13, 17, 23, 0.95) 100%);
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

        .text-muted-light {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        .divider-glow {
            border: none;
            height: 1px;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(139, 92, 246, 0.5),
                    transparent);
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
            margin-top: auto;
            padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 1rem);
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
            width: 100%;
        }

        .btn-logout-acrylic:hover {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(220, 38, 38, 0.3));
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
        }

        .main-content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
        }

        @media (max-width: 992px) {
            .acrylic-sidebar-fixed {
                position: relative;
                width: min(78vw, 260px);
                height: auto;
                box-shadow: none;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }

        .glass-card {
            background: rgba(31, 41, 55, 0.6);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            color: white;
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .form-control,
        .form-select {
            background: rgba(31, 41, 55, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #f3f4f6;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(31, 41, 55, 0.95);
            border-color: rgba(139, 92, 246, 0.6);
            color: white;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .btn-primary,
        .btn-secondary {
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            backdrop-filter: blur(10px);
            padding: 0.75rem 1.25rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.25), rgba(59, 130, 246, 0.25));
            border-color: rgba(139, 92, 246, 0.4);
            color: #c4b5fd;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.4), rgba(59, 130, 246, 0.4));
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.25);
        }

        .btn-secondary {
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.25), rgba(75, 85, 99, 0.25));
            border-color: rgba(107, 114, 128, 0.4);
            color: #d1d5db;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.4), rgba(75, 85, 99, 0.4));
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(107, 114, 128, 0.25);
        }

        body.light-mode .btn-primary {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.9), rgba(37, 99, 235, 0.9));
            border-color: rgba(99, 102, 241, 0.85);
            color: #ffffff;
            box-shadow: 0 10px 24px rgba(99, 102, 241, 0.25);
        }

        body.light-mode .btn-primary:hover {
            background: linear-gradient(135deg, #7c3aed, #2563eb);
            box-shadow: 0 14px 30px rgba(79, 70, 229, 0.35);
        }

        body.light-mode .btn-secondary {
            background: linear-gradient(135deg, rgba(226, 232, 240, 0.95), rgba(203, 213, 225, 0.95));
            border-color: rgba(148, 163, 184, 0.8);
            color: #0f172a;
            box-shadow: 0 8px 20px rgba(148, 163, 184, 0.25);
        }

        body.light-mode .btn-secondary:hover {
            background: linear-gradient(135deg, rgba(226, 232, 240, 1), rgba(203, 213, 225, 1));
            color: #020617;
            box-shadow: 0 12px 24px rgba(148, 163, 184, 0.35);
        }

        .text-danger {
            color: #fca5a5 !important;
        }

        body:not(.light-mode) .status-note {
            color: #c4c4c4ff !important;
        }
    </style>
</head>

<body>
    <div class="layout-wrapper">
        <!-- Sidebar -->
        @include('components.app-sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-gradient mb-0">
                        <i class="bi bi-pencil me-2"></i>Edit User
                    </h3>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Nama Lengkap</label>
                                <input type="text" name="full_name" class="form-control"
                                    value="{{ old('full_name', $user->full_name) }}" required>
                                @error('full_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Username</label>
                                <input type="text" name="username" class="form-control"
                                    value="{{ old('username', $user->username) }}" required>
                                @error('username')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="team_lead"
                                        {{ old('role', $user->role) == 'team_lead' ? 'selected' : '' }}>Team Lead
                                    </option>
                                    <option value="developer"
                                        {{ old('role', $user->role) == 'developer' ? 'selected' : '' }}>Developer
                                    </option>
                                    <option value="designer"
                                        {{ old('role', $user->role) == 'designer' ? 'selected' : '' }}>Designer
                                    </option>
                                </select>
                                @error('role')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Status</label>
                                <div class="form-control bg-secondary text-white"
                                    style="border: 1px solid rgba(255, 255, 255, 0.2);">
                                    {{ ucfirst($user->current_task_status) }}
                                </div>
                                <small class="text-muted status-note">Status tidak dapat diubah</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-white">Bergabung</label>
                                <div class="form-control bg-secondary text-white"
                                    style="border: 1px solid rgba(255, 255, 255, 0.2);">
                                    {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
