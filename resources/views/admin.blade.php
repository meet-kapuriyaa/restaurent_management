<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Annam QSR - Admin Control Panel</title>
    
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
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --bg-color: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
            --border-color: #e2e8f0;
            --primary-gradient: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
        }

        .navbar-admin {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
        }

        .admin-brand {
            font-weight: 700;
            font-size: 1.4rem;
            text-decoration: none;
            color: var(--text-main);
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

        .admin-brand span {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-tabs {
            border-bottom: 2px solid var(--border-color);
        }

        .nav-link-admin {
            border: none !important;
            color: var(--text-muted);
            font-weight: 600;
            padding: 1rem 1.5rem;
            position: relative;
            background: none;
        }

        .nav-link-admin.active {
            color: #15803d !important;
        }

        .nav-link-admin.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #15803d;
            border-radius: 3px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .card-admin {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.85rem;
            color: #ef4444;
            margin-top: 0.25rem;
        }

        .is-invalid {
            border-color: #ef4444 !important;
        }
    </style>
</head>
<body class="pb-5">

    <!-- Top Admin Header -->
    <nav class="navbar navbar-admin sticky-top mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a href="#" class="admin-brand d-flex align-items-center gap-2">
                <img src="/logo.jpg" alt="Logo" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                <span>Annam QSR <strong>Admin</strong></span>
            </a>
            <div class="d-flex align-items-center gap-3">
                @if(Auth::check() && Auth::user()->hasPermission('waiter_terminal'))
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                        <i class="bi bi-shop me-1"></i> Waiter Terminal
                    </a>
                @endif
                @if(Auth::check() && Auth::user()->hasPermission('kitchen_terminal'))
                    <a href="{{ route('orders.kitchen') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                        <i class="bi bi-fire me-1"></i> Kitchen KDS
                    </a>
                @endif
                @auth
                    <div class="dropdown">
                        <button class="profile-avatar-trigger dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="outline: none;">
                            <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="profile-avatar-img">
                            <span class="profile-avatar-status"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenu" style="border-radius: 12px; min-width: 240px; padding: 0;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-3 py-3 px-4" href="{{ route('profile.show') }}" style="border-top-left-radius: 12px; border-top-right-radius: 12px; background: none;">
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover; border: 1px solid #e2e8f0;">
                                        <span class="position-absolute" style="bottom: 0; right: 0; width: 12px; height: 12px; background-color: #22c55e; border: 2px solid #ffffff; border-radius: 50%;"></span>
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
                @endauth
            </div>
        </div>
    </nav>

    <div class="container">
        
        <!-- Tabbed Navigation Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <ul class="nav nav-tabs border-0" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link-admin active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard-pane" type="button" role="tab" aria-controls="dashboard-pane" aria-selected="true">
                        <i class="bi bi-graph-up-arrow me-1"></i> Analytics Hub
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link-admin" id="menu-tab" data-bs-toggle="tab" data-bs-target="#menu-pane" type="button" role="tab" aria-controls="menu-pane" aria-selected="false">
                        <i class="bi bi-egg-fried me-1"></i> Menu Management
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link-admin" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders-pane" type="button" role="tab" aria-controls="orders-pane" aria-selected="false">
                        <i class="bi bi-receipt me-1"></i> Orders Log
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link-admin" id="access-tab" data-bs-toggle="tab" data-bs-target="#access-pane" type="button" role="tab" aria-controls="access-pane" aria-selected="false">
                        <i class="bi bi-shield-lock me-1"></i> Access Control
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link-admin" id="tables-tab" data-bs-toggle="tab" data-bs-target="#tables-pane" type="button" role="tab" aria-controls="tables-pane" aria-selected="false">
                        <i class="bi bi-grid-3x3-gap me-1"></i> Tables Setup
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="adminTabContent">
            
            <!-- Pane 1: Analytics Hub -->
            <div class="tab-pane fade show active" id="dashboard-pane" role="tabpanel" aria-labelledby="dashboard-tab" tabindex="0">
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div>
                                <span class="text-secondary small fw-semibold uppercase tracking-wider block mb-1">TOTAL REVENUE</span>
                                <h3 id="stat-total-revenue" class="fw-bold mb-0 text-dark" data-amount="{{ $totalRevenue }}">₹{{ number_format($totalRevenue, 2) }}</h3>
                            </div>
                            <div class="stat-icon bg-success-subtle text-success">
                                <i class="bi bi-currency-rupee"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div>
                                <span class="text-secondary small fw-semibold uppercase tracking-wider block mb-1">ACTIVE ORDERS</span>
                                <h3 id="stat-active-orders" class="fw-bold mb-0 text-dark">{{ $activeOrdersCount }}</h3>
                            </div>
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="bi bi-activity"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div>
                                <span class="text-secondary small fw-semibold uppercase tracking-wider block mb-1">MENU ITEMS</span>
                                <h3 id="stat-menu-items" class="fw-bold mb-0 text-dark">{{ $totalFoodItemsCount }}</h3>
                            </div>
                            <div class="stat-icon bg-warning-subtle text-warning">
                                <i class="bi bi-book"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row g-4 mb-5">
                    <div class="col-lg-7">
                        <div class="card-admin p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-activity text-success me-2" style="color: #15803d;"></i>Weekly Revenue Trend</h5>
                            <div style="height: 300px; position: relative;">
                                <canvas id="revenueTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card-admin p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill text-success me-2" style="color: #15803d;"></i>Top 5 Selling Items</h5>
                            <div style="height: 300px; position: relative; display: flex; justify-content: center;">
                                <canvas id="topItemsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-admin">
                    <h4 class="fw-bold mb-3"><i class="bi bi-rocket-takeoff-fill text-indigo me-2"></i>Quick Terminal Shortcuts</h4>
                    <p class="text-secondary">Open KDS or waiter windows below to complete the simulated dining workflow.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('orders.index') }}" target="_blank" class="btn btn-success btn-lg px-4 py-3 fw-semibold text-white" style="background-color: #15803d;">
                            <i class="bi bi-plus-circle-fill me-2"></i> Open Customer Ordering Terminal
                        </a>
                        <a href="{{ route('orders.kitchen') }}" target="_blank" class="btn btn-outline-dark btn-lg px-4 py-3 fw-semibold">
                            <i class="bi bi-fire me-2"></i> Open Kitchen KDS Display
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pane 2: Menu Management -->
            <div class="tab-pane fade" id="menu-pane" role="tabpanel" aria-labelledby="menu-tab" tabindex="0">
                <div class="card-admin">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Menu Items Directory</h4>
                        <button type="button" class="btn btn-primary bg-success border-0 px-3 fw-semibold text-white" style="background-color: #15803d;" data-bs-toggle="modal" data-bs-target="#addFoodItemModal">
                            <i class="bi bi-plus-lg me-1"></i> Add Food Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($foodItems as $item)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $item->name }}</td>
                                        <td class="text-secondary small" style="max-width: 300px;">{{ $item->description ?: '-' }}</td>
                                        <td class="fw-semibold">₹{{ number_format($item->price, 2) }}</td>
                                        <td>
                                            @if($item->status === 'available')
                                                <span class="badge bg-success-subtle text-success">Available</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">Unavailable</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn {{ $item->status === 'available' ? 'btn-outline-warning' : 'btn-outline-success' }} toggle-status-btn" 
                                                        style="font-size: 0.72rem; padding: 0.15rem 0.35rem; font-weight: 600; line-height: 1.2;"
                                                        data-id="{{ $item->id }}"
                                                        title="{{ $item->status === 'available' ? 'Deactivate Item' : 'Activate Item' }}">
                                                    @if($item->status === 'available')
                                                        <i class="bi bi-x-circle-fill"></i> Deactivate
                                                    @else
                                                        <i class="bi bi-check-circle-fill"></i> Activate
                                                    @endif
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary edit-food-btn" 
                                                        data-id="{{ $item->id }}"
                                                        data-name="{{ $item->name }}"
                                                        data-price="{{ $item->price }}"
                                                        data-description="{{ $item->description }}"
                                                        data-status="{{ $item->status }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-food-btn" data-id="{{ $item->id }}">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-secondary">No food items listed in menu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pane 3: Order Manager -->
            <div class="tab-pane fade" id="orders-pane" role="tabpanel" aria-labelledby="orders-tab" tabindex="0">
                <div class="card-admin">
                    <h4 class="fw-bold mb-4">Total Orders Ledger</h4>

                    <!-- Filter Controls -->
                    <form id="order-filter-form" action="{{ route('admin.index') }}" method="GET" class="mb-4 bg-light p-3 border rounded-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="filter_type" class="form-label text-secondary fw-semibold small mb-1">Date Filter</label>
                                <select name="filter_type" id="filter_type" class="form-select form-select-sm">
                                    <option value="all" {{ request('filter_type') === 'all' || !request('filter_type') ? 'selected' : '' }}>All Data</option>
                                    <option value="single" {{ request('filter_type') === 'single' ? 'selected' : '' }}>Single Date</option>
                                    <option value="range" {{ request('filter_type') === 'range' ? 'selected' : '' }}>Custom Range</option>
                                </select>
                            </div>

                            <div class="col-md-3" id="filter_date_wrapper" style="{{ request('filter_type') === 'single' ? '' : 'display: none;' }}">
                                <label for="filter_date" class="form-label text-secondary fw-semibold small mb-1">Date</label>
                                <input type="date" name="filter_date" id="filter_date" class="form-control form-control-sm" value="{{ request('filter_date') }}">
                            </div>

                            <div class="col-md-3" id="filter_from_date_wrapper" style="{{ request('filter_type') === 'range' ? '' : 'display: none;' }}">
                                <label for="from_date" class="form-label text-secondary fw-semibold small mb-1">From Date</label>
                                <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
                            </div>

                            <div class="col-md-3" id="filter_to_date_wrapper" style="{{ request('filter_type') === 'range' ? '' : 'display: none;' }}">
                                <label for="to_date" class="form-label text-secondary fw-semibold small mb-1">To Date</label>
                                <input type="date" name="to_date" id="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-sm btn-primary fw-semibold px-3 d-inline-flex align-items-center justify-content-center" style="background-color: #15803d; border: none; height: 31px; border-radius: 8px;">
                                    <i class="bi bi-funnel-fill me-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.index') }}" class="btn btn-sm btn-outline-secondary fw-semibold px-3 d-inline-flex align-items-center justify-content-center" style="height: 31px; border-radius: 8px;">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items Ordered</th>
                                    <th>Total Price</th>
                                    <th>Order Status</th>
                                    <th>Payment Status</th>
                                    <th>Placed At</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="orders-list-tbody">
                                @include('admin.orders_rows')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pane 4: Access Control & Permissions Matrix -->
            <div class="tab-pane fade" id="access-pane" role="tabpanel" aria-labelledby="access-tab" tabindex="0">
                <div class="row g-4">
                    <!-- Left: Role Permissions Matrix -->
                    <div class="col-lg-7">
                        <div class="card-admin h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="fw-bold mb-0"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Role Actions & Pages Matrix</h4>
                                <button type="button" class="btn btn-sm btn-success text-white fw-semibold" style="background-color: #15803d;" id="add-role-btn">
                                    <i class="bi bi-plus-lg me-1"></i> Add Role
                                </button>
                            </div>
                            <p class="text-secondary small mb-4">Toggle view page access and action authorization (Insert, Update, Delete) per role in real-time.</p>

                            @php
                                $checkAllowed = function($role, $page) use ($permissions) {
                                    $p = $permissions->where('role', $role)->where('page', $page)->first();
                                    return $p && $p->is_allowed;
                                };
                            @endphp

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center" id="permissions-matrix-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-start">Role</th>
                                            <th>Waiter Page</th>
                                            <th>Kitchen Page</th>
                                            <th>Admin Page</th>
                                            <th>Insert</th>
                                            <th>Update</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($availableRoles as $roleKey)
                                            @php
                                                $roleLabel = ucfirst($roleKey);
                                            @endphp
                                            <tr data-role="{{ $roleKey }}">
                                                <td class="fw-bold text-dark text-start">{{ $roleLabel }}</td>
                                                @foreach(['waiter_terminal', 'kitchen_terminal', 'admin_panel', 'can_insert', 'can_update', 'can_delete'] as $pageKey)
                                                    <td>
                                                        <div class="form-check form-switch d-inline-block">
                                                            <input class="form-check-input permission-switch" type="checkbox" 
                                                                   data-role="{{ $roleKey }}" data-page="{{ $pageKey }}"
                                                                   {{ $checkAllowed($roleKey, $pageKey) ? 'checked' : '' }}
                                                                   {{ $roleKey === 'admin' ? 'disabled' : '' }}>
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right: User Management -->
                    <div class="col-lg-5">
                        <div class="card-admin">
                            <h4 class="fw-bold mb-3"><i class="bi bi-people-fill text-success me-2" style="color:#15803d;"></i>Employee Accounts</h4>
                            <p class="text-secondary small mb-4">Manage employee logins, active roles, or delete user accounts.</p>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="employee-accounts-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name / Email</th>
                                            <th>Role</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                                    <small class="text-secondary">{{ $user->email }}</small>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm user-role-select" data-id="{{ $user->id }}" {{ Auth::id() === $user->id ? 'disabled' : '' }} style="width: 130px;">
                                                        @if(!in_array($user->role, $availableRoles->toArray() ?? (array)$availableRoles))
                                                            <option value="{{ $user->role }}" selected>{{ ucfirst($user->role) }}</option>
                                                        @endif
                                                        @foreach($availableRoles as $roleKey)
                                                            <option value="{{ $roleKey }}" {{ $user->role === $roleKey ? 'selected' : '' }}>{{ ucfirst($roleKey) }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-user-btn" data-id="{{ $user->id }}" {{ Auth::id() === $user->id ? 'disabled' : '' }}>
                                                        <i class="bi bi-trash-fill"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pane 5: Tables Setup -->
            <div class="tab-pane fade" id="tables-pane" role="tabpanel" aria-labelledby="tables-tab" tabindex="0">
                <div class="row g-4">
                    <!-- Left Column: Add New Table Form -->
                    <div class="col-lg-4">
                        <div class="card border border-light-subtle shadow-sm p-4" style="border-radius: 16px;">
                            <h4 class="fw-bold text-dark mb-3"><i class="bi bi-plus-circle-fill text-success me-2"></i>Add New Table</h4>
                            <form id="addTableForm" novalidate>
                                <div class="mb-3">
                                    <label for="table_number" class="form-label small fw-semibold text-secondary">TABLE NUMBER</label>
                                    <input type="text" class="form-control form-control-lg" name="table_number" id="table_number" placeholder="e.g. T6" required style="border-radius: 10px;">
                                </div>
                                <div class="mb-3">
                                    <label for="capacity" class="form-label small fw-semibold text-secondary">SEATING CAPACITY</label>
                                    <input type="number" class="form-control form-control-lg" name="capacity" id="capacity" min="1" placeholder="e.g. 4" required style="border-radius: 10px;">
                                </div>
                                <button type="submit" class="btn btn-success btn-lg w-100 fw-bold mt-2" style="background-color: #15803d; border-radius: 12px;">
                                    <i class="bi bi-save me-1"></i> Save Table
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Right Column: Tables Directory -->
                    <div class="col-lg-8">
                        <div class="card border border-light-subtle shadow-sm p-4" style="border-radius: 16px;">
                            <h4 class="fw-bold text-dark mb-4"><i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Tables Directory</h4>
                            <div class="table-responsive">
                                <table class="table align-middle" style="min-width: 600px;">
                                    <thead class="table-light text-secondary small fw-bold">
                                        <tr>
                                            <th class="ps-3">TABLE NUMBER</th>
                                            <th>SEATING CAPACITY</th>
                                            <th>CURRENT STATUS</th>
                                            <th class="text-end pe-3">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold">
                                        @forelse($tables as $tbl)
                                            <tr>
                                                <td class="ps-3 text-dark fw-bold">{{ $tbl->table_number }}</td>
                                                <td>{{ $tbl->capacity }} Seats</td>
                                                <td>
                                                    @if($tbl->status === 'available')
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Available</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill"><i class="bi bi-dash-circle-fill me-1"></i> Occupied</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-3">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary edit-table-btn" data-id="{{ $tbl->id }}" data-number="{{ $tbl->table_number }}" data-capacity="{{ $tbl->capacity }}" style="border-radius: 8px;">
                                                            <i class="bi bi-pencil-fill"></i> Edit
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-table-btn" data-id="{{ $tbl->id }}" data-status="{{ $tbl->status }}" style="border-radius: 8px;">
                                                            <i class="bi bi-trash-fill"></i> Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-secondary">No tables configured in system database.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modals Section -->
    
    <!-- Modal 1: Add Food Item -->
    <div class="modal fade" id="addFoodItemModal" tabindex="-1" aria-labelledby="addFoodItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="addFoodItemForm" class="modal-content" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addFoodItemModalLabel">Add Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="food_name" class="form-label small fw-semibold">ITEM NAME</label>
                        <input type="text" class="form-control" name="name" id="food_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="food_price" class="form-label small fw-semibold">PRICE (₹)</label>
                        <input type="number" class="form-control" name="price" id="food_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="food_description" class="form-label small fw-semibold">DESCRIPTION</label>
                        <textarea class="form-control" name="description" id="food_description" rows="3"></textarea>
                    </div>
                    <div class="mb-2">
                        <label for="food_status" class="form-label small fw-semibold">AVAILABILITY</label>
                        <select class="form-select" name="status" id="food_status" required>
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary bg-success px-4 text-white" style="background-color: #15803d;">Save Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Edit Food Item -->
    <div class="modal fade" id="editFoodItemModal" tabindex="-1" aria-labelledby="editFoodItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editFoodItemForm" class="modal-content" novalidate>
                <input type="hidden" id="edit_food_id">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editFoodItemModalLabel">Edit Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_food_name" class="form-label small fw-semibold">ITEM NAME</label>
                        <input type="text" class="form-control" name="name" id="edit_food_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_food_price" class="form-label small fw-semibold">PRICE (₹)</label>
                        <input type="number" class="form-control" name="price" id="edit_food_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_food_description" class="form-label small fw-semibold">DESCRIPTION</label>
                        <textarea class="form-control" name="description" id="edit_food_description" rows="3"></textarea>
                    </div>
                    <div class="mb-2">
                        <label for="edit_food_status" class="form-label small fw-semibold">AVAILABILITY</label>
                        <select class="form-select" name="status" id="edit_food_status" required>
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary bg-success px-4 text-white" style="background-color: #15803d;">Update Item</button>
                </div>
            </form>
        </div>
    <!-- Modal 3: Edit Table -->
    <div class="modal fade" id="editTableModal" tabindex="-1" aria-labelledby="editTableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editTableForm" class="modal-content" novalidate style="border-radius: 16px;">
                <input type="hidden" id="edit_table_id">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editTableModalLabel">Edit Table Setup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_table_number" class="form-label small fw-semibold text-secondary">TABLE NUMBER</label>
                        <input type="text" class="form-control" name="table_number" id="edit_table_number" required style="border-radius: 10px;">
                    </div>
                    <div class="mb-2">
                        <label for="edit_table_capacity" class="form-label small fw-semibold text-secondary">SEATING CAPACITY</label>
                        <input type="number" class="form-control" name="capacity" id="edit_table_capacity" min="1" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                    <button type="submit" class="btn btn-primary bg-success px-4 text-white" style="background-color: #15803d; border-radius: 10px; border: none;">Update Table</button>
                </div>
            </form>
        </div>
    </div>



    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Setup AJAX CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Helper functions to update dashboard metrics dynamically
            function updateActiveOrders(diff) {
                const el = $('#stat-active-orders');
                if (el.length) {
                    let count = parseInt(el.text()) || 0;
                    count = Math.max(0, count + diff);
                    el.text(count);
                }
            }

            function updateMenuStats(diff) {
                const el = $('#stat-menu-items');
                if (el.length) {
                    let count = parseInt(el.text()) || 0;
                    count = Math.max(0, count + diff);
                    el.text(count);
                }
            }

            function addToRevenue(amount) {
                const el = $('#stat-total-revenue');
                if (el.length) {
                    let total = parseFloat(el.attr('data-amount')) || 0;
                    total += parseFloat(amount);
                    el.attr('data-amount', total);
                    el.text('₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                }
            }

            // Set up active tab from localStorage if exists (prevents losing tab position on refresh)
            const activeTab = localStorage.getItem('adminActiveTab');
            if (activeTab) {
                const tabTrigger = new bootstrap.Tab($(`#${activeTab}`));
                tabTrigger.show();
            }

            $('.nav-link-admin').on('shown.bs.tab', function(e) {
                localStorage.setItem('adminActiveTab', e.target.id);
            });

            // Generic Validation Highlighting Rules
            const validationOptions = {
                errorElement: 'div',
                errorClass: 'invalid-feedback',
                highlight: function(element) { $(element).addClass('is-invalid'); },
                unhighlight: function(element) { $(element).removeClass('is-invalid'); }
            };

            /* =========================================================================
             * Food Items Actions
             * ========================================================================= */
            
            // Add Food Item AJAX Validation & Submit
            $('#addFoodItemForm').validate({
                ...validationOptions,
                submitHandler: function(form, event) {
                    event.preventDefault();
                    
                    $.ajax({
                        url: "{{ route('admin.food-items.store') }}",
                        type: 'POST',
                        data: $(form).serialize(),
                        success: function(res) {
                            $('#addFoodItemModal').modal('hide');
                            Swal.fire({ icon: 'success', title: 'Success!', text: res.message, confirmButtonColor: '#15803d' })
                                .then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to save item.' });
                        }
                    });
                }
            });

            // Populating Edit Food Item Modal
            $('.edit-food-btn').on('click', function() {
                $('#edit_food_id').val($(this).data('id'));
                $('#edit_food_name').val($(this).data('name'));
                $('#edit_food_price').val($(this).data('price'));
                $('#edit_food_description').val($(this).data('description'));
                $('#edit_food_status').val($(this).data('status'));
                $('#editFoodItemModal').modal('show');
            });

            // Edit Food Item Submit
            $('#editFoodItemForm').validate({
                ...validationOptions,
                submitHandler: function(form, event) {
                    event.preventDefault();
                    const foodId = $('#edit_food_id').val();
                    const name = $('#edit_food_name').val();
                    const price = $('#edit_food_price').val();
                    const description = $('#edit_food_description').val();
                    const status = $('#edit_food_status').val();
                    
                    $.ajax({
                        url: `/admin/food-items/${foodId}`,
                        type: 'PUT',
                        data: $(form).serialize(),
                        success: function(res) {
                            $('#editFoodItemModal').modal('hide');
                            Swal.fire({ icon: 'success', title: 'Updated!', text: res.message, confirmButtonColor: '#15803d' });
                            
                            const editBtn = $(`.edit-food-btn[data-id="${foodId}"]`);
                            const row = editBtn.closest('tr');
                            
                            // Update row text details
                            row.find('td').eq(0).html(`<div class="fw-semibold text-dark">${name}</div>`);
                            row.find('td').eq(1).text(description || '-');
                            row.find('td').eq(2).text('₹' + parseFloat(price).toFixed(2));
                            
                            const badgeCell = row.find('td').eq(3);
                            const toggleBtn = row.find('.toggle-status-btn');
                            if (status === 'available') {
                                badgeCell.html('<span class="badge bg-success-subtle text-success">Available</span>');
                                toggleBtn.removeClass('btn-outline-success').addClass('btn-outline-warning');
                                toggleBtn.attr('title', 'Deactivate Item');
                                toggleBtn.html('<i class="bi bi-x-circle-fill"></i> Deactivate');
                            } else {
                                badgeCell.html('<span class="badge bg-danger-subtle text-danger">Unavailable</span>');
                                toggleBtn.removeClass('btn-outline-warning').addClass('btn-outline-success');
                                toggleBtn.attr('title', 'Activate Item');
                                toggleBtn.html('<i class="bi bi-check-circle-fill"></i> Activate');
                            }
                            
                            // Update edit button data attributes
                            editBtn.attr('data-name', name);
                            editBtn.attr('data-price', price);
                            editBtn.attr('data-description', description);
                            editBtn.attr('data-status', status);
                        },
                        error: function(xhr) {
                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to update item.' });
                        }
                    });
                }
            });

            // Delete Food Item Action
            $('.delete-food-btn').on('click', function() {
                const foodId = $(this).data('id');
                
                Swal.fire({
                    title: 'Delete Menu Item?',
                    text: "This action cannot be undone. All active orders referencing this item will lose connection.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/food-items/${foodId}`,
                            type: 'DELETE',
                            success: function(res) {
                                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, confirmButtonColor: '#15803d' });
                                const row = $(`.delete-food-btn[data-id="${foodId}"]`).closest('tr');
                                row.fadeOut(400, function() { $(this).remove(); });
                                updateMenuStats(-1);
                            },
                            error: function() {
                                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete item.' });
                            }
                        });
                    }
                });
            });

            // Toggle Food Item Status directly
            $('.toggle-status-btn').on('click', function() {
                const foodId = $(this).data('id');
                const btn = $(this);
                const row = btn.closest('tr');
                
                $.ajax({
                    url: `/admin/food-items/${foodId}/status`,
                    type: 'PATCH',
                    success: function(res) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        Toast.fire({ icon: 'success', title: res.message || 'Status updated!' });

                        const newStatus = res.status;
                        const badgeCell = row.find('td').eq(3);
                        
                        if (newStatus === 'available') {
                            badgeCell.html('<span class="badge bg-success-subtle text-success">Available</span>');
                            btn.removeClass('btn-outline-success').addClass('btn-outline-warning');
                            btn.attr('title', 'Deactivate Item');
                            btn.html('<i class="bi bi-x-circle-fill"></i> Deactivate');
                        } else {
                            badgeCell.html('<span class="badge bg-danger-subtle text-danger">Unavailable</span>');
                            btn.removeClass('btn-outline-warning').addClass('btn-outline-success');
                            btn.attr('title', 'Activate Item');
                            btn.html('<i class="bi bi-check-circle-fill"></i> Activate');
                        }

                        row.find('.edit-food-btn').attr('data-status', newStatus);
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
                    }
                });
            });

            /* =========================================================================
             * Order Manager Actions
             * ========================================================================= */

            // Inline Order Status Change handler
            $('.order-status-select').on('change', function() {
                const orderId = $(this).data('id');
                const selectEl = $(this);
                const oldStatus = selectEl.attr('data-current-status');
                const newStatus = selectEl.val();
                const row = selectEl.closest('tr');
                const amount = parseFloat(row.find('.order-amount-cell').attr('data-amount')) || 0;

                $.ajax({
                    url: `/admin/orders/${orderId}/status`,
                    type: 'PATCH',
                    data: { status: newStatus },
                    success: function(res) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        Toast.fire({ icon: 'success', title: res.message || 'Order status updated!' });
                        
                        selectEl.attr('data-current-status', newStatus);

                        if (newStatus === 'completed') {
                            const paymentBtn = row.find('.order-payment-btn');
                            if (paymentBtn.length && paymentBtn.data('status') === 'paid') {
                                paymentBtn.removeClass('btn-danger').addClass('btn-success');
                                paymentBtn.attr('data-status', 'unpaid');
                                paymentBtn.html('<i class="bi bi-check-circle-fill"></i> Paid');
                            }
                        }

                        if (oldStatus !== 'completed' && newStatus === 'completed') {
                            updateActiveOrders(-1);
                            addToRevenue(amount);
                        } else if (oldStatus === 'completed' && newStatus !== 'completed') {
                            updateActiveOrders(1);
                            addToRevenue(-amount);
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update order status.' });
                        selectEl.val(oldStatus);
                    }
                });
            });

            // Inline Order Payment Status Change handler with Payment Mode Picker Modal
            $(document).on('click', '.order-payment-btn', function() {
                const btn = $(this);
                const orderId = btn.data('id');
                const currentStatus = btn.attr('data-status'); // 'paid' or 'unpaid'

                if (currentStatus === 'paid') {
                    // Do nothing - payment cannot be undone
                    return;
                }

                // Show SweetAlert Payment Method Selection Dropdown
                Swal.fire({
                    title: 'Select Payment Method',
                    text: 'Choose the customer payment mode:',
                    icon: 'question',
                    input: 'select',
                    inputOptions: {
                        'cash': 'Cash',
                        'card': 'Credit / Debit Card',
                        'upi': 'UPI QR Checkout'
                    },
                    inputPlaceholder: 'Select payment method',
                    showCancelButton: true,
                    confirmButtonColor: '#15803d',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Confirm Payment',
                    inputValidator: (value) => {
                        return new Promise((resolve) => {
                            if (value) {
                                resolve();
                            } else {
                                resolve('You must select a payment method.');
                            }
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const paymentMethod = result.value;
                        const row = btn.closest('tr');
                        const amount = parseFloat(row.find('.order-amount-cell').attr('data-amount')) || 0;

                        $.ajax({
                            url: `/admin/orders/${orderId}/payment`,
                            type: 'POST',
                            data: {
                                _method: 'PATCH',
                                payment_status: 'paid',
                                payment_method: paymentMethod
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'bottom-end',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                Toast.fire({ icon: 'success', title: res.message || 'Payment recorded successfully!' });
                                
                                // Update button state
                                btn.removeClass('btn-danger btn-outline-danger').addClass('btn-success');
                                btn.attr('data-status', 'paid');
                                btn.data('status', 'paid');
                                btn.html('<i class="bi bi-check-circle-fill"></i> Paid');
                                
                                // If the order status isn't completed yet, auto-complete it
                                const statusSelect = row.find('.order-status-select');
                                if (statusSelect.length && statusSelect.attr('data-current-status') !== 'completed') {
                                    statusSelect.val('completed');
                                    statusSelect.attr('data-current-status', 'completed');
                                    updateActiveOrders(-1);
                                    addToRevenue(amount);
                                }
                            },
                            error: function() {
                                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to record payment.' });
                            }
                        });
                    }
                });
            });

            // Delete Order Action
            $('.delete-order-btn').on('click', function() {
                const orderId = $(this).data('id');
                Swal.fire({
                    title: 'Delete Order Record?',
                    text: "This removes the order completely from database logs and reports.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/orders/${orderId}`,
                            type: 'DELETE',
                            success: function(res) {
                                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, confirmButtonColor: '#15803d' });
                                const row = $(`.delete-order-btn[data-id="${orderId}"]`).closest('tr');
                                const statusSelect = row.find('.order-status-select');
                                if (statusSelect.length && statusSelect.attr('data-current-status') !== 'completed') {
                                    updateActiveOrders(-1);
                                }
                                row.fadeOut(400, function() { $(this).remove(); });
                            },
                            error: function() {
                                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete order.' });
                            }
                        });
                    }
                });
            });

            /* =========================================================================
             * Access Control & User Management Actions
             * ========================================================================= */

            // Toggle permission switches (delegated)
            $(document).on('change', '.permission-switch', function() {
                const role = $(this).data('role');
                const page = $(this).data('page');
                const isAllowed = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('admin.permissions.update') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        role: role,
                        page: page,
                        is_allowed: isAllowed
                    },
                    success: function(res) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        Toast.fire({ icon: 'success', title: res.message || 'Permission updated!' });
                    },
                    error: function(xhr) {
                        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to update permission.' });
                    }
                });
            });

            // Inline user role change (delegated)
            $(document).on('change', '.user-role-select', function() {
                const userId = $(this).data('id');
                const newRole = $(this).val();

                $.ajax({
                    url: `/admin/users/${userId}/role`,
                    type: 'POST',
                    data: {
                        _method: 'PATCH',
                        role: newRole
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        Toast.fire({ icon: 'success', title: res.message || 'User role updated!' });
                    },
                    error: function(xhr) {
                        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to update user role.' });
                    }
                });
            });

            // Delete user button click (delegated)
            $(document).on('click', '.delete-user-btn', function() {
                const userId = $(this).data('id');

                Swal.fire({
                    title: 'Delete Employee Account?',
                    text: "This removes the login credentials for this employee permanently.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/users/${userId}`,
                            type: 'DELETE',
                            success: function(res) {
                                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, confirmButtonColor: '#15803d' });
                                const row = $(`.delete-user-btn[data-id="${userId}"]`).closest('tr');
                                row.fadeOut(400, function() { $(this).remove(); });
                            },
                            error: function(xhr) {
                                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to delete user.' });
                            }
                        });
                    }
                });
            });



            // Initialize Chart.js Analytics
            const dailySalesData = @json($dailySales);
            const topSellingData = @json($topSelling);

            const revenueDates = dailySalesData.map(d => d.date);
            const revenueTotals = dailySalesData.map(d => parseFloat(d.total));

            const topItemNames = topSellingData.map(t => t.name);
            const topItemQtys = topSellingData.map(t => parseInt(t.total_qty));

            // Line Chart
            const ctx1 = document.getElementById('revenueTrendChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: revenueDates,
                    datasets: [{
                        label: 'Revenue (₹)',
                        data: revenueTotals,
                        borderColor: '#15803d',
                        backgroundColor: 'rgba(22, 163, 74, 0.08)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // Doughnut Chart
            const ctx2 = document.getElementById('topItemsChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: topItemNames,
                    datasets: [{
                        label: 'Quantity Sold',
                        data: topItemQtys,
                        backgroundColor: [
                            '#15803d',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444',
                            '#8b5cf6'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Add New Custom Role Action
            $('#add-role-btn').on('click', function() {
                Swal.fire({
                    title: 'Create New Custom Role',
                    text: 'Enter the role display name (e.g. Manager, Cashier):',
                    input: 'text',
                    inputPlaceholder: 'Role name',
                    showCancelButton: true,
                    confirmButtonColor: '#15803d',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Create Role',
                    inputValidator: (value) => {
                        if (!value || value.trim() === '') {
                            return 'You must enter a role name.';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const roleName = result.value.trim();

                        $.ajax({
                            url: "{{ route('admin.roles.create') }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: { role_name: roleName },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Created!',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });

                                    // Add the new role row to the Permissions Matrix table dynamically
                                    const newRow = `
                                        <tr data-role="${response.role_key}">
                                            <td class="fw-bold text-dark text-start">${response.role_label}</td>
                                            <td>
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input permission-switch" type="checkbox" data-role="${response.role_key}" data-page="waiter_terminal">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input permission-switch" type="checkbox" data-role="${response.role_key}" data-page="kitchen_terminal">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input permission-switch" type="checkbox" data-role="${response.role_key}" data-page="admin_panel">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input permission-switch" type="checkbox" data-role="${response.role_key}" data-page="can_insert">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input permission-switch" type="checkbox" data-role="${response.role_key}" data-page="can_update">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input permission-switch" type="checkbox" data-role="${response.role_key}" data-page="can_delete">
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                                    $('#permissions-matrix-table tbody').append(newRow);

                                    // Add option to user role selectors
                                    $('.user-role-select').each(function() {
                                        $(this).append(`<option value="${response.role_key}">${response.role_label}</option>`);
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Failed to create role.'
                                });
                            }
                        });
                    }
                });
            });

            // Filter UI toggle
            function toggleFilterInputs() {
                const type = $('#filter_type').val();
                if (type === 'single') {
                    $('#filter_date_wrapper').show();
                    $('#filter_from_date_wrapper').hide();
                    $('#filter_to_date_wrapper').hide();
                } else if (type === 'range') {
                    $('#filter_date_wrapper').hide();
                    $('#filter_from_date_wrapper').show();
                    $('#filter_to_date_wrapper').show();
                } else {
                    $('#filter_date_wrapper').hide();
                    $('#filter_from_date_wrapper').hide();
                    $('#filter_to_date_wrapper').hide();
                }
            }

            $('#filter_type').on('change', toggleFilterInputs);
            toggleFilterInputs(); // Run once on page load

            // AJAX Order Filter submission
            $('#order-filter-form').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const url = form.attr('action');
                const data = form.serialize();

                // Show spinner loader inside tbody
                $('#orders-list-tbody').html(`
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="spinner-border text-primary animate-spin" role="status" style="color: #4f46e5; width: 2.5rem; height: 2.5rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="mt-2 text-secondary small fw-semibold">Filtering ledger logs...</div>
                        </td>
                    </tr>
                `);

                // Send AJAX GET request
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: data,
                    success: function(htmlRows) {
                        setTimeout(function() {
                            $('#orders-list-tbody').html(htmlRows);
                        }, 300);
                    },
                    error: function(xhr) {
                        $('#orders-list-tbody').html(`
                            <tr>
                                <td colspan="8" class="text-center py-4 text-danger fw-semibold">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Failed to filter data. Please try again.
                                </td>
                            </tr>
                        `);
                    }
                });
            });

            // Handle Reset button click via AJAX
            $('#order-filter-form a.btn-outline-secondary').on('click', function(e) {
                e.preventDefault();
                
                // Clear fields
                $('#filter_type').val('all').trigger('change');
                $('#filter_date').val('');
                $('#from_date').val('');
                $('#to_date').val('');

                // Trigger form submit to load all data via AJAX
                $('#order-filter-form').submit();
            });

            /* =========================================================================
             * Table Management Actions
             * ========================================================================= */

            // Add Table AJAX Validation & Submit
            $('#addTableForm').validate({
                ...validationOptions,
                submitHandler: function(form, event) {
                    event.preventDefault();
                    
                    $.ajax({
                        url: "{{ route('admin.tables.store') }}",
                        type: 'POST',
                        data: $(form).serialize(),
                        success: function(res) {
                            Swal.fire({ icon: 'success', title: 'Success!', text: res.message, confirmButtonColor: '#15803d' })
                                .then(() => {
                                    localStorage.setItem('adminActiveTab', 'tables-tab');
                                    location.reload();
                                });
                        },
                        error: function(xhr) {
                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to save table.', confirmButtonColor: '#dc2626' });
                        }
                    });
                }
            });

            // Populating Edit Table Modal
            $('.edit-table-btn').on('click', function() {
                $('#edit_table_id').val($(this).data('id'));
                $('#edit_table_number').val($(this).data('number'));
                $('#edit_table_capacity').val($(this).data('capacity'));
                $('#editTableModal').modal('show');
            });

            // Edit Table Submit
            $('#editTableForm').validate({
                ...validationOptions,
                submitHandler: function(form, event) {
                    event.preventDefault();
                    const tableId = $('#edit_table_id').val();
                    
                    $.ajax({
                        url: `/admin/tables/${tableId}`,
                        type: 'PUT',
                        data: $(form).serialize(),
                        success: function(res) {
                            $('#editTableModal').modal('hide');
                            Swal.fire({ icon: 'success', title: 'Success!', text: res.message, confirmButtonColor: '#15803d' })
                                .then(() => {
                                    localStorage.setItem('adminActiveTab', 'tables-tab');
                                    location.reload();
                                });
                        },
                        error: function(xhr) {
                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to update table.', confirmButtonColor: '#dc2626' });
                        }
                    });
                }
            });

            // Delete Table Action
            $('.delete-table-btn').on('click', function() {
                const tableId = $(this).data('id');
                const status = $(this).data('status');

                if (status === 'occupied') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cannot Delete Table',
                        text: 'This table is currently occupied and cannot be deleted.',
                        confirmButtonColor: '#dc2626'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Delete Table?',
                    text: 'Are you sure you want to remove this table? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/tables/${tableId}`,
                            type: 'DELETE',
                            success: function(res) {
                                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, confirmButtonColor: '#15803d' })
                                    .then(() => {
                                        localStorage.setItem('adminActiveTab', 'tables-tab');
                                        location.reload();
                                    });
                            },
                            error: function(xhr) {
                                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to delete table.', confirmButtonColor: '#dc2626' });
                            }
                        });
                    }
                });
            });

        });
    </script>

</body>
</html>
