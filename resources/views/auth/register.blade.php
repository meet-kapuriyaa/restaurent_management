<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annam QSR - Register</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="/logo.jpg">

    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            --primary-gradient: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
            --card-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #0f172a;
        }

        .auth-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            width: 100%;
            max-width: 440px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            padding: 2.5rem;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-brand {
            text-decoration: none;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .auth-logo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .auth-brand span {
            font-weight: 800;
            font-size: 1.8rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-subtitle {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: #475569;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
        }

        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            outline: none;
        }

        .btn-auth {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 0.8rem;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            transition: all 0.2s ease;
            margin-top: 1rem;
        }

        .btn-auth:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.88rem;
            color: #64748b;
        }

        .auth-footer a {
            color: #15803d;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        
        <div class="auth-header">
            <a href="#" class="auth-brand">
                <img src="/logo.jpg" alt="Logo" class="auth-logo">
                <span>Annam QSR</span>
            </a>
            <p class="auth-subtitle">Create Employee Account</p>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 small border-0 rounded-3 mb-3">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Enter your full name">
            </div>

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="Enter your email address">
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="position-relative">
                    <input type="password" id="password" name="password" class="form-control pe-5" required placeholder="••••••••">
                    <button type="button" class="btn shadow-none position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent text-secondary pe-3" id="togglePassword">
                        <i class="bi bi-eye-slash fs-5"></i>
                    </button>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="position-relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control pe-5" required placeholder="••••••••">
                    <button type="button" class="btn shadow-none position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent text-secondary pe-3" id="togglePasswordConfirm">
                        <i class="bi bi-eye-slash fs-5"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-auth">Register Employee</button>
        </form>

        <div class="auth-footer">
            Already registered? <a href="{{ route('login') }}">Log in here</a>
        </div>

    </div>

    <script>
        function setupPasswordToggle(toggleId, inputId) {
            const toggle = document.querySelector(toggleId);
            const input = document.querySelector(inputId);

            toggle.addEventListener('click', function () {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye-slash');
                icon.classList.toggle('bi-eye');
            });
        }

        setupPasswordToggle('#togglePassword', '#password');
        setupPasswordToggle('#togglePasswordConfirm', '#password_confirmation');
    </script>
</body>
</html>
