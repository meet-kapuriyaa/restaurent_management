<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt_ORD-{{ $order->id }}</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --receipt-width: 380px;
            --text-color: #1e293b;
            --muted-color: #64748b;
            --border-style: dashed;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
            color: var(--text-color);
            margin: 0;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
        }

        .actions-bar {
            width: var(--receipt-width);
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            gap: 1rem;
        }

        .btn-action {
            flex: 1;
            padding: 0.6rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-print {
            background-color: #15803d;
            color: white;
        }

        .btn-print:hover {
            background-color: #16a34a;
        }

        .btn-close-window {
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-close-window:hover {
            background-color: #f8fafc;
        }

        .receipt-container {
            background: #ffffff;
            width: var(--receipt-width);
            padding: 2rem 1.5rem;
            box-sizing: border-box;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        /* receipt decoration lines */
        .receipt-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .restaurant-name {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin: 0;
            text-transform: uppercase;
        }

        .restaurant-subtitle {
            font-size: 0.85rem;
            color: var(--muted-color);
            margin: 0.2rem 0 0.8rem 0;
        }

        .divider {
            border-top: 1.5px var(--border-style) #cbd5e1;
            margin: 1rem 0;
        }

        .meta-info {
            font-size: 0.85rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.25rem;
        }

        .meta-label {
            color: var(--muted-color);
            font-weight: 500;
        }

        .meta-value {
            font-weight: 600;
            text-align: right;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            margin: 1rem 0;
        }

        .items-table th {
            text-align: left;
            color: var(--muted-color);
            font-weight: 600;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table td {
            padding: 0.6rem 0;
            vertical-align: top;
        }

        .item-name {
            font-weight: 600;
        }

        .item-details {
            font-size: 0.8rem;
            color: var(--muted-color);
        }

        .item-total {
            font-weight: 700;
            text-align: right;
        }

        .totals-section {
            font-size: 0.95rem;
            margin-top: 1rem;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.4rem;
        }

        .totals-label {
            font-weight: 500;
        }

        .totals-value {
            font-weight: 600;
        }

        .grand-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 1.2rem;
            font-weight: 800;
            margin-top: 0.8rem;
            padding-top: 0.8rem;
            border-top: 2px var(--border-style) #94a3b8;
        }

        .payment-badge-container {
            text-align: center;
            margin-top: 1.5rem;
        }

        .badge-payment {
            display: inline-block;
            padding: 0.4rem 1.2rem;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 50px;
        }

        .badge-paid {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-unpaid {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.8rem;
            color: var(--muted-color);
            line-height: 1.4;
        }

        /* PRINT CONFIGURATION */
        @media print {
            body {
                background-color: #ffffff;
                color: #000000;
                padding: 0;
                margin: 0;
            }

            .actions-bar {
                display: none !important;
            }

            .receipt-container {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0.5cm !important;
            }

            /* Prevent printing URL headers/footers */
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Actions for Browser Window -->
    <div class="actions-bar">
        <button onclick="window.print()" class="btn-action btn-print">
            <i class="bi bi-printer-fill"></i> Print / Save PDF
        </button>
        <button onclick="window.close()" class="btn-action btn-close-window">
            <i class="bi bi-x-lg"></i> Close Window
        </button>
    </div>

    <!-- Receipt Contents -->
    <div class="receipt-container">
        
        <div class="receipt-header">
            <h1 class="restaurant-name">Annam QSR</h1>
            <p class="restaurant-subtitle">Authentic South Indian Fast Food<br>123 Culinary Boulevard, Foodville</p>
            <div class="divider"></div>
        </div>

        <div class="meta-info">
            <div class="meta-row">
                <span class="meta-label">Receipt No:</span>
                <span class="meta-value">#ORD-{{ $order->id }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Table:</span>
                <span class="meta-value">{{ $order->table ? $order->table->table_number : 'Takeout' }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Date:</span>
                <span class="meta-value">{{ $order->created_at->format('d M Y, h:i A') }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Customer Name:</span>
                <span class="meta-value">{{ $order->customer_name }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Contact Number:</span>
                <span class="meta-value">{{ $order->contact_number }}</span>
            </div>
            @if($order->payment_status === 'paid')
                <div class="meta-row">
                    <span class="meta-label">Payment Mode:</span>
                    <span class="meta-value" style="text-transform: uppercase;">{{ $order->payment_method ?: 'Cash' }}</span>
                </div>
            @endif
        </div>

        <div class="divider"></div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="text-align: right;">Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->foodItem ? $item->foodItem->name : 'Unknown Dish' }}</div>
                            <div class="item-details">{{ $item->quantity }} x ₹{{ number_format($item->price, 2) }}</div>
                        </td>
                        <td class="item-total">
                            ₹{{ number_format($item->quantity * $item->price, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="totals-section">
            <div class="totals-row">
                <span class="totals-label">Subtotal:</span>
                <span class="totals-value">₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div class="totals-row">
                <span class="totals-label">GST (0%):</span>
                <span class="totals-value">₹0.00</span>
            </div>
            <div class="grand-total-row">
                <span>TOTAL AMOUNT:</span>
                <span>₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <div class="payment-badge-container">
            @if($order->payment_status === 'paid')
                <span class="badge-payment badge-paid"><i class="bi bi-check-circle-fill"></i> Paid</span>
            @else
                <span class="badge-payment badge-unpaid"><i class="bi bi-exclamation-triangle-fill"></i> Unpaid</span>
            @endif
        </div>

        @if($order->payment_status === 'unpaid' || $order->payment_method === 'upi')
            <div class="divider"></div>
            <div style="text-align: center; margin: 1.5rem 0;">
                <p class="small fw-semibold text-secondary" style="font-size: 0.8rem; margin: 0 0 8px 0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">UPI Quick Scan Checkout</p>
                <div style="background: #ffffff; padding: 10px; display: inline-block; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=upi://pay?pa=restaurant@bank&pn=Annam%20QSR&am={{ $order->total_amount }}&tn=ORD-{{ $order->id }}" alt="UPI QR Code" style="width: 150px; height: 150px; display: block;">
                </div>
                <p class="small text-muted" style="font-size: 0.75rem; color: #64748b; margin: 6px 0 0 0;">Scan using GPay, PhonePe, or Paytm</p>
            </div>
        @endif

        <div class="receipt-footer">
            <p>Thank you for dining with us!</p>
            <p style="font-size: 0.75rem; margin-top: 0.5rem; letter-spacing: 0.5px;">POWERED BY ANNAM QSR RMS</p>
        </div>

    </div>

    <!-- Auto Print Script -->
    <script>
        window.onload = function() {
            // Trigger browser print dialog automatically
            window.print();
        };
    </script>
</body>
</html>
