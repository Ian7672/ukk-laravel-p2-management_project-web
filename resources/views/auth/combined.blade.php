<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(167, 139, 250, 0.1) 0%, transparent 50%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Auth Container */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
            overflow-y: auto;
        }

        /* Auth Card - Glass Morphism */
        .auth-card {
            width: 100%;
            max-width: 900px;
            background: rgba(31, 41, 55, 0.7);
            backdrop-filter: blur(30px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .auth-header {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.9), rgba(59, 130, 246, 0.9));
            padding: 3rem 2rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .auth-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 30% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 70% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        }

        .auth-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }

        .auth-header p {
            font-size: 1.1rem;
            opacity: 0.95;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }

        /* Tabs */
        .auth-tabs {
            display: flex;
            background: rgba(17, 24, 39, 0.5);
            backdrop-filter: blur(10px);
        }

        .auth-tab {
            flex: 1;
            padding: 1.25rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            font-size: 1.1rem;
            border-bottom: 3px solid transparent;
        }

        .auth-tab:hover {
            background: rgba(139, 92, 246, 0.1);
            color: rgba(255, 255, 255, 0.9);
        }

        .auth-tab.active {
            background: rgba(139, 92, 246, 0.2);
            color: white;
            border-bottom-color: #8b5cf6;
            box-shadow: inset 0 -3px 0 #8b5cf6;
        }

        /* Content */
        .auth-content {
            padding: 2.5rem;
            background: rgba(17, 24, 39, 0.3);
        }

        /* Form Groups */
        .form-group-modern {
            margin-bottom: 1.5rem;
        }

        .form-label-modern {
            color: #e5e7eb;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: block;
            font-size: 0.95rem;
        }

        .form-label-modern i {
            color: #8b5cf6;
            margin-right: 0.5rem;
        }

        /* Input Groups */
        .input-group-modern {
            position: relative;
        }

        .input-group-modern i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #8b5cf6;
            z-index: 2;
            font-size: 1.1rem;
        }

        .form-control-modern {
            width: 100%;
            padding: 1rem 1rem 1rem 3.25rem;
            height: 55px;
            font-size: 1rem;
            background: rgba(31, 41, 55, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #f3f4f6;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-control-modern::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-control-modern:focus {
            outline: none;
            background: rgba(31, 41, 55, 0.9);
            border-color: #8b5cf6;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.2);
            color: white;
        }

        .form-control-modern:focus + i {
            color: #a78bfa;
        }

        /* Select Styling */
        select.form-control-modern {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%238b5cf6' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 3rem;
        }

        select.form-control-modern option {
            background: #1f2937;
            color: #f3f4f6;
        }

        /* Buttons */
        .btn-auth {
            width: 100%;
            height: 55px;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            margin-top: 1rem;
        }

        .btn-auth::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-auth:hover::before {
            left: 100%;
        }

        .btn-login {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(16, 185, 129, 0.4);
        }

        .btn-register {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.3);
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.4);
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid;
            font-weight: 500;
        }

        .alert-danger-modern {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .alert-success-modern {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .alert ul {
            list-style: none;
            padding-left: 0;
            margin: 0.5rem 0 0 0;
        }

        .alert li {
            padding: 0.25rem 0;
        }

        /* Hidden State */
        .hidden {
            display: none !important;
        }

        /* Responsive */
        @media (max-width: 1199.98px) {
            .auth-card {
                max-width: 760px;
            }
        }

        @media (max-width: 991.98px) {
            body {
                align-items: flex-start;
                padding-top: 2.5rem;
            }

            .auth-container {
                padding: 1.75rem 1.25rem 3rem;
                min-height: auto;
            }

            .auth-card {
                max-width: 620px;
                border-radius: 24px;
                margin-inline: auto;
            }

            .auth-header {
                padding: 2.5rem 2rem;
            }

            .auth-content {
                padding: 2rem 1.75rem;
            }

            .auth-tabs {
                flex-direction: column;
            }

            .auth-tab {
                width: 100%;
                border-bottom-width: 0;
                border-left: 3px solid transparent;
                text-align: left;
                padding: 1rem 1.25rem;
            }

            .auth-tab + .auth-tab {
                border-top: 1px solid rgba(255, 255, 255, 0.05);
            }

            .auth-tab.active {
                border-left-color: #8b5cf6;
                box-shadow: inset 0 0 0 rgba(0, 0, 0, 0);
            }

            .auth-content .row {
                row-gap: 1.25rem;
            }
        }

        @media (max-width: 767.98px) {
            .auth-card {
                max-width: 100%;
                border-radius: 22px;
            }

            .auth-header h1 {
                font-size: 1.95rem;
            }

            .auth-content {
                padding: 1.75rem 1.5rem;
            }

            .form-control-modern {
                height: 50px;
                font-size: 0.95rem;
            }

            .btn-auth {
                height: 50px;
                font-size: 1rem;
            }
        }

        @media (max-width: 575.98px) {
            body {
                padding-top: 1.5rem;
            }

            .auth-container {
                padding: 1.25rem 1rem 2.5rem;
            }

            .auth-card {
                border-radius: 18px;
            }

            .auth-header {
                padding: 2.25rem 1.5rem;
            }

            .auth-header h1 {
                font-size: 1.75rem;
            }

            .auth-header p {
                font-size: 0.95rem;
            }

            .auth-content {
                padding: 1.25rem 1rem 1.5rem;
            }

            .auth-tab {
                padding: 0.9rem 1rem;
                font-size: 1rem;
            }

            .form-label-modern {
                font-size: 0.85rem;
            }

            .input-group-modern i {
                left: 1rem;
            }

            .form-control-modern {
                padding: 0.8rem 1rem 0.8rem 2.6rem;
                font-size: 0.9rem;
            }

            .btn-auth {
                height: 48px;
                font-size: 0.95rem;
            }
        }

        /* Smooth Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
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
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>🚀 Project</h1>
                <p>Modern Project Management System</p>
            </div>
            
            <div class="auth-tabs">
                <button class="auth-tab active" onclick="switchTab('login')">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
                <button class="auth-tab" onclick="switchTab('register')">
                    <i class="bi bi-person-plus me-2"></i>Register
                </button>
            </div>
            
            <div class="auth-content">
                <!-- Login Form -->
                <div id="loginForm">
                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-person"></i>Username
                            </label>
                            <div class="input-group-modern">
                                <i class="bi bi-person"></i>
                                <input type="text" name="username" class="form-control-modern" 
                                       placeholder="Enter your username" required autofocus>
                            </div>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-lock"></i>Password
                            </label>
                            <div class="input-group-modern">
                                <i class="bi bi-lock"></i>
                                <input type="password" name="password" class="form-control-modern" 
                                       placeholder="Enter your password" required>
                            </div>
                        </div>
                        
                        @if(session('error'))
                            <div class="alert alert-danger-modern">
                                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            </div>
                        @endif
                        
                        <button type="submit" class="btn-auth btn-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login to Dashboard
                        </button>
                    </form>
                </div>
                
                <!-- Register Form -->
                <div id="registerForm" class="hidden">
                    <form action="{{ route('register.post') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="bi bi-person"></i>Full Name
                                    </label>
                                    <div class="input-group-modern">
                                        <i class="bi bi-person"></i>
                                        <input type="text" name="full_name" class="form-control-modern" 
                                               placeholder="Enter your full name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="bi bi-at"></i>Username
                                    </label>
                                    <div class="input-group-modern">
                                        <i class="bi bi-at"></i>
                                        <input type="text" name="username" class="form-control-modern" 
                                               placeholder="Choose a username" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-envelope"></i>Email
                            </label>
                            <div class="input-group-modern">
                                <i class="bi bi-envelope"></i>
                                <input type="email" name="email" class="form-control-modern" 
                                       placeholder="Enter your email" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="bi bi-lock"></i>Password
                                    </label>
                                    <div class="input-group-modern">
                                        <i class="bi bi-lock"></i>
                                        <input type="password" name="password" class="form-control-modern" 
                                               placeholder="Create a password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="bi bi-lock-fill"></i>Confirm Password
                                    </label>
                                    <div class="input-group-modern">
                                        <i class="bi bi-lock-fill"></i>
                                        <input type="password" name="password_confirmation" class="form-control-modern" 
                                               placeholder="Confirm your password" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-briefcase"></i>Role
                            </label>
                            <div class="input-group-modern">
                                <i class="bi bi-briefcase"></i>
                                <select name="role" class="form-control-modern" required>
                                    <option value="">Select your role</option>
                                    <option value="team_lead">Team Lead</option>
                                    <option value="developer">Developer</option>
                                    <option value="designer">Designer</option>
                                </select>
                            </div>
                        </div>
                        
                        @if(session('success'))
                            <div class="alert alert-success-modern">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-danger-modern">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Please fix the following errors:</strong>
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <button type="submit" class="btn-auth btn-register">
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function switchTab(tab) {
            // Get the button that was clicked
            const clickedButton = event.target.closest('.auth-tab');
            
            // Remove active class from all tabs
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            if (clickedButton) {
                clickedButton.classList.add('active');
            }
            
            // Hide all forms
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('registerForm').classList.add('hidden');
            
            // Show selected form
            if (tab === 'login') {
                document.getElementById('loginForm').classList.remove('hidden');
                // Focus on first input
                setTimeout(() => {
                    document.querySelector('#loginForm input[name="username"]').focus();
                }, 100);
            } else {
                document.getElementById('registerForm').classList.remove('hidden');
                // Focus on first input
                setTimeout(() => {
                    document.querySelector('#registerForm input[name="full_name"]').focus();
                }, 100);
            }
        }
        
        // Add floating animation to form elements
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control-modern');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                    this.parentElement.style.transition = 'transform 0.3s ease';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Auto-focus first input on page load
            const firstInput = document.querySelector('#loginForm input[name="username"]');
            if (firstInput) {
                firstInput.focus();
            }
        });

        // Handle form submission feedback
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing...';
                
                // Re-enable after 3 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 3000);
            });
        });
    </script>
</body>
</html>
