@extends('layouts.customer')

@section('title', 'Customer Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Welcome back, {{ auth('customer')->user()->name }}!</h1>
                <p class="text-muted">Here's what's happening with your orders today.</p>
            </div>
            <div>
                <a href="{{ route('customer.medicines') }}" class="btn btn-primary">
                    <i class="fas fa-pills me-2"></i>Browse Medicines
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Orders
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_orders }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-bag fa-2x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Orders
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pending_orders }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Delivered Orders
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $delivered_orders }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Spent
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($total_spent, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-rupee-sign fa-2x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recent_orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_orders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $order->items->count() }} item(s)</small>
                                    </td>
                                    <td>
                                        <strong>₹{{ number_format($order->total_amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No orders yet</h5>
                        <p class="text-muted">Start shopping to see your orders here.</p>
                        <a href="{{ route('customer.medicines') }}" class="btn btn-primary">
                            <i class="fas fa-pills me-2"></i>Browse Medicines
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recommended Medicines -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recommended for You</h6>
            </div>
            <div class="card-body">
                @if($recommended_medicines->count() > 0)
                    @foreach($recommended_medicines as $medicine)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="flex-shrink-0">
                            <img src="{{ $medicine->photo_url }}" alt="{{ $medicine->name }}" 
                                 class="rounded" width="50" height="50" style="object-fit: cover;">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">{{ $medicine->name }}</h6>
                            <p class="mb-1 text-muted small">{{ $medicine->brand }}</p>
                            <strong class="text-primary">₹{{ number_format($medicine->price, 2) }}</strong>
                        </div>
                        <div class="flex-shrink-0">
                            <button class="btn btn-sm btn-outline-primary" onclick="addToCart({{ $medicine->id }})">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-pills fa-2x text-muted mb-2"></i>
                        <p class="text-muted small mb-0">No recommendations available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('customer.medicines') }}" class="btn btn-outline-primary btn-block h-100 d-flex flex-column justify-content-center align-items-center py-3">
                            <i class="fas fa-pills fa-2x mb-2"></i>
                            <span>Browse Medicines</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('customer.cart') }}" class="btn btn-outline-success btn-block h-100 d-flex flex-column justify-content-center align-items-center py-3">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <span>View Cart</span>
                            @if($cart_items > 0)
                                <small class="badge bg-danger">{{ $cart_items }}</small>
                            @endif
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('customer.orders') }}" class="btn btn-outline-info btn-block h-100 d-flex flex-column justify-content-center align-items-center py-3">
                            <i class="fas fa-list-alt fa-2x mb-2"></i>
                            <span>My Orders</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('customer.profile') }}" class="btn btn-outline-warning btn-block h-100 d-flex flex-column justify-content-center align-items-center py-3">
                            <i class="fas fa-user-cog fa-2x mb-2"></i>
                            <span>My Profile</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function addToCart(medicineId) {
    // Add to cart functionality
    addToCart(medicineId, 1);
}

// Update cart count on page load
$(document).ready(function() {
    updateCartCount({{ $cart_items }});
});
</script>
@endpush
@endsection