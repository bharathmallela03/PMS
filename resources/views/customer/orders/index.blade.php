@extends('layouts.customer')

@section('title', 'My Orders')

@push('styles')
<style>
    .order-card {
        transition: all 0.2s ease-in-out;
        border-left: 4px solid #4e73df;
    }
    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    .order-item-images {
        display: flex;
    }
    .order-item-images .item-image {
        height: 45px;
        width: 45px;
        object-fit: cover;
        border: 2px solid #fff;
        border-radius: 50%;
        margin-left: -15px;
        background-color: #f8f9fc;
    }
    .order-item-images .item-image:first-child {
        margin-left: 0;
    }
    .order-item-images .more-items {
        height: 45px;
        width: 45px;
        border-radius: 50%;
        margin-left: -15px;
        background-color: #e9ecef;
        color: #495057;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        border: 2px solid #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Orders</h1>
        <form action="{{ route('customer.orders') }}" method="GET" class="d-none d-md-block">
            <div class="input-group">
                <label class="input-group-text" for="statusFilter"><i class="fas fa-filter"></i></label>
                <select name="status" id="statusFilter" class="form-select" onchange="this.form.submit()" style="width: 180px;">
                    <option value="">All Statuses</option>
                    <option value="pending" @if(request('status') == 'pending') selected @endif>Pending</option>
                    <option value="processing" @if(request('status') == 'processing') selected @endif>Processing</option>
                    <option value="shipped" @if(request('status') == 'shipped') selected @endif>Shipped</option>
                    <option value="delivered" @if(request('status') == 'delivered') selected @endif>Delivered</option>
                    <option value="cancelled" @if(request('status') == 'cancelled') selected @endif>Cancelled</option>
                </select>
            </div>
        </form>
    </div>

    @if($orders->isEmpty())
        <div class="text-center p-5 mt-5">
            <i class="fas fa-shopping-basket fa-4x text-gray-400 mb-3"></i>
            <h4 class="text-gray-700">No orders to show.</h4>
            <p class="text-muted">It looks like you haven't placed any orders matching this status.</p>
            <a href="{{ route('customer.medicines') }}" class="btn btn-primary mt-2">
                <i class="fas fa-pills me-2"></i>Browse Medicines
            </a>
        </div>
    @else
        <div class="list-group">
            @foreach($orders as $order)
                <a href="{{ route('customer.orders.show', $order->id) }}" class="list-group-item list-group-item-action order-card shadow-sm mb-3 rounded-3 p-3">
                    <div class="row align-items-center g-3">
                        <!-- Order Info -->
                        <div class="col-md-4 col-lg-3">
                            <h6 class="mb-1 text-primary">Order #{{ $order->id }}</h6>
                            <small class="text-muted">{{ $order->created_at->format('D, d M Y') }}</small>
                        </div>

                        <!-- Item Images -->
                        <div class="col-md-3 col-lg-4 d-none d-md-block">
                            <div class="order-item-images">
                                @foreach($order->items->take(4) as $item)
                                    <img src="https://placehold.co/75x75/EBF4FF/7F9CF5?text={{ urlencode($item->medicine->name) }}" 
                                         alt="{{ $item->medicine->name }}" 
                                         class="item-image"
                                         title="{{ $item->medicine->name }}">
                                @endforeach
                                @if($order->items->count() > 4)
                                    <div class="more-items" title="{{ $order->items->count() - 4 }} more items">
                                        +{{ $order->items->count() - 4 }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-6 col-md-2 text-center">
                            <span class="badge bg-{{ $order->status_color ?? 'secondary' }} fs-6">{{ ucfirst($order->status) }}</span>
                        </div>

                        <!-- Total and Action -->
                        <div class="col-6 col-md-3 col-lg-3 text-end">
                            <h5 class="mb-1">₹{{ number_format($order->total_amount, 2) }}</h5>
                            <span class="text-primary d-none d-lg-inline">View Details <i class="fas fa-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($orders->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    @endif
</div>
@endsection