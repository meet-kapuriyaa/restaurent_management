<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Annam QSR - User Profile</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5, Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <link rel="icon" type="image/jpeg" href="/logo.jpg">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
            --bg-color: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
        }

        .navbar-profile {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 1.4rem;
            color: #0f172a;
            text-decoration: none;
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

        .profile-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            padding: 2.5rem;
            height: 100%;
        }

        .form-label-title {
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #475569;
            text-transform: uppercase;
        }

        .form-control {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 0.65rem 0.75rem;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .form-control:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
        }

        .invalid-feedback {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .btn-save {
            background-color: #15803d;
            border: none;
            color: #ffffff;
            font-weight: 600;
            padding: 0.6rem 1.75rem;
            border-radius: 6px;
            transition: background-color 0.2s ease;
        }

        .btn-save:hover {
            background-color: #16a34a;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-profile sticky-top mb-5">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="brand-logo d-flex align-items-center gap-2">
                    <img src="/logo.jpg" alt="Logo" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                    <span>Annam QSR</span>
                </a>
                
                @php
                    $role = Auth::user()->role;
                    $backUrl = route('home');
                    if ($role === 'admin') {
                        $backUrl = route('admin.index');
                    } elseif ($role === 'waiter') {
                        $backUrl = route('orders.index');
                    } elseif ($role === 'chef') {
                        $backUrl = route('orders.kitchen');
                    }
                @endphp
                <a href="{{ $backUrl }}" class="btn btn-sm btn-outline-secondary fw-semibold ms-2">
                    <i class="bi bi-arrow-left me-1"></i> Back to Panel
                </a>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                @auth
                    <div class="dropdown">
                        <button class="profile-avatar-trigger dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="outline: none;">
                            <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="profile-avatar-img">
                            <span class="profile-avatar-status"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenu" style="border-radius: 12px; min-width: 240px; padding: 0;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-3 py-3 px-4" href="#" style="border-top-left-radius: 12px; border-top-right-radius: 12px; background: none; cursor: default;">
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

    <!-- Main Container -->
    <div class="container-fluid px-md-5 px-3 pb-5">
        <div class="row g-4">
            
            <!-- Left: Profile Information -->
            <div class="col-md-6">
                <div class="profile-card">
                    <h4 class="fw-bold text-dark mb-1">Profile Information</h4>
                    <p class="text-secondary small mb-4">Update your account's profile information and email address.</p>
                    
                    <form id="profileInfoForm" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label form-label-title mb-1">Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label form-label-title mb-1">Email</label>
                            <input type="email" class="form-control" name="email" id="email" value="{{ Auth::user()->email }}" required>
                        </div>
                        <button type="submit" class="btn btn-save shadow-sm">Save</button>
                    </form>
                </div>
            </div>
            
            <!-- Right: Update Password -->
            <div class="col-md-6">
                <div class="profile-card">
                    <h4 class="fw-bold text-dark mb-1">Update Password</h4>
                    <p class="text-secondary small mb-4">Ensure your account is using a long, random password to stay secure.</p>
                    
                    <form id="profilePasswordForm" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label form-label-title mb-1">Current Password</label>
                            <input type="password" class="form-control" name="current_password" id="current_password" placeholder="••••••••" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label form-label-title mb-1">New Password</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="••••••••" required>
                        </div>
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label form-label-title mb-1">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-save shadow-sm">Save</button>
                    </form>
                </div>
            </div>
            
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
            
            // Validate Profile Info Form
            $('#profileInfoForm').validate({
                rules: {
                    name: { required: true },
                    email: { required: true, email: true }
                },
                messages: {
                    name: "Please enter your name.",
                    email: {
                        required: "Please enter your email.",
                        email: "Please enter a valid email address."
                    }
                },
                errorElement: 'div',
                errorClass: 'invalid-feedback',
                highlight: function(element) { $(element).addClass('is-invalid'); },
                unhighlight: function(element) { $(element).removeClass('is-invalid'); },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    
                    Swal.fire({
                        title: 'Saving changes...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    
                    $.ajax({
                        url: "{{ route('profile.info.update') }}",
                        type: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            Swal.close();
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved!',
                                text: response.message,
                                confirmButtonColor: '#15803d'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.close();
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                const validator = $('#profileInfoForm').validate();
                                validator.showErrors(xhr.responseJSON.errors);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Failed to update profile information.',
                                    confirmButtonColor: '#15803d'
                                });
                            }
                        }
                    });
                }
            });

            // Validate Password Form
            $('#profilePasswordForm').validate({
                rules: {
                    current_password: { required: true },
                    password: { required: true, minlength: 8 },
                    password_confirmation: { required: true, equalTo: "#password" }
                },
                messages: {
                    current_password: "You must enter your current password.",
                    password: {
                        required: "Please enter a new password.",
                        minlength: "New password must be at least 8 characters."
                    },
                    password_confirmation: {
                        required: "Please confirm your new password.",
                        equalTo: "Confirm password does not match the new password."
                    }
                },
                errorElement: 'div',
                errorClass: 'invalid-feedback',
                highlight: function(element) { $(element).addClass('is-invalid'); },
                unhighlight: function(element) { $(element).removeClass('is-invalid'); },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    
                    Swal.fire({
                        title: 'Updating password...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    
                    $.ajax({
                        url: "{{ route('profile.password.update') }}",
                        type: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            Swal.close();
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: response.message,
                                confirmButtonColor: '#15803d'
                            }).then(() => {
                                $('#current_password').val('');
                                $('#password').val('');
                                $('#password_confirmation').val('');
                            });
                        },
                        error: function(xhr) {
                            Swal.close();
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                const validator = $('#profilePasswordForm').validate();
                                validator.showErrors(xhr.responseJSON.errors);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Failed to update password.',
                                    confirmButtonColor: '#15803d'
                                });
                            }
                        }
                    });
                }
            });

        });
    </script>
</body>
</html>
