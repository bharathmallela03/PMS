@extends('layouts.customer')

@section('title', 'My Addresses')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Addresses</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addressModal">
            <i class="fas fa-plus me-2"></i> Add New Address
        </button>
    </div>

    @if($addresses->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                <h4 class="card-title">No addresses found</h4>
                <p class="card-text text-muted">You haven't saved any addresses yet.</p>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($addresses as $address)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">{{ ucfirst($address->type) }}</h5>
                            @if($address->is_default)
                                <span class="badge bg-success">Default</span>
                            @endif
                        </div>
                        <p class="card-text">
                            <strong>{{ $address->name }}</strong><br>
                            {{ $address->address_line_1 }}<br>
                            @if($address->address_line_2)
                                {{ $address->address_line_2 }}<br>
                            @endif
                            {{ $address->city }}, {{ $address->state }} - {{ $address->pincode }}<br>
                            Phone: {{ $address->phone }}
                        </p>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <button class="btn btn-sm btn-outline-secondary edit-address" data-bs-toggle="modal" data-bs-target="#addressModal" data-address='{{ $address->toJson() }}'>Edit</button>
                        <button class="btn btn-sm btn-outline-danger delete-address" data-id="{{ $address->id }}">Delete</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Add/Edit Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addressModalLabel">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addressForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="addressId" name="address_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modal_type" class="form-label">Address Type</label>
                            <select class="form-select" id="modal_type" name="type" required>
                                <option value="home">Home</option>
                                <option value="office">Office</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                         <div class="col-md-6">
                            <label for="modal_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="modal_name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="modal_phone" name="phone" required>
                        </div>
                        <div class="col-12">
                            <label for="modal_address_line_1" class="form-label">Address Line 1</label>
                            <input type="text" class="form-control" id="modal_address_line_1" name="address_line_1" required>
                        </div>
                        <div class="col-12">
                            <label for="modal_address_line_2" class="form-label">Address Line 2 <span class="text-muted">(Optional)</span></label>
                            <input type="text" class="form-control" id="modal_address_line_2" name="address_line_2">
                        </div>
                        <div class="col-md-4">
                            <label for="modal_city" class="form-label">City</label>
                            <input type="text" class="form-control" id="modal_city" name="city" required>
                        </div>
                        <div class="col-md-4">
                            <label for="modal_state" class="form-label">State</label>
                            <input type="text" class="form-control" id="modal_state" name="state" required>
                        </div>
                        <div class="col-md-4">
                            <label for="modal_pincode" class="form-label">Pincode</label>
                            <input type="text" class="form-control" id="modal_pincode" name="pincode" required>
                        </div>
                         <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="modal_is_default" name="is_default" value="1">
                                <label class="form-check-label" for="modal_is_default">
                                    Set as default address
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Reset modal on open for adding a new address
    $('#addressModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const address = button.data('address');
        const modal = $(this);

        $('#addressForm')[0].reset();
        $('#addressId').val('');
        modal.find('.modal-title').text('Add New Address');

        if (address) {
            modal.find('.modal-title').text('Edit Address');
            $('#addressId').val(address.id);
            $('#modal_type').val(address.type);
            $('#modal_name').val(address.name);
            $('#modal_phone').val(address.phone);
            $('#modal_address_line_1').val(address.address_line_1);
            $('#modal_address_line_2').val(address.address_line_2);
            $('#modal_city').val(address.city);
            $('#modal_state').val(address.state);
            $('#modal_pincode').val(address.pincode);
            $('#modal_is_default').prop('checked', address.is_default);
        }
    });

    // Handle form submission for add/edit
    $('#addressForm').on('submit', function(e) {
        e.preventDefault();
        const addressId = $('#addressId').val();
        const url = addressId ? `/customer/addresses/${addressId}` : '{{ route("customer.addresses.store") }}';
        const method = addressId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    alert(response.message || 'An error occurred.');
                }
            },
            error: function() {
                alert('An error occurred. Please check your input.');
            }
        });
    });

    // Handle delete address
    $('.delete-address').on('click', function() {
        if (confirm('Are you sure you want to delete this address?')) {
            const addressId = $(this).data('id');
            $.ajax({
                url: `/customer/addresses/${addressId}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        alert(response.message || 'Could not delete the address.');
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the address.');
                }
            });
        }
    });
});
</script>
@endpush
