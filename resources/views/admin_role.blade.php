<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Annam QSR - Role Management</title>
    
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
    
    <style>
        :root {
            --bg-color: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
            --border-color: #e2e8f0;
            --primary-gradient: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
            --blue-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            --red-gradient: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        }

        .table-modules th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            background-color: #f8fafc;
            border-bottom: 2px solid #cbd5e1;
        }

        .table-modules td {
            font-size: 0.9rem;
            color: #334155;
            padding: 1rem 0.75rem;
        }

        .btn-action-square {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            color: white;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .btn-action-view { background-color: #10b981; }
        .btn-action-view.disabled-view { background-color: #ef4444; }
        .btn-action-edit { background-color: #06b6d4; }
        .btn-action-delete { background-color: #ef4444; }

        .btn-action-square:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }

        .tree-view-item {
            list-style: none;
            padding-left: 1.5rem;
            position: relative;
            margin-bottom: 0.5rem;
        }

        .tree-view-item::before {
            content: '';
            position: absolute;
            top: 12px;
            left: 0;
            width: 1rem;
            height: 1px;
            background-color: #cbd5e1;
        }

        .tree-view-item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 1px;
            height: 100%;
            background-color: #cbd5e1;
        }

        .tree-view-item:last-child::after {
            height: 12px;
        }

        .tree-view-icon {
            color: #64748b;
            margin-right: 0.5rem;
            font-size: 1.1rem;
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
        
        .card-admin {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
        }
        
        .btn-action-square {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            color: #ffffff;
        }
        .btn-action-status {
            background-color: #dcfce7;
            color: #166534;
        }
        .btn-action-status.inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .btn-action-status:hover {
            opacity: 0.9;
        }
        .btn-action-delete {
            background-color: #fee2e2;
            color: #ef4444;
        }
        .btn-action-delete:hover {
            background-color: #ef4444;
            color: #ffffff;
        }
    </style>
</head>
<body class="pb-5">

    <!-- Top Admin Header -->
    <nav class="navbar navbar-admin sticky-top mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.index') }}" class="admin-brand d-flex align-items-center gap-2">
                <img src="/logo.jpg" alt="Logo" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                <span>Annam QSR <strong>Admin</strong></span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.index') }}" class="btn btn-sm btn-outline-success fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
                <a href="{{ route('admin.icons.index') }}" class="btn btn-sm btn-outline-success fw-semibold">
                    <i class="bi bi-gear-fill me-1"></i> Manage Icons
                </a>
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
                                        <span class="text-secondary small">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span>
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

    <div class="container-fluid px-md-5 px-3 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}" class="text-success text-decoration-none">Admin Panel</a></li>
                        <li class="breadcrumb-item active" aria-current="page">System Settings</li>
                    </ol>
                </nav>
                <h2 class="fw-bold mb-0">System settings</h2>
            </div>
        </div>

        <!-- System Feature Toggles Section -->
        <div class="row mt-5 mb-5">
            <div class="col-12">
                <div class="card-admin">
                    <h4 class="fw-bold mb-3"><i class="bi bi-toggle-on text-success me-2"></i>System Feature Toggles</h4>
                    <p class="text-secondary small mb-4">Enable or disable core system features in real-time. When a feature is disabled, the corresponding screens, buttons, and endpoints will be blocked and hidden from the browser.</p>
                    
                    <div class="row g-4">
                        @foreach($features as $feature)
                            <div class="col-md-6 col-lg-4">
                                <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between" style="border-radius: 12px !important;">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">{{ $feature->display_name }}</h5>
                                        <p class="text-secondary small mb-3">{{ $feature->description }}</p>
                                    </div>
                                    <div class="form-check form-switch mt-auto pt-2">
                                        <input class="form-check-input feature-toggle-switch" type="checkbox" role="switch" 
                                               id="feature_{{ $feature->key }}" 
                                               data-key="{{ $feature->key }}" 
                                               {{ $feature->is_enabled ? 'checked' : '' }}
                                               style="cursor: pointer; width: 2.5rem; height: 1.25rem;">
                                        <label class="form-check-label small fw-semibold ms-2 text-secondary" for="feature_{{ $feature->key }}">
                                            {{ $feature->is_enabled ? 'Active / Enabled' : 'Inactive / Disabled' }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
        </div>

        <!-- Add New/Edit Module Collapsible Card -->
        <div class="card-admin mb-4 mt-5 d-none" id="add-module-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" id="form-card-title">Add New Module</h5>
                <button type="button" class="btn-close" id="btn-close-form" aria-label="Close"></button>
            </div>
            
            <form id="module-form" novalidate>
                <input type="hidden" id="edit_module_id">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="parent_id" class="form-label" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600;">Menu</label>
                        <select class="form-select" name="parent_id" id="parent_id" style="border-radius: 8px; border: 1px solid var(--border-color); padding: 0.6rem 0.8rem; font-size: 0.9rem;">
                            <option value="">Select Menu (Main Parent)</option>
                            @foreach($parentModules as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="title" class="form-label" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600;">Title</label>
                        <input type="text" class="form-control" name="title" id="title" required placeholder="e.g. Testimonials" style="border-radius: 8px; border: 1px solid var(--border-color); padding: 0.6rem 0.8rem; font-size: 0.9rem;">
                    </div>
                    <div class="col-md-3">
                        <label for="url" class="form-label" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600;">URL</label>
                        <input type="text" class="form-control" name="url" id="url" required placeholder="e.g. #testimonials-pane" style="border-radius: 8px; border: 1px solid var(--border-color); padding: 0.6rem 0.8rem; font-size: 0.9rem;">
                    </div>
                    <div class="col-md-3">
                        <label for="icon_class" class="form-label" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600;">Icon</label>
                        <select class="form-select" name="icon_class" id="icon_class" style="border-radius: 8px; border: 1px solid var(--border-color); padding: 0.6rem 0.8rem; font-size: 0.9rem;">
                            <option value="">Select Icon</option>
                            @foreach($icons as $icon)
                                <option value="{{ $icon->class }}">{{ $icon->name }} ({{ $icon->class }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold text-white" style="background: var(--blue-gradient); border: none; border-radius: 8px;" id="btn-save-module">Save</button>
                    <button type="button" class="btn btn-sm btn-secondary px-4 fw-semibold" style="border-radius: 8px;" id="btn-cancel-module">Cancel</button>
                </div>
            </form>
        </div>

        <div class="row g-4 mb-5">
            <!-- Left panel: Modules Directory -->
            <div class="col-lg-8">
                <div class="card-admin h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Module Builder</h4>
                        <button type="button" class="btn btn-sm btn-primary fw-semibold px-3" style="background: var(--blue-gradient); border: none; border-radius: 8px;" id="btn-show-add-form">
                            <i class="bi bi-plus-lg me-1"></i> Add New
                        </button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3 bg-light p-2 rounded-3 border">
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-secondary fw-semibold">Show</span>
                            <select class="form-select form-select-sm" style="width: 70px;" id="modules-entries-count">
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                            <span class="small text-secondary fw-semibold">entries</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-secondary fw-semibold">Search:</span>
                            <input type="text" id="search-modules-input" class="form-control form-control-sm" style="width: 200px;">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-modules" id="modules-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 60px;">#</th>
                                    <th>Module Name</th>
                                    <th>URL</th>
                                    <th>Parent</th>
                                    <th class="text-center">Icon Class</th>
                                    <th class="text-end" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($flatModules as $index => $module)
                                    <tr class="module-row" data-id="{{ $module->id }}">
                                        <td class="text-center text-secondary fw-semibold">{{ $index + 1 }}</td>
                                        <td class="fw-semibold">
                                            @if($module->parent_id)
                                                <span class="text-secondary me-1">↳</span>{{ $module->title }}
                                            @else
                                                {{ $module->title }}
                                            @endif
                                        </td>
                                        <td class="text-secondary font-monospace">{{ $module->url }}</td>
                                        <td>
                                            @if($module->parent_id)
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded">{{ $module->parent->title }}</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success border px-2 py-1 rounded">Main</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-monospace text-secondary">
                                            @if($module->icon_class)
                                                <i class="bi {{ $module->icon_class }} me-2"></i>{{ $module->icon_class }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <button type="button" class="btn-action-square btn-action-view toggle-visibility-btn {{ !$module->is_visible ? 'disabled-view' : '' }}" 
                                                        data-id="{{ $module->id }}"
                                                        title="{{ $module->is_visible ? 'Hide Module' : 'Show Module' }}">
                                                    @if($module->is_visible)
                                                        <i class="bi bi-eye-fill"></i>
                                                    @else
                                                        <i class="bi bi-eye-slash-fill"></i>
                                                    @endif
                                                </button>
                                                <button type="button" class="btn-action-square btn-action-edit edit-module-btn" 
                                                        data-id="{{ $module->id }}"
                                                        data-title="{{ $module->title }}"
                                                        data-url="{{ $module->url }}"
                                                        data-parent="{{ $module->parent_id }}"
                                                        data-icon="{{ $module->icon_class }}"
                                                        title="Edit Module">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button type="button" class="btn-action-square btn-action-delete delete-module-btn" 
                                                        data-id="{{ $module->id }}"
                                                        title="Delete Module">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-secondary">No modules seeded yet. Click "+ Add New" to create one.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right panel: Tree Layout & Ordering -->
            <div class="col-lg-4">
                <div class="card-admin h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">View:</h4>
                        <button type="button" class="btn btn-sm btn-primary fw-semibold px-3" style="background: var(--blue-gradient); border: none; border-radius: 8px;" id="btn-enable-sorting">
                            <i class="bi bi-arrow-down-up me-1"></i> Sorting
                        </button>
                    </div>

                    <div class="p-3 border rounded-3 bg-light mb-4" id="sorting-instruction" style="display: none; border-color: #cbd5e1 !important;">
                        <p class="small text-secondary mb-0"><i class="bi bi-info-circle me-1"></i> Adjust weights using the input order fields, then click **Apply Sorting** to save changes.</p>
                    </div>

                    <!-- Tree Root UL -->
                    <ul class="ps-0 mb-0" id="modules-tree-list">
                        @foreach($roots as $root)
                            <li class="tree-root-item mb-3" style="list-style: none;" data-id="{{ $root->id }}">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="fw-bold d-flex align-items-center text-dark">
                                        @if($root->icon_class)
                                            <i class="bi {{ $root->icon_class }} tree-view-icon"></i>
                                        @else
                                            <i class="bi bi-folder tree-view-icon"></i>
                                        @endif
                                        <span>{{ $root->title }}</span>
                                    </div>
                                    <input type="number" class="form-control form-control-sm sort-weight-input text-center" 
                                           data-id="{{ $root->id }}" value="{{ $root->order_weight }}" 
                                           style="width: 55px; padding: 0.2rem; display: none;">
                                </div>

                                @if($root->children->count() > 0)
                                    <ul class="mt-2 ps-3">
                                        @foreach($root->children as $child)
                                            <li class="tree-view-item" data-id="{{ $child->id }}">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="text-secondary small fw-medium">
                                                        @if($child->icon_class)
                                                            <i class="bi {{ $child->icon_class }} me-1 text-secondary"></i>
                                                        @else
                                                            <i class="bi bi-file-earmark me-1 text-secondary"></i>
                                                        @endif
                                                        {{ $child->title }}
                                                    </span>
                                                    <input type="number" class="form-control form-control-sm sort-weight-input text-center" 
                                                           data-id="{{ $child->id }}" value="{{ $child->order_weight }}" 
                                                           style="width: 55px; padding: 0.2rem; display: none;">
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4" id="sorting-actions-wrapper" style="display: none;">
                        <button type="button" class="btn btn-sm btn-success text-white w-100 py-2 fw-semibold" style="background-color: #15803d; border-radius: 8px;" id="btn-save-sort-order">
                            <i class="bi bi-check-lg me-1"></i> Apply Sorting
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts: JQuery, Bootstrap, SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // CSRF Token Setup for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Toggle Dynamic Feature Flags
            $(document).on('change', '.feature-toggle-switch', function() {
                const switchInput = $(this);
                const key = switchInput.data('key');
                const isEnabled = switchInput.is(':checked') ? 1 : 0;
                const label = switchInput.siblings('label');

                // Update text instantly
                label.text(isEnabled ? 'Active / Enabled' : 'Inactive / Disabled');

                $.ajax({
                    url: "{{ route('admin.features.toggle') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        key: key,
                        is_enabled: isEnabled
                    },
                    success: function(res) {
                        if (res.success) {
                            const toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            toast.fire({
                                icon: 'success',
                                title: res.message
                            });
                        } else {
                            // Revert state
                            switchInput.prop('checked', !isEnabled);
                            label.text(!isEnabled ? 'Active / Enabled' : 'Inactive / Disabled');
                            Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                        }
                    },
                    error: function(xhr) {
                        // Revert state
                        switchInput.prop('checked', !isEnabled);
                        label.text(!isEnabled ? 'Active / Enabled' : 'Inactive / Disabled');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to toggle feature status.'
                        });
                    }
                });
            });

            // Toggle creation/edit form visibility
            $('#btn-show-add-form').click(function() {
                $('#form-card-title').text('Add New Module');
                $('#edit_module_id').val('');
                $('#module-form')[0].reset();
                $('#add-module-card').removeClass('d-none').hide().slideDown(300);
            });

            $('#btn-close-form, #btn-cancel-module').click(function() {
                $('#add-module-card').slideUp(300, function() {
                    $(this).addClass('d-none');
                });
            });

            // Edit module trigger
            $(document).on('click', '.edit-module-btn', function() {
                const btn = $(this);
                const id = btn.data('id');
                const title = btn.data('title');
                const url = btn.data('url');
                const parent = btn.data('parent');
                const icon = btn.data('icon');

                $('#form-card-title').text('Edit Module details');
                $('#edit_module_id').val(id);
                $('#title').val(title);
                $('#url').val(url);
                $('#parent_id').val(parent || '');
                $('#icon_class').val(icon || '');

                $('#add-module-card').removeClass('d-none').hide().slideDown(300);
                $('html, body').animate({ scrollTop: $('#add-module-card').offset().top - 20 }, 'slow');
            });

            // Submit Add/Edit form
            $('#module-form').submit(function(e) {
                e.preventDefault();
                const id = $('#edit_module_id').val();
                const isEdit = id !== '';
                const url = isEdit ? `/admin/modules/${id}` : '/admin/modules';
                const method = isEdit ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: method,
                        title: $('#title').val().trim(),
                        url: $('#url').val().trim(),
                        parent_id: $('#parent_id').val() || null,
                        icon_class: $('#icon_class').val() || null
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            confirmButtonColor: '#15803d'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: xhr.responseJSON?.message || 'Failed to save module configuration details.'
                        });
                    }
                });
            });

            // Toggle visibility logic
            $(document).on('click', '.toggle-visibility-btn', function() {
                const btn = $(this);
                const id = btn.data('id');

                $.ajax({
                    url: `/admin/modules/${id}/toggle`,
                    type: 'POST',
                    success: function(res) {
                        if (res.success) {
                            const toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            toast.fire({
                                icon: 'success',
                                title: res.message
                            });

                            if (res.is_visible) {
                                btn.removeClass('disabled-view').find('i').removeClass('bi-eye-slash-fill').addClass('bi-eye-fill');
                                btn.attr('title', 'Hide Module');
                            } else {
                                btn.addClass('disabled-view').find('i').removeClass('bi-eye-fill').addClass('bi-eye-slash-fill');
                                btn.attr('title', 'Show Module');
                            }
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to toggle visibility.'
                        });
                    }
                });
            });

            // Delete module configuration logic
            $(document).on('click', '.delete-module-btn', function() {
                const btn = $(this);
                const id = btn.data('id');

                Swal.fire({
                    title: 'Delete Module?',
                    text: 'Are you sure you want to delete this module? Any child sub-menus linked to it will also be deleted.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/modules/${id}`,
                            type: 'POST',
                            data: {
                                _method: 'DELETE'
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: res.message,
                                    confirmButtonColor: '#15803d'
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Failed to delete module.'
                                });
                            }
                        });
                    }
                });
            });

            // Enable sorting mode toggles
            $('#btn-enable-sorting').click(function() {
                const isVisible = $('.sort-weight-input').first().is(':visible');
                if (isVisible) {
                    $('.sort-weight-input').hide(200);
                    $('#sorting-instruction').hide(200);
                    $('#sorting-actions-wrapper').hide(200);
                    $(this).html('<i class="bi bi-arrow-down-up me-1"></i> Sorting');
                } else {
                    $('.sort-weight-input').show(200);
                    $('#sorting-instruction').show(200);
                    $('#sorting-actions-wrapper').show(200);
                    $(this).html('<i class="bi bi-x-lg me-1"></i> Close');
                }
            });

            // Save sort order weights
            $('#btn-save-sort-order').click(function() {
                const weights = [];
                $('.sort-weight-input').each(function() {
                    weights.push({
                        id: $(this).data('id'),
                        order_weight: parseInt($(this).val()) || 0
                    });
                });

                $.ajax({
                    url: '/admin/modules/sort',
                    type: 'POST',
                    data: {
                        order: weights
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sorted!',
                            text: res.message,
                            confirmButtonColor: '#15803d'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to save sorting order weights.'
                        });
                    }
                });
            });

            // Local Table Filter search & entries pagination function
            function filterAndPaginateModules() {
                const query = $('#search-modules-input').val().toLowerCase();
                const limit = parseInt($('#modules-entries-count').val()) || 10;
                
                let visibleCount = 0;
                $('#modules-table tbody tr').each(function() {
                    const row = $(this);
                    const name = row.find('td:nth-child(2)').text().toLowerCase();
                    const url = row.find('td:nth-child(3)').text().toLowerCase();
                    
                    const matchesSearch = name.includes(query) || url.includes(query);
                    
                    if (matchesSearch) {
                        if (visibleCount < limit) {
                            row.show();
                            visibleCount++;
                        } else {
                            row.hide();
                        }
                    } else {
                        row.hide();
                    }
                });
            }

            $('#search-modules-input').on('keyup', filterAndPaginateModules);
            $('#modules-entries-count').on('change', filterAndPaginateModules);
            filterAndPaginateModules();
        });
    </script>
</body>
</html>
