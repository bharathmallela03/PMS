@extends('layouts.customer')

@section('title', 'Browse Medicines')

@push('styles')
<style>
    .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important;
    }
    .stretched-link::after {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1;
        content: "";
    }
    .filter-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35em 0.65em;
        font-size: .75em;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 50rem;
        background-color: #4e73df;
    }
    .filter-badge .btn-close {
        margin-left: 0.5rem;
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    /* Custom style for the aside modal */
    .modal-dialog-aside {
        max-width: 90%;
        margin: 0 auto;
    }
    @media (min-width: 576px) {
        .modal-dialog-aside {
            max-width: 320px;
            margin: 1.75rem 0 1.75rem auto;
        }
    }
</style>
@endpush

@section('content')

<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-aside">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filters</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('customer.medicines') }}" method="GET" id="filterForm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Filter By</h6>
                        <a href="{{ route('customer.medicines') }}" class="btn-link text-decoration-none small">Clear All</a>
                    </div>
                    <div class="mb-4">
                        <h6>Category</h6>
                        <ul class="list-unstyled">
                            @foreach($categories as $category)
                            <li>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="category" value="{{ $category }}" id="cat-{{ $loop->iteration }}" @if(request('category') == $category) checked @endif>
                                    <label class="form-check-label" for="cat-{{ $loop->iteration }}">
                                        {{ $category }}
                                    </label>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h6>Price Range</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="price_range" id="price_any" value="" @if(!request('price_range')) checked @endif>
                            <label class="form-check-label" for="price_any">Any Price</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="price_range" id="price1" value="under_100" @if(request('price_range') == 'under_100') checked @endif>
                            <label class="form-check-label" for="price1">Under ₹100</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="price_range" id="price2" value="100_500" @if(request('price_range') == '100_500') checked @endif>
                            <label class="form-check-label" for="price2">₹100 to ₹500</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="price_range" id="price3" value="500_1000" @if(request('price_range') == '500_1000') checked @endif>
                            <label class="form-check-label" for="price3">₹500 to ₹1000</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="price_range" id="price4" value="above_1000" @if(request('price_range') == 'above_1000') checked @endif>
                            <label class="form-check-label" for="price4">Above ₹1000</label>
                        </div>
                    </div>

                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h1 class="h3 mb-2 mb-md-0 text-gray-800">All Products</h1>

        <div class="d-flex align-items-center">
            <button class="btn btn-outline-primary me-2" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i> Filters
            </button>

            <form action="{{ route('customer.medicines') }}" method="GET" id="sortForm">
                <div class="d-flex align-items-center">
                    <label for="sort" class="form-label me-2 mb-0 d-none d-sm-block">Sort By:</label>
                    <select name="sort" id="sort" class="form-select" style="width: auto;" onchange="this.form.submit()">
                        <option value="relevance" @if(request('sort') == 'relevance' || !request('sort')) selected @endif>Relevance</option>
                        <option value="price_low" @if(request('sort') == 'price_low') selected @endif>Price: Low to High</option>
                        <option value="price_high" @if(request('sort') == 'price_high') selected @endif>Price: High to Low</option>
                        <option value="name" @if(request('sort') == 'name') selected @endif>Name (A-Z)</option>
                    </select>
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="price_range" value="{{ request('price_range') }}">
                </div>
            </form>
        </div>
    </div>

    @if(request()->hasAny(['category', 'price_range', 'search']))
    <div class="mb-3 d-flex align-items-center flex-wrap">
        <small class="text-muted me-2 mb-1">Active Filters:</small>
        @if(request('search'))
            <span class="filter-badge me-2 mb-1">Search: {{ request('search') }}
                <a href="{{ route('customer.medicines', request()->except('search')) }}" class="btn-close btn-close-white" aria-label="Remove Filter"></a>
            </span>
        @endif
        @if(request('category'))
            <span class="filter-badge me-2 mb-1">Category: {{ request('category') }}
                <a href="{{ route('customer.medicines', request()->except('category')) }}" class="btn-close btn-close-white" aria-label="Remove Filter"></a>
            </span>
        @endif
         @if(request('price_range'))
            <span class="filter-badge me-2 mb-1">Price: {{ str_replace('_', ' ', request('price_range')) }}
                <a href="{{ route('customer.medicines', request()->except('price_range')) }}" class="btn-close btn-close-white" aria-label="Remove Filter"></a>
            </span>
        @endif
    </div>
    @endif

    <div class="row g-3">
        @forelse($medicines as $medicine)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm product-card">
                    <a href="{{ route('customer.medicines.show', $medicine->id) }}">
                        <img src="https://placehold.co/400x400/EBF4FF/7F9CF5?text={{ urlencode($medicine->name) }}" class="card-img-top p-3" alt="{{ $medicine->name }}">
                    </a>
                    <div class="card-body d-flex flex-column pt-0 p-3">
                        <h6 class="card-title fw-bold">
                            <a href="{{ route('customer.medicines.show', $medicine->id) }}" class="text-dark text-decoration-none stretched-link">
                                {{ Str::limit($medicine->name, 45) }}
                            </a>
                        </h6>
                        <p class="card-text text-muted small flex-grow-1">{{ $medicine->company->name ?? 'Generic' }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <p class="fw-bold fs-5 mb-0">₹{{ number_format($medicine->price, 2) }}</p>
                            <button class="btn btn-outline-primary btn-sm fw-bold add-to-cart-btn" data-id="{{ $medicine->id }}" style="z-index: 2;">ADD</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card card-body text-center py-5">
                    <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No medicines found.</h4>
                    <p class="text-muted">Your search and filters did not match any products.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $medicines->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // AJAX setup for CSRF token
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $('.add-to-cart-btn').on('click', function(e) {
        e.preventDefault(); // Prevent link from being followed

        var button = $(this);
        var medicineId = button.data('id');

        // Add loading state
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

        $.ajax({
            url: "{{ route('customer.cart.add') }}",
            type: 'POST',
            data: {
                medicine_id: medicineId,
                quantity: 1
            },
            success: function(response) {
                if (response.success) {
                    // Success state
                    button.removeClass('btn-outline-primary').addClass('btn-success').html('<i class="fas fa-check"></i> Added');
                    // Update cart count in navbar if it exists
                    if ($('.cart-count').length) {
                        $('.cart-count').text(response.cart_count);
                    }
                } else {
                    // Error state
                    button.removeClass('btn-outline-primary').addClass('btn-danger').text('Error');
                    alert(response.message || 'Could not add to cart.');
                }

                // Revert button state after a delay
                setTimeout(function() {
                    button.prop('disabled', false).removeClass('btn-success btn-danger').addClass('btn-outline-primary').html('ADD');
                }, 2000);
            },
            error: function() {
                alert('An error occurred. Please try again.');
                 setTimeout(function() {
                    button.prop('disabled', false).removeClass('btn-success btn-danger').addClass('btn-outline-primary').html('ADD');
                }, 2000);
            }
        });
    });
});
</script>
@endpush