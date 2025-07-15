@extends('layouts.pharmacist')

@section('title', 'Add New Medicine')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Add New Medicine</h1>
        <p class="text-muted">Add a new medicine to your inventory</p>
    </div>
    <div>
        <a href="{{ route('pharmacist.medicines') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Medicines
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Medicine Information</h6>
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

                <form method="POST" action="{{ route('pharmacist.medicines.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-12 mb-4">
                            <h6 class="text-primary border-bottom pb-2">Basic Information</h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Medicine Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="brand" class="form-label">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="brand" name="brand" 
                                   value="{{ old('brand') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="generic_name" class="form-label">Generic Name</label>
                            <input type="text" class="form-control" id="generic_name" name="generic_name" 
                                   value="{{ old('generic_name') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="analgesic" {{ old('category') === 'analgesic' ? 'selected' : '' }}>Analgesic</option>
                                <option value="antibiotic" {{ old('category') === 'antibiotic' ? 'selected' : '' }}>Antibiotic</option>
                                <option value="antacid" {{ old('category') === 'antacid' ? 'selected' : '' }}>Antacid</option>
                                <option value="vitamin" {{ old('category') === 'vitamin' ? 'selected' : '' }}>Vitamin</option>
                                <option value="antiseptic" {{ old('category') === 'antiseptic' ? 'selected' : '' }}>Antiseptic</option>
                                <option value="antidiabetic" {{ old('category') === 'antidiabetic' ? 'selected' : '' }}>Antidiabetic</option>
                                <option value="antihistamine" {{ old('category') === 'antihistamine' ? 'selected' : '' }}>Antihistamine</option>
                                <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="company_id" class="form-label">Company <span class="text-danger">*</span></label>
                            <select class="form-select" id="company_id" name="company_id" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="batch_number" class="form-label">Batch Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="batch_number" name="batch_number" 
                                   value="{{ old('batch_number') }}" required>
                        </div>

                        <!-- Stock and Pricing -->
                        <div class="col-12 mb-4 mt-4">
                            <h6 class="text-primary border-bottom pb-2">Stock & Pricing Information</h6>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   value="{{ old('quantity') }}" min="0" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="minimum_stock" class="form-label">Minimum Stock Alert</label>
                            <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" 
                                   value="{{ old('minimum_stock', 50) }}" min="1">
                            <small class="text-muted">Alert when stock goes below this number</small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date" 
                                   value="{{ old('expiry_date') }}" min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cost_price" class="form-label">Cost Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="cost_price" name="cost_price" 
                                   value="{{ old('cost_price') }}" step="0.01" min="0" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Selling Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="price" name="price" 
                                   value="{{ old('price') }}" step="0.01" min="0" required>
                        </div>

                        <!-- Photo and Description -->
                        <div class="col-12 mb-4 mt-4">
                            <h6 class="text-primary border-bottom pb-2">Additional Information</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="photo" class="form-label">Medicine Photo</label>
                            <input type="file" class="form-control" id="photo" name="photo" 
                                   accept="image/*" onchange="previewImage(this)">
                            <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                            <div class="mt-2">
                                <img id="imagePreview" src="#" alt="Preview" 
                                     style="max-width: 200px; max-height: 200px; display: none;" class="img-thumbnail">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            <small class="text-muted">Brief description of the medicine and its uses</small>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 mt-4">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('pharmacist.medicines') }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Add Medicine
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result).show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Auto-calculate margin
$('#cost_price, #price').on('input', function() {
    const costPrice = parseFloat($('#cost_price').val()) || 0;
    const sellingPrice = parseFloat($('#price').val()) || 0;
    
    if (costPrice > 0 && sellingPrice > 0) {
        const margin = ((sellingPrice - costPrice) / costPrice * 100).toFixed(2);
        const profit = (sellingPrice - costPrice).toFixed(2);
        
        if (!$('#margin-info').length) {
            $('#price').after('<small id="margin-info" class="text-info"></small>');
        }
        
        $('#margin-info').text(`Margin: ${margin}% (₹${profit} profit)`);
    }
});
</script>
@endpush
@endsection