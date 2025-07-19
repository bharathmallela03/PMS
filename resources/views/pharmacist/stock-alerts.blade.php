@extends('layouts.pharmacist')

@section('title', 'Stock Alerts - Pharmacy Management')
@section('page-title', 'Stock Alerts')

@section('content')
@stack('styles')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-white mb-2">Stock Alerts</h2>
            <p class="text-gray-400">Monitor inventory levels and low stock warnings</p>
        </div>
        <div class="flex space-x-3">
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Alert
            </button>
            <button onclick="refreshStockData()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
            <button onclick="openBulkOrderModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Bulk Order
            </button>
        </div>
    </div>
</div>

<!-- Alert Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-xl p-6 text-white card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm font-medium">Critical Alerts</p>
                <p class="text-2xl font-bold">{{$criticalAlerts ?? 12}}</p>
                <p class="text-red-100 text-sm mt-1">Requires immediate action</p>
            </div>
            <div class="bg-red-500 bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 rounded-xl p-6 text-white card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm font-medium">Low Stock</p>
                <p class="text-2xl font-bold">{{$lowStockItems ?? 38}}</p>
                <p class="text-yellow-100 text-sm mt-1">Below minimum threshold</p>
            </div>
            <div class="bg-yellow-500 bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Out of Stock</p>
                <p class="text-2xl font-bold">{{$outOfStockItems ?? 7}}</p>
                <p class="text-blue-100 text-sm mt-1">Zero inventory</p>
            </div>
            <div class="bg-blue-500 bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl p-6 text-white card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Well Stocked</p>
                <p class="text-2xl font-bold">{{$wellStockedItems ?? 342}}</p>
                <p class="text-green-100 text-sm mt-1">Above minimum levels</p>
            </div>
            <div class="bg-green-500 bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap gap-4">
            <div class="min-w-0 flex-1 md:flex-none md:w-64">
                <input type="text" id="medicineSearch" placeholder="Search medicines..." 
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <select id="categoryFilter" class="bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                <option value="prescription">Prescription</option>
                <option value="otc">Over-the-counter</option>
                <option value="supplements">Supplements</option>
                <option value="emergency">Emergency</option>
            </select>
            <select id="alertLevelFilter" class="bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Alert Levels</option>
                <option value="critical">Critical</option>
                <option value="low">Low Stock</option>
                <option value="out">Out of Stock</option>
            </select>
        </div>
        <button onclick="applyFilters()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
            Apply Filters
        </button>
    </div>
</div>

