@extends('layouts.pharmacist')

@section('title', 'Order Details - #' . $order->order_number)

@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@section('content')
<div class="container mx-auto px-6 py-8 bg-gray-50">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
            <p class="text-gray-600 mt-2">Order #<span class="font-semibold">{{ $order->order_number }}</span></p>
        </div>
        <a href="{{ route('pharmacist.orders') }}" class="text-blue-600 hover:underline flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Orders
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-md overflow-hidden">
        <!-- Order Summary Header -->
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Order Date</p>
                    <p class="font-semibold text-gray-800">{{ $order->created_at->format('d M, Y - h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Order Status</p>
                    <p class="font-semibold text-gray-800">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-{{ $order->status_color ?? 'gray' }}-100 text-{{ $order->status_color ?? 'gray' }}-800">{{ ucfirst($order->status) }}</span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Amount</p>
                    <p class="font-semibold text-gray-800">₹{{ number_format($order->total_amount, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Customer & Shipping Details -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer & Shipping</h3>
                    <div class="space-y-3 text-gray-700">
                        <p><strong>Customer:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                        <p><strong>Contact:</strong> {{ $order->customer->contact_number ?? 'N/A' }}</p>
                        <div class="pt-2">
                            <p class="font-semibold">Shipping Address:</p>
                            <address class="not-italic border-l-4 border-gray-200 pl-4 mt-1">
                                {{ $order->shipping_address['name'] }}<br>
                                {{ $order->shipping_address['address_line_1'] }}<br>
                                @if(!empty($order->shipping_address['address_line_2']))
                                    {{ $order->shipping_address['address_line_2'] }}<br>
                                @endif
                                {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} - {{ $order->shipping_address['pincode'] }}<br>
                                Phone: {{ $order->shipping_address['phone'] }}
                            </address>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment & Billing</h3>
                    <div class="space-y-3 text-gray-700">
                        <p><strong>Payment Method:</strong> <span class="uppercase">{{ $order->payment_method }}</span></p>
                        <p><strong>Payment Status:</strong> <span class="capitalize">{{ $order->payment_status }}</span></p>
                         <div class="pt-2">
                            <p class="font-semibold">Billing Address:</p>
                            <address class="not-italic border-l-4 border-gray-200 pl-4 mt-1">
                                {{-- Assuming billing is same as shipping for this example --}}
                                {{ $order->billing_address['name'] }}<br>
                                {{ $order->billing_address['address_line_1'] }}<br>
                                {{ $order->billing_address['city'] }}, {{ $order->billing_address['pincode'] }}
                            </address>
                        </div>
                    </div>
                    <!-- Update Status Form -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-800 mb-2">Update Order Status</h4>
                        <form id="updateStatusForm">
                            @csrf
                            @method('PUT')
                            <div class="flex items-center space-x-4">
                                <select name="status" id="statusSelect" class="block w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300 font-medium whitespace-nowrap">
                                    Save Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Items Ordered -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Items Ordered</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-3 px-4 text-gray-600 font-semibold">Product</th>
                                <th class="text-center py-3 px-4 text-gray-600 font-semibold">Quantity</th>
                                <th class="text-right py-3 px-4 text-gray-600 font-semibold">Price</th>
                                <th class="text-right py-3 px-4 text-gray-600 font-semibold">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="py-3 px-4">{{ $item->medicine->name ?? 'Medicine not found' }}</td>
                                <td class="py-3 px-4 text-center">{{ $item->quantity }}</td>
                                <td class="py-3 px-4 text-right">₹{{ number_format($item->price, 2) }}</td>
                                <td class="py-3 px-4 text-right">₹{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right py-3 px-4 font-semibold text-gray-700">Subtotal</td>
                                <td class="text-right py-3 px-4">₹{{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right py-3 px-4 font-semibold text-gray-700">Shipping</td>
                                <td class="text-right py-3 px-4">₹{{ number_format($order->shipping_amount, 2) }}</td>
                            </tr>
                             <tr>
                                <td colspan="3" class="text-right py-3 px-4 font-semibold text-gray-700">Tax</td>
                                <td class="text-right py-3 px-4">₹{{ number_format($order->tax_amount, 2) }}</td>
                            </tr>
                            <tr class="font-bold text-lg text-gray-900 bg-gray-50">
                                <td colspan="3" class="text-right py-4 px-4">Grand Total</td>
                                <td class="text-right py-4 px-4">₹{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const url = `/pharmacist/orders/{{ $order->id }}/status`;
        
        $.ajax({
            url: url,
            type: 'POST', // Use POST for AJAX to allow Laravel's method spoofing
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    alert('Order status updated successfully!');
                    window.location.reload();
                } else {
                    alert(response.message || 'An error occurred.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});
</script>
@endpush
