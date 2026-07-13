@forelse($orders as $order)
    <tr>
        <td class="fw-bold">#ORD-{{ $order->id }}</td>
        <td>
            @if($order->table)
                <div class="mb-1">
                    <span class="badge text-white" style="background: var(--primary-gradient) !important; font-size: 0.75rem;">
                        <i class="bi bi-hash"></i> Table {{ $order->table->table_number }}
                    </span>
                </div>
            @endif
            <div class="fw-semibold text-dark">{{ $order->customer_name }}</div>
            <small class="text-muted">{{ $order->contact_number }}</small>
        </td>
        <td>
            @foreach($order->orderItems as $item)
                <span class="badge bg-light text-dark border me-1 mb-1">
                    {{ $item->foodItem ? $item->foodItem->name : 'Unknown' }} (x{{ $item->quantity }})
                </span>
            @endforeach
        </td>
        <td class="fw-semibold text-primary order-amount-cell" data-amount="{{ $order->total_amount }}">₹{{ number_format($order->total_amount, 2) }}</td>
        <td>
            <select class="form-select form-select-sm order-status-select" data-id="{{ $order->id }}" data-current-status="{{ $order->status }}" style="width: 130px;">
                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </td>
        <td>
            @if($order->payment_status === 'paid')
                <button type="button" class="btn btn-sm btn-success order-payment-btn" data-id="{{ $order->id }}" data-status="paid" style="width: 100px; cursor: default; pointer-events: none; opacity: 1 !important;">
                    <i class="bi bi-check-circle-fill"></i> Paid
                </button>
            @else
                <button type="button" class="btn btn-sm btn-danger order-payment-btn" data-id="{{ $order->id }}" data-status="unpaid" style="width: 100px; cursor: pointer;" onclick="markOrderAsPaid({{ $order->id }}, this)">
                    <i class="bi bi-x-circle-fill"></i> Unpaid
                </button>
            @endif
        </td>
        <td class="text-secondary small" title="{{ $order->created_at->format('Y-m-d H:i:s') }}">{{ $order->created_at->format('d M Y, h:i A') }}</td>
        <td class="text-end">
            <div class="d-flex justify-content-end gap-2">
                @if($order->status === 'completed')
                    <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Print/Download Receipt">
                        <i class="bi bi-receipt"></i> Invoice
                    </a>
                @else
                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Invoice is only available after order is completed">
                        <i class="bi bi-receipt"></i> Invoice
                    </button>
                @endif

                <button type="button" class="btn btn-sm btn-outline-danger delete-order-btn" data-id="{{ $order->id }}">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-4 text-secondary">No orders recorded in system database.</td>
    </tr>
@endforelse
