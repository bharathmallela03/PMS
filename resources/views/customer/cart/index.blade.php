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
@extends('layouts.customer')

@section('title', 'Shopping Cart')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Your Shopping Cart</h1>

    <div class="row" id="cart-container">
        <div class="col-lg-8">
            <div id="cart-items-wrapper">
                @if($cartItems->isEmpty())
                    @include('customer.cart._empty_cart')
                @else
                    @include('customer.cart._cart_items', ['cartItems' => $cartItems])
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm position-sticky" style="top: 20px;">
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
                            <span id="summary-tax">--</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                            <div>
                                <strong>Total amount</strong>
                                <p class="mb-0 small text-muted">(including tax)</p>
                            </div>
                            <strong id="summary-total" class="fs-5">--</strong>
                        </li>
                    </ul>

                    <a href="{{ route('customer.checkout') }}" id="checkout-btn" class="btn btn-primary btn-lg w-100 {{ $cartItems->isEmpty() ? 'disabled' : '' }}">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // --- Update Quantity ---
    $('body').on('click', '.update-quantity', function() {
        const button = $(this);
        const cartItemId = button.data('id');
        const change = parseInt(button.data('change'));
        const quantityInput = button.siblings('.quantity-input');
        const currentQuantity = parseInt(quantityInput.val());
        const newQuantity = currentQuantity + change;

        if (newQuantity < 1) {
            return; // Prevent quantity from going below 1
        }
        
        // Disable buttons to prevent multiple clicks
        button.parent().find('.update-quantity').prop('disabled', true);

        $.ajax({
            url: `/customer/cart/update/${cartItemId}`,
            type: 'POST', // Use POST with a hidden method or set up a PATCH route
            data: {
                quantity: newQuantity,
                _method: 'PATCH' // Or handle as POST in routes
            },
            success: function(response) {
                if (response.success) {
                    quantityInput.val(newQuantity);
                    // Update item subtotal and the main order summary
                    $(`#cart-item-${cartItemId}`).find('.item-subtotal').text(response.item_subtotal.toFixed(2));
                    updateOrderSummary(response.summary);
                } else {
                    alert(response.message || 'Could not update quantity.');
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON.message || 'An error occurred. Please try again.');
            },
            complete: function() {
                 // Re-enable buttons
                button.parent().find('.update-quantity').prop('disabled', false);
            }
        });
    });

    // --- Remove Item from Cart ---
    $('body').on('click', '.remove-item', function() {
        if (!confirm('Are you sure you want to remove this item?')) {
            return;
        }

        const cartItemId = $(this).data('id');
        const itemRow = $(`#cart-item-${cartItemId}`);

        $.ajax({
            url: `/customer/cart/remove/${cartItemId}`,
            type: 'POST',
            data: {
                _method: 'DELETE'
            },
            success: function(response) {
                if (response.success) {
                    itemRow.fadeOut(300, function() { 
                        $(this).remove(); 
                        if (response.cart_empty) {
                            // If cart is now empty, show the empty message
                             $('#cart-items-wrapper').html(response.empty_cart_html);
                        }
                    });
                    updateOrderSummary(response.summary);
                } else {
                    alert('Could not remove item.');
                }
            },
            error: function() {
                alert('An error occurred while removing the item.');
            }
        });
    });

    // --- Helper function to update the entire summary box ---
    function updateOrderSummary(summary) {
        if(summary) {
            $('#summary-subtotal').text('₹' + summary.subtotal.toFixed(2));
            $('#summary-shipping').text(summary.shipping > 0 ? '₹' + summary.shipping.toFixed(2) : 'Free');
            $('#summary-tax').text('₹' + summary.tax.toFixed(2));
            $('#summary-total').text('₹' + summary.total.toFixed(2));

            // Enable/disable checkout button
            if (summary.subtotal > 0) {
                 $('#checkout-btn').removeClass('disabled');
            } else {
                 $('#checkout-btn').addClass('disabled');
            }
        }
    }
    
    // --- Initial Calculation on Page Load ---
    function initialSummaryCalc() {
        let subtotal = parseFloat('{{ $total }}');
        if (isNaN(subtotal)) subtotal = 0;
        
        let shipping = subtotal > 500 || subtotal === 0 ? 0 : 50.00;
        let tax = subtotal * 0.05;
        let total = subtotal + shipping + tax;
        
        updateOrderSummary({
            subtotal: subtotal,
            shipping: shipping,
            tax: tax,
            total: total
        });
    }

    initialSummaryCalc();
});
</script>
@endpush