@extends('layouts.admin')

@section('title', 'Manage Suppliers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Manage Suppliers</h1>
        <p class="text-muted">View and manage all suppliers in the system</p>
    </div>
    <div>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Add New Supplier
        </a>
    </div>
</div>

<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Suppliers List</h6>
    </div>
    <div class="card-body">
        @if($suppliers->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Shop Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>City</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="fas fa-truck text-white"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $supplier->name }}</strong>
                                        @if($supplier->needsPasswordSetup())
                                            <br><small class="text-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Password not set
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $supplier->shop_name }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->contact_number }}</td>
                            <td>{{ $supplier->city }}, {{ $supplier->state }}</td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $supplier->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger confirm-delete" 
                                            data-url="{{ route('admin.suppliers.delete', $supplier->id) }}"
                                            data-method="DELETE"
                                            data-message="Are you sure you want to delete this supplier?"
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
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $suppliers->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No suppliers found</h5>
                <p class="text-muted">Get started by adding your first supplier.</p>
                <a href="{{ route('admin.suppliers.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Add First Supplier
                </a>
            </div>
        @endif
    </div>
</div>
@endsection