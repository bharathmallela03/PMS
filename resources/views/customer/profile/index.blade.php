@extends('layouts.customer')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manage Your Profile</h1>

    <div class="card shadow-sm">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profile-details-tab" data-bs-toggle="tab" data-bs-target="#profile-details" type="button" role="tab">Profile Details</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="change-password-tab" data-bs-toggle="tab" data-bs-target="#change-password" type="button" role="tab">Change Password</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="profileTabsContent">
                <!-- Profile Details Tab -->
                <div class="tab-pane fade show active" id="profile-details" role="tabpanel">
                    <h5 class="card-title mb-4">Update Your Information</h5>
                    <form id="updateProfileForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $customer->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $customer->email }}">
                            </div>
                            <div class="col-md-6">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ $customer->contact_number }}" required>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ $customer->address }}">
                            </div>
                            <div class="col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" value="{{ $customer->city }}">
                            </div>
                            <div class="col-md-4">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state" value="{{ $customer->state }}">
                            </div>
                            <div class="col-md-4">
                                <label for="pincode" class="form-label">Pincode</label>
                                <input type="text" class="form-control" id="pincode" name="pincode" value="{{ $customer->pincode }}">
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Change Password Tab -->
                <div class="tab-pane fade" id="change-password" role="tabpanel">
                    <h5 class="card-title mb-4">Update Your Password</h5>
                    <form id="updatePasswordForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update Profile
    $('#updateProfileForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ url("customer/profile/update") }}', // Changed from route() to url()
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred. Please check your input.');
            }
        });
    });

    // Update Password
    $('#updatePasswordForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ url("customer/password/update") }}', // Changed from route() to url()
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#updatePasswordForm')[0].reset();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred. Please check your passwords.');
            }
        });
    });
});
</script>
@endpush
