@extends('layouts.customer')

@section('title', 'Shopping Cart')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Your Shopping Cart</h1>

    <div class="row">
        <div class="col-lg-8">
            @if($cartItems->isEmpty())
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h4 class="card-title">Your cart is empty</h4>
                        <p class="card-text text-muted">Looks like you haven't added anything to your cart yet.</p>
                        <a href="{{ route('customer.medicines') }}" class="btn btn-primary mt-3">Continue Shopping</a>
                    </div>
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ $cartItems->count() }} Items</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <tbody>
                                    @foreach($cartItems as $item)
                                        <tr id="cart-item-{{ $item->id }}">
                                            <td style="width: 120px;">
                                                <img src="https://placehold.co/100x100/EBF4FF/7F9CF5?text={{ urlencode($item->medicine->name) }}" alt="{{ $item->medicine->name }}" class="img-fluid rounded">
                                            </td>
                                            <td>
                                                <h6 class="mb-0">{{ $item->medicine->name }}</h6>
                                                <small class="text-muted">{{ $item->medicine->company->name ?? 'N/A' }}</small>
                                            </td>
                                            <td style="width: 150px;">
                                                <div class="input-group">
                                                    <button class="btn btn-outline-secondary btn-sm update-quantity" data-id="{{ $item->id }}" data-change="-1">-</button>
                                                    <input type="text" class="form-control form-control-sm text-center quantity-input" value="{{ $item->quantity }}" readonly>
                                                    <button class="btn btn-outline-secondary btn-sm update-quantity" data-id="{{ $item->id }}" data-change="1">+</button>
                                                </div>
                                            </td>
                                            <td class="fw-bold" style="width: 120px;">
                                                ₹<span class="item-subtotal">{{ number_format($item->medicine->price * $item->quantity, 2) }}</span>
                                            </td>
                                            <td style="width: 50px;">
                                                <button class="btn btn-sm btn-outline-danger remove-item" data-id="{{ $item->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Subtotal
                            <span id="summary-subtotal">₹{{ number_format($total, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Shipping
                            <span id="summary-shipping">--</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Tax (5%)
                            <span id="summary-tax">₹{{ number_format($total * 0.05, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                            <div>
                                <strong>Total amount</strong>
                                <p class="mb-0">(including VAT)</p>
                            </div>
                            <strong id="summary-total">--</strong>
                        </li>
                    </ul>

                    <a href="{{ route('customer.checkout') }}" class="btn btn-primary btn-lg w-100 {{ $cartItems->isEmpty() ? 'disabled' : '' }}">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
