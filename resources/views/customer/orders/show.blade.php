@extends('layouts.customer')

@section('title', 'Order Details')

@push('styles')
{{-- Add this stack to your main layout file's <head> section: @stack('styles') --}}
<style>
.timeline {
    list-style-type: none;
    position: relative;
    padding-left: 1.5rem;
    margin: 0;
}
.timeline-item {
    position: relative;
    padding-bottom: 2.5rem;
    padding-left: 2rem;
}
.timeline-item:last-child {
    padding-bottom: 0;
}
/* The vertical line */
.timeline-item::before {
    content: '';
    background-color: #e9ecef;
    width: 3px;
    position: absolute;
    top: 5px;
    bottom: 0;
    left: 0;
}
.timeline-item:last-child::before {
    height: 5px; /* Stop the line at the last item's icon */
}
/* The circle icon */
.timeline-icon {
    position: absolute;
    left: -8px; /* Adjust to center on the line */
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: #fff;
    border: 3px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    z-index: 1;
}
.timeline-content {
    margin-top: -5px;
}
/* Completed State */
.timeline-item.completed::before {
    background-color: #1cc88a;
}
.timeline-item.completed .timeline-icon {
    border-color: #1cc88a;
    background-color: #1cc88a;
    color: #fff;
}
.timeline-item.completed .timeline-content {
    color: #5a5c69;
}
/* Active State */
.timeline-item.active .timeline-icon {
    border-color: #4e73df;
    transform: scale(1.2);
}
.timeline-item.active .timeline-content,
.timeline-item.active .timeline-content strong {
    font-weight: 700;
    color: #4e73df;
}
/* Pending State */
.timeline-item.pending .timeline-content {
    color: #858796;
}
.timeline-item.pending .timeline-icon {
    border-color: #e9ecef;
}
/* Cancelled State */
.timeline-item.active-cancelled .timeline-icon {
    border-color: #e74a3b;
    background-color: #e74a3b;
    color: #fff;
}
.timeline-item.active-cancelled .timeline-content,
.timeline-item.active-cancelled .timeline-content strong {
    color: #e74a3b;
    font-weight: 700;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Order #{{ $order->id }}</h1>
       @if($order->status == 'delivered')
    <div>
        <a href="{{ route('customer.orders.invoice.download', $order->id) }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-download me-2"></i> Download Invoice
        </a>
    </div>
@endif
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Order Tracking</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        @foreach($timeline as $step)
                            @php
                                $class = $step['status']; // 'completed', 'active', 'pending', 'active-cancelled'
                            @endphp
                            <li class="timeline-item {{ $class }}">
                                <div class="timeline-icon">
                                    <i class="fas {{ $step['icon'] }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <strong>{{ $step['title'] }}</strong>
                                    @if($step['date'])
                                        <p class="small text-muted mb-0">{{ $step['date']->format('D, d M, Y - h:i A') }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Order Date:</strong> {{ $order->created_at->format('d M, Y') }}
                        </div>
                        <div class="col-md-4 text-md-center">
                            <strong>Status:</strong> <span class="badge bg-{{ $order->status_color }}">{{ ucfirst($order->status) }}</span>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <strong>Total:</strong> ₹{{ number_format($order->total_amount, 2) }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <h5 class="mb-3">Shipping Address</h5>
                            <p class="mb-1"><strong>{{ $order->shipping_address['name'] }}</strong></p>
                            <p class="mb-1">{{ $order->shipping_address['address_line_1'] }}</p>
                            @if(!empty($order->shipping_address['address_line_2']))
                                <p class="mb-1">{{ $order->shipping_address['address_line_2'] }}</p>
                            @endif
                            <p class="mb-1">{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} - {{ $order->shipping_address['pincode'] }}</p>
                            <p class="mb-1">Phone: {{ $order->shipping_address['phone'] }}</p>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3">Order Summary</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Subtotal</span>
                                    <span>₹{{ number_format($order->subtotal, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Shipping</span>
                                    <span>₹{{ number_format($order->shipping_amount, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Tax</span>
                                    <span>₹{{ number_format($order->tax_amount, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 fw-bold">
                                    <span>Total</span>
                                    <span>₹{{ number_format($order->total_amount, 2) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
             <h5 class="mb-0">Items in this Order</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://placehold.co/75x75/EBF4FF/7F9CF5?text={{ urlencode($item->medicine->name) }}" class="img-fluid rounded me-3" style="width: 75px;" alt="{{ $item->medicine->name }}">
                                    <div>
                                        <h6 class="mb-0">{{ $item->medicine->name }}</h6>
                                        <small class="text-muted">{{ $item->medicine->company->name ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($order->canBeCancelled())
                <div class="text-end mt-4">
                    <button class="btn btn-outline-danger" id="cancelOrderBtn" data-id="{{ $order->id }}">Cancel Order</button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#cancelOrderBtn').on('click', function() {
        if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
            const orderId = $(this).data('id');
            
            $.ajax({
                url: '/customer/orders/' + orderId + '/cancel',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Order cancelled successfully.');
                        window.location.reload();
                    } else {
                        alert(response.message || 'Could not cancel the order.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    });
});
</script>
@endpush