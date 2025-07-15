@extends('layouts.admin')

@section('title', 'Manage Customers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Manage Customers</h1>
        <p class="text-muted">View and manage all customers in the system</p>
    </div>
    <div>
        <a href="{{ route('register') }}" class="btn btn-info" target="_blank">
            <i class="fas fa-external-link-alt me-2"></i>Customer Registration
        </a>
    </div>
</div>

<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info">Customers List</h6>
    </div>
    <div class="card-body">
        @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>City</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $customer->name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $customer->email ?? 'N/A' }}</td>
                            <td>{{ $customer->contact_number }}</td>
                            <td>{{ $customer->city ?? 'N/A' }}</td>
                            <td>
                                @if($customer->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $customer->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#customerModal{{ $customer->id }}"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger confirm-delete" 
                                            data-url="{{ route('admin.customers.delete', $customer->id) }}"
                                            data-method="DELETE"
                                            data-message="Are you sure you want to delete this customer?"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Customer Details Modal -->
                        <div class="modal fade" id="customerModal{{ $customer->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Customer Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-6"><strong>Name:</strong></div>
                                            <div class="col-6">{{ $customer->name }}</div>
                                            
                                            <div class="col-6"><strong>Email:</strong></div>
                                            <div class="col-6">{{ $customer->email ?? 'N/A' }}</div>
                                            
                                            <div class="col-6"><strong>Contact:</strong></div>
                                            <div class="col-6">{{ $customer->contact_number }}</div>
                                            
                                            <div class="col-6"><strong>Address:</strong></div>
                                            <div class="col-6">{{ $customer->address ?? 'N/A' }}</div>
                                            
                                            <div class="col-6"><strong>City:</strong></div>
                                            <div class="col-6">{{ $customer->city ?? 'N/A' }}</div>
                                            
                                            <div class="col-6"><strong>State:</strong></div>
                                            <div class="col-6">{{ $customer->state ?? 'N/A' }}</div>
                                            
                                            <div class="col-6"><strong>Pincode:</strong></div>
                                            <div class="col-6">{{ $customer->pincode ?? 'N/A' }}</div>
                                            
                                            <div class="col-6"><strong>Country:</strong></div>
                                            <div class="col-6">{{ $customer->country ?? 'N/A' }}</div>
                                            
                                            <div class="col-6"><strong>Joined:</strong></div>
                                            <div class="col-6">{{ $customer->created_at->format('M d, Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $customers->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No customers found</h5>
                <p class="text-muted">Customers will appear here once they register.</p>
                <a href="{{ route('register') }}" class="btn btn-info" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>Open Registration Page
                </a>
            </div>
        @endif
    </div>
</div>
@endsection