<!-- Stock Alerts Table -->
<div class="bg-gray-800 rounded-xl border border-gray-700">
    <div class="p-6 border-b border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Stock Alerts</h3>
            <div class="flex items-center space-x-4">
                <label class="flex items-center">
                    <input type="checkbox" id="criticalOnlyToggle" class="rounded bg-gray-600 border-gray-500 text-red-600 focus:ring-red-500">
                    <span class="ml-2 text-sm text-gray-300">Show critical only</span>
                </label>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full" id="stockAlertsTable">
            <thead class="bg-gray-700">
                <tr>
                    <th class="text-left py-3 px-6 text-gray-300 font-medium">
                        <input type="checkbox" id="selectAllAlerts" class="rounded bg-gray-600 border-gray-500 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="text-left py-3 px-6 text-gray-300 font-medium">Medicine</th>
                    <th class="text-left py-3 px-6 text-gray-300 font-medium">Category</th>
                    <th class="text-left py-3 px-6 text-gray-300 font-medium">Current Stock</th>
                    <th class="text-left py-3 px-6 text-gray-300 font-medium">Min. Threshold</th>
                    <th class="text-left py-3 px-6 text-gray-300 font-medium">Alert Level</th>
                    <th class="text-left py-3 px-6 text-gray-300 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700" id="alertsTableBody">
                <!-- Sample Data -->
                <tr class="hover:bg-gray-700 transition-colors duration-200 alert-row" data-category="prescription" data-level="critical" data-name="Aspirin 100mg">
                    <td class="py-4 px-6">
                        <input type="checkbox" class="alert-checkbox rounded bg-gray-600 border-gray-500 text-blue-600 focus:ring-blue-500" value="1">
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 8.172V5L8 4z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-medium">Aspirin 100mg</div>
                                <div class="text-gray-400 text-sm">SKU: ASP-100-001</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-900 text-blue-300">
                            Prescription
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-white font-medium">5 units</div>
                        <div class="text-gray-400 text-sm">Exp: Dec 2025</div>
                    </td>
                    <td class="py-4 px-6 text-gray-300">20 units</td>
                    <td class="py-4 px-6">
                        <div class="flex items-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-900 text-red-300 mr-2">
                                Critical
                            </span>
                            <div class="w-16 bg-gray-700 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full" style="width: 25%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex space-x-2">
                            <button onclick="reorderMedicine('1', 'Aspirin 100mg')" class="text-green-400 hover:text-green-300 transition-colors" title="Reorder">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </button>
                            <button onclick="editAlert('1')" class="text-blue-400 hover:text-blue-300 transition-colors" title="Edit Alert">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="dismissAlert('1')" class="text-gray-400 hover:text-gray-300 transition-colors" title="Dismiss">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-700 transition-colors duration-200 alert-row" data-category="supplements" data-level="low" data-name="Vitamin D3 1000IU">
                    <td class="py-4 px-6">
                        <input type="checkbox" class="alert-checkbox rounded bg-gray-600 border-gray-500 text-blue-600 focus:ring-blue-500" value="2">
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 8.172V5L8 4z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-medium">Vitamin D3 1000IU</div>
                                <div class="text-gray-400 text-sm">SKU: VIT-D3-004</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-900 text-purple-300">
                            Supplements
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-white font-medium">8 units</div>
                        <div class="text-gray-400 text-sm">Exp: Sep 2025</div>
                    </td>
                    <td class="py-4 px-6 text-gray-300">20 units</td>
                    <td class="py-4 px-6">
                        <div class="flex items-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-900 text-yellow-300 mr-2">
                                Low Stock
                            </span>
                            <div class="w-16 bg-gray-700 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: 40%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex space-x-2">
                            <button onclick="reorderMedicine('2', 'Vitamin D3 1000IU')" class="text-green-400 hover:text-green-300 transition-colors" title="Reorder">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </button>
                            <button onclick="editAlert('2')" class="text-blue-400 hover:text-blue-300 transition-colors" title="Edit Alert">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="dismissAlert('2')" class="text-gray-400 hover:text-gray-300 transition-colors" title="Dismiss">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-700 transition-colors duration-200 alert-row" data-category="otc" data-level="out" data-name="Paracetamol 500mg">
                    <td class="py-4 px-6">
                        <input type="checkbox" class="alert-checkbox rounded bg-gray-600 border-gray-500 text-blue-600 focus:ring-blue-500" value="3">
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 8.172V5L8 4z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-medium">Paracetamol 500mg</div>
                                <div class="text-gray-400 text-sm">SKU: PAR-500-003</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-900 text-green-300">
                            OTC
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-white font-medium">0 units</div>
                        <div class="text-gray-400 text-sm">Last sold: Jul 18</div>
                    </td>
                    <td class="py-4 px-6 text-gray-300">25 units</td>
                    <td class="py-4 px-6">
                        <div class="flex items-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-900 text-blue-300 mr-2">
                                Out of Stock
                            </span>
                            <div class="w-16 bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex space-x-2">
                            <button onclick="reorderMedicine('3', 'Paracetamol 500mg')" class="text-green-400 hover:text-green-300 transition-colors" title="Reorder">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </button>
                            <button onclick="editAlert('3')" class="text-blue-400 hover:text-blue-300 transition-colors" title="Edit Alert">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="dismissAlert('3')" class="text-gray-400 hover:text-gray-300 transition-colors" title="Dismiss">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Bulk Actions Panel -->
