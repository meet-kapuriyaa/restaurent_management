<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kitchen Display System - Dashboard</title>
    
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
            --bg-light: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --primary-gradient: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            min-height: 100vh;
        }

        .navbar-kds {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .kds-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
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

        .kds-brand span {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .order-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .order-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.1);
            border-color: #cbd5e1;
        }

        .order-item-list {
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            padding: 0.8rem 1rem;
        }

        .order-item-list li {
            border-bottom: 1px dashed #e2e8f0;
            padding: 0.5rem 0;
            font-size: 1.15rem;
            font-weight: 500;
            color: #334155;
        }

        .order-item-list li:last-child {
            border-bottom: none;
        }

        .timer-badge {
            background-color: #f1f5f9;
            border: 1px solid var(--border-color);
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35em 0.75em;
            border-radius: 50rem;
        }

        .empty-grid-state {
            padding: 5rem 1rem;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-grid-state i {
            font-size: 4rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            display: block;
        }
    </style>
</head>
<body>

    <!-- KDS Header Bar -->
    <nav class="navbar navbar-kds sticky-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a href="#" class="kds-brand d-flex align-items-center gap-2">
                <img src="/logo.jpg" alt="Logo" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                <span>Kitchen Display</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span id="kds-time" class="fw-medium text-secondary"><i class="bi bi-clock me-1"></i> --:--:--</span>
                @if(Auth::check() && Auth::user()->hasPermission('admin_panel'))
                    <a href="{{ route('admin.index') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                        <i class="bi bi-speedometer2 me-1"></i> Admin Panel
                    </a>
                @endif
                @if(Auth::check() && Auth::user()->hasPermission('waiter_terminal'))
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                        <i class="bi bi-shop me-1"></i> Order Panel
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
                <span class="badge bg-light text-dark border px-3 py-2 fw-medium" id="kds-live-status">
                    <span class="spinner-grow spinner-grow-sm text-success me-1" role="status" style="width: 8px; height: 8px;"></span>
                    Live Terminal
                </span>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="refresh-kds-btn" title="Refresh Board">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-fluid py-4 px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1 text-dark">Active Orders Log</h2>
                <p class="text-secondary mb-0">Accept incoming orders and mark completed orders below.</p>
            </div>
            <span class="badge bg-primary px-3 py-2 rounded-pill fw-semibold fs-6" id="active-orders-count">0 Active Orders</span>
        </div>

        <!-- Orders Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="kitchen-orders-grid">
            <!-- Order cards populated dynamically -->
        </div>
    </div>

    <!-- Script assets -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Setup AJAX CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Format Live Terminal Time
            function updateKDSTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                $('#kds-time').html(`<i class="bi bi-clock me-1"></i> ${timeString}`);
            }
            setInterval(updateKDSTime, 1000);
            updateKDSTime();

            // Track highest order ID seen to trigger chimes on new ones
            let maxOrderId = 0;

            // programmatically play synth alert chime
            function playChime() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    
                    // Note 1 (E5)
                    const osc1 = audioCtx.createOscillator();
                    const gain1 = audioCtx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(659.25, audioCtx.currentTime);
                    gain1.gain.setValueAtTime(0.08, audioCtx.currentTime);
                    gain1.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.4);
                    osc1.connect(gain1);
                    gain1.connect(audioCtx.destination);
                    osc1.start();
                    osc1.stop(audioCtx.currentTime + 0.4);

                    // Note 2 (A5)
                    setTimeout(() => {
                        const osc2 = audioCtx.createOscillator();
                        const gain2 = audioCtx.createGain();
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(880.00, audioCtx.currentTime);
                        gain2.gain.setValueAtTime(0.08, audioCtx.currentTime);
                        gain2.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.6);
                        osc2.connect(gain2);
                        gain2.connect(audioCtx.destination);
                        osc2.start();
                        osc2.stop(audioCtx.currentTime + 0.6);
                    }, 120);
                } catch (e) {
                    console.log('AudioContext not allowed yet: ', e);
                }
            }

            // Function to load and render order grid cards
            function loadKDSGrid() {
                const $grid = $('#kitchen-orders-grid');
                const $countBadge = $('#active-orders-count');

                $.ajax({
                    url: "{{ route('orders.pending') }}",
                    type: 'GET',
                    success: function(allOrders) {
                        const orders = allOrders.filter(order => order.status === 'pending' || order.status === 'preparing');
                        $grid.empty();
                        $countBadge.text(`${orders.length} Active Orders`);

                        if (orders.length === 0) {
                            $grid.html(`
                                <div class="col-12 w-100">
                                    <div class="empty-grid-state card p-5 border">
                                        <i class="bi bi-clipboard-check"></i>
                                        <h4 class="fw-bold text-dark">All Caught Up!</h4>
                                        <p class="mb-0">No active kitchen orders at the moment.</p>
                                    </div>
                                </div>
                            `);
                            maxOrderId = 0; // reset
                            return;
                        }

                        // Check for new orders
                        let hasNewOrder = false;
                        orders.forEach(order => {
                            if (maxOrderId > 0 && order.id > maxOrderId) {
                                hasNewOrder = true;
                            }
                        });

                        if (hasNewOrder) {
                            playChime();
                        }

                        // Update maxOrderId
                        const currentMax = Math.max(...orders.map(o => o.id));
                        if (currentMax > maxOrderId) {
                            maxOrderId = currentMax;
                        }

                        orders.forEach(order => {
                            // Calculate time elapsed
                            const orderTime = new Date(order.created_at);
                            const elapsedMins = Math.floor((new Date() - orderTime) / 60000);
                            const elapsedText = elapsedMins > 0 ? `${elapsedMins}m ago` : 'just now';

                            // Generate items list HTML
                            let itemsHTML = '<ul class="list-unstyled order-item-list mb-3">';
                            order.order_items.forEach(item => {
                                const itemName = item.food_item ? item.food_item.name : 'Unknown Food';
                                let itemModifiers = '';
                                if (item.modifiers && item.modifiers.trim() !== '') {
                                    itemModifiers = `<div class="text-success small ms-4 fw-semibold" style="color: #15803d;"><i class="bi bi-gear-fill me-1"></i>Custom: ${item.modifiers}</div>`;
                                }
                                itemsHTML += `<li class="mb-2"><span class="text-primary fw-bold fs-5 me-2">${item.quantity}x</span> ${itemName}${itemModifiers}</li>`;
                            });
                            itemsHTML += '</ul>';

                            // Style status badge
                            let statusBadge = '';
                            if (order.status === 'pending') {
                                statusBadge = '<span class="badge bg-warning text-dark status-badge">Pending</span>';
                            } else if (order.status === 'preparing') {
                                statusBadge = '<span class="badge bg-primary status-badge">Preparing</span>';
                            } else if (order.status === 'ready') {
                                statusBadge = '<span class="badge bg-success status-badge">Ready</span>';
                            } else {
                                statusBadge = `<span class="badge bg-secondary status-badge">${order.status}</span>`;
                            }

                            // Define buttons state
                            const isAccepted = order.status !== 'pending';
                            const tableName = order.table ? order.table.table_number : 'Takeout';

                            // Special instructions block
                            let instructionsHTML = '';
                            if (order.special_instructions && order.special_instructions.trim() !== '') {
                                instructionsHTML = `
                                    <div class="alert alert-warning py-2 px-3 small border-0 rounded-3 mb-3" style="background-color: #fef08a; color: #854d0e;">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i><strong>Chef Note:</strong> ${order.special_instructions}
                                    </div>
                                `;
                            }

                            $grid.append(`
                                <div class="col">
                                    <div class="order-card">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="fw-bold fs-5 text-dark">#ORD-${order.daily_no}</span>
                                                <div class="d-flex gap-2 align-items-center">
                                                    <span class="badge bg-dark rounded-pill px-2.5 py-1.5"><i class="bi bi-tablet-landscape me-1"></i> ${tableName}</span>
                                                    ${statusBadge}
                                                    <span class="timer-badge"><i class="bi bi-clock me-1"></i> ${elapsedText}</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Customer details -->
                                            <div class="mb-3">
                                                <h5 class="fw-bold text-dark mb-0">${order.customer_name}</h5>
                                                <small class="text-muted"><i class="bi bi-telephone-fill me-1"></i> ${order.contact_number}</small>
                                            </div>

                                            ${instructionsHTML}

                                            ${itemsHTML}
                                        </div>

                                        <!-- Card Action Buttons: Accept / Complete -->
                                        <div class="d-flex gap-2 border-top pt-3 mt-2">
                                            <button type="button" class="btn btn-lg w-50 status-change-btn ${isAccepted ? 'btn-light border text-muted' : 'btn-outline-primary fw-semibold'}" 
                                                    data-id="${order.id}" data-target-status="preparing" ${isAccepted ? 'disabled' : ''}>
                                                <i class="bi ${isAccepted ? 'bi-check-circle-fill text-success' : 'bi-play-fill'} me-1"></i> 
                                                ${isAccepted ? 'Accepted' : 'Accept'}
                                            </button>
                                            <button type="button" class="btn btn-lg btn-success w-50 status-change-btn fw-semibold" 
                                                    data-id="${order.id}" data-target-status="ready" data-current-status="${order.status}">
                                                <i class="bi bi-check-lg me-1"></i> Ready
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    },
                    error: function() {
                        console.error('Failed to load active orders for Kitchen Display Dashboard.');
                    }
                });
            }

            // Handle status change buttons clicks
            $(document).on('click', '.status-change-btn', function() {
                const orderId = $(this).data('id');
                const nextStatus = $(this).data('target-status');
                const currentStatus = $(this).data('current-status');

                // If attempting to complete without accepting first
                if (nextStatus === 'ready' && currentStatus === 'pending') {
                    Swal.fire({
                        title: 'Cannot Mark as Ready',
                        text: 'You must accept the order before marking it as ready.',
                        icon: 'warning',
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                changeOrderStatus(orderId, nextStatus);
            });

            // Perform status transition call
            function changeOrderStatus(orderId, status) {
                $.ajax({
                    url: `/orders/${orderId}/status`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        status: status
                    },
                    success: function(response) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: response.message || 'Order updated!'
                        });
                        
                        loadKDSGrid();
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update order status. Please try again.',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }

            // Manual Refresh
            $('#refresh-kds-btn').on('click', function() {
                loadKDSGrid();
            });

            // Auto-polling every 3 seconds for near real-time updates
            setInterval(loadKDSGrid, 3000);

            // Initial load
            loadKDSGrid();

        });
    </script>

</body>
</html>
</html>
