@extends('layouts.admin')

@section('title', 'Manage Pharmacists')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Manage Pharmacists</h1>
        <p class="text-muted">View and manage all pharmacists in the system</p>
    </div>
    <div>
        <a href="{{ route('admin.pharmacists.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Pharmacist
        </a>
    </div>
</div>

<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Pharmacists List</h6>
    </div>
    <div class="card-body">
        @if($pharmacists->count() > 0)
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
                        @foreach($pharmacists as $pharmacist)
                        <tr>
                            <td>{{ $pharmacist->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="fas fa-user-md text-white"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $pharmacist->name }}</strong>
                                        @if($pharmacist->needsPasswordSetup())
                                            <br><small class="text-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Password not set
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $pharmacist->shop_name }}</td>
                            <td>{{ $pharmacist->email }}</td>
                            <td>{{ $pharmacist->contact_number }}</td>
                            <td>{{ $pharmacist->city }}, {{ $pharmacist->state }}</td>
                            <td>
                                @if($pharmacist->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $pharmacist->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.pharmacists.edit', $pharmacist->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger confirm-delete" 
                                            data-url="{{ route('admin.pharmacists.delete', $pharmacist->id) }}"
                                            data-method="DELETE"
                                            data-message="Are you sure you want to delete this pharmacist?"
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
                {{ $pharmacists->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No pharmacists found</h5>
                <p class="text-muted">Get started by adding your first pharmacist.</p>
                <a href="{{ route('admin.pharmacists.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add First Pharmacist
                </a>
            </div>
        @endif
    </div>
</div>
@endsection