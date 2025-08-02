@extends('layouts.pharmacist')

@section('title', 'Sales Report')

@push('styles')
<style>
    .stat-card {
        background-color: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }
    .stat-card .stat-title {
        font-size: 1rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
    }
    .stat-card .stat-icon {
        font-size: 2.5rem;
        color: #0047FF;
        opacity: 0.2;
        position: absolute;
        right: 1.5rem;
        bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">📊 Sales Report</h1>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pharmacist.reports.sales') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="period" class="form-label">Period</label>
                        <select name="period" id="period" class="form-select">
                            <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filter Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card position-relative">
                <i class="fas fa-rupee-sign stat-icon"></i>
                <div class="stat-title">Total Sales</div>
                <div class="stat-value">&#8377;{{ number_format($data['totalSales'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card position-relative">
                <i class="fas fa-file-invoice stat-icon"></i>
                <div class="stat-title">Total Orders</div>
                <div class="stat-value">{{ $data['totalOrders'] }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card position-relative">
                <i class="fas fa-chart-pie stat-icon"></i>
                <div class="stat-title">Average Order Value</div>
                <div class="stat-value">&#8377;{{ number_format($data['averageOrderValue'], 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Sales Over Time</h5>
        </div>
        <div class="card-body">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Top Selling Medicines -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Top Selling Medicines</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Medicine Name</th>
                            <th class="text-end">Total Sold</th>
                            <th class="text-end">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['topMedicines'] as $medicine)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $medicine->name }}</td>
                            <td class="text-end">{{ $medicine->total_sold }}</td>
                            <td class="text-end">&#8377;{{ number_format($medicine->total_revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">No sales data available for the selected period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js is required for the chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // Prepare data from the controller for the chart
    const salesData = @json($data['salesByPeriod'] ?? []);
    const labels = salesData.map(item => item.period);
    const totals = salesData.map(item => item.total);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales',
                data: totals,
                borderColor: '#0047FF',
                backgroundColor: 'rgba(0, 71, 255, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value;
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush
