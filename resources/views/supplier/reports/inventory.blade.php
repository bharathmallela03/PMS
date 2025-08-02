@extends('layouts.supplier')

@section('title', 'Inventory Report')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Inventory Report</h1>
        <a href="{{ route('supplier.reports.inventory.export') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Medicines Card -->
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Unique Medicines</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $medicines->sum('count') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pills fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Medicine Categories -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Medicines by Category</h6>
                </div>
                <div class="card-body">
                    @if($medicines->isEmpty())
                        <p class="text-center">No inventory data available to generate a report.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Category</th>
                                        <th>Number of Unique Medicines</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($medicines as $category)
                                    <tr>
                                        <td>{{ $category->category }}</td>
                                        <td>{{ $category->count }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