<div class="bg-gray-800 rounded-xl p-6 mt-6 border border-gray-700 opacity-50" id="bulkActionsPanel">
    <h3 class="text-lg font-semibold text-white mb-4">Bulk Actions</h3>
    <div class="flex flex-wrap gap-4">
        <button onclick="bulkReorder()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            Reorder Selected Items
        </button>
        <button onclick="exportReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4 4m4-4v12"></path>
            </svg>
            Export Report
        </button>
    </div>
    <div class="mt-4 text-sm text-gray-400">
        <span id="selectedCount">0</span> items selected
    </div>
</div>

<!-- Reorder Modal -->
<div id="reorderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4 border border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Quick Reorder</h3>
            <button onclick="closeReorderModal()" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Medicine</label>
                <input type="text" id="reorderMedicineName" readonly class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Quantity to Order</label>
                <input type="number" id="reorderQuantity" value="50" min="1" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Supplier</label>
                <select id="reorderSupplier" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white">
                    <option value="1">MedSupply Co.</option>
                    <option value="2">PharmaDirect</option>
                    <option value="3">HealthPlus Distributors</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="closeReorderModal()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button onclick="submitReorder()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                    Place Order
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Stock alerts page loaded successfully!');
    initializeStockAlerts();
    updateBulkActionsVisibility();
    
    setTimeout(() => {
        if (window.pharmacyUtils) {
            window.pharmacyUtils.showNotification('Stock alerts monitoring is active', 'info');
        }
    }, 1500);
});

function initializeStockAlerts() {
    // Search functionality
    const searchInput = document.getElementById('medicineSearch');
    if (searchInput) {
        searchInput.addEventListener('input', filterAlerts);
    }

    // Filter change handlers
    ['categoryFilter', 'alertLevelFilter'].forEach(filterId => {
        const filter = document.getElementById(filterId);
        if (filter) {
            filter.addEventListener('change', filterAlerts);
        }
    });

    // Critical only toggle
    const criticalToggle = document.getElementById('criticalOnlyToggle');
    if (criticalToggle) {
        criticalToggle.addEventListener('change', filterAlerts);
    }

    // Select all functionality
    const selectAll = document.getElementById('selectAllAlerts');
    const checkboxes = document.querySelectorAll('.alert-checkbox');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionsVisibility();
        });
    }

    // Individual checkbox handling
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.alert-checkbox:checked');
            selectAll.checked = checkedBoxes.length === checkboxes.length;
            selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < checkboxes.length;
            updateBulkActionsVisibility();
        });
    });
}

function filterAlerts() {
    const searchTerm = document.getElementById('medicineSearch').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const alertLevelFilter = document.getElementById('alertLevelFilter').value;
    const criticalOnly = document.getElementById('criticalOnlyToggle').checked;
    
    const rows = document.querySelectorAll('.alert-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const name = row.dataset.name.toLowerCase();
        const category = row.dataset.category;
        const level = row.dataset.level;
        
        let show = true;
        
        if (searchTerm && !name.includes(searchTerm)) {
            show = false;
        }
        
        if (categoryFilter && category !== categoryFilter) {
            show = false;
        }
        
        if (alertLevelFilter && level !== alertLevelFilter) {
            show = false;
        }
        
        if (criticalOnly && level !== 'critical') {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
}

function updateBulkActionsVisibility() {
    const checkedBoxes = document.querySelectorAll('.alert-checkbox:checked');
    const panel = document.getElementById('bulkActionsPanel');
    const selectedCount = document.getElementById('selectedCount');
    
    if (checkedBoxes.length > 0) {
        panel.classList.remove('opacity-50');
        panel.classList.add('ring-2', 'ring-blue-500');
        selectedCount.textContent = checkedBoxes.length;
    } else {
        panel.classList.add('opacity-50');
        panel.classList.remove('ring-2', 'ring-blue-500');
        selectedCount.textContent = '0';
    }
}

function applyFilters() {
    filterAlerts();
    if (window.pharmacyUtils) {
        window.pharmacyUtils.showNotification('Filters applied successfully', 'success');
    }
}

function refreshStockData() {
    if (window.pharmacyUtils) {
        window.pharmacyUtils.showNotification('Refreshing stock data...', 'info');
    }
    
    setTimeout(() => {
        if (window.pharmacyUtils) {
            window.pharmacyUtils.showNotification('Stock data refreshed successfully', 'success');
        }
        location.reload();
    }, 2000);
}

function reorderMedicine(alertId, medicineName) {
    document.getElementById('reorderMedicineName').value = medicineName;
    document.getElementById('reorderModal').classList.remove('hidden');
    document.getElementById('reorderModal').classList.add('flex');
    document.getElementById('reorderModal').dataset.alertId = alertId;
}

function closeReorderModal() {
    document.getElementById('reorderModal').classList.add('hidden');
    document.getElementById('reorderModal').classList.remove('flex');
}

function submitReorder() {
    const alertId = document.getElementById('reorderModal').dataset.alertId;
    const medicineName = document.getElementById('reorderMedicineName').value;
    const quantity = document.getElementById('reorderQuantity').value;
    const supplier = document.getElementById('reorderSupplier').value;
    
    if (!quantity || quantity < 1) {
        if (window.pharmacyUtils) {
            window.pharmacyUtils.showNotification('Please enter a valid quantity', 'error');
        }
        return;
    }
    
    if (window.pharmacyUtils) {
        window.pharmacyUtils.showNotification('Processing reorder...', 'info');
    }
    
    setTimeout(() => {
        closeReorderModal();
        if (window.pharmacyUtils) {
            window.pharmacyUtils.showNotification(`Reorder placed for ${medicineName} (${quantity} units)`, 'success');
        }
    }, 1000);
}

function editAlert(alertId) {
    if (window.pharmacyUtils) {
        window.pharmacyUtils.showNotification('Edit alert functionality would open here', 'info');
    }
}

function dismissAlert(alertId) {
    if (confirm('Are you sure you want to dismiss this alert?')) {
        if (window.pharmacyUtils) {
            window.pharmacyUtils.showNotification('Alert dismissed successfully', 'success');
        }
        
        const row = document.querySelector(`input[value="${alertId}"]`).closest('tr');
        if (row) {
            row.remove();
        }
    }
}

function bulkReorder() {
    const checkedBoxes = document.querySelectorAll('.alert-checkbox:checked');
    if (checkedBoxes.length === 0) {
        if (window.pharmacyUtils) {
            window.pharmacyUtils.showNotification('Please select items to reorder', 'warning');
        }
        return;
    }
    
    if (window.pharmacyUtils) {
        window.pharmacyUtils.showNotification(`Bulk reorder initiated for ${checkedBoxes.length} items`, 'success');
    }
}

function openBulkOrderModal() {
    if (window.pharmacyUtils) {
        window.pharmacyUtils.showNotification('Bulk order modal would open here', 'info');
    }
}

function exportReport() {
    if (window.pharmacyUtils) {
        window.pharmacyUtils.showNotification('Generating export report...', 'info');
    }
    
    setTimeout(() => {
        const blob = new Blob(['Stock Alerts Report - Generated on ' + new Date().toISOString()], 
                              { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'stock-alerts-report.csv';
        a.click();
        window.URL.revokeObjectURL(url);
        
        if (window.pharmacyUtils) {
            window.pharmacyUtils.showNotification('Report exported successfully', 'success');
        }
    }, 1000);
}
</script>
@endpush

@push('styles')
<style>
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #374151;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #6B7280;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #9CA3AF;
}

.w-16 > div {
    transition: width 0.5s ease-in-out;
}

tbody tr {
    transition: all 0.2s ease-in-out;
}

tbody tr:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

#reorderModal {
    backdrop-filter: blur(4px);
}

#reorderModal > div {
    animation: modalSlide 0.3s ease-out;
}

@keyframes modalSlide {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bg-red-900 {
    animation: criticalPulse 2s infinite;
}

@keyframes criticalPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.card-hover {
    transition: all 0.3s ease-in-out;
}

.card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

tr:has(.alert-checkbox:checked) {
    background-color: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
}

.alert-row {
    transition: opacity 0.3s ease-in-out;
}
</style>
@endpush