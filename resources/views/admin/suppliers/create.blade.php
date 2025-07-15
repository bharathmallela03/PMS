@extends('layouts.admin')

@section('title', 'Add New Supplier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Add New Supplier</h1>
        <p class="text-muted">Create a new supplier account</p>
    </div>
    <div>
        <a href="{{ route('admin.suppliers') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Suppliers
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Supplier Information</h6>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.suppliers.store') }}">
                    @csrf
                    
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-12 mb-4">
                            <h6 class="text-success border-bottom pb-2">Personal Information</h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                       value="{{ old('contact_number') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="shop_name" class="form-label">Company/Shop Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <input type="text" class="form-control" id="shop_name" name="shop_name" 
                                       value="{{ old('shop_name') }}" required>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="col-12 mb-4 mt-4">
                            <h6 class="text-success border-bottom pb-2">Address Information</h6>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="city" name="city" 
                                   value="{{ old('city') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="state" name="state" 
                                   value="{{ old('state') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="pincode" class="form-label">Pincode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pincode" name="pincode" 
                                   value="{{ old('pincode') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="country" name="country" 
                                   value="{{ old('country', 'India') }}" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 mt-4">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('admin.suppliers') }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Create Supplier
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Info Box -->
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> After creating the supplier, they will receive an email with instructions to set up their password.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection