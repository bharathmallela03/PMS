@extends('layouts.customer')

@section('title', $medicine->name)

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row">
                <!-- Product Image -->
                <div class="col-md-5 text-center">
                    <img src="https://placehold.co/600x600/EBF4FF/7F9CF5?text={{ urlencode($medicine->name) }}" class="img-fluid rounded" alt="{{ $medicine->name }}">
                </div>

                <!-- Product Details -->
                <div class="col-md-7">
                    <h1 class="h2 fw-bold">{{ $medicine->name }}</h1>
                    <p class="text-muted">By {{ $medicine->company->name ?? 'Unknown Brand' }}</p>

                    <!-- Ratings Placeholder -->
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-success me-2">4.5 <i class="fas fa-star fa-xs"></i></span>
                        <span class="text-muted">10,531 ratings</span>
                    </div>

                    <hr>

                    <!-- Price -->
                    <div class="mb-3">
                        @php
                            // Placeholder for MRP and discount calculation
                            $mrp = $medicine->price * 1.25;
                            $discount = 0;
                            if ($mrp > 0) {
                                $discount = (($mrp - $medicine->price) / $mrp) * 100;
                            }
                        @endphp
                        <span class="h2 fw-bold me-3">₹{{ number_format($medicine->price, 2) }}</span>
                        <span class="text-muted text-decoration-line-through">MRP ₹{{ number_format($mrp, 2) }}</span>
                        <span class="text-success fw-bold ms-2">({{ round($discount) }}% OFF)</span>
                    </div>

                    <!-- Description -->
                    <p class="text-muted">{{ $medicine->description ?? 'No description available for this product.' }}</p>

                    <!-- Add to Cart Form -->
                    <form id="addToCartForm" class="mt-4">
                        @csrf
                        <input type="hidden" name="medicine_id" value="{{ $medicine->id }}">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-3">
                                <label for="quantity" class="form-label">Quantity:</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="{{ $medicine->quantity }}">
                            </div>
                            <div class="col-md-8 col-lg-9 mt-3 mt-md-0">
                                <button type="submit" class="btn btn-primary btn-lg px-5" @if($medicine->quantity <= 0) disabled @endif>
                                    <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                         @if($medicine->quantity < 10 && $medicine->quantity > 0)
                            <p class="text-danger mt-2">Only {{ $medicine->quantity }} items left in stock!</p>
                         @elseif($medicine->quantity <= 0)
                             <p class="text-danger mt-2 fw-bold">Out of Stock</p>
                         @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div class="mt-5">
        <h3 class="mb-4">Related Products</h3>
        <div class="row g-4">
            @forelse($relatedMedicines as $related)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        <a href="{{ route('customer.medicines.show', $related->id) }}" class="text-decoration-none">
                            <img src="https://placehold.co/400x400/EBF4FF/7F9CF5?text={{ urlencode($related->name) }}" class="card-img-top p-3" alt="{{ $related->name }}">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-dark fs-6 mb-1">
                                <a href="{{ route('customer.medicines.show', $related->id) }}" class="text-decoration-none text-dark stretched-link">
                                    {{ Str::limit($related->name, 50) }}
                                </a>
                            </h5>
                            <p class="card-text text-muted small flex-grow-1">{{ $related->generic_name ?? 'bottle of 30 tablets' }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="fw-bold fs-5 mb-0">₹{{ number_format($related->price, 2) }}</p>
                                <button class="btn btn-outline-primary fw-bold btn-sm">ADD</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p>No related products found.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#addToCartForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        $.ajax({
            url: '{{ route("customer.cart.add") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    // Update cart count in the header
                    $('.cart-count').text(response.cart_count).show();
                    alert(response.message); // Or use a more elegant notification system
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                // Handle validation errors or other server errors
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            }
        });
    });
});
</script>
@endpush
