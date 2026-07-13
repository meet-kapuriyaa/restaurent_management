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
    
    <!-- Chart.js Local -->
    <script src="{{ asset('js/chart.js') }}"></script>
    <script src="{{ asset('js/chartjs-plugin-datalabels.js') }}"></script>
    
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

        .admin-category-filter-btn {
            color: #15803d;
            border-color: #15803d;
        }
        .admin-category-filter-btn:hover, .admin-category-filter-btn.active {
            color: #fff !important;
            background: var(--primary-gradient) !important;
            border-color: #15803d !important;
            box-shadow: 0 4px 10px rgba(22, 197, 94, 0.2);
        }

        .admin-order-filter-btn {
            color: #15803d;
            border-color: #15803d;
        }
        .admin-order-filter-btn:hover, .admin-order-filter-btn.active {
            color: #fff !important;
            background: var(--primary-gradient) !important;
            border-color: #15803d !important;
            box-shadow: 0 4px 10px rgba(22, 197, 94, 0.2);
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

        /* Rounded square action buttons matching the user reference image style */
        .btn-action-square {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: none;
            color: #ffffff !important;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 0;
            margin: 0 2px;
            text-decoration: none;
        }

        .btn-action-square i {
            font-size: 1.15rem;
        }

        /* Green status button */
        .btn-action-status {
            background-color: #2ecc71; /* Emerald green */
        }
        .btn-action-status:hover {
            background-color: #27ae60;
            transform: translateY(-1px);
        }
        .btn-action-status.inactive {
            background-color: #ff4757; /* Coral red when inactive */
        }
        .btn-action-status.inactive:hover {
            background-color: #e03d4b;
        }

        /* Cyan edit button */
        .btn-action-edit {
            background-color: #00d2fc; /* Vibrant cyan */
        }
        .btn-action-edit:hover {
            background-color: #00b5da;
            transform: translateY(-1px);
        }

        /* Red delete button */
        .btn-action-delete {
            background-color: #ff4757 !important; /* Warm coral red */
            color: #fff !important;
        }
        .btn-action-delete:hover {
            background-color: #e03d4b !important;
            color: #fff !important;
            transform: translateY(-1px);
        }

        /* Custom Permission Switch Colors */
        .permission-switch-insert.form-check-input:checked {
            background-color: #10b981 !important; /* Emerald Green */
            border-color: #10b981 !important;
        }
        .permission-switch-update.form-check-input:checked {
            background-color: #f59e0b !important; /* Amber Yellow */
            border-color: #f59e0b !important;
        }
        .permission-switch-delete.form-check-input:checked {
            background-color: #ef4444 !important; /* Crimson Red */
            border-color: #ef4444 !important;
        }

        /* Audit Sub-Tabs Style */
        .audit-sub-link.active {
            background-color: #15803d !important;
            color: #fff !important;
            border-color: #15803d !important;
        }
        .audit-sub-link:hover:not(.active) {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
            border-color: #cbd5e1 !important;
        }

        /* Sticky Table Footer & Pagination styling */
        .sticky-table-footer {
            position: sticky;
            bottom: 0;
            background-color: #f8fafc !important;
            z-index: 5;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.08);
        }
        .sticky-table-footer td {
            background-color: #f1f5f9 !important;
            border-top: 2px solid #cbd5e1 !important;
            font-weight: 700;
        }
        .admin-pagination .page-link {
            color: #15803d !important;
            border-radius: 6px;
            margin: 0 2px;
            cursor: pointer;
        }
        .admin-pagination .page-item.active .page-link {
            background-color: #15803d !important;
            border-color: #15803d !important;
            color: #fff !important;
        }
        .admin-pagination .page-item.disabled .page-link {
            color: #94a3b8 !important;
            cursor: not-allowed;
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
                        <i class="bi bi-shop me-1"></i> Order Panel
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

    <div class="container-fluid px-md-5 px-3">
        
        <!-- Tabbed Navigation Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <ul class="nav nav-tabs border-0" id="adminTabs" role="tablist">
                @php $isActiveSet = false; @endphp
                @foreach($modules as $mod)
                    @php
                        if ($mod->url === '#audit-pane' && !\App\Models\Feature::isActive('audit_reports')) {
                            continue;
                        }
                        $isActive = !$isActiveSet;
                        if ($isActive) {
                            $isActiveSet = true;
                        }
                    @endphp
                    <li class="nav-item" role="presentation">
                        <button class="nav-link-admin {{ $isActive ? 'active' : '' }}" 
                                id="{{ str_replace('#', '', $mod->url) }}-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="{{ $mod->url }}" 
                                type="button" 
                                role="tab" 
                                aria-controls="{{ str_replace('#', '', $mod->url) }}" 
                                aria-selected="{{ $isActive ? 'true' : 'false' }}">
                            <i class="bi {{ $mod->icon_class }} me-1"></i> {{ $mod->title }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="tab-content" id="adminTabContent">
            
            <!-- Pane 1: Analytics Hub -->
            <div class="tab-pane fade show active" id="dashboard-pane" role="tabpanel" aria-labelledby="dashboard-tab" tabindex="0">
                <div class="row g-4 mb-5">
                    <!-- Card 1: Total Gross Sales -->
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0 !important;">
                            <div>
                                <span class="text-secondary small fw-semibold uppercase tracking-wider block mb-1">TOTAL GROSS SALES</span>
                                <h3 id="stat-total-revenue" class="fw-bold mb-0" style="color: #166534 !important;" data-amount="{{ $totalRevenue }}">₹{{ number_format($totalRevenue, 2) }}</h3>
                                <div class="small fw-semibold mt-1 {{ $revenueChange >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $revenueTrendText }}
                                </div>
                            </div>
                            <div class="stat-icon" style="background: #15803d; color: #fff;">
                                <i class="bi bi-currency-rupee"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Card 2: Total Orders Filled -->
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card h-100" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd !important;">
                            <div>
                                <span class="text-secondary small fw-semibold uppercase tracking-wider block mb-1">TOTAL ORDERS FILLED</span>
                                <h3 id="stat-total-orders" class="fw-bold mb-0" style="color: #075985 !important;">{{ $totalOrdersFilled }}</h3>
                                <div class="text-muted small fw-semibold mt-1">
                                    {{ $currentlyPreparing }} currently preparing
                                </div>
                            </div>
                            <div class="stat-icon" style="background: #0284c7; color: #fff;">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Card 3: Top Selling Item -->
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card h-100" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fcd34d !important;">
                            <div>
                                <span class="text-secondary small fw-semibold uppercase tracking-wider block mb-1">TOP SELLING ITEM</span>
                                <h3 class="fw-bold mb-0" style="color: #92400e !important; font-size: 1.15rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px;" title="{{ $topSellingName }} ({{ $topSellingQty }} sold)">{{ $topSellingName }} ({{ $topSellingQty }} sold)</h3>
                                <div class="small mt-1 text-muted opacity-0">&nbsp;</div>
                            </div>
                            <div class="stat-icon" style="background: #d97706; color: #fff;">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Card 4: Lowest Selling Item -->
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card h-100" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 1px solid #fecaca !important;">
                            <div>
                                <span class="text-secondary small fw-semibold uppercase tracking-wider block mb-1">LOWEST SELLING ITEM</span>
                                <h3 class="fw-bold mb-0" style="color: #991b1b !important; font-size: 1.15rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px;" title="{{ $lowestSellingName }} ({{ $lowestSellingQty }} sold)">{{ $lowestSellingName }} ({{ $lowestSellingQty }} sold)</h3>
                                <div class="small mt-1 text-muted opacity-0">&nbsp;</div>
                            </div>
                            <div class="stat-icon" style="background: #dc2626; color: #fff;">
                                <i class="bi bi-graph-down-arrow"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Report Overview -->
                <div class="row g-4 mb-5">
                    <!-- Daily Sales Report -->
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0 !important; border-left: 5px solid #15803d !important;">
                            <div>
                                <span class="text-secondary small fw-semibold uppercase tracking-wider block mb-1">DAILY SALES REPORT</span>
                                <h3 class="fw-bold mb-0" style="color: #166534 !important;">₹{{ number_format($dailySalesAmount, 2) }}</h3>
                                <div class="text-muted small fw-semibold mt-1">
                                    {{ $dailyOrdersCount }} orders processed
                                </div>
                                <div class="text-secondary small mt-0.5" style="font-size: 0.75rem;">
                                    {{ $dailyDateLabel }}
                                </div>
                            </div>
                            <div class="stat-icon" style="background: #15803d; color: #fff;">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Weekly Sales Report -->
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card h-100" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd !important; border-left: 5px solid #0284c7 !important;">
                            <div>
                                <span class="text-secondary small fw-semibold uppercase tracking-wider block mb-1">WEEKLY SALES REPORT</span>
                                <h3 class="fw-bold mb-0" style="color: #075985 !important;">₹{{ number_format($weeklySalesAmount, 2) }}</h3>
                                <div class="text-muted small fw-semibold mt-1">
                                    {{ $weeklyOrdersCount }} orders processed
                                </div>
                                <div class="text-secondary small mt-0.5" style="font-size: 0.75rem;">
                                    {{ $weeklyDateLabel }}
                                </div>
                            </div>
                            <div class="stat-icon" style="background: #0284c7; color: #fff;">
                                <i class="bi bi-calendar-range"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Monthly Sales Report -->
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card h-100" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fcd34d !important; border-left: 5px solid #d97706 !important;">
                            <div>
                                <span class="text-secondary small fw-semibold uppercase tracking-wider block mb-1">MONTHLY SALES REPORT</span>
                                <h3 class="fw-bold mb-0" style="color: #92400e !important;">₹{{ number_format($monthlySalesAmount, 2) }}</h3>
                                <div class="text-muted small fw-semibold mt-1">
                                    {{ $monthlyOrdersCount }} orders processed
                                </div>
                                <div class="text-secondary small mt-0.5" style="font-size: 0.75rem;">
                                    {{ $monthlyDateLabel }}
                                </div>
                            </div>
                            <div class="stat-icon" style="background: #d97706; color: #fff;">
                                <i class="bi bi-calendar3"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Yearly Sales Report -->
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card h-100" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); border: 1px solid #e9d5ff !important; border-left: 5px solid #7c3aed !important;">
                            <div>
                                <span class="text-secondary small fw-semibold uppercase tracking-wider block mb-1">YEARLY SALES REPORT</span>
                                <h3 class="fw-bold mb-0" style="color: #5b21b6 !important;">₹{{ number_format($yearlySalesAmount, 2) }}</h3>
                                <div class="text-muted small fw-semibold mt-1">
                                    {{ $yearlyOrdersCount }} orders processed
                                </div>
                                <div class="text-secondary small mt-0.5" style="font-size: 0.75rem;">
                                    {{ $yearlyDateLabel }}
                                </div>
                            </div>
                            <div class="stat-icon" style="background: #7c3aed; color: #fff;">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row g-4 mb-5">
                    <div class="col-lg-7">
                        <div class="card-admin p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill text-success me-2" style="color: #15803d;"></i>Day-Wise Revenue Trend</h5>
                            <div style="height: 300px; position: relative;">
                                <canvas id="revenueTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card-admin p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill text-success me-2" style="color: #15803d;"></i>Sales Share by Category</h5>
                            <div style="height: 300px; position: relative; display: flex; justify-content: center;">
                                <canvas id="topItemsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Analytics Row -->
                <div class="row g-4 mb-5">
                    <div class="col-lg-6">
                        <div class="card-admin p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-clock-fill text-success me-2" style="color: #15803d;"></i>Hourly Sales Distribution (Busy Hours)</h5>
                            <div style="height: 300px; position: relative;">
                                <canvas id="hourlySalesChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card-admin p-4" style="height: 100%;">
                            <h5 class="fw-bold mb-3"><i class="bi bi-trophy-fill text-success me-2" style="color: #15803d;"></i>Top Staff/Waiter Performance</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Waiter Name</th>
                                            <th class="text-center">Orders Handled</th>
                                            <th class="text-end">Revenue Generated</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($waiterPerformance as $performance)
                                            <tr>
                                                <td class="fw-semibold text-dark"><i class="bi bi-person-badge text-muted me-1"></i> {{ $performance->name }}</td>
                                                <td class="text-center">{{ $performance->total_orders }}</td>
                                                <td class="text-end fw-semibold text-success">₹{{ number_format($performance->total_revenue, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-secondary small">No waiter activity logged yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pane 2: Menu Management -->
            <div class="tab-pane fade" id="menu-pane" role="tabpanel" aria-labelledby="menu-tab" tabindex="0">
                <div class="card-admin">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Menu Items Directory</h4>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-dark px-3 fw-semibold d-inline-flex align-items-center gap-1.5" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
                                <i class="bi bi-tags-fill"></i> Manage Categories
                            </button>
                            <button type="button" class="btn btn-primary bg-success border-0 px-3 fw-semibold text-white d-inline-flex align-items-center gap-1.5" style="background-color: #15803d; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#addFoodItemModal">
                                <i class="bi bi-plus-lg"></i> Add Food Item
                            </button>
                        </div>
                    </div>

                    <!-- Category Filtering Tabs -->
                    <div class="d-flex flex-wrap gap-2 mb-4" id="admin-category-filter-bar">
                        <button type="button" class="btn btn-sm btn-outline-secondary active admin-category-filter-btn px-3 py-1.5 fw-semibold" style="border-radius: 8px;" data-category="all">
                            All
                        </button>
                        @foreach($categories as $cat)
                            <button type="button" class="btn btn-sm btn-outline-secondary admin-category-filter-btn px-3 py-1.5 fw-semibold" style="border-radius: 8px;" data-category="{{ $cat->id }}">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                        <button type="button" class="btn btn-sm btn-outline-secondary admin-category-filter-btn px-3 py-1.5 fw-semibold" style="border-radius: 8px;" data-category="uncategorized">
                            Uncategorized
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($foodItems as $item)
                                    <tr class="admin-food-row" data-category="{{ $item->category_id ?: 'uncategorized' }}">
                                        <td>
                                            <img src="{{ $item->image_path ?: '/logo.jpg' }}" alt="{{ $item->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;" class="border">
                                        </td>
                                        <td class="fw-bold text-dark">{{ $item->name }}</td>
                                        <td class="text-secondary small" style="max-width: 300px;">{{ $item->description ?: '-' }}</td>
                                        <td class="fw-semibold">₹{{ number_format($item->price, 2) }}</td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary">{{ $item->category ? $item->category->name : 'Uncategorized' }}</span>
                                        </td>
                                        <td>
                                            @if($item->status === 'available')
                                                <span class="badge bg-success-subtle text-success">Available</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">Unavailable</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <button type="button" class="btn-action-square btn-action-status toggle-status-btn {{ $item->status === 'unavailable' ? 'inactive' : '' }}" 
                                                        data-id="{{ $item->id }}"
                                                        title="{{ $item->status === 'available' ? 'Deactivate Menu Item' : 'Activate Menu Item' }}">
                                                    @if($item->status === 'available')
                                                        <i class="bi bi-eye-fill"></i>
                                                    @else
                                                        <i class="bi bi-eye-slash-fill"></i>
                                                    @endif
                                                </button>
                                                <button type="button" class="btn-action-square btn-action-edit edit-food-btn" 
                                                        data-id="{{ $item->id }}"
                                                        data-name="{{ $item->name }}"
                                                        data-price="{{ $item->price }}"
                                                        data-description="{{ $item->description }}"
                                                        data-status="{{ $item->status }}"
                                                        data-category="{{ $item->category_id }}"
                                                        data-image="{{ $item->image_path }}"
                                                        title="Edit Menu Item">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button type="button" class="btn-action-square btn-action-delete delete-food-btn" 
                                                        data-id="{{ $item->id }}"
                                                        title="Delete Menu Item">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-secondary">No food items listed in menu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div id="menu-pagination-container" class="mt-3 admin-pagination"></div>
                </div>
            </div>

            <!-- Pane 3: Order Manager -->
            <div class="tab-pane fade" id="orders-pane" role="tabpanel" aria-labelledby="orders-tab" tabindex="0">
                <div class="card-admin">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                        <h4 class="fw-bold mb-0">Total Orders Ledger</h4>
                        <button type="button" class="btn btn-emerald px-3 py-2 d-flex align-items-center gap-2 shadow-sm text-white fw-semibold" onclick="downloadOrdersCSV()" style="background-color: #047857; border: none; border-radius: 8px; transition: background-color 0.2s; white-space: nowrap;">
                            <i class="bi bi-file-earmark-spreadsheet-fill fs-5"></i> Export Orders Log
                        </button>
                    </div>

                    <!-- Filter Controls -->
                    <form id="order-filter-form" action="{{ route('admin.index') }}" method="GET" class="mb-4 bg-light p-3 border rounded-3">
                        <!-- Date Input Fields (Shown dynamically above the buttons) -->
                        <div id="order-date-inputs-container" class="mb-3 p-3 bg-white border rounded-3 shadow-sm" style="display: none;">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4" id="order-filter-single-wrapper" style="display: none;">
                                    <label for="filter_date" class="form-label text-secondary fw-semibold small mb-1">Select Date</label>
                                    <input type="date" name="filter_date" id="filter_date" class="form-control form-control-sm" value="{{ request('filter_date') }}" style="border-radius: 8px;">
                                </div>
                                <div class="col-md-4" id="order-filter-from-wrapper" style="display: none;">
                                    <label for="from_date" class="form-label text-secondary fw-semibold small mb-1">From Date</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}" style="border-radius: 8px;">
                                </div>
                                <div class="col-md-4" id="order-filter-to-wrapper" style="display: none;">
                                    <label for="to_date" class="form-label text-secondary fw-semibold small mb-1">To Date</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}" style="border-radius: 8px;">
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary fw-semibold px-4 d-inline-flex align-items-center justify-content-center" style="background-color: #15803d; border: none; height: 36px; border-radius: 8px;">
                                        <i class="bi bi-funnel-fill me-1"></i> Apply Filter
                                    </button>
                                    <button type="button" id="reset-order-date-btn" class="btn btn-sm btn-outline-secondary fw-semibold px-3 d-inline-flex align-items-center justify-content-center" style="height: 36px; border-radius: 8px;">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden field to keep track of filter_type -->
                        <input type="hidden" name="filter_type" id="filter_type" value="{{ request('filter_type', 'all') }}">

                        <!-- Filter Buttons/Pills Bar -->
                        <div class="d-flex flex-wrap gap-2" id="admin-order-filter-bar">
                            <button type="button" class="btn btn-sm btn-outline-secondary {{ request('filter_type', 'all') === 'today' ? 'active' : '' }} admin-order-filter-btn px-3 py-1.5 fw-semibold" style="border-radius: 8px;" data-filter-type="today">
                                Today
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary {{ request('filter_type', 'all') === 'all' ? 'active' : '' }} admin-order-filter-btn px-3 py-1.5 fw-semibold" style="border-radius: 8px;" data-filter-type="all">
                                All Data
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary {{ request('filter_type', 'all') === 'single' ? 'active' : '' }} admin-order-filter-btn px-3 py-1.5 fw-semibold" style="border-radius: 8px;" data-filter-type="single">
                                Single Date
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary {{ request('filter_type', 'all') === 'range' ? 'active' : '' }} admin-order-filter-btn px-3 py-1.5 fw-semibold" style="border-radius: 8px;" data-filter-type="range">
                                Custom Range
                            </button>
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
                    <div id="orders-pagination-container" class="mt-3 admin-pagination"></div>
                </div>
            </div>

            <!-- Pane 4: Access Control & Permissions Matrix -->
            <div class="tab-pane fade" id="access-pane" role="tabpanel" aria-labelledby="access-tab" tabindex="0">
                <div class="row g-4">
                    <!-- Top: Employee Accounts -->
                    <div class="col-12">
                        <div class="card-admin">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="fw-bold mb-0"><i class="bi bi-people-fill text-success me-2" style="color:#15803d;"></i>Employee Accounts</h4>
                                <button type="button" class="btn btn-sm btn-success text-white fw-semibold" style="background-color: #15803d;" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                                    <i class="bi bi-person-plus-fill me-1"></i> Add Employee
                                </button>
                            </div>
                            <p class="text-secondary small mb-4">Manage employee logins, active roles, or delete user accounts.</p>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="employee-accounts-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Email Address</th>
                                            <th>Role</th>
                                            @if(App\Models\Feature::isActive('salary_payroll'))
                                                <th>Salary</th>
                                            @endif
                                            <th class="text-end" style="padding-right: 20px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr data-user-id="{{ $user->id }}">
                                                <td class="fw-semibold text-dark user-name-cell">{{ $user->name }}</td>
                                                <td class="text-secondary user-email-cell">{{ $user->email }}</td>
                                                <td class="user-role-cell">{{ ucfirst($user->role) }}</td>
                                                @if(App\Models\Feature::isActive('salary_payroll'))
                                                    <td class="salary-cell">
                                                        @if($user->role !== 'admin')
                                                            ₹{{ number_format($user->salary, 2) }}
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                @endif
                                                <td class="text-end" style="padding-right: 20px;">
                                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                                        @if($user->role === 'admin')
                                                            <button type="button" class="btn-action-square btn-action-status disabled" disabled title="System Protected">
                                                                <i class="bi bi-lock-fill"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn-action-square btn-action-status toggle-user-status-btn {{ !$user->is_active ? 'inactive' : '' }}" 
                                                                    data-id="{{ $user->id }}"
                                                                    data-status="{{ $user->is_active ? 1 : 0 }}"
                                                                    title="{{ $user->is_active ? 'Deactivate Employee' : 'Activate Employee' }}">
                                                                @if($user->is_active)
                                                                    <i class="bi bi-eye-fill"></i>
                                                                @else
                                                                    <i class="bi bi-eye-slash-fill"></i>
                                                                @endif
                                                            </button>
                                                            <button type="button" class="btn-action-square btn-action-edit edit-user-btn" 
                                                                    data-id="{{ $user->id }}" 
                                                                    data-name="{{ $user->name }}" 
                                                                    data-email="{{ $user->email }}" 
                                                                    data-role="{{ $user->role }}" 
                                                                    data-salary="{{ $user->salary }}"
                                                                    title="Edit Employee">
                                                                <i class="bi bi-pencil-fill"></i>
                                                            </button>
                                                            <button type="button" class="btn-action-square btn-action-delete delete-user-btn" 
                                                                    data-id="{{ $user->id }}" 
                                                                    title="Delete Employee">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div id="employees-pagination-container" class="mt-3 admin-pagination"></div>
                        </div>
                    </div>

                    <!-- Bottom: Role Permissions Matrix -->
                    <div class="col-12">
                        <div class="card-admin">
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
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($availableRoles as $roleKey)
                                            @php
                                                $roleLabel = ucfirst($roleKey);
                                                $roleActiveRecord = \App\Models\RolePermission::where('role', $roleKey)->where('page', 'role_active')->first();
                                                $isRoleActive = $roleActiveRecord ? (bool)$roleActiveRecord->is_allowed : true;
                                            @endphp
                                            <tr data-role="{{ $roleKey }}">
                                                <td class="fw-bold text-dark text-start">{{ $roleLabel }}</td>
                                                @foreach(['waiter_terminal', 'kitchen_terminal', 'admin_panel', 'can_insert', 'can_update', 'can_delete'] as $pageKey)
                                                    <td>
                                                        <div class="form-check form-switch d-inline-block">
                                                            @php
                                                                $customClass = '';
                                                                if ($pageKey === 'can_insert') $customClass = 'permission-switch-insert';
                                                                elseif ($pageKey === 'can_update') $customClass = 'permission-switch-update';
                                                                elseif ($pageKey === 'can_delete') $customClass = 'permission-switch-delete';
                                                            @endphp
                                                            <input class="form-check-input permission-switch {{ $customClass }}" type="checkbox" 
                                                                   data-role="{{ $roleKey }}" data-page="{{ $pageKey }}"
                                                                   {{ $checkAllowed($roleKey, $pageKey) ? 'checked' : '' }}
                                                                   {{ $roleKey === 'admin' ? 'disabled' : '' }}>
                                                        </div>
                                                    </td>
                                                @endforeach
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @if($roleKey === 'admin')
                                                            <button type="button" class="btn-action-square btn-action-status disabled" disabled title="System Protected">
                                                                <i class="bi bi-lock-fill"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn-action-square btn-action-status toggle-role-status-btn {{ !$isRoleActive ? 'inactive' : '' }}" 
                                                                    data-role="{{ $roleKey }}"
                                                                    data-status="{{ $isRoleActive ? 1 : 0 }}"
                                                                    title="{{ $isRoleActive ? 'Deactivate Role' : 'Activate Role' }}">
                                                                @if($isRoleActive)
                                                                    <i class="bi bi-eye-fill"></i>
                                                                @else
                                                                    <i class="bi bi-eye-slash-fill"></i>
                                                                @endif
                                                            </button>
                                                            <button type="button" class="btn-action-square btn-action-delete delete-role-btn" 
                                                                    data-role="{{ $roleKey }}"
                                                                    title="Delete Role">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Pending Approval Requests -->
                    <div class="card-admin mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-person-plus-fill text-success me-2" style="color: #15803d;"></i>Pending Approval Requests
                            </h4>
                            <span class="badge bg-success text-white fw-bold px-3 py-1.5" style="border-radius: 50rem; background-color: #15803d !important;" id="pending-requests-count">
                                {{ $pendingUsers->count() }} {{ $pendingUsers->count() === 1 ? 'Request' : 'Requests' }}
                            </span>
                        </div>
                        <p class="text-secondary small mb-4">Newly registered accounts waiting for role assignment and system access activation.</p>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center" id="pending-users-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-start">Name</th>
                                        <th class="text-start">Email Address</th>
                                        <th>Registered At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingUsers as $pending)
                                        <tr data-user-id="{{ $pending->id }}">
                                            <td class="fw-semibold text-dark text-start user-name-cell">{{ $pending->name }}</td>
                                            <td class="text-secondary text-start user-email-cell">{{ $pending->email }}</td>
                                            <td class="text-secondary">{{ $pending->created_at->format('d M Y, h:i A') }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button type="button" class="btn-action-square bg-success text-white assign-role-btn" 
                                                            style="background-color: #15803d !important;"
                                                            data-id="{{ $pending->id }}"
                                                            data-name="{{ $pending->name }}"
                                                            data-email="{{ $pending->email }}"
                                                            title="Assign Role & Access">
                                                        <i class="bi bi-person-check-fill"></i>
                                                    </button>
                                                    <button type="button" class="btn-action-square btn-action-delete delete-user-btn" 
                                                            data-id="{{ $pending->id }}" 
                                                            title="Reject & Delete Account">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="no-pending-requests-row">
                                            <td colspan="4" class="text-center py-4 text-secondary small">No pending approval requests at the moment.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
                                                    @elseif($tbl->status === 'occupied')
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill"><i class="bi bi-dash-circle-fill me-1"></i> Occupied</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill"><i class="bi bi-eye-slash-fill me-1"></i> Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-3">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        <button type="button" class="btn-action-square btn-action-status toggle-table-status-btn {{ $tbl->status === 'unavailable' ? 'inactive' : '' }}" 
                                                                data-id="{{ $tbl->id }}"
                                                                data-status="{{ $tbl->status }}"
                                                                title="{{ $tbl->status === 'unavailable' ? 'Activate Table' : 'Deactivate Table' }}">
                                                            @if($tbl->status === 'unavailable')
                                                                <i class="bi bi-eye-slash-fill"></i>
                                                            @else
                                                                <i class="bi bi-eye-fill"></i>
                                                            @endif
                                                        </button>
                                                        <button type="button" class="btn-action-square btn-action-edit edit-table-btn" 
                                                                data-id="{{ $tbl->id }}" 
                                                                data-number="{{ $tbl->table_number }}" 
                                                                data-capacity="{{ $tbl->capacity }}"
                                                                title="Edit Table Details">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>
                                                        <button type="button" class="btn-action-square btn-action-delete delete-table-btn" 
                                                                data-id="{{ $tbl->id }}" 
                                                                data-status="{{ $tbl->status }}"
                                                                title="Delete Table">
                                                            <i class="bi bi-trash-fill"></i>
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
                            <div id="tables-pagination-container" class="mt-3 admin-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pane 6: Customer CRM -->
            <div class="tab-pane fade" id="crm-pane" role="tabpanel" aria-labelledby="crm-tab" tabindex="0">
                <div class="card-admin p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                        <div>
                            <h4 class="fw-bold mb-1"><i class="bi bi-people-fill text-success me-2" style="color: #15803d;"></i>Customer CRM</h4>
                            <p class="text-secondary small mb-0">Monitor customer visits and total spends.</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-emerald px-3 py-2 d-flex align-items-center gap-2 shadow-sm text-white fw-semibold" onclick="downloadCRMCSV()" style="background-color: #047857; border: none; border-radius: 8px; transition: background-color 0.2s; white-space: nowrap;">
                                <i class="bi bi-file-earmark-spreadsheet-fill fs-5"></i> Export CRM CSV
                            </button>
                            <div style="width: 250px;">
                                <input type="text" id="crm-search-input" class="form-control" placeholder="Search customer name or phone..." style="border-radius: 8px;">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="crm-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Phone Number</th>
                                    <th class="text-center">Total Visits</th>
                                    <th class="text-end">Total Spend</th>
                                    <th>Member Since</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            {{ $customer->name }}
                                            @if($customer->total_spend >= 1000)
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill ms-1" style="font-size: 0.65rem;">VIP</span>
                                            @endif
                                        </td>
                                        <td class="fw-semibold text-dark"><i class="bi bi-telephone text-muted me-1"></i> {{ $customer->phone_number }}</td>
                                        <td class="text-center fw-medium">{{ $customer->total_visits }}</td>
                                        <td class="text-end fw-semibold text-success">₹{{ number_format($customer->total_spend, 2) }}</td>
                                        <td class="text-secondary small">{{ $customer->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-secondary">No customers registered in the CRM directory yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div id="crm-pagination-container" class="mt-3 admin-pagination"></div>
                </div>
            </div>

            <!-- Pane 7: Audit Reports -->
            @if(App\Models\Feature::isActive('audit_reports'))
            <div class="tab-pane fade" id="audit-pane" role="tabpanel" aria-labelledby="audit-tab" tabindex="0">
                @php
                    $settledOrders = $allSettledOrders;
                    $totalNetIncome = $settledOrders->sum('total_amount');
                    $settledCount = $settledOrders->count();
                    $avgBasket = $settledCount > 0 ? $totalNetIncome / $settledCount : 0.00;

                    $sumSubtotal = 0;
                    $sumDiscount = 0;
                    $sumTax = 0;
                    foreach($settledOrders as $sOrder) {
                        $sumSubtotal += ($sOrder->total_amount - $sOrder->tax_amount + $sOrder->discount_amount);
                        $sumDiscount += $sOrder->discount_amount;
                        $sumTax += $sOrder->tax_amount;
                    }
                @endphp

                <div class="card-admin p-4 mb-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                        <div>
                            <h4 class="fw-bold mb-1"><i class="bi bi-file-earmark-bar-graph text-success me-2" style="color: #15803d;"></i>Audit Reports</h4>
                            <p class="text-secondary small mb-0">Review financial records, settled totals, and tax audit ledgers dynamically calculated from orders.</p>
                        </div>
                    </div>

                    <!-- Sub-tabs navigation pills -->
                    <ul class="nav nav-pills mb-4 gap-2" id="auditSubTabs" role="tablist" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; list-style: none; padding-left: 0;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active audit-sub-link btn-sm px-4 py-2 fw-semibold shadow-sm" id="sales-ledger-subtab" data-bs-toggle="pill" data-bs-target="#sales-ledger-pane" type="button" role="tab" aria-selected="true" style="border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; color: #475569; transition: all 0.2s;">
                                <i class="bi bi-journal-text me-1"></i> Sales Ledger
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link audit-sub-link btn-sm px-4 py-2 fw-semibold shadow-sm" id="tax-gst-subtab" data-bs-toggle="pill" data-bs-target="#tax-gst-pane" type="button" role="tab" aria-selected="false" style="border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; color: #475569; transition: all 0.2s;">
                                <i class="bi bi-percent me-1"></i> Tax & GST Audits
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="auditSubTabContent">
                        <!-- Sub-Pane 1: Sales Ledger -->
                        <div class="tab-pane fade show active" id="sales-ledger-pane" role="tabpanel" aria-labelledby="sales-ledger-subtab">
                            
                            <!-- KPI Summary Cards Row -->
                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <div class="p-3 border rounded shadow-sm d-flex align-items-center gap-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #bbf7d0 !important;">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #15803d; color: #fff;">
                                            <i class="bi bi-currency-rupee fs-4"></i>
                                        </div>
                                        <div>
                                            <span class="text-secondary small fw-semibold uppercase block mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">TOTAL NET INCOME</span>
                                            <h4 class="fw-bold mb-0 text-success" id="audit-kpi-net-income" style="color: #166534 !important;">₹{{ number_format($totalNetIncome, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded shadow-sm d-flex align-items-center gap-3" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-color: #bae6fd !important;">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #0284c7; color: #fff;">
                                            <i class="bi bi-receipt-cutoff fs-4"></i>
                                        </div>
                                        <div>
                                            <span class="text-secondary small fw-semibold uppercase block mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">TOTAL SETTLED INVOICES</span>
                                            <h4 class="fw-bold mb-0 text-info" id="audit-kpi-settled-count" style="color: #075985 !important;">{{ $settledCount }} bills</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded shadow-sm d-flex align-items-center gap-3" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); border-color: #e9d5ff !important;">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #7c3aed; color: #fff;">
                                            <i class="bi bi-basket3 fs-4"></i>
                                        </div>
                                        <div>
                                            <span class="text-secondary small fw-semibold uppercase block mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">AVERAGE ORDER BASKET</span>
                                            <h4 class="fw-bold mb-0 text-primary" id="audit-kpi-avg-basket" style="color: #5b21b6 !important;">₹{{ number_format($avgBasket, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Selection Panel (Shown dynamically above the buttons) -->
                            <div id="audit-date-inputs-container" class="mb-3 p-3 bg-light border rounded-3 shadow-sm" style="display: none; width: 100%;">
                                <div class="row g-3 align-items-end">
                                    <!-- Day picker: full calendar -->
                                    <div class="col-md-3" id="audit-filter-day-wrapper" style="display: none;">
                                        <label for="audit_filter_date" class="form-label text-secondary fw-semibold small mb-1">Select Date</label>
                                        <input type="date" id="audit_filter_date" class="form-control form-control-sm" style="border-radius: 8px;">
                                    </div>
                                    <!-- Month picker: only month and year -->
                                    <div class="col-md-3" id="audit-filter-month-wrapper" style="display: none;">
                                        <label for="audit_filter_month" class="form-label text-secondary fw-semibold small mb-1">Select Month</label>
                                        <input type="month" id="audit_filter_month" class="form-control form-control-sm" style="border-radius: 8px;">
                                    </div>
                                    <!-- Year picker: select option dropdown of years -->
                                    <div class="col-md-3" id="audit-filter-year-wrapper" style="display: none;">
                                        <label for="audit_filter_year" class="form-label text-secondary fw-semibold small mb-1">Select Year</label>
                                        <select id="audit_filter_year" class="form-select form-select-sm" style="border-radius: 8px;">
                                            <option value="">Choose Year</option>
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026" selected>2026</option>
                                            <option value="2027">2027</option>
                                            <option value="2028">2028</option>
                                            <option value="2029">2029</option>
                                            <option value="2030">2030</option>
                                        </select>
                                    </div>
                                    <!-- Custom Range picker -->
                                    <div class="col-md-3" id="audit-filter-from-wrapper" style="display: none;">
                                        <label for="audit_from_date" class="form-label text-secondary fw-semibold small mb-1">From Date</label>
                                        <input type="date" id="audit_from_date" class="form-control form-control-sm" style="border-radius: 8px;">
                                    </div>
                                    <div class="col-md-3" id="audit-filter-to-wrapper" style="display: none;">
                                        <label for="audit_to_date" class="form-label text-secondary fw-semibold small mb-1">To Date</label>
                                        <input type="date" id="audit_to_date" class="form-control form-control-sm" style="border-radius: 8px;">
                                    </div>
                                    <!-- Action Buttons -->
                                    <div class="col-md-3 d-flex gap-2">
                                        <button type="button" id="apply-audit-filter-btn" class="btn btn-sm btn-primary fw-semibold px-4 d-inline-flex align-items-center justify-content-center" style="background-color: #15803d; border: none; height: 36px; border-radius: 8px;">
                                            <i class="bi bi-funnel-fill me-1"></i> Apply
                                        </button>
                                        <button type="button" id="reset-audit-filter-btn" class="btn btn-sm btn-outline-secondary fw-semibold px-3 d-inline-flex align-items-center justify-content-center" style="height: 36px; border-radius: 8px;">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions & Filter Row -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="small fw-semibold text-secondary mb-0"><i class="bi bi-funnel-fill text-success"></i> FILTER PERIOD:</span>
                                    <input type="hidden" id="audit-active-filter-type" value="all">
                                    
                                    <!-- Pill Buttons for Period Filter -->
                                    <div class="d-flex flex-wrap gap-2" id="admin-audit-filter-bar">
                                        <button type="button" class="btn btn-sm btn-outline-secondary active admin-order-filter-btn px-3 py-1.5 fw-semibold" style="border-radius: 8px;" data-audit-filter="all">
                                            All Records
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary admin-order-filter-btn px-3 py-1.5 fw-semibold" style="border-radius: 8px;" data-audit-filter="day">
                                            Day
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary admin-order-filter-btn px-3 py-1.5 fw-semibold" style="border-radius: 8px;" data-audit-filter="month">
                                            Month
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary admin-order-filter-btn px-3 py-1.5 fw-semibold" style="border-radius: 8px;" data-audit-filter="year">
                                            Year
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary admin-order-filter-btn px-3 py-1.5 fw-semibold" style="border-radius: 8px;" data-audit-filter="range">
                                            Custom Range
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-emerald px-4 py-2 d-flex align-items-center gap-2 shadow-sm text-white fw-semibold" onclick="downloadSalesCSV()" style="background-color: #047857; border: none; border-radius: 8px; transition: background-color 0.2s;">
                                    <i class="bi bi-file-earmark-spreadsheet-fill fs-5"></i> Download Sales CSV
                                </button>
                            </div>

                            <!-- Financial Audit Table -->
                            <div class="table-responsive border rounded">
                                <table class="table table-hover align-middle mb-0" id="audit-sales-table">
                                    <thead class="table-light">
                                        <tr style="border-bottom: 2px solid #cbd5e1;">
                                            <th class="ps-3 py-3 text-secondary small fw-bold text-uppercase">Invoice Bill No</th>
                                            <th class="py-3 text-secondary small fw-bold text-uppercase">Diner Client</th>
                                            <th class="py-3 text-secondary small fw-bold text-uppercase text-end">Subtotal</th>
                                            <th class="py-3 text-secondary small fw-bold text-uppercase text-end">Disc Applied</th>
                                            <th class="py-3 text-secondary small fw-bold text-uppercase text-end">GST (5%)</th>
                                            <th class="py-3 text-secondary small fw-bold text-uppercase text-end">Settle Total</th>
                                            <th class="pe-3 py-3 text-secondary small fw-bold text-uppercase text-center">Method</th>
                                        </tr>
                                    </thead>
                                    <tbody style="border-top: 0;">
                                        @forelse($settledOrders as $sOrder)
                                            @php
                                                $orderSubtotal = $sOrder->total_amount - $sOrder->tax_amount + $sOrder->discount_amount;
                                                $formattedInvoiceNo = $sOrder->created_at->format('Ymd') . sprintf('%04d', $sOrder->id);
                                            @endphp
                                            <tr class="audit-row" data-date="{{ $sOrder->created_at->format('Y-m-d') }}" data-subtotal="{{ $orderSubtotal }}" data-discount="{{ $sOrder->discount_amount }}" data-tax="{{ $sOrder->tax_amount }}" data-total="{{ $sOrder->total_amount }}" style="border-bottom: 1px solid #e2e8f0;">
                                                <td class="ps-3 py-3 fw-semibold text-dark">{{ $formattedInvoiceNo }}</td>
                                                <td>{{ $sOrder->customer_name }}</td>
                                                <td class="text-end fw-medium">₹{{ number_format($orderSubtotal, 2) }}</td>
                                                <td class="text-end text-danger fw-medium">₹{{ number_format($sOrder->discount_amount, 2) }}</td>
                                                <td class="text-end text-secondary fw-medium">₹{{ number_format($sOrder->tax_amount, 2) }}</td>
                                                <td class="text-end fw-bold text-success">₹{{ number_format($sOrder->total_amount, 2) }}</td>
                                                <td class="pe-3 text-center">
                                                    @php
                                                        $badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                                                        $methodLabel = strtoupper($sOrder->payment_method ?: 'UPI');
                                                        if (strtolower($sOrder->payment_method) === 'cash') {
                                                            $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                                        } elseif (strtolower($sOrder->payment_method) === 'card') {
                                                            $badgeClass = 'bg-info-subtle text-info border border-info-subtle';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }} px-2 py-1 rounded">{{ $methodLabel }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-secondary">No settled/paid invoices found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-light sticky-table-footer">
                                        <tr style="border-top: 2px solid #cbd5e1;">
                                            <td class="ps-3 py-3 fw-bold text-dark" colspan="2">Total Ledger Summary</td>
                                            <td class="text-end fw-bold" id="audit-footer-subtotal">₹{{ number_format($sumSubtotal, 2) }}</td>
                                            <td class="text-end text-danger fw-bold" id="audit-footer-discount">₹{{ number_format($sumDiscount, 2) }}</td>
                                            <td class="text-end text-secondary fw-bold" id="audit-footer-tax">₹{{ number_format($sumTax, 2) }}</td>
                                            <td class="text-end fw-bold text-success" id="audit-footer-total">₹{{ number_format($totalNetIncome, 2) }}</td>
                                            <td class="pe-3 text-center">-</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div id="audit-pagination-container" class="mt-3 admin-pagination"></div>
                        </div>

                        <!-- Sub-Pane 2: Tax & GST Audits -->
                        <div class="tab-pane fade" id="tax-gst-pane" role="tabpanel" aria-labelledby="tax-gst-subtab">
                            
                            <!-- GST Breakdown Cards Row -->
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded shadow-sm d-flex align-items-center gap-3" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-color: #fecaca !important;">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #dc2626; color: #fff;">
                                            <i class="bi bi-calculator fs-4"></i>
                                        </div>
                                        <div>
                                            <span class="text-secondary small fw-semibold uppercase block mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">TOTAL OUTPUT GST (5%)</span>
                                            <h4 class="fw-bold mb-0 text-danger" id="tax-kpi-output-gst" style="color: #991b1b !important;">₹{{ number_format($sumTax, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded shadow-sm d-flex align-items-center gap-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #bbf7d0 !important;">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #16a34a; color: #fff;">
                                            <i class="bi bi-shield-check fs-4"></i>
                                        </div>
                                        <div>
                                            <span class="text-secondary small fw-semibold uppercase block mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">AUDITED TAX LIABILITIES</span>
                                            <h4 class="fw-bold mb-0 text-success" style="color: #166534 !important;">Fully Reconciled</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 border rounded shadow-sm bg-white">
                                <h5 class="fw-bold mb-3"><i class="bi bi-shield-fill-check text-success me-2"></i>GST Audit Reconciliation Summary</h5>
                                <p class="text-secondary mb-4">The following table breaks down output tax liabilities collected across taxable sales transactions. No inventory, inputs, or physical stock adjustments are tracked in this view.</p>
                                
                                <div class="table-responsive border rounded">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr style="border-bottom: 2px solid #cbd5e1;">
                                                <th class="ps-3 py-3 text-secondary small fw-bold text-uppercase">Tax Category</th>
                                                <th class="py-3 text-secondary small fw-bold text-uppercase text-center">Tax Slab</th>
                                                <th class="py-3 text-secondary small fw-bold text-uppercase text-end">Gross Sales</th>
                                                <th class="py-3 text-secondary small fw-bold text-uppercase text-end">Exempt/Disc</th>
                                                <th class="py-3 text-secondary small fw-bold text-uppercase text-end">Taxable Amount</th>
                                                <th class="pe-3 py-3 text-secondary small fw-bold text-uppercase text-end">GST Collected</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                                <td class="ps-3 py-3 fw-semibold text-dark"><i class="bi bi-cup-hot text-muted me-1"></i> Food & Beverage Services</td>
                                                <td class="text-center fw-medium"><span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded">5.0%</span></td>
                                                <td class="text-end fw-medium" id="tax-table-gross">₹{{ number_format($sumSubtotal, 2) }}</td>
                                                <td class="text-end text-danger fw-medium" id="tax-table-discount">₹{{ number_format($sumDiscount, 2) }}</td>
                                                <td class="text-end fw-semibold" id="tax-table-taxable">₹{{ number_format($sumSubtotal - $sumDiscount, 2) }}</td>
                                                <td class="pe-3 text-end fw-bold text-danger" id="tax-table-gst">₹{{ number_format($sumTax, 2) }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td class="ps-3 py-3 fw-bold text-dark" colspan="2">Total Audited Liabilities</td>
                                                <td class="text-end fw-bold" id="tax-footer-gross">₹{{ number_format($sumSubtotal, 2) }}</td>
                                                <td class="text-end text-danger fw-bold" id="tax-footer-discount">₹{{ number_format($sumDiscount, 2) }}</td>
                                                <td class="text-end fw-bold" id="tax-footer-taxable">₹{{ number_format($sumSubtotal - $sumDiscount, 2) }}</td>
                                                <td class="pe-3 text-end fw-bold text-danger" id="tax-footer-gst">₹{{ number_format($sumTax, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Modals Section -->
    
    <!-- Modal: Add Employee Account -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="addEmployeeForm" class="modal-content" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addEmployeeModalLabel">Add Employee Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="emp_name" class="form-label small fw-semibold">NAME</label>
                        <input type="text" class="form-control" name="name" id="emp_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="emp_email" class="form-label small fw-semibold">EMAIL ADDRESS</label>
                        <input type="email" class="form-control" name="email" id="emp_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="emp_password" class="form-label small fw-semibold">PASSWORD</label>
                        <input type="password" class="form-control" name="password" id="emp_password" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="emp_role" class="form-label small fw-semibold">ROLE</label>
                        <select class="form-select" name="role" id="emp_role" required>
                            @foreach($availableRoles as $roleKey)
                                @if($roleKey !== 'admin')
                                    <option value="{{ $roleKey }}">{{ ucfirst($roleKey) }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @if(App\Models\Feature::isActive('salary_payroll'))
                        <div class="mb-3">
                            <label for="emp_salary" class="form-label small fw-semibold">SALARY (₹)</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="salary" id="emp_salary">
                        </div>
                    @else
                        <input type="hidden" name="salary" id="emp_salary" value="0">
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 fw-semibold" style="border-radius: 8px;" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn text-white px-4 fw-semibold" style="background-color: #15803d; border-radius: 8px;">Save Employee</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Employee Account -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editEmployeeForm" class="modal-content" novalidate>
                <input type="hidden" name="id" id="edit_emp_id">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editEmployeeModalLabel">Edit Employee Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_emp_name" class="form-label small fw-semibold">NAME</label>
                        <input type="text" class="form-control" name="name" id="edit_emp_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_emp_email" class="form-label small fw-semibold">EMAIL ADDRESS</label>
                        <input type="email" class="form-control" name="email" id="edit_emp_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_emp_role" class="form-label small fw-semibold">ROLE</label>
                        <select class="form-select" name="role" id="edit_emp_role" required>
                            @foreach($availableRoles as $roleKey)
                                <option value="{{ $roleKey }}">{{ ucfirst($roleKey) }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(App\Models\Feature::isActive('salary_payroll'))
                        <div class="mb-3" id="edit_emp_salary_wrapper">
                            <label for="edit_emp_salary" class="form-label small fw-semibold">SALARY (₹)</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="salary" id="edit_emp_salary">
                        </div>
                    @else
                        <input type="hidden" name="salary" id="edit_emp_salary">
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 fw-semibold" style="border-radius: 8px;" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn text-white px-4 fw-semibold" style="background-color: #15803d; border-radius: 8px;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal 1: Add Food Item -->
    <div class="modal fade" id="addFoodItemModal" tabindex="-1" aria-labelledby="addFoodItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="addFoodItemForm" class="modal-content" enctype="multipart/form-data" novalidate>
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
                        <label for="food_category" class="form-label small fw-semibold">CATEGORY</label>
                        <select class="form-select food-category-select" name="category_id" id="food_category" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="food_image" class="form-label small fw-semibold">ITEM IMAGE</label>
                        <input type="file" class="form-control" name="image" id="food_image" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="food_description" class="form-label small fw-semibold">DESCRIPTION</label>
                        <textarea class="form-control" name="description" id="food_description" rows="3" required></textarea>
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
            <form id="editFoodItemForm" class="modal-content" enctype="multipart/form-data" novalidate>
                <input type="hidden" id="edit_food_id">
                <input type="hidden" name="_method" value="PUT">
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
                        <label for="edit_food_category" class="form-label small fw-semibold">CATEGORY</label>
                        <select class="form-select food-category-select" name="category_id" id="edit_food_category" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_food_image" class="form-label small fw-semibold">ITEM IMAGE</label>
                        <input type="file" class="form-control mb-2" name="image" id="edit_food_image" accept="image/*">
                        <div id="edit_image_preview_container" class="d-none">
                            <span class="d-block small text-secondary mb-1">Current Image:</span>
                            <img id="edit_image_preview" src="" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;" class="border">
                        </div>
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
    </div>

    <!-- Modal: Manage Categories -->
    <div class="modal fade" id="manageCategoriesModal" tabindex="-1" aria-labelledby="manageCategoriesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="manageCategoriesModalLabel">Manage Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add Category Form inline -->
                    <form id="addCategoryForm" class="mb-4 bg-light p-3 border rounded-3" novalidate data-mode="add" data-edit-id="">
                        <label id="category-form-label" for="new_category_name" class="form-label small fw-semibold text-secondary mb-1.5">ADD NEW CATEGORY</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="name" id="new_category_name" placeholder="Category name..." required style="border-radius: 8px 0 0 8px;">
                            <button type="button" id="cancel-category-edit-btn" class="btn btn-secondary px-3 fw-semibold text-white" style="display: none; border-radius: 0;">Cancel</button>
                            <button type="submit" id="category-submit-btn" class="btn btn-success px-3 fw-semibold text-white" style="background-color: #15803d; border-radius: 0 8px 8px 0;">Add</button>
                        </div>
                    </form>

                    <!-- Categories list -->
                    <label class="form-label small fw-semibold text-secondary">EXISTING CATEGORIES (Drag & Drop to Reorder)</label>
                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                        <table class="table table-sm align-middle mb-0" id="categories-table-list">
                            <thead class="table-light text-secondary small fw-bold">
                                <tr>
                                    <th style="width: 50px;">SORT</th>
                                    <th>NAME</th>
                                    <th class="text-end">ACTION</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold" id="categories-sortable-tbody">
                                @forelse($categories as $cat)
                                    <tr class="draggable-cat-row" data-cat-id="{{ $cat->id }}" draggable="true" style="cursor: move;">
                                        <td>
                                            <i class="bi bi-grip-vertical text-secondary fs-5" style="cursor: grab;"></i>
                                        </td>
                                        <td class="text-dark category-name-text">{{ $cat->name }}</td>
                                        <td class="text-end">
                                            @if(strtolower($cat->name) !== 'uncategorized')
                                                <button type="button" class="btn btn-sm edit-category-btn px-2.5 py-1.5 me-1 text-white" data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" style="background-color: #00c0f9; border-radius: 8px; border: none;" title="Rename Category">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-category-btn px-2.5 py-1" data-id="{{ $cat->id }}" style="border-radius: 6px;">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            @else
                                                <span class="text-muted small italic">System</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="no-categories-row">
                                        <td colspan="3" class="text-center py-3 text-secondary small">No categories configured.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Close</button>
                </div>
            </div>
        </div>
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
        // Generic client-side paginator helper
        function paginateTable(tableSelector, containerSelector, pageSize = 50, rowSelector = 'tbody tr') {
            const table = $(tableSelector);
            const container = $(containerSelector);
            
            // Filter down to rows that are NOT hidden by search/filter logic
            const activeRows = table.find(rowSelector).filter(function() {
                // Exclude empty rows or spacer rows
                if ($(this).attr('id') === 'audit-no-rows-tr' || $(this).hasClass('no-pending-requests-row') || $(this).hasClass('no-pending-requests-row') || $(this).closest('tfoot').length) {
                    return false;
                }
                return !$(this).hasClass('filter-hidden');
            });

            const totalRows = activeRows.length;
            const totalPages = Math.ceil(totalRows / pageSize) || 1;

            // Get/Set current page
            let currentPage = table.data('current-page') || 1;
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
            table.data('current-page', currentPage);

            // Hide all rows matching rowSelector first
            table.find(rowSelector).hide();
            
            // Show slice for current page
            const start = (currentPage - 1) * pageSize;
            const end = start + pageSize;
            const pageSlice = activeRows.slice(start, end);
            
            pageSlice.show();

            // Render pagination controls
            renderPaginationControls(container, currentPage, totalPages, function(newPage) {
                table.data('current-page', newPage);
                paginateTable(tableSelector, containerSelector, pageSize, rowSelector);
            });
        }

        function renderPaginationControls(container, currentPage, totalPages, onPageChange) {
            container.empty();
            if (totalPages <= 1) return;

            const ul = $('<ul class="pagination pagination-sm justify-content-center mb-0 mt-3"></ul>');

            // Previous button
            const prevItem = $(`<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"></li>`);
            const prevLink = $('<button class="page-link" type="button">Previous</button>');
            if (currentPage > 1) {
                prevLink.on('click', () => onPageChange(currentPage - 1));
            }
            prevItem.append(prevLink);
            ul.append(prevItem);

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                const pageItem = $(`<li class="page-item ${currentPage === i ? 'active' : ''}"></li>`);
                const pageLink = $(`<button class="page-link" type="button">${i}</button>`);
                if (currentPage !== i) {
                    pageLink.on('click', () => onPageChange(i));
                }
                pageItem.append(pageLink);
                ul.append(pageItem);
            }

            // Next button
            const nextItem = $(`<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"></li>`);
            const nextLink = $('<button class="page-link" type="button">Next</button>');
            if (currentPage < totalPages) {
                nextLink.on('click', () => onPageChange(currentPage + 1));
            }
            nextItem.append(nextLink);
            ul.append(nextItem);

            container.append($('<nav aria-label="Page navigation"></nav>').append(ul));
        }

        // Global function to apply period filtering to audit reports
        function applyAuditFilter(filter) {
            let settledCount = 0;
            let sumSubtotal = 0;
            let sumDiscount = 0;
            let sumTax = 0;
            let totalNetIncome = 0;

            const selectedDate = $('#audit_filter_date').val(); // YYYY-MM-DD
            const selectedMonth = $('#audit_filter_month').val(); // YYYY-MM
            const selectedYear = $('#audit_filter_year').val(); // YYYY
            const selectedFrom = $('#audit_from_date').val(); // YYYY-MM-DD
            const selectedTo = $('#audit_to_date').val(); // YYYY-MM-DD

            const rows = document.querySelectorAll("#audit-sales-table tbody tr.audit-row");
            
            rows.forEach(row => {
                const rowDate = row.getAttribute('data-date'); // "YYYY-MM-DD"
                let showRow = false;

                if (filter === 'all') {
                    showRow = true;
                } else if (filter === 'day') {
                    showRow = !selectedDate || (rowDate === selectedDate);
                } else if (filter === 'month') {
                    showRow = !selectedMonth || (rowDate.substring(0, 7) === selectedMonth);
                } else if (filter === 'year') {
                    showRow = !selectedYear || (rowDate.substring(0, 4) === selectedYear);
                } else if (filter === 'range') {
                    let dateInFrom = true;
                    let dateInTo = true;
                    if (selectedFrom) {
                        dateInFrom = (rowDate >= selectedFrom);
                    }
                    if (selectedTo) {
                        dateInTo = (rowDate <= selectedTo);
                    }
                    showRow = dateInFrom && dateInTo;
                }

                if (showRow) {
                    row.style.display = '';
                    settledCount++;
                    
                    const subtotal = parseFloat(row.getAttribute('data-subtotal')) || 0;
                    const discount = parseFloat(row.getAttribute('data-discount')) || 0;
                    const tax = parseFloat(row.getAttribute('data-tax')) || 0;
                    const total = parseFloat(row.getAttribute('data-total')) || 0;

                    sumSubtotal += subtotal;
                    sumDiscount += discount;
                    sumTax += tax;
                    totalNetIncome += total;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update KPI Cards
            const avgBasket = settledCount > 0 ? (totalNetIncome / settledCount) : 0;
            
            $('#audit-kpi-net-income').text('₹' + totalNetIncome.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#audit-kpi-settled-count').text(settledCount + ' bills');
            $('#audit-kpi-avg-basket').text('₹' + avgBasket.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            // Update Total Ledger Summary Footer Row
            $('#audit-footer-subtotal').text('₹' + sumSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#audit-footer-discount').text('₹' + sumDiscount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#audit-footer-tax').text('₹' + sumTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#audit-footer-total').text('₹' + totalNetIncome.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            // Update Tax Sub-Tab Cards
            $('#tax-kpi-output-gst').text('₹' + sumTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            // Update Tax Sub-Tab Table Rows
            $('#tax-table-gross').text('₹' + sumSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#tax-table-discount').text('₹' + sumDiscount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            const taxableAmount = sumSubtotal - sumDiscount;
            $('#tax-table-taxable').text('₹' + taxableAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#tax-table-gst').text('₹' + sumTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            // Update Tax Sub-Tab Footer
            $('#tax-footer-gross').text('₹' + sumSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#tax-footer-discount').text('₹' + sumDiscount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#tax-footer-taxable').text('₹' + taxableAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#tax-footer-gst').text('₹' + sumTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            
            // Check if no rows visible and display empty state
            const noRowsTr = document.getElementById('audit-no-rows-tr');
            if (settledCount === 0) {
                if (!noRowsTr) {
                    const tr = document.createElement('tr');
                    tr.id = 'audit-no-rows-tr';
                    tr.innerHTML = '<td colspan="7" class="text-center py-4 text-secondary">No settled/paid invoices found for the selected period.</td>';
                    document.querySelector('#audit-sales-table tbody').appendChild(tr);
                } else {
                    noRowsTr.style.display = '';
                }
            } else {
                if (noRowsTr) {
                    noRowsTr.style.display = 'none';
                }
            }

            // Paginate Audit Reports table
            $('#audit-sales-table').data('current-page', 1);
            paginateTable('#audit-sales-table', '#audit-pagination-container', 50, 'tbody tr.audit-row');
        }

        // Global function to download customer CRM as CSV (Name and Phone)
        function downloadCRMCSV() {
            let csv = [];
            // Add header
            csv.push('"Customer Name","Phone Number"');
            
            const rows = document.querySelectorAll("#crm-table tbody tr");
            
            rows.forEach(tr => {
                // If it's the empty row, skip
                if (tr.querySelector('td[colspan]')) return;
                
                const cols = tr.querySelectorAll("td");
                if (cols.length >= 2) {
                    // Column 0 is Name (clean of VIP badge)
                    let name = cols[0].innerText.replace(/VIP/g, '').trim();
                    // Column 1 is Phone (clean of icons/spaces)
                    let phone = cols[1].innerText.trim();
                    
                    name = '"' + name.replace(/"/g, '""') + '"';
                    phone = '"' + phone.replace(/"/g, '""') + '"';
                    
                    csv.push(name + "," + phone);
                }
            });
            
            const csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "customer_crm_" + new Date().toISOString().split('T')[0] + ".csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 2000
            });
            Toast.fire({ icon: 'success', title: 'CRM CSV exported successfully!' });
        }

        // Global function to download sales ledger as CSV
        function downloadSalesCSV() {
            let csv = [];
            const rows = document.querySelectorAll("#audit-sales-table tr");
            
            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");
                
                for (let j = 0; j < cols.length; j++) {
                    let text = cols[j].innerText.trim();
                    text = text.replace(/₹/g, '').replace(/,/g, '');
                    text = '"' + text.replace(/"/g, '""') + '"';
                    row.push(text);
                }
                
                csv.push(row.join(","));
            }
            
            const csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "sales_ledger_audit_" + new Date().toISOString().split('T')[0] + ".csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 2000
            });
            Toast.fire({ icon: 'success', title: 'Sales CSV downloaded successfully!' });
        }

        // Global function to download orders log as CSV
        function downloadOrdersCSV() {
            let csv = [];
            // Add header row
            csv.push('"Order ID","Customer Name","Items Ordered","Total Price","Order Status","Payment Status","Placed At"');
            
            const rows = document.querySelectorAll("#orders-list-tbody tr");
            
            rows.forEach(tr => {
                // If it's the empty/no-orders row, skip
                if (tr.querySelector('td[colspan]')) return;
                
                const cols = tr.querySelectorAll("td");
                if (cols.length >= 7) {
                    let orderId = cols[0].innerText.trim();
                    let customer = cols[1].innerText.trim();
                    let items = cols[2].innerText.trim().replace(/\n/g, '; ');
                    let totalPrice = cols[3].innerText.replace(/₹/g, '').replace(/,/g, '').trim();
                    
                    // Order status might be a select dropdown
                    let orderStatus = '';
                    const statusSelect = cols[4].querySelector('select');
                    if (statusSelect) {
                        orderStatus = statusSelect.value;
                    } else {
                        orderStatus = cols[4].innerText.trim();
                    }
                    
                    // Payment status might be a button or text badge
                    let paymentStatus = '';
                    const paymentBtn = cols[5].querySelector('button');
                    if (paymentBtn) {
                        paymentStatus = paymentBtn.innerText.trim();
                    } else {
                        paymentStatus = cols[5].innerText.trim();
                    }
                    
                    let placedAt = cols[6].innerText.trim();
                    
                    // Escape values
                    orderId = '"' + orderId.replace(/"/g, '""') + '"';
                    customer = '"' + customer.replace(/"/g, '""') + '"';
                    items = '"' + items.replace(/"/g, '""') + '"';
                    totalPrice = '"' + totalPrice.replace(/"/g, '""') + '"';
                    orderStatus = '"' + orderStatus.replace(/"/g, '""') + '"';
                    paymentStatus = '"' + paymentStatus.replace(/"/g, '""') + '"';
                    placedAt = '"' + placedAt.replace(/"/g, '""') + '"';
                    
                    csv.push([orderId, customer, items, totalPrice, orderStatus, paymentStatus, placedAt].join(","));
                }
            });
            
            const csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "orders_log_" + new Date().toISOString().split('T')[0] + ".csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 2000
            });
            Toast.fire({ icon: 'success', title: 'Orders Log CSV exported successfully!' });
        }

        // Global function for inline click handling to bypass caching/document ready crashes
        function markOrderAsPaid(orderId, btnElement) {
            const btn = $(btnElement);
            if (btn.attr('disabled') || btn.hasClass('disabled')) {
                return;
            }

            const row = btn.closest('tr');
            const amount = parseFloat(row.find('.order-amount-cell').attr('data-amount') || row.find('.order-amount-cell').data('amount')) || 0;

            // Show loading spinner
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ...');

            $.ajax({
                url: `/admin/orders/${orderId}/payment`,
                type: 'POST',
                data: {
                    _method: 'PATCH',
                    payment_status: 'paid',
                    payment_method: 'cash'
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
                    btn.removeClass('btn-danger').addClass('btn-success')
                       .html('<i class="bi bi-check-circle-fill"></i> Paid')
                       .removeAttr('onclick')
                       .css({ 'cursor': 'default', 'pointer-events': 'none', 'opacity': '1' });
                    
                    // If the order status isn't completed yet, auto-complete it
                    const statusSelect = row.find('.order-status-select');
                    if (statusSelect.length && statusSelect.attr('data-current-status') !== 'completed') {
                        statusSelect.val('completed');
                        statusSelect.attr('data-current-status', 'completed');

                        // Dynamically enable the invoice button if present
                        const invoiceBtn = row.find('a.btn-outline-primary, button.btn-outline-secondary');
                        if (invoiceBtn.is('button')) {
                            const newLink = $('<a>')
                                .attr('href', `/admin/orders/${orderId}/invoice`)
                                .attr('target', '_blank')
                                .addClass('btn btn-sm btn-outline-primary')
                                .attr('title', 'Print/Download Receipt')
                                .html('<i class="bi bi-receipt"></i> Invoice');
                            invoiceBtn.replaceWith(newLink);
                        }

                        // Update metrics if the dashboard functions exist
                        if (typeof window.updateActiveOrders === 'function') {
                            window.updateActiveOrders(-1);
                        }
                        if (typeof window.addToRevenue === 'function') {
                            window.addToRevenue(amount);
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="bi bi-x-circle-fill"></i> Unpaid');
                    const errorMsg = xhr.responseJSON?.message || 'Failed to record payment.';
                    Swal.fire({ icon: 'error', title: 'Error', text: errorMsg, confirmButtonColor: '#dc2626' });
                }
            });
        }

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
            if (activeTab && $(`#${activeTab}`).length) {
                try {
                    const tabTrigger = new bootstrap.Tab($(`#${activeTab}`)[0]);
                    tabTrigger.show();
                } catch (e) {
                    console.error("Failed to restore active tab:", e);
                }
            }

            $('.nav-link-admin').on('shown.bs.tab', function(e) {
                localStorage.setItem('adminActiveTab', e.target.id);
            });

            // Generic Validation Highlighting Rules
            const validationOptions = {
                errorElement: 'div',
                errorClass: 'text-danger mb-1 small fw-bold d-block',
                highlight: function(element) { $(element).addClass('is-invalid'); },
                unhighlight: function(element) { $(element).removeClass('is-invalid'); },
                errorPlacement: function(error, element) {
                    error.insertBefore(element);
                }
            };

            /* =========================================================================
             * Food Items Actions
             * ========================================================================= */
            
            // Add Food Item AJAX Validation & Submit
            $('#addFoodItemForm').validate({
                ...validationOptions,
                messages: {
                    name: "Please enter the food item name.",
                    price: {
                        required: "Please enter the price.",
                        number: "Please enter a valid price amount."
                    },
                    category_id: "Please select a category.",
                    image: "Please upload an image for the food item.",
                    description: "Please enter the description of the food item."
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    
                    $.ajax({
                        url: "{{ route('admin.food-items.store') }}",
                        type: 'POST',
                        data: new FormData(form),
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
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
                
                // Set category select field
                const categoryId = $(this).data('category');
                $('#edit_food_category').val(categoryId);

                // Set image preview
                const imagePath = $(this).data('image');
                if (imagePath) {
                    $('#edit_image_preview').attr('src', imagePath);
                    $('#edit_image_preview_container').removeClass('d-none');
                } else {
                    $('#edit_image_preview').attr('src', '');
                    $('#edit_image_preview_container').addClass('d-none');
                }
                
                $('#editFoodItemModal').modal('show');
            });

            // Edit Food Item Submit
            $('#editFoodItemForm').validate({
                ...validationOptions,
                messages: {
                    name: "Please enter the food item name.",
                    price: {
                        required: "Please enter the price.",
                        number: "Please enter a valid price amount."
                    },
                    category_id: "Please select a category.",
                    description: "Please enter the description of the food item."
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    const foodId = $('#edit_food_id').val();
                    const name = $('#edit_food_name').val();
                    const price = $('#edit_food_price').val();
                    const description = $('#edit_food_description').val();
                    const status = $('#edit_food_status').val();
                    
                    $.ajax({
                        url: `/admin/food-items/${foodId}`,
                        type: 'POST', // Use POST with PUT spoofing inside FormData
                        data: new FormData(form),
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            $('#editFoodItemModal').modal('hide');
                            Swal.fire({ icon: 'success', title: 'Updated!', text: res.message, confirmButtonColor: '#15803d' });
                            
                            const editBtn = $(`.edit-food-btn[data-id="${foodId}"]`);
                            const row = editBtn.closest('tr');
                            const updatedItem = res.food_item;
                            
                            // Update row category attribute
                            row.attr('data-category', updatedItem.category_id || 'uncategorized');
                            
                            // Update row details
                            const imageUrl = updatedItem.image_path ? updatedItem.image_path : '/logo.jpg';
                            row.find('td').eq(0).find('img').attr('src', imageUrl);
                            row.find('td').eq(1).text(name);
                            row.find('td').eq(2).text(description || '-');
                            row.find('td').eq(3).text('₹' + parseFloat(price).toFixed(2));
                            
                            const catName = updatedItem.category ? updatedItem.category.name : 'Uncategorized';
                            row.find('td').eq(4).html(`<span class="badge bg-secondary-subtle text-secondary">${catName}</span>`);
                            
                            const badgeCell = row.find('td').eq(5);
                            const toggleBtn = row.find('.toggle-status-btn');
                            if (status === 'available') {
                                badgeCell.html('<span class="badge bg-success-subtle text-success">Available</span>');
                                toggleBtn.removeClass('inactive');
                                toggleBtn.attr('title', 'Deactivate Menu Item');
                                toggleBtn.html('<i class="bi bi-eye-fill"></i>');
                            } else {
                                badgeCell.html('<span class="badge bg-danger-subtle text-danger">Unavailable</span>');
                                toggleBtn.addClass('inactive');
                                toggleBtn.attr('title', 'Activate Menu Item');
                                toggleBtn.html('<i class="bi bi-eye-slash-fill"></i>');
                            }
                            
                            // Update edit button data attributes
                            editBtn.attr('data-name', name);
                            editBtn.attr('data-price', price);
                            editBtn.attr('data-description', description);
                            editBtn.attr('data-status', status);
                            editBtn.attr('data-category', updatedItem.category_id);
                            editBtn.attr('data-image', updatedItem.image_path);
                        },
                        error: function(xhr) {
                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to update item.' });
                        }
                    });
                }
            });

            // Admin Menu Category Filter Click Listener
            $(document).on('click', '.admin-category-filter-btn', function() {
                $('.admin-category-filter-btn').removeClass('active');
                $(this).addClass('active');

                const selectedCategory = $(this).attr('data-category');

                $('.admin-food-row').each(function() {
                    const row = $(this);
                    if (selectedCategory === 'all' || row.attr('data-category') == selectedCategory) {
                        row.removeClass('filter-hidden');
                    } else {
                        row.addClass('filter-hidden');
                    }
                });

                // Reset page to 1 and re-paginate
                $('#menu-pane table').data('current-page', 1);
                paginateTable('#menu-pane table', '#menu-pagination-container', 50, 'tbody tr.admin-food-row');
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
                        const badgeCell = row.find('td').eq(5);
                        
                        if (newStatus === 'available') {
                            badgeCell.html('<span class="badge bg-success-subtle text-success">Available</span>');
                            btn.removeClass('inactive');
                            btn.attr('title', 'Deactivate Menu Item');
                            btn.html('<i class="bi bi-eye-fill"></i>');
                        } else {
                            badgeCell.html('<span class="badge bg-danger-subtle text-danger">Unavailable</span>');
                            btn.addClass('inactive');
                            btn.attr('title', 'Activate Menu Item');
                            btn.html('<i class="bi bi-eye-slash-fill"></i>');
                        }

                        row.find('.edit-food-btn').attr('data-status', newStatus);
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
                    }
                });
            });

            /* =========================================================================
             * Categories CRUD Actions
             * ========================================================================= */

            $('#addCategoryForm').validate({
                ...validationOptions,
                messages: {
                    name: "Please enter a category name."
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    
                    const formEl = $(form);
                    const mode = formEl.attr('data-mode') || 'add';
                    const editId = formEl.attr('data-edit-id') || '';
                    
                    if (mode === 'edit') {
                        $.ajax({
                            url: `/admin/categories/${editId}`,
                            type: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: formEl.serialize(),
                            success: function(res) {
                                const cat = res.category;
                                
                                // Find row and update name and data attributes
                                const row = $(`#categories-sortable-tbody tr[data-cat-id="${cat.id}"]`);
                                row.find('.category-name-text').text(cat.name);
                                row.find('.edit-category-btn').attr('data-name', cat.name);
                                
                                // Update select dropdowns
                                $(`.food-category-select option[value="${cat.id}"]`).text(cat.name);
                                
                                // Update badges in main food items table
                                $(`.edit-food-btn[data-category="${cat.id}"]`).each(function() {
                                    const editBtn = $(this);
                                    const foodRow = editBtn.closest('tr');
                                    foodRow.find('td').eq(4).html(`<span class="badge bg-success-subtle text-success">${cat.name}</span>`);
                                });
                                
                                // Reset the form back to ADD mode
                                $('#cancel-category-edit-btn').trigger('click');
                                
                                // Show success toast
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'bottom-end',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                Toast.fire({ icon: 'success', title: res.message });
                            },
                            error: function(xhr) {
                                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to rename category.' });
                            }
                        });
                    } else {
                        $.ajax({
                            url: "{{ route('admin.categories.store') }}",
                            type: 'POST',
                            data: formEl.serialize(),
                            success: function(res) {
                                const cat = res.category;
                                
                                // 1. Clear input
                                $('#new_category_name').val('');
                                
                                // 2. Remove 'No categories' row if it exists
                                $('#no-categories-row').remove();
                                
                                // 3. Append to modal list table
                                $('#categories-table-list tbody').append(`
                                    <tr class="draggable-cat-row" data-cat-id="${cat.id}" draggable="true" style="cursor: move;">
                                        <td>
                                            <i class="bi bi-grip-vertical text-secondary fs-5" style="cursor: grab;"></i>
                                        </td>
                                        <td class="text-dark category-name-text">${cat.name}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm edit-category-btn px-2.5 py-1.5 me-1 text-white" data-id="${cat.id}" data-name="${cat.name}" style="background-color: #00c0f9; border-radius: 8px; border: none;" title="Rename Category">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-category-btn px-2.5 py-1" data-id="${cat.id}" style="border-radius: 6px;">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `);
                                
                                // 4. Append to select dropdowns in Food Item Modals
                                $('.food-category-select').append(`
                                    <option value="${cat.id}">${cat.name}</option>
                                `);
                                
                                // Show success toast
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'bottom-end',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                Toast.fire({ icon: 'success', title: res.message });
                            },
                            error: function(xhr) {
                                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to save category.' });
                            }
                        });
                    }
                }
            });

            // Delete Category Click Handler
            $(document).on('click', '.delete-category-btn', function() {
                const catId = $(this).data('id');
                const btn = $(this);
                
                Swal.fire({
                    title: 'Delete Category?',
                    text: "Any food items referencing this category will be marked as 'Uncategorized'.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/categories/${catId}`,
                            type: 'DELETE',
                            success: function(res) {
                                // 1. Remove from modal list
                                btn.closest('tr').fadeOut(300, function() {
                                    $(this).remove();
                                    if ($('#categories-table-list tbody tr').length === 0) {
                                        $('#categories-table-list tbody').append(`
                                            <tr id="no-categories-row">
                                                <td colspan="3" class="text-center py-3 text-secondary small">No categories configured.</td>
                                            </tr>
                                        `);
                                    }
                                });
                                
                                // 2. Remove option from Food Item select dropdowns
                                $(`.food-category-select option[value="${catId}"]`).remove();
                                
                                // 3. Update main table badges where this category was used
                                $(`.edit-food-btn[data-category="${catId}"]`).each(function() {
                                    const editBtn = $(this);
                                    editBtn.attr('data-category', ''); // Clear category ID
                                    const row = editBtn.closest('tr');
                                    row.find('td').eq(4).html('<span class="badge bg-secondary-subtle text-secondary">Uncategorized</span>');
                                });

                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'bottom-end',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                Toast.fire({ icon: 'success', title: res.message });
                            },
                            error: function(xhr) {
                                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to delete category.' });
                            }
                        });
                    }
                });
            });

            // Native HTML5 Drag and Drop Category Sorting
            let dragRow = null;

            $(document).on('dragstart', '.draggable-cat-row', function(e) {
                dragRow = this;
                $(this).addClass('opacity-50');
                e.originalEvent.dataTransfer.setData('text/plain', '');
            });

            $(document).on('dragend', '.draggable-cat-row', function(e) {
                $(this).removeClass('opacity-50');
            });

            $(document).on('dragover', '.draggable-cat-row', function(e) {
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = 'move';
            });

            $(document).on('drop', '.draggable-cat-row', function(e) {
                e.preventDefault();
                if (this !== dragRow) {
                    const tbody = $('#categories-sortable-tbody');
                    const allRows = Array.from(tbody.children('.draggable-cat-row'));
                    const dragIndex = allRows.indexOf(dragRow);
                    const targetIndex = allRows.indexOf(this);

                    if (dragIndex < targetIndex) {
                        $(this).after(dragRow);
                    } else {
                        $(this).before(dragRow);
                    }

                    // Save the new category sort order
                    saveCategoryOrder();
                }
            });

            function saveCategoryOrder() {
                const categoryOrder = [];
                $('#categories-sortable-tbody .draggable-cat-row').each(function(index) {
                    categoryOrder.push({
                        id: $(this).data('cat-id'),
                        order_weight: index + 1
                    });
                });

                $.ajax({
                    url: "{{ route('admin.categories.sort') }}",
                    type: 'POST',
                    data: {
                        order: categoryOrder
                    },
                    success: function(res) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        Toast.fire({ icon: 'success', title: 'Category order updated!' });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Sorting Error',
                            text: xhr.responseJSON?.message || 'Failed to update category order.'
                        });
                    }
                });
            }

            // Edit Category click handler
            $(document).on('click', '.edit-category-btn', function() {
                const catId = $(this).data('id');
                const currentName = $(this).attr('data-name');
                
                const form = $('#addCategoryForm');
                form.attr('data-mode', 'edit');
                form.attr('data-edit-id', catId);
                
                $('#new_category_name').val(currentName).focus();
                $('#category-form-label').text('EDIT CATEGORY');
                
                $('#cancel-category-edit-btn').show();
                $('#category-submit-btn')
                    .text('Update')
                    .css('background-color', '#00c0f9')
                    .removeClass('btn-success')
                    .addClass('btn-info');
            });

            // Cancel Category Edit click handler
            $(document).on('click', '#cancel-category-edit-btn', function() {
                const form = $('#addCategoryForm');
                form.attr('data-mode', 'add');
                form.attr('data-edit-id', '');
                
                $('#new_category_name').val('');
                $('#category-form-label').text('ADD NEW CATEGORY');
                
                $(this).hide();
                $('#category-submit-btn')
                    .text('Add')
                    .css('background-color', '#15803d')
                    .removeClass('btn-info')
                    .addClass('btn-success');
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

                        // Enable or disable the invoice button dynamically
                        const invoiceBtn = row.find('a.btn-outline-primary, button.btn-outline-secondary');
                        if (newStatus === 'completed') {
                            if (invoiceBtn.is('button')) {
                                const newLink = $('<a>')
                                    .attr('href', `/admin/orders/${orderId}/invoice`)
                                    .attr('target', '_blank')
                                    .addClass('btn btn-sm btn-outline-primary')
                                    .attr('title', 'Print/Download Receipt')
                                    .html('<i class="bi bi-receipt"></i> Invoice');
                                invoiceBtn.replaceWith(newLink);
                            }
                        } else {
                            if (invoiceBtn.is('a')) {
                                const newBtn = $('<button>')
                                    .attr('type', 'button')
                                    .addClass('btn btn-sm btn-outline-secondary')
                                    .prop('disabled', true)
                                    .attr('title', 'Invoice is only available after order is completed')
                                    .html('<i class="bi bi-receipt"></i> Invoice');
                                invoiceBtn.replaceWith(newBtn);
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



            // Delete Order Action
            $(document).on('click', '.delete-order-btn', function() {
                const btn = $(this);
                const orderId = btn.attr('data-id');
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
                                const row = btn.closest('tr');
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

            // Toggle User Status button
            $(document).on('click', '.toggle-user-status-btn', function() {
                const btn = $(this);
                const userId = btn.data('id');
                const currentStatus = btn.data('status');
                const newStatus = currentStatus === 1 ? 0 : 1;

                $.ajax({
                    url: `/admin/users/${userId}/status`,
                    type: 'POST',
                    data: {
                        _method: 'PATCH',
                        is_active: newStatus
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        btn.data('status', newStatus);
                        btn.attr('data-status', newStatus);
                        if (newStatus === 1) {
                            btn.removeClass('inactive');
                            btn.find('i').removeClass('bi-eye-slash-fill').addClass('bi-eye-fill');
                            btn.attr('title', 'Deactivate Employee');
                        } else {
                            btn.addClass('inactive');
                            btn.find('i').removeClass('bi-eye-fill').addClass('bi-eye-slash-fill');
                            btn.attr('title', 'Activate Employee');
                        }

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        Toast.fire({ icon: 'success', title: res.message || 'Status updated!' });
                    },
                    error: function(xhr) {
                        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to update user status.' });
                    }
                });
            });

            // Edit User Button handler (Opens Edit Modal)
            $(document).on('click', '.edit-user-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const email = $(this).data('email');
                const role = $(this).data('role');
                const salary = $(this).data('salary');

                // Dynamically show the admin option first to ensure we can set value correctly
                $('#edit_emp_role option[value="admin"]').show();

                $('#edit_emp_id').val(id);
                $('#edit_emp_name').val(name);
                $('#edit_emp_email').val(email);
                $('#edit_emp_role').val(role);
                $('#edit_emp_salary').val(parseFloat(salary) === 0 ? '' : salary);

                if (role === 'admin') {
                    $('#edit_emp_salary_wrapper').hide();
                    $('#edit_emp_role').prop('disabled', true);
                } else {
                    $('#edit_emp_salary_wrapper').show();
                    $('#edit_emp_role').prop('disabled', false);
                    $('#edit_emp_role option[value="admin"]').hide();
                }

                $('#editEmployeeModal').modal('show');
            });

            // Assign Role Button handler (Opens Edit Modal)
            $(document).on('click', '.assign-role-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const email = $(this).data('email');

                $('#edit_emp_id').val(id);
                $('#edit_emp_name').val(name);
                $('#edit_emp_email').val(email);
                
                // Select first non-admin role by default
                const firstRole = $('#edit_emp_role option:not([value="admin"])').first().val();
                $('#edit_emp_role').val(firstRole);
                
                $('#edit_emp_salary').val('');

                $('#edit_emp_salary_wrapper').show();
                $('#edit_emp_role').prop('disabled', false);
                $('#edit_emp_role option[value="admin"]').hide();

                $('#editEmployeeModal').modal('show');
            });

            // Edit Modal Role Dropdown change trigger
            $('#edit_emp_role').on('change', function() {
                if ($(this).val() === 'admin') {
                    $('#edit_emp_salary_wrapper').hide();
                } else {
                    $('#edit_emp_salary_wrapper').show();
                }
            });

            // Delete Role Handler
            $(document).on('click', '.delete-role-btn', function() {
                const role = $(this).data('role');

                Swal.fire({
                    title: 'Delete Role?',
                    text: `Are you sure you want to delete the role "${role}"? Users with this role will be reset to pending.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ff4757',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/roles/${role}`,
                            type: 'POST',
                            data: {
                                _method: 'DELETE'
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: res.message,
                                        confirmButtonColor: '#15803d'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: res.message || 'Failed to delete role.',
                                        confirmButtonColor: '#ff4757'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Failed to delete role.',
                                    confirmButtonColor: '#ff4757'
                                });
                            }
                        });
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
                                const isPending = row.closest('#pending-users-table').length > 0;
                                row.fadeOut(400, function() { 
                                    $(this).remove(); 
                                    if (isPending) {
                                         const countBadge = $('#pending-requests-count');
                                         let count = parseInt(countBadge.text()) || 0;
                                         count = Math.max(0, count - 1);
                                         countBadge.text(count + (count === 1 ? ' Request' : ' Requests'));
                                         
                                         if ($('#pending-users-table tbody tr').length === 0 || ($('#pending-users-table tbody tr').length === 1 && $('#pending-users-table tbody tr').hasClass('no-pending-requests-row'))) {
                                             $('#pending-users-table tbody').html(`
                                                 <tr class="no-pending-requests-row">
                                                     <td colspan="4" class="text-center py-4 text-secondary small">No pending approval requests at the moment.</td>
                                                 </tr>
                                             `);
                                         }
                                    }
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to delete user.' });
                            }
                        });
                    }
                });
            });
            // Initialize Chart.js Analytics
            const dailySalesData = @json($dailySales) || [];
            const categorySalesData = @json($categorySalesData) || [];

            const revenueDates = Array.isArray(dailySalesData) ? dailySalesData.map(d => d.date) : [];
            const revenueTotals = Array.isArray(dailySalesData) ? dailySalesData.map(d => parseFloat(d.total) || 0) : [];

            const categoryNames = Array.isArray(categorySalesData) ? categorySalesData.map(c => `${c.name} (${c.percentage}%)`) : [];
            const categoryTotals = Array.isArray(categorySalesData) ? categorySalesData.map(c => parseFloat(c.total) || 0) : [];

            // Bar Chart (Day-Wise Revenue Trend)
            try {
                const ctx1 = document.getElementById('revenueTrendChart').getContext('2d');
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: revenueDates,
                        datasets: [{
                            label: 'Revenue (₹)',
                            data: revenueTotals,
                            backgroundColor: 'rgba(21, 128, 61, 0.85)',
                            borderColor: '#15803d',
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false
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
            } catch (e) {
                console.error("Failed to initialize revenueTrendChart:", e);
            }

            // Disable datalabels globally so it doesn't display on bar charts
            if (typeof Chart !== 'undefined' && Chart.defaults && Chart.defaults.plugins) {
                Chart.defaults.plugins.datalabels = { display: false };
            }

            // Doughnut Chart (Sales Share by Category)
            try {
                const ctx2 = document.getElementById('topItemsChart').getContext('2d');
                const hasDatalabels = typeof ChartDataLabels !== 'undefined';
                new Chart(ctx2, {
                    type: 'doughnut',
                    plugins: hasDatalabels ? [ChartDataLabels] : [],
                    data: {
                        labels: categoryNames,
                        datasets: [{
                            label: 'Sales (₹)',
                            data: categoryTotals,
                            backgroundColor: [
                                '#15803d',
                                '#10b981',
                                '#f59e0b',
                                '#ef4444',
                                '#8b5cf6',
                                '#3b82f6',
                                '#6b7280'
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
                            },
                            datalabels: {
                                display: hasDatalabels,
                                color: '#fff',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                formatter: (value, ctx) => {
                                    if (categorySalesData && categorySalesData[ctx.dataIndex]) {
                                        const pct = categorySalesData[ctx.dataIndex].percentage;
                                        return pct > 3 ? `${pct}%` : '';
                                    }
                                    return '';
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed;
                                        return ` Sales: ₹${value.toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (e) {
                console.error("Failed to initialize topItemsChart:", e);
            }

            // Hourly Sales Chart (Busy Hours)
            try {
                const hourlySalesRaw = @json($hourlySales) || [];
                const hoursLabels = Array.from({ length: 24 }, (_, i) => `${i}:00`);
                const hourlyRevenueData = Array(24).fill(0);
                
                if (Array.isArray(hourlySalesRaw)) {
                    hourlySalesRaw.forEach(item => {
                        const h = parseInt(item.hour);
                        if (h >= 0 && h < 24) {
                            hourlyRevenueData[h] = parseFloat(item.total) || 0;
                        }
                    });
                }

                const ctx3 = document.getElementById('hourlySalesChart').getContext('2d');
                new Chart(ctx3, {
                    type: 'bar',
                    data: {
                        labels: hoursLabels,
                        datasets: [{
                            label: 'Hourly Sales (₹)',
                            data: hourlyRevenueData,
                            backgroundColor: '#10b981',
                            borderRadius: 4
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
            } catch (e) {
                console.error("Failed to initialize hourlySalesChart:", e);
            }

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
                                                    <input class="form-check-input permission-switch permission-switch-insert" type="checkbox" data-role="${response.role_key}" data-page="can_insert">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input permission-switch permission-switch-update" type="checkbox" data-role="${response.role_key}" data-page="can_update">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input permission-switch permission-switch-delete" type="checkbox" data-role="${response.role_key}" data-page="can_delete">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button type="button" class="btn-action-square btn-action-status toggle-role-status-btn" 
                                                            data-role="${response.role_key}"
                                                            data-status="1"
                                                            title="Deactivate Role">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                    <button type="button" class="btn-action-square btn-action-delete delete-role-btn" 
                                                            data-role="${response.role_key}"
                                                            title="Delete Role">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                                    $('#permissions-matrix-table tbody').append(newRow);

                                    // Add option to user role selectors and modals
                                    $('.user-role-select, #edit_emp_role, #emp_role').each(function() {
                                        // Avoid duplicate options and do not add admin
                                        if (response.role_key !== 'admin' && !$(this).find(`option[value="${response.role_key}"]`).length) {
                                            $(this).append(`<option value="${response.role_key}">${response.role_label}</option>`);
                                        }
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

            // Toggle Role status dynamically (delegated)
            $(document).on('click', '.toggle-role-status-btn', function() {
                const btn = $(this);
                const roleKey = btn.data('role');
                const currentStatus = parseInt(btn.attr('data-status'));
                const newStatus = currentStatus === 1 ? 0 : 1;
                const actionText = newStatus === 1 ? 'Activate' : 'Deactivate';

                Swal.fire({
                    title: `${actionText} Role?`,
                    text: `Are you sure you want to ${actionText.toLowerCase()} this role?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `Yes, ${actionText.toLowerCase()} it!`
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.roles.toggle-status') }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                role: roleKey,
                                status: newStatus
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });

                                    // Update button UI
                                    btn.attr('data-status', response.is_active ? 1 : 0);
                                    btn.attr('title', response.is_active ? 'Deactivate Role' : 'Activate Role');
                                    
                                    if (response.is_active) {
                                        btn.removeClass('inactive');
                                        btn.html('<i class="bi bi-eye-fill"></i>');
                                    } else {
                                        btn.addClass('inactive');
                                        btn.html('<i class="bi bi-eye-slash-fill"></i>');
                                    }
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Failed to toggle role status.'
                                });
                            }
                        });
                    }
                });
            });



            // Filter UI toggle based on pills selection
            $(document).on('click', '.admin-order-filter-btn', function() {
                $('.admin-order-filter-btn').removeClass('active');
                $(this).addClass('active');

                const type = $(this).attr('data-filter-type');
                $('#filter_type').val(type);

                if (type === 'single') {
                    $('#order-date-inputs-container').show();
                    $('#order-filter-single-wrapper').show();
                    $('#order-filter-from-wrapper').hide();
                    $('#order-filter-to-wrapper').hide();
                } else if (type === 'range') {
                    $('#order-date-inputs-container').show();
                    $('#order-filter-single-wrapper').hide();
                    $('#order-filter-from-wrapper').show();
                    $('#order-filter-to-wrapper').show();
                } else {
                    // Hide container for 'today' or 'all' and submit form immediately via AJAX
                    $('#order-date-inputs-container').hide();
                    $('#filter_date').val('');
                    $('#from_date').val('');
                    $('#to_date').val('');
                    
                    $('#order-filter-form').submit();
                }
            });

            // Initialize pills and dynamic date containers on page load
            const initialFilterType = $('#filter_type').val() || 'all';
            $('.admin-order-filter-btn').removeClass('active');
            $(`.admin-order-filter-btn[data-filter-type="${initialFilterType}"]`).addClass('active');

            if (initialFilterType === 'single') {
                $('#order-date-inputs-container').show();
                $('#order-filter-single-wrapper').show();
                $('#order-filter-from-wrapper').hide();
                $('#order-filter-to-wrapper').hide();
            } else if (initialFilterType === 'range') {
                $('#order-date-inputs-container').show();
                $('#order-filter-single-wrapper').hide();
                $('#order-filter-from-wrapper').show();
                $('#order-filter-to-wrapper').show();
            } else {
                $('#order-date-inputs-container').hide();
            }

            // Filter UI toggle for Audit Reports based on pill click
            $(document).on('click', '.admin-order-filter-btn[data-audit-filter]', function() {
                $('.admin-order-filter-btn[data-audit-filter]').removeClass('active');
                $(this).addClass('active');

                const filter = $(this).attr('data-audit-filter');
                $('#audit-active-filter-type').val(filter);

                if (filter === 'all') {
                    $('#audit-date-inputs-container').hide();
                    $('#audit_filter_date').val('');
                    $('#audit_filter_month').val('');
                    $('#audit_filter_year').val('');
                    $('#audit_from_date').val('');
                    $('#audit_to_date').val('');
                    applyAuditFilter('all');
                } else if (filter === 'day') {
                    $('#audit-date-inputs-container').show();
                    $('#audit-filter-day-wrapper').show();
                    $('#audit-filter-month-wrapper').hide();
                    $('#audit-filter-year-wrapper').hide();
                    $('#audit-filter-from-wrapper').hide();
                    $('#audit-filter-to-wrapper').hide();
                } else if (filter === 'month') {
                    $('#audit-date-inputs-container').show();
                    $('#audit-filter-day-wrapper').hide();
                    $('#audit-filter-month-wrapper').show();
                    $('#audit-filter-year-wrapper').hide();
                    $('#audit-filter-from-wrapper').hide();
                    $('#audit-filter-to-wrapper').hide();
                } else if (filter === 'year') {
                    $('#audit-date-inputs-container').show();
                    $('#audit-filter-day-wrapper').hide();
                    $('#audit-filter-month-wrapper').hide();
                    $('#audit-filter-year-wrapper').show();
                    $('#audit-filter-from-wrapper').hide();
                    $('#audit-filter-to-wrapper').hide();
                } else if (filter === 'range') {
                    $('#audit-date-inputs-container').show();
                    $('#audit-filter-day-wrapper').hide();
                    $('#audit-filter-month-wrapper').hide();
                    $('#audit-filter-year-wrapper').hide();
                    $('#audit-filter-from-wrapper').show();
                    $('#audit-filter-to-wrapper').show();
                }
            });

            // Handle Apply Button click for Audit Reports
            $('#apply-audit-filter-btn').on('click', function() {
                const filter = $('#audit-active-filter-type').val();
                applyAuditFilter(filter);
            });

            // Handle Reset Button click for Audit Reports
            $('#reset-audit-filter-btn').on('click', function() {
                // Clear fields
                $('#audit_filter_date').val('');
                $('#audit_filter_month').val('');
                $('#audit_filter_year').val('');
                $('#audit_from_date').val('');
                $('#audit_to_date').val('');

                // Hide container
                $('#audit-date-inputs-container').hide();

                // Reset pills back to All Records
                $('#audit-active-filter-type').val('all');
                $('.admin-order-filter-btn[data-audit-filter]').removeClass('active');
                $('.admin-order-filter-btn[data-audit-filter="all"]').addClass('active');

                // Apply
                applyAuditFilter('all');
            });

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

            // Handle Reset button click inside the dynamic date selection panel
            $('#reset-order-date-btn').on('click', function() {
                // Clear inputs
                $('#filter_date').val('');
                $('#from_date').val('');
                $('#to_date').val('');

                // Hide container
                $('#order-date-inputs-container').hide();

                // Reset filter type to today
                $('#filter_type').val('today');

                // Toggle pills active state back to Today
                $('.admin-order-filter-btn').removeClass('active');
                $('.admin-order-filter-btn[data-filter-type="today"]').addClass('active');

                // Trigger submission
                $('#order-filter-form').submit();
            });

            /* =========================================================================
             * Table Management Actions
             * ========================================================================= */

            // Add Table AJAX Validation & Submit
            $('#addTableForm').validate({
                ...validationOptions,
                messages: {
                    table_number: "Please enter a table number (e.g. T6).",
                    capacity: {
                        required: "Please enter the table seating capacity.",
                        min: "Seating capacity must be at least 1."
                    }
                },
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
                messages: {
                    table_number: "Please enter a table number.",
                    capacity: {
                        required: "Please enter the table seating capacity.",
                        min: "Seating capacity must be at least 1."
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    const tableId = $('#edit_table_id').val();
                    const tableNumber = $('#edit_table_number').val();
                    const capacity = $('#edit_table_capacity').val();
                    
                    $.ajax({
                        url: `/admin/tables/${tableId}`,
                        type: 'PUT',
                        data: $(form).serialize(),
                        success: function(res) {
                            $('#editTableModal').modal('hide');
                            Swal.fire({ icon: 'success', title: 'Success!', text: res.message, confirmButtonColor: '#15803d' });
                            
                            const editBtn = $(`.edit-table-btn[data-id="${tableId}"]`);
                            const row = editBtn.closest('tr');
                            
                            // Update text in row
                            row.find('td').eq(0).text(tableNumber);
                            row.find('td').eq(1).text(capacity + ' Seats');
                            
                            // Update attributes on edit button
                            editBtn.data('number', tableNumber).attr('data-number', tableNumber);
                            editBtn.data('capacity', capacity).attr('data-capacity', capacity);
                        },
                        error: function(xhr) {
                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to update table.', confirmButtonColor: '#dc2626' });
                        }
                    });
                }
            });

            // Toggle Table Status directly
            $('.toggle-table-status-btn').on('click', function() {
                const tableId = $(this).data('id');
                const btn = $(this);
                const status = btn.data('status');

                if (status === 'occupied') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cannot Deactivate Table',
                        text: 'This table is currently occupied and its status cannot be changed.',
                        confirmButtonColor: '#dc2626'
                    });
                    return;
                }

                $.ajax({
                    url: `/admin/tables/${tableId}/status`,
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
                        const row = btn.closest('tr');
                        const badgeCell = row.find('td').eq(2);

                        if (newStatus === 'available') {
                            badgeCell.html('<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Available</span>');
                            btn.removeClass('inactive');
                            btn.attr('title', 'Deactivate Table');
                            btn.html('<i class="bi bi-eye-fill"></i>');
                            btn.data('status', 'available');
                            btn.attr('data-status', 'available');
                        } else {
                            badgeCell.html('<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill"><i class="bi bi-eye-slash-fill me-1"></i> Inactive</span>');
                            btn.addClass('inactive');
                            btn.attr('title', 'Activate Table');
                            btn.html('<i class="bi bi-eye-slash-fill"></i>');
                            btn.data('status', 'unavailable');
                            btn.attr('data-status', 'unavailable');
                        }

                        // Update delete button status
                        row.find('.delete-table-btn').data('status', newStatus).attr('data-status', newStatus);
                    },
                    error: function(xhr) {
                        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to update status.', confirmButtonColor: '#dc2626' });
                    }
                });
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


            // CRM Table Search Input Handler
            $('#crm-search-input').on('keyup', function() {
                const query = $(this).val().toLowerCase();
                $('#crm-table tbody tr').each(function() {
                    const text = $(this).text().toLowerCase();
                    if (text.indexOf(query) > -1) {
                        $(this).removeClass('filter-hidden');
                    } else {
                        $(this).addClass('filter-hidden');
                    }
                });
                $('#crm-table').data('current-page', 1);
                paginateTable('#crm-table', '#crm-pagination-container', 50, 'tbody tr');
            });

            // Edit Employee form handler
            $('#editEmployeeForm').validate({
                ...validationOptions,
                messages: {
                    name: "Please enter the employee's name.",
                    email: {
                        required: "Please enter the email address.",
                        email: "Please enter a valid email address."
                    },
                    role: "Please select a role."
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    const userId = $('#edit_emp_id').val();
                    const name = $('#edit_emp_name').val().trim();
                    const email = $('#edit_emp_email').val().trim();
                    const role = $('#edit_emp_role').val();
                    const salary = parseFloat($('#edit_emp_salary').val()) || 0;

                    $.ajax({
                        url: `/admin/users/${userId}`,
                        type: 'POST',
                        data: {
                            _method: 'PUT',
                            name: name,
                            email: email,
                            role: role,
                            salary: salary
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
                                    text: res.message || 'Employee details updated successfully!',
                                    confirmButtonColor: '#15803d'
                                }).then(() => {
                                    // Check if they were a pending user
                                    const pendingRow = $(`#pending-users-table tr[data-user-id="${userId}"]`);
                                    if (pendingRow.length) {
                                        pendingRow.remove();

                                        // Update pending count
                                        const countBadge = $('#pending-requests-count');
                                        let count = parseInt(countBadge.text()) || 0;
                                        count = Math.max(0, count - 1);
                                        countBadge.text(count + (count === 1 ? ' Request' : ' Requests'));

                                        if ($('#pending-users-table tbody tr').length === 0 || ($('#pending-users-table tbody tr').length === 1 && $('#pending-users-table tbody tr').hasClass('no-pending-requests-row'))) {
                                            $('#pending-users-table tbody').html(`
                                                <tr class="no-pending-requests-row">
                                                    <td colspan="4" class="text-center py-4 text-secondary small">No pending approval requests at the moment.</td>
                                                </tr>
                                            `);
                                        }

                                        // Construct new employee row
                                        const roleLabel = res.user.role.charAt(0).toUpperCase() + res.user.role.slice(1);
                                        const salaryVal = res.user.role === 'admin' ? 
                                            '<span class="text-muted">—</span>' : 
                                            '₹' + parseFloat(res.user.salary).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        
                                        const newRow = `
                                            <tr data-user-id="${res.user.id}">
                                                <td class="fw-semibold text-dark user-name-cell">${res.user.name}</td>
                                                <td class="text-secondary user-email-cell">${res.user.email}</td>
                                                <td class="user-role-cell">${roleLabel}</td>
                                                <td class="salary-cell">${salaryVal}</td>
                                                <td class="text-end" style="padding-right: 20px;">
                                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                                        <button type="button" class="btn-action-square btn-action-status toggle-user-status-btn" 
                                                                data-id="${res.user.id}"
                                                                data-status="${res.user.is_active ? 1 : 0}"
                                                                title="${res.user.is_active ? 'Deactivate Employee' : 'Activate Employee'}">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </button>
                                                        <button type="button" class="btn-action-square btn-action-edit edit-user-btn" 
                                                                data-id="${res.user.id}" 
                                                                data-name="${res.user.name}" 
                                                                data-email="${res.user.email}" 
                                                                data-role="${res.user.role}" 
                                                                data-salary="${res.user.salary}"
                                                                title="Edit Info">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>
                                                        <button type="button" class="btn-action-square btn-action-delete delete-user-btn" 
                                                                data-id="${res.user.id}"
                                                                title="Delete Employee">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        `;

                                        // Add below admin row(s)
                                        const lastAdminRow = $('#employee-accounts-table tbody tr').filter(function() {
                                            return $(this).find('.user-role-cell').text().trim().toLowerCase() === 'admin';
                                        }).last();

                                        if (lastAdminRow.length) {
                                            lastAdminRow.after(newRow);
                                        } else {
                                            $('#employee-accounts-table tbody').prepend(newRow);
                                        }
                                    } else {
                                        // Find row in employee table and update
                                        const tr = $(`tr[data-user-id="${userId}"]`);
                                        tr.find('.user-name-cell').text(res.user.name);
                                        tr.find('.user-email-cell').text(res.user.email);
                                        tr.find('.user-role-cell').text(res.user.role.charAt(0).toUpperCase() + res.user.role.slice(1));
                                        
                                        if (res.user.role === 'admin') {
                                            tr.find('.salary-cell').html('<span class="text-muted">—</span>');
                                        } else {
                                            tr.find('.salary-cell').text('₹' + parseFloat(res.user.salary).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                                        }

                                        // Update Edit button data attributes
                                        const editBtn = tr.find('.edit-user-btn');
                                        editBtn.attr('data-name', res.user.name);
                                        editBtn.attr('data-email', res.user.email);
                                        editBtn.attr('data-role', res.user.role);
                                        editBtn.attr('data-salary', res.user.salary);
                                        
                                        // jQuery's .data() cache also needs updating
                                        editBtn.data('name', res.user.name);
                                        editBtn.data('email', res.user.email);
                                        editBtn.data('role', res.user.role);
                                        editBtn.data('salary', res.user.salary);
                                    }

                                    $('#editEmployeeModal').modal('hide');
                                });
                            } else {
                                Swal.fire('Error', res.message || 'Failed to save employee changes.', 'error');
                            }
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to update employee.';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                }
            });

            // Add Employee form handler
            $('#addEmployeeForm').validate({
                ...validationOptions,
                messages: {
                    name: "Please enter the employee's name.",
                    email: {
                        required: "Please enter the email address.",
                        email: "Please enter a valid email address."
                    },
                    password: "Please enter a password.",
                    role: "Please select a role."
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    const name = $('#emp_name').val().trim();
                    const email = $('#emp_email').val().trim();
                    const password = $('#emp_password').val();
                    const role = $('#emp_role').val();
                    const salary = parseFloat($('#emp_salary').val()) || 0;

                    $.ajax({
                        url: '/admin/users',
                        type: 'POST',
                        data: {
                            name: name,
                            email: email,
                            password: password,
                            role: role,
                            salary: salary
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: res.message || 'Employee account created successfully!'
                                }).then(() => {
                                    const roleLabel = res.user.role.charAt(0).toUpperCase() + res.user.role.slice(1);
                                    const salaryVal = res.user.role === 'admin' ? 
                                        '<span class="text-muted">—</span>' : 
                                        '₹' + parseFloat(res.user.salary).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    
                                    const newRow = `
                                        <tr data-user-id="${res.user.id}">
                                            <td class="fw-semibold text-dark user-name-cell">${res.user.name}</td>
                                            <td class="text-secondary user-email-cell">${res.user.email}</td>
                                            <td class="user-role-cell">${roleLabel}</td>
                                            <td class="salary-cell">${salaryVal}</td>
                                            <td class="text-end" style="padding-right: 20px;">
                                                <div class="d-flex justify-content-end align-items-center gap-1">
                                                    <button type="button" class="btn-action-square btn-action-status toggle-user-status-btn" 
                                                            data-id="${res.user.id}"
                                                            data-status="1"
                                                            title="Deactivate Employee">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                    <button type="button" class="btn-action-square btn-action-edit edit-user-btn" 
                                                            data-id="${res.user.id}" 
                                                            data-name="${res.user.name}" 
                                                            data-email="${res.user.email}" 
                                                            data-role="${res.user.role}" 
                                                            data-salary="${res.user.salary}"
                                                            title="Edit Info">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </button>
                                                    <button type="button" class="btn-action-square btn-action-delete delete-user-btn" 
                                                            data-id="${res.user.id}"
                                                            title="Delete Employee">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                                    const lastAdminRow = $('#employee-accounts-table tbody tr').filter(function() {
                                        return $(this).find('.user-role-cell').text().trim().toLowerCase() === 'admin';
                                    }).last();

                                    if (lastAdminRow.length) {
                                        lastAdminRow.after(newRow);
                                    } else {
                                        $('#employee-accounts-table tbody').prepend(newRow);
                                    }
                                    $('#addEmployeeModal').modal('hide');
                                    form.reset();
                                });
                            } else {
                                Swal.fire('Error', res.message || 'Failed to save employee.', 'error');
                            }
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to save employee.';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                }
            });

            // Initial Table Paginations
            paginateTable('#menu-pane table', '#menu-pagination-container', 50, 'tbody tr.admin-food-row');
            paginateTable('#orders-pane table', '#orders-pagination-container', 50, 'tbody tr');
            paginateTable('#employee-accounts-table', '#employees-pagination-container', 50, 'tbody tr');
            paginateTable('#tables-pane table', '#tables-pagination-container', 50, 'tbody tr');
            paginateTable('#crm-table', '#crm-pagination-container', 50, 'tbody tr');
            paginateTable('#audit-sales-table', '#audit-pagination-container', 50, 'tbody tr.audit-row');

            // Expose the ready helper functions to the window scope safely after declaration
            window.updateActiveOrders = updateActiveOrders;
            window.addToRevenue = addToRevenue;
        });
    </script>

</body>
</html>
