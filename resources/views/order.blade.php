<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Annam QSR - Order Placement System</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="/logo.jpg">
    
    <style>
        .pointer-cursor {
            cursor: pointer;
        }
        .navbar-waiter {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
        }
        .waiter-brand {
            font-weight: 800;
            font-size: 1.4rem;
            color: #0f172a;
            text-decoration: none;
        }
        .table-selected {
            border: 2px solid #15803d !important;
            box-shadow: 0 0 15px rgba(22, 163, 74, 0.3) !important;
            transform: scale(1.03);
        }
        .btn-outline-indigo {
            color: #15803d;
            border-color: #15803d;
        }
        .btn-outline-indigo:hover, .btn-check:checked + .btn-outline-indigo {
            color: #fff;
            background-color: #15803d;
            border-color: #15803d;
        }
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
            border-radius: 16px;
            padding: 2.5rem;
            color: white;
            margin-bottom: 2rem;
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

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            background-color: #ffffff;
        }

        .card-menu-item {
            cursor: pointer;
            border: 1px solid #f1f5f9;
        }

        .card-menu-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
            border-color: #e2e8f0;
        }

        .btn-primary-gradient {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.35);
            color: white;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 50% !important;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: bold;
            transition: all 0.2s ease;
        }

        .qty-btn:active {
            transform: scale(0.9);
        }

        .price-tag {
            font-size: 1.25rem;
            font-weight: 700;
            color: #15803d;
        }

        .cart-item-row {
            transition: all 0.3s ease;
        }

        /* Error styling for jQuery Validate */
        .invalid-feedback {
            display: block;
            font-size: 0.85rem;
            color: #ef4444;
            margin-top: 0.25rem;
        }

        .is-invalid {
            border-color: #ef4444 !important;
            background-image: none !important;
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            border-radius: 50rem;
        }

        .pending-orders-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .empty-cart-state {
            padding: 3rem 1rem;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-cart-state i {
            font-size: 3rem;
            background: linear-gradient(135deg, #cbd5e1 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            display: block;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-waiter sticky-top mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a href="#" class="waiter-brand d-flex align-items-center gap-2">
                <img src="/logo.jpg" alt="Logo" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                <span>Annam QSR <strong>Waiter</strong></span>
            </a>
            <div class="d-flex align-items-center gap-3">
                @if(Auth::check() && Auth::user()->hasPermission('admin_panel'))
                    <a href="{{ route('admin.index') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                        <i class="bi bi-speedometer2 me-1"></i> Admin Panel
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
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary fw-semibold"><i class="bi bi-box-arrow-in-right me-1"></i> Log In</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container">

        <!-- Banner -->
        <div class="header-banner">
            <h1 class="fw-bold mb-2">Order Placement Hub</h1>
            <p class="mb-0 opacity-75">Select a table, explore our menu, and place orders instantly.</p>
        </div>



        <div class="row g-4">
            <!-- Left Side: Food Menu -->
            <div class="col-lg-7">
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Menu Selection</h4>
                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold" style="color: #15803d; background-color: #dcfce7;">
                            {{ count($foodItems) }} Available Items
                        </span>
                    </div>

                    <div class="row g-3">
                        @forelse($foodItems as $item)
                            <div class="col-md-6">
                                <div class="card card-menu-item p-3 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="fw-bold mb-0">{{ $item->name }}</h5>
                                            <span class="price-tag">₹{{ number_format($item->price, 2) }}</span>
                                        </div>
                                        <p class="text-secondary small mb-3">{{ $item->description ?: 'No description available.' }}</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle-fill me-1"></i> Available
                                        </span>
                                        <button type="button" 
                                                class="btn btn-sm btn-primary-gradient add-to-cart-btn"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                data-price="{{ $item->price }}">
                                            <i class="bi bi-plus-lg me-1"></i> Add to Order
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="bi bi-search text-muted" style="font-size: 2.5rem;"></i>
                                <p class="text-muted mt-2">No food items found in the database.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Side: Table Select & Cart Form -->
            <div class="col-lg-5">
                <!-- Order Setup & Cart Form Wrapper -->
                <form id="orderForm" novalidate>
                    @csrf
                    
                    <!-- Order Type & Table Selection Card -->
                    <div class="card p-4 mb-4">
                        <h4 class="fw-bold mb-3">Order Type & Table</h4>
                        
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-semibold d-block">ORDER TYPE</label>
                            <div class="btn-group w-100" role="group" aria-label="Order Type Select">
                                <input type="radio" class="btn-check" name="order_type" id="type-dinein" value="dinein" checked>
                                <label class="btn btn-outline-indigo w-50 fw-semibold" for="type-dinein">
                                    <i class="bi bi-shop me-1"></i> Dine-in
                                </label>
                                
                                <input type="radio" class="btn-check" name="order_type" id="type-takeaway" value="takeaway">
                                <label class="btn btn-outline-indigo w-50 fw-semibold" for="type-takeaway">
                                    <i class="bi bi-box-seam me-1"></i> Takeaway
                                </label>
                            </div>
                        </div>

                        <!-- Table Map Section -->
                        <div id="table-selection-section" class="mb-0">
                            <label class="form-label text-secondary small fw-semibold">SELECT A TABLE</label>
                            
                            <!-- Hidden Table ID Input -->
                            <input type="hidden" name="table_id" id="selectedTableId" value="" required>
                            <input type="hidden" name="append_to_order_id" id="appendToOrderId" value="">
                            <div id="table-error-container"></div>
                            
                            <div class="row row-cols-3 g-2 mt-2" id="table-grid">
                                @foreach($tables as $table)
                                    @php
                                        $isOccupied = $table->status === 'occupied';
                                    @endphp
                                    <div class="col">
                                        <div class="card p-2 text-center pointer-cursor table-map-card border h-100 d-flex flex-column justify-content-between {{ $isOccupied ? 'border-danger bg-danger-subtle text-danger' : 'border-success bg-success-subtle text-success' }}" 
                                             data-id="{{ $table->id }}" 
                                             data-number="{{ $table->table_number }}" 
                                             data-status="{{ $table->status }}"
                                             style="border-radius: 12px; transition: all 0.2s ease;">
                                            <div class="fw-bold fs-5">{{ $table->table_number }}</div>
                                            <div class="small opacity-75">Cap: {{ $table->capacity }}</div>
                                            <div class="mt-1">
                                                <span class="badge {{ $isOccupied ? 'bg-danger' : 'bg-success' }}" style="font-size: 0.65rem;">
                                                    {{ ucfirst($table->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Customer Details Card -->
                    <div class="card p-4 mb-4">
                        <h4 class="fw-bold mb-3">Customer Details</h4>
                        <div class="mb-3">
                            <label for="customer_name" class="form-label text-secondary small fw-semibold">CUSTOMER NAME</label>
                            <input type="text" class="form-control form-control-lg" name="customer_name" id="customer_name" placeholder="customer name" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact_number" class="form-label text-secondary small fw-semibold">CONTACT NUMBER</label>
                            <input type="text" class="form-control form-control-lg" name="contact_number" id="contact_number" placeholder="+1234567890" required>
                        </div>
                        <div class="mb-0">
                            <label for="special_instructions" class="form-label text-secondary small fw-semibold">SPECIAL INSTRUCTIONS (CHEF NOTES)</label>
                            <textarea class="form-control" name="special_instructions" id="special_instructions" rows="2" placeholder="e.g. Extra spicy, serve dressing on the side..."></textarea>
                        </div>
                    </div>

                    <!-- Cart Card -->
                    <div class="card p-4">
                        <h4 class="fw-bold mb-3">Selected Items</h4>

                        <!-- Hidden Input for Cart Validation (required for jQuery Validation) -->
                        <input type="hidden" name="cart_items_count" id="cartItemsCount" value="" required>
                        <div id="cart-error-container"></div>

                        <!-- Cart Items Container -->
                        <div id="cart-contents" class="mb-4">
                            <!-- Populated dynamically via Javascript -->
                            <div class="empty-cart-state">
                                <i class="bi bi-cart3"></i>
                                <span class="fw-medium">Your order cart is empty</span>
                                <p class="small text-muted mt-1">Select items from the menu to build the order.</p>
                            </div>
                        </div>

                        <!-- Price summary (hidden when empty) -->
                        <div id="cart-summary" class="d-none border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-semibold text-secondary">Total Amount:</span>
                                <span class="fw-bold fs-4 text-primary" id="cart-total-price">₹0.00</span>
                            </div>

                            <button type="submit" class="btn btn-primary-gradient w-100 btn-lg py-3 fw-semibold shadow-sm">
                                <i class="bi bi-send-fill me-2"></i> Submit Order
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section: Live Pending Orders -->
        <section class="mt-5">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0"><i class="bi bi-activity text-warning me-2"></i>Live Pending Orders Monitor</h4>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="refresh-orders-btn">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh Logs
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Items Ordered</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Placed At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="pending-orders-tbody">
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <div class="spinner-border spinner-border-sm text-secondary me-2" role="status"></div>
                                    Loading live orders...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Script assets: jQuery, jQuery Validation, Bootstrap, SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Cart Data Store
            let cart = {};

            // Render Cart Contents
            function renderCart() {
                const $cartContents = $('#cart-contents');
                const $cartSummary = $('#cart-summary');
                const $cartTotal = $('#cart-total-price');
                const $cartCountInput = $('#cartItemsCount');

                // Clear container
                $cartContents.empty();

                const itemsArray = Object.values(cart);
                const itemsCount = itemsArray.length;

                // Update hidden validator field
                $cartCountInput.val(itemsCount > 0 ? itemsCount : '');

                if (itemsCount === 0) {
                    $cartContents.html(`
                        <div class="empty-cart-state">
                            <i class="bi bi-cart3"></i>
                            <span class="fw-medium">Your order cart is empty</span>
                            <p class="small text-muted mt-1">Select items from the menu to build the order.</p>
                        </div>
                    `);
                    $cartSummary.addClass('d-none');
                    return;
                }

                $cartSummary.removeClass('d-none');

                let totalHTML = '<div class="table-responsive"><table class="table table-sm align-middle">';
                totalHTML += '<thead><tr class="text-muted small"><th>Item</th><th class="text-center" style="width: 100px;">Qty</th><th class="text-end">Subtotal</th><th></th></tr></thead><tbody>';

                let orderTotal = 0;

                itemsArray.forEach((item, index) => {
                    const subtotal = item.price * item.quantity;
                    orderTotal += subtotal;
                    const cartKey = item.id + '_' + (item.modifiers || '').replace(/\s+/g, '').toLowerCase();

                    // Display modifiers in the cart if present
                    let modifiersHTML = '';
                    if (item.modifiers && item.modifiers.trim() !== '') {
                        modifiersHTML = `<div class="text-indigo small mt-1"><i class="bi bi-gear-fill me-1"></i>Custom: ${item.modifiers}</div>`;
                    }

                    totalHTML += `
                        <tr class="cart-item-row">
                            <td>
                                <div class="fw-semibold">${item.name}</div>
                                <div class="text-muted small">₹${parseFloat(item.price).toFixed(2)} each</div>
                                ${modifiersHTML}
                                
                                <!-- Hidden inputs for standard form submission backing -->
                                <input type="hidden" name="items[${index}][food_item_id]" value="${item.id}">
                                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                                <input type="hidden" name="items[${index}][modifiers]" value="${item.modifiers || ''}">
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-light border qty-btn decrease-qty" data-id="${cartKey}">-</button>
                                    <span class="fw-semibold small px-1">${item.quantity}</span>
                                    <button type="button" class="btn btn-sm btn-light border qty-btn increase-qty" data-id="${cartKey}">+</button>
                                </div>
                            </td>
                            <td class="text-end fw-semibold">₹${subtotal.toFixed(2)}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-link text-danger p-0 remove-item-btn" data-id="${cartKey}" title="Remove Item">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                totalHTML += '</tbody></table></div>';
                $cartContents.html(totalHTML);
                $cartTotal.text(`₹${orderTotal.toFixed(2)}`);

                // Re-run validation on cart count to clear error if valid
                if (typeof $('#orderForm').validate === 'function') {
                    $('#orderForm').validate().element('#cartItemsCount');
                }
            }

            // Add item to cart button click -> Open Modifiers Modal
            $(document).on('click', '.add-to-cart-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = parseFloat($(this).data('price'));

                // Set modal parameters
                $('#modifiersModal').data('item-id', id);
                $('#modifiersModal').data('item-name', name);
                $('#modifiersModal').data('item-price', price);
                
                $('#modifier-item-name').text(name);

                // Reset modal options
                $('#spice-mild').prop('checked', true);
                $('#addon-cheese').prop('checked', false);
                $('#addon-onions').prop('checked', false);
                $('#addon-sauce').prop('checked', false);

                // Show modal
                const myModal = new bootstrap.Modal(document.getElementById('modifiersModal'));
                myModal.show();
            });

            // Confirm Modifiers button click
            $('#confirm-modifiers-btn').on('click', function() {
                const id = $('#modifiersModal').data('item-id');
                const name = $('#modifiersModal').data('item-name');
                let price = parseFloat($('#modifiersModal').data('item-price'));

                // Get selected modifiers
                const spiceLevel = $('input[name="spice_level"]:checked').val();
                let modifiersList = [spiceLevel];
                
                if ($('#addon-cheese').is(':checked')) {
                    modifiersList.push('Extra Cheese');
                    price += 30.00; // Extra cheese price addition
                }
                if ($('#addon-onions').is(':checked')) {
                    modifiersList.push('No Onions');
                }
                if ($('#addon-sauce').is(':checked')) {
                    modifiersList.push('Extra Sauce');
                }

                const modifiersStr = modifiersList.join(', ');
                const cartKey = id + '_' + modifiersStr.replace(/\s+/g, '').toLowerCase();

                if (cart[cartKey]) {
                    cart[cartKey].quantity += 1;
                } else {
                    cart[cartKey] = {
                        id: id,
                        name: name,
                        price: price,
                        quantity: 1,
                        modifiers: modifiersStr
                    };
                }

                renderCart();

                // Hide modal
                const modalEl = document.getElementById('modifiersModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                modalInstance.hide();

                // Toast notification
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: `${name} (${spiceLevel}) added to cart.`
                });
            });

            // Increment quantity
            $(document).on('click', '.increase-qty', function() {
                const id = $(this).data('id');
                if (cart[id]) {
                    cart[id].quantity += 1;
                    renderCart();
                }
            });

            // Decrement quantity
            $(document).on('click', '.decrease-qty', function() {
                const id = $(this).data('id');
                if (cart[id]) {
                    if (cart[id].quantity > 1) {
                        cart[id].quantity -= 1;
                    } else {
                        delete cart[id];
                    }
                    renderCart();
                }
            });

            // Remove item completely
            $(document).on('click', '.remove-item-btn', function() {
                const id = $(this).data('id');
                const name = cart[id] ? cart[id].name : 'Item';
                
                Swal.fire({
                    title: 'Remove Item?',
                    text: `Are you sure you want to remove ${name} from the order?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        delete cart[id];
                        renderCart();
                    }
                });
            });

            // Toggle Order Type and Table Selection Section
            $('input[name="order_type"]').on('change', function() {
                if ($('#type-takeaway').is(':checked')) {
                    $('#table-selection-section').slideUp(200);
                    $('#selectedTableId').val('');
                    $('.table-map-card').removeClass('table-selected');
                    if (typeof $('#orderForm').validate === 'function') {
                        $('#orderForm').validate().element('#selectedTableId');
                    }
                } else {
                    $('#table-selection-section').slideDown(200);
                }
            });

            // Handle Table Selection click interaction
            $(document).on('click', '.table-map-card', function() {
                const status = $(this).data('status');
                const id = $(this).data('id');
                const number = $(this).data('number');

                if (status === 'occupied') {
                    // Fetch active order details for this table
                    $.ajax({
                        url: `/orders/active-by-table/${id}`,
                        type: 'GET',
                        success: function(response) {
                            if (response.success && response.order) {
                                Swal.fire({
                                    title: `Table ${number} is Occupied`,
                                    text: `Table ${number} is occupied by ${response.order.customer_name}. Would you like to add more items to their active order?`,
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#15803d',
                                    cancelButtonColor: '#64748b',
                                    confirmButtonText: 'Yes, add items',
                                    cancelButtonText: 'No'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Fill form fields
                                        $('#customer_name').val(response.order.customer_name).prop('readonly', true);
                                        $('#contact_number').val(response.order.contact_number).prop('readonly', true);
                                        $('#appendToOrderId').val(response.order.id);
                                        
                                        // Select the table in the UI
                                        $('.table-map-card').removeClass('table-selected');
                                        $(`.table-map-card[data-id="${id}"]`).addClass('table-selected');
                                        $('#selectedTableId').val(id);
                                        
                                        // Clear table validation error
                                        if (typeof $('#orderForm').validate === 'function') {
                                            $('#orderForm').validate().element('#selectedTableId');
                                        }

                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'info',
                                            title: 'Ready to add items to existing order.',
                                            showConfirmButton: false,
                                            timer: 3000
                                        });
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Could not fetch order details for this table.',
                                    confirmButtonColor: '#dc2626'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to retrieve active order. Please try again.',
                                confirmButtonColor: '#dc2626'
                            });
                        }
                    });
                    return;
                }

                $('.table-map-card').removeClass('table-selected');
                $(this).addClass('table-selected');
                $('#selectedTableId').val(id);

                // Clear append state and restore fields since a free table was selected
                $('#appendToOrderId').val('');
                $('#customer_name').prop('readonly', false);
                $('#contact_number').prop('readonly', false);

                // Re-run validation on table_id input to clear errors
                if (typeof $('#orderForm').validate === 'function') {
                    $('#orderForm').validate().element('#selectedTableId');
                }
            });

            // Auto-fetch customer name by contact number
            $('#contact_number').on('blur change', function() {
                // If it is set to readonly (because we selected an occupied table), don't fetch/overwrite
                if ($(this).prop('readonly')) {
                    return;
                }
                
                const phone = $(this).val().trim();
                if (phone.length >= 5) {
                    $.ajax({
                        url: "{{ route('customers.lookup') }}",
                        type: 'GET',
                        data: { phone: phone },
                        success: function(response) {
                            if (response.success && response.name) {
                                const currentName = $('#customer_name').val().trim();
                                if (!currentName) {
                                    $('#customer_name').val(response.name);
                                    
                                    // Visual cue/toast
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'success',
                                        title: `Welcome back, ${response.name}!`,
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                }
                            }
                        }
                    });
                }
            });

            // jQuery Validation configuration
            $('#orderForm').validate({
                ignore: [], // Make sure hidden fields are not ignored
                rules: {
                    customer_name: {
                        required: true,
                        minlength: 2
                    },
                    contact_number: {
                        required: true
                    },
                    cart_items_count: {
                        required: true,
                        min: 1
                    },
                    table_id: {
                        required: {
                            depends: function(element) {
                                return $('#type-dinein').is(':checked');
                            }
                        }
                    }
                },
                messages: {
                    customer_name: {
                        required: "Please enter the customer's name.",
                        minlength: "Customer's name must be at least 2 characters long."
                    },
                    contact_number: {
                        required: "Please enter the customer's contact number."
                    },
                    cart_items_count: {
                        required: "Your order cart must have at least one food item.",
                        min: "Your order cart must have at least one food item."
                    },
                    table_id: {
                        required: "Please select an available table for Dine-in orders."
                    }
                },
                errorElement: 'div',
                errorClass: 'invalid-feedback',
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                errorPlacement: function(error, element) {
                    if (element.attr('name') === 'cart_items_count') {
                        error.appendTo('#cart-error-container');
                    } else if (element.attr('name') === 'table_id') {
                        error.appendTo('#table-error-container');
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    
                    Swal.fire({
                        title: 'Place Order?',
                        text: "Are you ready to submit this order to the kitchen?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#15803d',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, submit order'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitOrderViaAjax();
                        }
                    });
                }
            });

            // Submit order via AJAX
            function submitOrderViaAjax() {
                const customerName = $('#customer_name').val();
                const contactNumber = $('#contact_number').val();
                const tableId = $('#selectedTableId').val();
                const specialInstructions = $('#special_instructions').val();
                
                const itemsPayload = Object.values(cart).map(item => ({
                    food_item_id: item.id,
                    quantity: item.quantity,
                    modifiers: item.modifiers || ''
                }));

                Swal.fire({
                    title: 'Processing Order...',
                    text: 'Sending order to kitchen, please wait.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('orders.store') }}",
                    type: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify({
                        customer_name: customerName,
                        contact_number: contactNumber,
                        table_id: tableId,
                        special_instructions: specialInstructions,
                        items: itemsPayload,
                        append_to_order_id: $('#appendToOrderId').val() || null
                    }),
                    success: function(response) {
                        Swal.close();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Order placed successfully!',
                            confirmButtonColor: '#15803d'
                        }).then(() => {
                            // Reset form states
                            cart = {};
                            renderCart();
                            
                            // Reset inputs
                            $('#customer_name').val('').prop('readonly', false);
                            $('#contact_number').val('').prop('readonly', false);
                            $('#special_instructions').val('');
                            $('#selectedTableId').val('');
                            $('#appendToOrderId').val('');
                            $('.table-map-card').removeClass('table-selected');

                            // Mark the ordered table card as occupied in the UI
                            if (tableId) {
                                const tableCard = $(`.table-map-card[data-id="${tableId}"]`);
                                tableCard.removeClass('border-success bg-success-subtle text-success pointer-cursor')
                                         .addClass('border-danger bg-danger-subtle text-danger')
                                         .attr('data-status', 'occupied');
                                tableCard.find('.badge').removeClass('bg-success').addClass('bg-danger').text('Occupied');
                            }

                            // Reload pending orders table logs
                            loadPendingOrders();
                        });
                    },
                    error: function(xhr) {
                        Swal.close();
                        
                        if (xhr.status === 422) {
                            // Handle backend Validation Errors
                            const errors = xhr.responseJSON.errors;
                            let errorListHTML = '<ul class="text-start mb-0">';
                            $.each(errors, function(field, messages) {
                                $.each(messages, function(index, message) {
                                    errorListHTML += `<li>${message}</li>`;
                                });
                            });
                            errorListHTML += '</ul>';

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Warning',
                                html: errorListHTML,
                                confirmButtonColor: '#ef4444'
                            });
                        } else {
                            // Handle standard Server Errors (500, etc.)
                            const errorMessage = xhr.responseJSON?.message || 'Failed to submit the order. Please try again.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: errorMessage,
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    }
                });
            }

            // Function to load and render live pending orders
            function loadPendingOrders() {
                const $tbody = $('#pending-orders-tbody');
                
                $.ajax({
                    url: "{{ route('orders.pending') }}",
                    type: 'GET',
                    success: function(orders) {
                        $tbody.empty();
                        
                        if (orders.length === 0) {
                            $tbody.html(`
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-info-circle me-1"></i> No pending orders at the moment.
                                    </td>
                                </tr>
                            `);
                            return;
                        }

                        orders.forEach(order => {
                            // Formulate items listing text
                            let itemsText = '';
                            order.order_items.forEach((item, idx) => {
                                const itemName = item.food_item ? item.food_item.name : 'Unknown Food';
                                itemsText += `<span class="badge bg-light text-dark border me-1 mb-1">${itemName} (x${item.quantity})</span>`;
                            });

                             // Style badges dynamically
                             let statusBadge = '';
                             if (order.status === 'pending') {
                                 statusBadge = '<span class="badge bg-warning text-dark badge-status">Pending</span>';
                             } else if (order.status === 'preparing') {
                                 statusBadge = '<span class="badge bg-primary badge-status">Preparing</span>';
                             } else if (order.status === 'ready') {
                                 statusBadge = '<span class="badge bg-success badge-status">Ready</span>';
                             } else {
                                 statusBadge = `<span class="badge bg-secondary badge-status">${order.status}</span>`;
                             }
                             let paymentBadge = order.payment_status === 'paid' 
                                 ? '<span class="badge bg-success badge-status">Paid</span>' 
                                 : '<span class="badge bg-secondary badge-status">Unpaid</span>';

                            // Format placed time
                            const placedTime = new Date(order.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                            $tbody.append(`
                                <tr>
                                    <td class="fw-bold">#ORD-${order.id}</td>
                                    <td>
                                        <div class="fw-semibold">${order.customer_name}</div>
                                        <div class="text-muted small">${order.contact_number}</div>
                                    </td>
                                    <td>${itemsText}</td>
                                    <td class="fw-semibold text-primary">₹${parseFloat(order.total_amount).toFixed(2)}</td>
                                    <td>${statusBadge}</td>
                                    <td>${paymentBadge}</td>
                                    <td class="text-secondary small">${placedTime}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success complete-order-btn px-3 fw-medium" data-id="${order.id}">
                                            <i class="bi bi-check-lg me-1"></i> Complete Order
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    },
                    error: function() {
                        $tbody.html(`
                            <tr>
                                <td colspan="8" class="text-center py-4 text-danger">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Failed to refresh live orders.
                                </td>
                            </tr>
                        `);
                    }
                });
            }

            // Click listener for complete order button
            $(document).on('click', '.complete-order-btn', function() {
                const orderId = $(this).data('id');
                
                Swal.fire({
                    title: 'Complete Order?',
                    text: "This will mark the order as completed and record the payment.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, complete order'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show progress spinner
                        Swal.fire({
                            title: 'Completing Order...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Ajax call to complete the order
                        $.ajax({
                            url: `/orders/${orderId}/complete`,
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Completed!',
                                    text: response.message || 'Order completed successfully!',
                                    confirmButtonColor: '#15803d'
                                }).then(() => {
                                    loadPendingOrders();
                                });
                            },
                            error: function(xhr) {
                                Swal.close();
                                const errorText = xhr.responseJSON?.message || 'Failed to complete order. Please try again.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorText,
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        });
                    }
                });
            });

            // Function to dynamically disable table option in dropdown once occupied (Deprecated, table map used instead)
            function updateTablesDropdown(occupiedTableId) {
                // No-op
            }

            // Refresh orders click binding
            $('#refresh-orders-btn').on('click', function() {
                loadPendingOrders();
            });

            // Initial load of orders
            loadPendingOrders();
        });
    </script>

    <!-- Modifiers Modal -->
    <div class="modal fade" id="modifiersModal" tabindex="-1" aria-labelledby="modifiersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modifiersModalLabel">Customize Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4 id="modifier-item-name" class="fw-bold text-success mb-3" style="color: #15803d;">Item Name</h4>
                    
                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-semibold">SPICINESS LEVEL</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="spice_level" id="spice-mild" value="Mild" checked>
                            <label class="btn btn-outline-secondary w-100" for="spice-mild">Mild</label>
                            
                            <input type="radio" class="btn-check" name="spice_level" id="spice-medium" value="Medium">
                            <label class="btn btn-outline-secondary w-100" for="spice-medium">Medium</label>
                            
                            <input type="radio" class="btn-check" name="spice_level" id="spice-hot" value="Hot">
                            <label class="btn btn-outline-secondary w-100" for="spice-hot">Hot</label>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-secondary small fw-semibold">ADDITIONAL ADD-ONS</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="addon-cheese" value="Extra Cheese">
                            <label class="form-check-label" for="addon-cheese">Extra Cheese (+₹30.00)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="addon-onions" value="No Onions">
                            <label class="form-check-label" for="addon-onions">No Onions</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="addon-sauce" value="Extra Sauce">
                            <label class="form-check-label" for="addon-sauce">Extra Sauce</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-success text-white fw-semibold w-100 py-2.5 rounded-3" id="confirm-modifiers-btn" style="background-color: #15803d;">
                        <i class="bi bi-cart-plus me-1"></i> Add to Order
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
</html>
