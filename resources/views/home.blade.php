<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annam QSR - Central Landing</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="/logo.jpg">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
            --accent-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            --hover-shadow: 0 15px 35px rgba(22, 197, 94, 0.15);
            --bg-color: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .profile-avatar-trigger {
            background: none;
            border: none;
            padding: 0;
            position: relative;
            cursor: pointer;
            display: inline-block;
        }
        .profile-avatar-trigger::after {
            display: none !important;
        }
        .profile-avatar-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
            transition: border-color 0.2s ease;
        }
        .profile-avatar-trigger:hover .profile-avatar-img {
            border-color: #15803d;
        }
        .profile-avatar-status {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 12px;
            height: 12px;
            background-color: #22c55e;
            border: 2px solid #ffffff;
            border-radius: 50%;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.05);
        }

        .header-banner {
            background: var(--primary-gradient);
            border-radius: 20px;
            padding: 3rem 2.5rem;
            color: white;
            margin-bottom: 2.5rem;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.25);
            position: relative;
            overflow: hidden;
        }

        .header-banner::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            pointer-events: none;
        }

        .portal-card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #ffffff;
            overflow: hidden;
            height: 100%;
        }

        .portal-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--hover-shadow);
        }

        .btn-primary-gradient {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.35);
            color: white;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        .icon-waiter {
            background-color: #dcfce7;
            color: #15803d;
        }

        .icon-kitchen {
            background-color: #fef3c7;
            color: #d97706;
        }

        .icon-admin {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .role-badge {
            font-size: 0.85rem;
            padding: 0.5em 1em;
            border-radius: 50rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="py-4">

    <div class="container-fluid px-md-5 px-3 my-auto">
        <!-- Header -->
        <header class="d-flex justify-content-between align-items-center mb-4">
            <a href="#" class="navbar-brand d-flex align-items-center gap-2">
                <img src="/logo.jpg" alt="Logo" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                <span>Annam QSR</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                @if(Auth::user()->is_active === false)
                    <span class="badge bg-danger-subtle text-danger role-badge">
                        Status: Inactive
                    </span>
                @else
                    <span class="badge bg-success-subtle text-success role-badge">
                        Role: {{ ucfirst(Auth::user()->role) }}
                    </span>
                @endif
                <div class="dropdown">
                    <button class="profile-avatar-trigger dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="outline: none;">
                        <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="profile-avatar-img">
                        <span class="profile-avatar-status" style="{{ Auth::user()->is_active === false ? 'background-color: #ef4444 !important;' : '' }}"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenu" style="border-radius: 12px; min-width: 240px; padding: 0;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-3 py-3 px-4" href="{{ route('profile.show') }}" style="border-top-left-radius: 12px; border-top-right-radius: 12px; background: none;">
                                <div class="position-relative d-inline-block">
                                    <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover; border: 1px solid #e2e8f0;">
                                    <span class="position-absolute" style="bottom: 0; right: 0; width: 12px; height: 12px; background-color: {{ Auth::user()->is_active === false ? '#ef4444' : '#22c55e' }}; border: 2px solid #ffffff; border-radius: 50%;"></span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark" style="font-size: 1.05rem; line-height: 1.2;">{{ Auth::user()->name }}</span>
                                    <span class="text-secondary small">{{ ucfirst(Auth::user()->role) }}</span>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-0"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-3 py-3 px-4 text-secondary fw-semibold" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; font-size: 1rem; border: none; background: none; width: 100%;">
                                    <i class="bi bi-power" style="font-size: 1.25rem;"></i> Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Banner -->
        <div class="header-banner" style="{{ Auth::user()->is_active === false ? 'background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.25) !important;' : '' }}">
            <h1 class="fw-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="mb-0 opacity-75">
                @if(Auth::user()->is_active === false)
                    Your account is currently inactive. Please check your status details below.
                @else
                    Access your assigned system portals and manage restaurant services below.
                @endif
            </p>
        </div>

        @php
            $hasWaiter = Auth::user()->hasPermission('waiter_terminal');
            $hasKitchen = Auth::user()->hasPermission('kitchen_terminal');
            $hasAdmin = Auth::user()->hasPermission('admin_panel');
        @endphp

        <!-- Portals Grid -->
        <div class="row g-4 justify-content-center">
            @if(Auth::user()->is_active === false)
                <!-- Inactive Account View -->
                <div class="col-md-8 text-center py-5">
                    <div class="card p-5 border-0 shadow-sm" style="border-radius: 20px; border-top: 5px solid #ef4444 !important;">
                        <div class="mb-4">
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-3 text-danger">Account Deactivated</h3>
                        <p class="text-secondary mb-4 mx-auto" style="max-width: 500px;">
                            Your account is inactive. Please contact the manager or admin.
                        </p>
                        <div class="d-inline-block">
                            <button type="button" class="btn btn-outline-danger px-4 py-2" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Check Status
                            </button>
                        </div>
                    </div>
                </div>
            @elseif(!$hasWaiter && !$hasKitchen && !$hasAdmin)
                <!-- Pending Access View -->
                <div class="col-md-8 text-center py-5">
                    <div class="card p-5 border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="mb-4">
                            <i class="bi bi-shield-lock-fill text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-3">Access Pending Assignment</h3>
                        <p class="text-secondary mb-4 mx-auto" style="max-width: 500px;">
                            Your account registration is successful! However, you currently do not have access permissions for any terminal. Please ask your restaurant administrator to assign your role permissions from the control panel.
                        </p>
                        <div class="d-inline-block">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Check Status
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- Waiter Terminal Portal Card -->
                @if($hasWaiter && App\Models\Feature::isActive('waiter_terminal'))
                    <div class="col-md-4">
                        <div class="card portal-card p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="card-icon icon-waiter">
                                    <i class="bi bi-shop"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">Order Panel</h4>
                                <p class="text-secondary small mb-4">
                                    Place restaurant orders, customize modifiers, specify customer details, choose tables, and print invoices.
                                </p>
                            </div>
                            <a href="{{ route('orders.index') }}" class="btn btn-primary-gradient w-100 py-2.5">
                                Open Terminal <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Kitchen Display Terminal Portal Card -->
                @if($hasKitchen && App\Models\Feature::isActive('kitchen_terminal'))
                    <div class="col-md-4">
                        <div class="card portal-card p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="card-icon icon-kitchen">
                                    <i class="bi bi-fire"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">Kitchen Display</h4>
                                <p class="text-secondary small mb-4">
                                    Monitor pending orders in real-time, update food cooking statuses, and trigger notification chimes for ready meals.
                                </p>
                            </div>
                            <a href="{{ route('orders.kitchen') }}" class="btn btn-warning w-100 py-2.5 fw-semibold text-white" style="background-color: #d97706; border: none; border-radius: 10px;">
                                Open KDS Board <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Admin Panel Card -->
                @if($hasAdmin)
                    <div class="col-md-4">
                        <div class="card portal-card p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="card-icon icon-admin">
                                    <i class="bi bi-speedometer2"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">Admin Panel</h4>
                                <p class="text-secondary small mb-4">
                                    View financial analytics, modify menu items, oversee access permissions, adjust roles, and audit order logs.
                                </p>
                            </div>
                            <a href="{{ route('admin.index') }}" class="btn btn-danger w-100 py-2.5 fw-semibold text-white" style="background-color: #dc2626; border: none; border-radius: 10px;">
                                Open Admin Panel <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
