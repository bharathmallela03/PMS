@extends('layouts.pharmacist')

@section('title', 'Orders Management')

@push('styles')
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
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
                <div class="flex space-x-3 flex-wrap">
                    <button class="gradient-green text-white px-6 py-3 rounded-lg hover:shadow-lg transition-all duration-200 flex items-center font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New Order
                    </button>
                    <button class="gradient-blue text-white px-6 py-3 rounded-lg hover:shadow-lg transition-all duration-200 flex items-center font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4 4m4-4v12"></path>
                        </svg>
                        Export Data
                    </button>
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
                        <p class="text-3xl font-bold">{{ $orders->total() }}</p>
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
                        <p class="text-green-100 text-sm font-medium">Completed Orders</p>
                        <p class="text-3xl font-bold">{{ $orders->where('status', 'delivered')->count() }}</p>
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
                        <p class="text-3xl font-bold">{{ $orders->where('status', 'pending')->count() }}</p>
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
                        <p class="text-3xl font-bold">{{ $orders->where('created_at', '>=', today())->count() }}</p>
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
                            <th class="text-left py-4 px-6 text-gray-700 font-semibold">Order ID</th>
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
                                <td class="py-4 px-6 text-gray-900 font-semibold">#{{ $order->id }}</td>
                                <td class="py-4 px-6 text-gray-900">{{ $order->customer->name ?? 'N/A' }}</td>
                                <td class="py-4 px-6 text-gray-900 font-semibold">₹{{ number_format($order->total_amount, 2) }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-{{ $order->status_color ?? 'gray' }}-100 text-{{ $order->status_color ?? 'gray' }}-800">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td class="py-4 px-6 text-gray-700">{{ $order->created_at->format('d M, Y') }}</td>
                                <td class="py-4 px-6">
                                    <a href="#" class="text-blue-600 hover:underline">View</a>
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
@endsection
