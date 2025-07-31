@extends('layouts.customer')

@section('title', 'My Orders')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">My Orders</h1>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Your Order History</h5>
            <form action="{{ route('customer.orders') }}" method="GET">
                <div class="input-group">
                    <select name="status" class="form-select" onchange="this.form.submit()">
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
        <div class="card-body p-0">
            @if($orders->isEmpty())
                <div class="text-center p-5">
                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No orders found.</h4>
                    <p>You haven't placed any orders with us yet.</p>
                    <a href="{{ route('customer.medicines') }}" class="btn btn-primary mt-2">Start Shopping</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Order ID</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Total</th>
                                <th scope="col">Items</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->created_at->format('d M, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>₹{{ number_format($order->total_amount, 2) }}</td>
                                    <td>{{ $order->items->count() }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($orders->hasPages())
            <div class="card-footer bg-white">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
