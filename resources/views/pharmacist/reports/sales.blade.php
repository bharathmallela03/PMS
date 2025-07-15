@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="min-h-screen bg-gray-900 text-white">
    <!-- Header -->
    <div class="bg-gray-800 border-b border-gray-700">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Sales Report</h1>
                    <p class="text-gray-400 mt-1">Comprehensive sales analytics and insights</p>
                </div>
                <div class="flex space-x-3">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4 4m4-4v12"></path>
                        </svg>
                        Export
                    </button>
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="container mx-auto px-6 py-6">
        <div class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700">
            <h2 class="text-xl font-semibold mb-4 text-white">Filter Options</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Date Range</label>
                    <select class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Last 7 days</option>
                        <option>Last 30 days</option>
                        <option>Last 3 months</option>
                        <option>Custom Range</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                    <select class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>All Categories</option>
                        <option>Prescription</option>
                        <option>Over-the-counter</option>
                        <option>Supplements</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>All Status</option>
                        <option>Completed</option>
                        <option>Pending</option>
                        <option>Cancelled</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors duration-200">
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Sales</p>
                        <p class="text-2xl font-bold">${{number_format($totalSales ?? 45250, 2)}}</p>
                        <p class="text-blue-100 text-sm mt-1">+12.5% from last month</p>
                    </div>
                    <div class="bg-blue-500 bg-opacity-30 rounded-full p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Orders</p>
                        <p class="text-2xl font-bold">{{$totalOrders ?? 1248}}</p>
                        <p class="text-green-100 text-sm mt-1">+8.3% from last month</p>
                    </div>
                    <div class="bg-green-500 bg-opacity-30 rounded-full p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Avg. Order Value</p>
                        <p class="text-2xl font-bold">${{number_format($avgOrderValue ?? 36.25, 2)}}</p>
                        <p class="text-purple-100 text-sm mt-1">+3.8% from last month</p>
                    </div>
                    <div class="bg-purple-500 bg-opacity-30 rounded-full p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-orange-600 to-orange-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Top Products</p>
                        <p class="text-2xl font-bold">{{$topProducts ?? 89}}</p>
                        <p class="text-orange-100 text-sm mt-1">Different items sold</p>
                    </div>
                    <div class="bg-orange-500 bg-opacity-30 rounded-full p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-white">Sales Trend</h3>
                <div class="h-64 bg-gray-700 rounded-lg flex items-center justify-center">
                    <p class="text-gray-400">Chart will be rendered here</p>
                </div>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-white">Category Distribution</h3>
                <div class="h-64 bg-gray-700 rounded-lg flex items-center justify-center">
                    <p class="text-gray-400">Pie chart will be rendered here</p>
                </div>
            </div>
        </div>

        <!-- Recent Sales Table -->
        <div class="bg-gray-800 rounded-xl border border-gray-700">
            <div class="p-6 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Recent Sales</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="text-left py-3 px-6 text-gray-300 font-medium">Order ID</th>
                            <th class="text-left py-3 px-6 text-gray-300 font-medium">Customer</th>
                            <th class="text-left py-3 px-6 text-gray-300 font-medium">Product</th>
                            <th class="text-left py-3 px-6 text-gray-300 font-medium">Quantity</th>
                            <th class="text-left py-3 px-6 text-gray-300 font-medium">Amount</th>
                            <th class="text-left py-3 px-6 text-gray-300 font-medium">Status</th>
                            <th class="text-left py-3 px-6 text-gray-300 font-medium">Date</th>
                            <th class="text-left py-3 px-6 text-gray-300 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($recentSales ?? [] as $sale)
                        <tr class="hover:bg-gray-700 transition-colors duration-200">
                            <td class="py-4 px-6 text-white font-medium">#{{$sale->id ?? 'ORD-'.rand(1000,9999)}}</td>
                            <td class="py-4 px-6 text-gray-300">{{$sale->customer_name ?? 'John Doe'}}</td>
                            <td class="py-4 px-6 text-gray-300">{{$sale->product_name ?? 'Aspirin 100mg'}}</td>
                            <td class="py-4 px-6 text-gray-300">{{$sale->quantity ?? rand(1,5)}}</td>
                            <td class="py-4 px-6 text-white font-medium">${{number_format($sale->amount ?? rand(15,150), 2)}}</td>
                            <td class="py-4 px-6">
                                @php
                                    $status = $sale->status ?? 'completed';
                                    $statusClass = $status === 'completed' ? 'bg-green-900 text-green-300' : 
                                                  ($status === 'pending' ? 'bg-yellow-900 text-yellow-300' : 'bg-red-900 text-red-300');
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{$statusClass}}">
                                    {{ucfirst($status)}}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-gray-300">{{$sale->created_at ?? now()->format('M d, Y')}}</td>
                            <td class="py-4 px-6">
                                <div class="flex space-x-2">
                                    <button class="text-blue-400 hover:text-blue-300 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-green-400 hover:text-green-300 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        @for($i = 1; $i <= 10; $i++)
                        <tr class="hover:bg-gray-700 transition-colors duration-200">
                            <td class="py-4 px-6 text-white font-medium">#ORD-{{rand(1000,9999)}}</td>
                            <td class="py-4 px-6 text-gray-300">Customer {{$i}}</td>
                            <td class="py-4 px-6 text-gray-300">Medicine {{$i}}</td>
                            <td class="py-4 px-6 text-gray-300">{{rand(1,5)}}</td>
                            <td class="py-4 px-6 text-white font-medium">${{number_format(rand(15,150), 2)}}</td>
                            <td class="py-4 px-6">
                                @php
                                    $statuses = ['completed', 'pending', 'cancelled'];
                                    $randomStatus = $statuses[array_rand($statuses)];
                                    $statusClass = $randomStatus === 'completed' ? 'bg-green-900 text-green-300' : 
                                                  ($randomStatus === 'pending' ? 'bg-yellow-900 text-yellow-300' : 'bg-red-900 text-red-300');
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{$statusClass}}">
                                    {{ucfirst($randomStatus)}}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-gray-300">{{now()->subDays(rand(1,30))->format('M d, Y')}}</td>
                            <td class="py-4 px-6">
                                <div class="flex space-x-2">
                                    <button class="text-blue-400 hover:text-blue-300 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-green-400 hover:text-green-300 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endfor
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-3 border-t border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-400">
                        Showing 1 to 10 of {{$totalResults ?? 247}} results
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition-colors">
                            Previous
                        </button>
                        <button class="px-3 py-1 bg-blue-600 text-white rounded">1</button>
                        <button class="px-3 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition-colors">2</button>
                        <button class="px-3 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition-colors">3</button>
                        <button class="px-3 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition-colors">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add any JavaScript for charts or interactive elements
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Initialize charts here
        console.log('Sales report loaded');
    });
</script>
@endpush
@endsection