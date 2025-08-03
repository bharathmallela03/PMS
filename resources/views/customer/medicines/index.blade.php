@extends('layouts.customer')

@section('title', 'Browse Medicines')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h1 class="h3 mb-2 mb-md-0 text-gray-800">All Products</h1>
        
        <form action="{{ route('customer.medicines') }}" method="GET" id="sortForm" class="w-100 w-md-auto">
             <div class="d-flex align-items-center">
                <label for="sort" class="form-label me-2 mb-0">Sort By:</label>
                <select name="sort" id="sort" class="form-select form-select-sm" style="width: auto;" onchange="document.getElementById('sortForm').submit();">
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

    <div class="row g-3 g-md-4">
        @forelse($medicines as $medicine)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm product-card">
                    <a href="{{ route('customer.medicines.show', $medicine->id) }}" class="text-decoration-none">
                        <img src="https://placehold.co/400x400/EBF4FF/7F9CF5?text={{ urlencode($medicine->name) }}" class="card-img-top p-3" alt="{{ $medicine->name }}">
                    </a>
                    <div class="card-body d-flex flex-column p-2 p-md-3">
                        <h5 class="card-title fw-bold text-dark fs-6 mb-1">
                            <a href="{{ route('customer.medicines.show', $medicine->id) }}" class="text-decoration-none text-dark stretched-link">
                                {{ Str::limit($medicine->name, 40) }}
                            </a>
                        </h5>
                        <p class="card-text text-muted small flex-grow-1">{{ $medicine->generic_name ?? 'bottle of 30 tablets' }}</p>
                        
                        {{-- Placeholder for ratings --}}
                        <div class="d-flex align-items-center small mb-2">
                            <span class="badge bg-success me-2">4.5 <i class="fas fa-star fa-xs"></i></span>
                            <span class="text-muted d-none d-md-inline">10,531 ratings</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @php
                                    $mrp = $medicine->price * 1.25;
                                    $discount = (($mrp - $medicine->price) / $mrp) * 100;
                                @endphp
                                <span class="text-muted small text-decoration-line-through d-none d-md-block">MRP ₹{{ number_format($mrp, 2) }}</span>
                                <span class="text-success small fw-bold">{{ round($discount) }}% OFF</span>
                                <p class="fw-bold fs-6 fs-md-5 mb-0">₹{{ number_format($medicine->price, 2) }}</p>
                            </div>
                            <button class="btn btn-outline-primary btn-sm fw-bold">ADD</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card card-body text-center py-5">
                    <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No medicines found.</h4>
                    <p class="text-muted">Please try adjusting your search or filters.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $medicines->appends(request()->query())->links() }}
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
    .card-title a {
        color: #212529;
    }
    .card-title a:hover {
        color: #0d6efd;
    }

    /* ==============================================
    MEDIA QUERIES FOR RESPONSIVE DESIGN
    ==============================================
    */

    /* Targeting mobile devices (screens smaller than 768px) */
    @media (max-width: 767.98px) {
        .h3 {
            font-size: 1.5rem; /* Make main heading smaller on mobile */
        }
        
        .product-card .card-title {
            font-size: 0.85rem; /* Adjust product title font size */
            min-height: 40px; /* Ensure titles have uniform height */
        }
        
        .product-card .card-text {
            font-size: 0.75rem; /* Adjust generic name font size */
        }
        
        .product-card .fw-bold.fs-6 { /* Targets the price */
             font-size: 1rem !important;
        }

        .product-card .btn {
            padding: 0.25rem 0.5rem; /* Smaller button padding */
            font-size: 0.75rem;
        }

        .g-3 { /* Reduce gutter/gap between cards on mobile */
            --bs-gutter-x: 0.75rem;
            --bs-gutter-y: 0.75rem;
        }
    }
</style>
@endpush