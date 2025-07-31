@extends('layouts.pharmacist')

@section('title', 'Orders Management')

@push('styles')
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        .gradient-blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .gradient-green { background: linear-gradient(135deg, #10b981, #047857); }
        .gradient-yellow { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .gradient-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .hover-shadow:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.3s ease;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 card-shadow">
        <div class="container mx-auto px-6 py-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📋 Orders Management</h1>
                    <p class="text-gray-600 mt-2">Manage pharmacy orders and prescriptions efficiently</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 fade-in">
            <div class="gradient-blue rounded-xl p-6 text-white card-shadow hover-shadow transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Orders</p>
                        <p class="text-3xl font-bold">{{ $totalOrders ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-500 bg-opacity-30 rounded-full p-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-green rounded-xl p-6 text-white card-shadow hover-shadow transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Delivered Orders</p>
                        <p class="text-3xl font-bold">{{ $deliveredOrders ?? 0 }}</p>
                    </div>
                    <div class="bg-green-500 bg-opacity-30 rounded-full p-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-yellow rounded-xl p-6 text-white card-shadow hover-shadow transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-medium">Pending Orders</p>
                        <p class="text-3xl font-bold">{{ $pendingOrders ?? 0 }}</p>
                    </div>
                    <div class="bg-yellow-500 bg-opacity-30 rounded-full p-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-purple rounded-xl p-6 text-white card-shadow hover-shadow transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Today's Orders</p>
                        <p class="text-3xl font-bold">{{ $todayOrders ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-500 bg-opacity-30 rounded-full p-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl border border-gray-200 card-shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">All Orders</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-4 px-6 text-gray-700 font-semibold">Order #</th>
                            <th class="text-left py-4 px-6 text-gray-700 font-semibold">Customer</th>
                            <th class="text-left py-4 px-6 text-gray-700 font-semibold">Total</th>
                            <th class="text-left py-4 px-6 text-gray-700 font-semibold">Status</th>
                            <th class="text-left py-4 px-6 text-gray-700 font-semibold">Date</th>
                            <th class="text-left py-4 px-6 text-gray-700 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="py-4 px-6 text-gray-900 font-semibold">{{ $order->order_number }}</td>
                                <td class="py-4 px-6 text-gray-900">{{ $order->customer->name ?? 'N/A' }}</td>
                                <td class="py-4 px-6 text-gray-900 font-semibold">₹{{ number_format($order->total_amount, 2) }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-{{ $order->status_color ?? 'gray' }}-100 text-{{ $order->status_color ?? 'gray' }}-800">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td class="py-4 px-6 text-gray-700">{{ $order->created_at->format('d M, Y') }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('pharmacist.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-lg transition-all" title="View Order">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="text-green-600 hover:text-green-800 p-2 hover:bg-green-50 rounded-lg transition-all update-status-btn" title="Update Status" data-order-id="{{ $order->id }}" data-current-status="{{ $order->status }}">
                                             <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ route('pharmacist.billing.invoice.download', $order->id) }}" class="text-purple-600 hover:text-purple-800 p-2 hover:bg-purple-50 rounded-lg transition-all" title="Print Invoice">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-gray-500">
                                    <i class="fas fa-box-open fa-3x mb-3"></i>
                                    <p>No orders found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Update Order Status</h3>
            <div class="mt-2 px-7 py-3">
                <form id="updateStatusForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="orderId" name="order_id">
                    <select id="statusSelect" name="status" class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </form>
            </div>
            <div class="items-center px-4 py-3">
                <button id="submitStatusUpdate" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                    Update
                </button>
                 <button id="closeModal" class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const modal = $('#updateStatusModal');

    // Open the modal
    $('.update-status-btn').on('click', function() {
        const orderId = $(this).data('order-id');
        const currentStatus = $(this).data('current-status');
        
        modal.find('#orderId').val(orderId);
        modal.find('#statusSelect').val(currentStatus);
        modal.find('form').attr('action', `/pharmacist/orders/${orderId}/status`);
        
        modal.removeClass('hidden');
    });

    // Close the modal
    $('#closeModal').on('click', function() {
        modal.addClass('hidden');
    });

    // Handle form submission via the Update button
    $('#submitStatusUpdate').on('click', function() {
        const form = $('#updateStatusForm');
        const url = form.attr('action');

        $.ajax({
            url: url,
            type: 'POST', // Use POST to tunnel PUT request
            data: form.serialize(), // Sends form data including _method
            success: function(response) {
                if (response.success) {
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
