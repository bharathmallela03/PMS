@extends('layouts.pharmacist')

@section('title', 'Manage Medicines')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Manage Medicines</h1>
        <p class="text-muted">View and manage your medicine inventory</p>
    </div>
    <div>
        {{-- ## BUTTON ADDED HERE ## --}}
        <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#importModal">
    <i class="fas fa-upload me-2"></i>Import Medicines
</button>
        
        <a href="{{ route('pharmacist.medicines.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Medicine
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('pharmacist.medicines') }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search medicines..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="btn-group w-100" role="group">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-filter"></i>
                        </button>
                        <a href="{{ route('pharmacist.medicines') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Medicines Inventory</h6>
        <div class="btn-group" role="group">
            <a href="{{ route('pharmacist.medicines.export') }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel me-1"></i>Export
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($medicines->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Category</th>
                            <th>Company</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicines as $medicine)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $medicine->photo_url }}" alt="{{ $medicine->name }}" 
                                         class="rounded me-3" width="50" height="50" style="object-fit: cover;">
                                    <div>
                                        <strong>{{ $medicine->name }}</strong><br>
                                        <small class="text-muted">{{ $medicine->brand }}</small><br>
                                        @if($medicine->generic_name)
                                            <small class="text-info">{{ $medicine->generic_name }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $medicine->category }}</span>
                            </td>
                            <td>{{ $medicine->company->name ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-{{ $medicine->quantity <= 10 ? 'danger' : ($medicine->quantity <= 50 ? 'warning' : 'success') }} me-2">
                                        {{ $medicine->quantity }}
                                    </span>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="updateStock({{ $medicine->id }}, 'subtract', 1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm" 
                                                onclick="updateStock({{ $medicine->id }}, 'add', 1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <small class="text-muted">Min: {{ $medicine->minimum_stock ?? 50 }}</small>
                                </div>
                            </td>
                            <td>
                                <strong>₹{{ number_format($medicine->price, 2) }}</strong><br>
                                <small class="text-muted">Cost: ₹{{ number_format($medicine->cost_price, 2) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $medicine->isExpiringSoon() ? 'warning' : ($medicine->isExpired() ? 'danger' : 'success') }}">
                                    {{ $medicine->expiry_date->format('M d, Y') }}
                                </span>
                                @if($medicine->isExpired())
                                    <br><small class="text-danger">Expired</small>
                                @elseif($medicine->isExpiringSoon())
                                    <br><small class="text-warning">Expiring Soon</small>
                                @endif
                            </td>
                            <td>
                                @if($medicine->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                                @if($medicine->isLowStock())
                                    <br><span class="badge bg-warning">Low Stock</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('pharmacist.medicines.edit', $medicine->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#medicineModal{{ $medicine->id }}"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger confirm-delete" 
                                            data-url="{{ route('pharmacist.medicines.delete', $medicine->id) }}"
                                            data-method="DELETE"
                                            data-message="Are you sure you want to delete this medicine?"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $medicines->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-pills fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No medicines found</h5>
                <p class="text-muted">Start building your inventory by adding medicines.</p>
                <a href="{{ route('pharmacist.medicines.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add First Medicine
                </a>
            </div>
        @endif
    </div>
</div>

{{-- ========================================== --}}
{{--         MODAL FOR EXCEL UPLOAD             --}}
{{-- ========================================== --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg animate__animated animate__zoomIn">
        <div class="modal-content">
            <div class="modal-header bg-sidebar">
                <h5 class="modal-title" id="importModalLabel">Excel Upload - Medicine Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pharmacist.medicines.import.store') }}" method="POST" enctype="multipart/form-data" class="dropzone" id="import-dropzone">
                    @csrf
                    <div class="dz-message" data-dz-message>
                        <div class="text-center">
                             <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                             <h4 class="mt-2">Drag and drop a file here or click</h4>
                             <p class="text-muted">(Only .xlsx, .xls, or .csv files are accepted)</p>
                        </div>
                    </div>
                    <div class="fallback">
                       <input name="import_file" type="file" />
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ route('pharmacist.medicines.import.template') }}" class="btn btn-link">
                        <i class="fas fa-download me-1"></i>Download Excel Template
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="upload-button">Save</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateStock(medicineId, action, quantity) {
    if (confirm('Are you sure you want to ' + action + ' ' + quantity + ' unit(s)?')) {
        $.ajax({
            url: `/pharmacist/medicines/${medicineId}/update-stock`,
            type: 'POST',
            data: {
                action: action,
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Update the stock display
                    location.reload();
                    showNotification(response.message, 'success');
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showNotification(response?.message || 'Error updating stock', 'error');
            }
        });
    }
}
</script>
@endpush
@endsection