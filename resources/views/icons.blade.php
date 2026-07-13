<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Annam QSR - Icon Configuration</title>
    
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

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
        }

        .navbar-admin {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
        }

        .admin-brand {
            font-weight: 700;
            color: var(--text-main);
            text-decoration: none;
            font-size: 1.25rem;
        }

        .profile-avatar-trigger {
            background: none;
            border: none;
            padding: 0;
            position: relative;
        }

        .profile-avatar-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-color);
        }

        .card-admin {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
        }

        .table-icons th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            background-color: #f8fafc;
            border-bottom: 2px solid #cbd5e1;
        }

        .table-icons td {
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

        .btn-action-edit { background-color: #06b6d4; }
        .btn-action-delete { background-color: #ef4444; }

        .btn-action-square:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-action-square:active {
            transform: translateY(0);
        }

        .icon-preview-box {
            width: 38px;
            height: 38px;
            background-color: #f1f5f9;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #334155;
            border: 1px solid var(--border-color);
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
                <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-success fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> System Settings
                </a>
                @auth
                    <div class="dropdown">
                        <button class="profile-avatar-trigger dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="outline: none;">
                            <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="profile-avatar-img">
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenu" style="border-radius: 12px; min-width: 240px; padding: 0;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-3 py-3 px-4" href="{{ route('profile.show') }}" style="border-top-left-radius: 12px; border-top-right-radius: 12px; background: none;">
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover; border: 1px solid #e2e8f0;">
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
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}" class="text-success text-decoration-none">System Settings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Icon Management</li>
                    </ol>
                </nav>
                <h2 class="fw-bold mb-0">Icon Management</h2>
            </div>
        </div>

        <!-- Dynamic Icon creation/edit Card -->
        <div class="card-admin mb-4 d-none" id="add-icon-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" id="form-card-title">Add New Icon</h5>
                <button type="button" class="btn-close" id="btn-close-form" aria-label="Close"></button>
            </div>
            
            <form id="icon-form" novalidate>
                <input type="hidden" id="edit_icon_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600;">Display Name</label>
                        <input type="text" class="form-control" name="name" id="name" required placeholder="e.g. Gallery Icon" style="border-radius: 8px; border: 1px solid var(--border-color); padding: 0.6rem 0.8rem; font-size: 0.9rem;">
                    </div>
                    <div class="col-md-6">
                        <label for="class" class="form-label" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600;">CSS Icon Class (Bootstrap Icon)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="class" id="class" required placeholder="e.g. bi-image" style="border-radius: 8px 0 0 8px; border: 1px solid var(--border-color); padding: 0.6rem 0.8rem; font-size: 0.9rem;">
                            <span class="input-group-text bg-light border" style="border-radius: 0 8px 8px 0;" id="class-live-preview"><i class="bi bi-question-circle"></i></span>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold text-white" style="background: var(--blue-gradient); border: none; border-radius: 8px;" id="btn-save-icon">Save</button>
                    <button type="button" class="btn btn-sm btn-secondary px-4 fw-semibold" style="border-radius: 8px;" id="btn-cancel-icon">Cancel</button>
                </div>
            </form>
        </div>

        <div class="row g-4 mb-5">
            <!-- Icon Directory Listing -->
            <div class="col-12">
                <div class="card-admin">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">System Icons Gallery</h4>
                        <button type="button" class="btn btn-sm btn-primary fw-semibold px-3" style="background: var(--blue-gradient); border: none; border-radius: 8px;" id="btn-show-add-form">
                            <i class="bi bi-plus-lg me-1"></i> Add Icon
                        </button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3 bg-light p-2 rounded-3 border">
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-secondary fw-semibold">Show</span>
                            <select class="form-select form-select-sm" style="width: 70px;" id="icons-entries-count">
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                            <span class="small text-secondary fw-semibold">entries</span>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="max-width: 250px;">
                            <span class="small text-secondary fw-semibold">Search:</span>
                            <input type="text" class="form-control form-control-sm" id="search-icons-input" placeholder="Search icon name/class..." style="border-radius: 6px;">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-icons" id="icons-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Index</th>
                                    <th>Display Name</th>
                                    <th>CSS Class</th>
                                    <th style="width: 150px;">Preview</th>
                                    <th style="width: 120px;" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($icons as $index => $icon)
                                    <tr class="icon-row" data-id="{{ $icon->id }}">
                                        <td class="text-secondary fw-semibold">{{ $index + 1 }}</td>
                                        <td class="fw-bold text-dark">{{ $icon->name }}</td>
                                        <td><code>{{ $icon->class }}</code></td>
                                        <td>
                                            <div class="icon-preview-box">
                                                <i class="bi {{ $icon->class }}"></i>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end align-items-center gap-1">
                                                <button type="button" class="btn-action-square btn-action-edit edit-icon-btn" 
                                                        data-id="{{ $icon->id }}" 
                                                        data-name="{{ $icon->name }}" 
                                                        data-class="{{ $icon->class }}"
                                                        title="Edit Icon">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button type="button" class="btn-action-square btn-action-delete delete-icon-btn" 
                                                        data-id="{{ $icon->id }}" 
                                                        title="Delete Icon">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-secondary">No icons loaded yet. Click "Add Icon" to start adding dynamic icons.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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

            // Update live class preview when typing
            $('#class').on('input', function() {
                const value = $(this).val().trim();
                const iconPreview = $('#class-live-preview');
                if (value.startsWith('bi-') && value.length > 3) {
                    iconPreview.html(`<i class="bi ${value}"></i>`);
                } else {
                    iconPreview.html(`<i class="bi bi-question-circle"></i>`);
                }
            });

            // Toggle creation/edit form visibility
            $('#btn-show-add-form').click(function() {
                $('#form-card-title').text('Add New Icon');
                $('#edit_icon_id').val('');
                $('#icon-form')[0].reset();
                $('#class-live-preview').html(`<i class="bi bi-question-circle"></i>`);
                $('#add-icon-card').removeClass('d-none').hide().slideDown(300);
            });

            $('#btn-close-form, #btn-cancel-icon').click(function() {
                $('#add-icon-card').slideUp(300, function() {
                    $(this).addClass('d-none');
                });
            });

            // Edit icon details trigger
            $(document).on('click', '.edit-icon-btn', function() {
                const btn = $(this);
                const id = btn.data('id');
                const name = btn.data('name');
                const cls = btn.data('class');

                $('#form-card-title').text('Edit Icon Details');
                $('#edit_icon_id').val(id);
                $('#name').val(name);
                $('#class').val(cls);
                $('#class-live-preview').html(`<i class="bi ${cls}"></i>`);

                $('#add-icon-card').removeClass('d-none').hide().slideDown(300);
                $('html, body').animate({ scrollTop: $('#add-icon-card').offset().top - 20 }, 'slow');
            });

            // Submit Add/Edit form
            $('#icon-form').submit(function(e) {
                e.preventDefault();
                const id = $('#edit_icon_id').val();
                const isEdit = id !== '';
                const url = isEdit ? `/icon/${id}` : '/icon';
                const method = isEdit ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: method,
                        name: $('#name').val().trim(),
                        class: $('#class').val().trim()
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
                            text: xhr.responseJSON?.message || 'Failed to save icon configuration.'
                        });
                    }
                });
            });

            // Delete icon configuration
            $(document).on('click', '.delete-icon-btn', function() {
                const btn = $(this);
                const id = btn.data('id');

                Swal.fire({
                    title: 'Delete Icon?',
                    text: 'Are you sure you want to delete this icon config? This icon won\'t be selectable for modules anymore.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/icon/${id}`,
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
                                    text: xhr.responseJSON?.message || 'Failed to delete icon.'
                                });
                            }
                        });
                    }
                });
            });

            // Local table filter pagination & search
            function filterAndPaginateIcons() {
                const query = $('#search-icons-input').val().toLowerCase();
                const limit = parseInt($('#icons-entries-count').val()) || 10;
                
                let visibleCount = 0;
                $('#icons-table tbody tr').each(function() {
                    const row = $(this);
                    const name = row.find('td:nth-child(2)').text().toLowerCase();
                    const cls = row.find('td:nth-child(3)').text().toLowerCase();
                    
                    const matchesSearch = name.includes(query) || cls.includes(query);
                    
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

            $('#search-icons-input').on('keyup', filterAndPaginateIcons);
            $('#icons-entries-count').on('change', filterAndPaginateIcons);
            filterAndPaginateIcons();
        });
    </script>
</body>
</html>
