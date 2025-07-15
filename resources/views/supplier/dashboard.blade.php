@extends('layouts.supplier')

@section('title', 'Supplier Dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Supplier Dashboard</h1>
            <p class="text-muted">Welcome back, {{ auth('supplier')->user()->name }}!</p>
        </div>
        <div>
            <span class="badge bg-light text-dark">{{ now()->format('F d, Y') }}</span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Medicines
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_medicines ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pills fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Medicines
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $active_medicines ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pending_requests ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Low Stock Items
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $low_stock_count ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('supplier.medicines.create') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-plus fa-fw"></i> Add Medicine
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('supplier.medicines.bulk-upload') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-upload fa-fw"></i> Bulk Upload
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('supplier.medicines') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-list fa-fw"></i> View Medicines
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('supplier.reports.inventory') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-chart-bar fa-fw"></i> Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Stock Requests</h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No recent stock requests. Check back later!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection