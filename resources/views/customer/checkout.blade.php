@extends('layouts.customer')

@section('title', 'Checkout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Checkout</h1>

    <form id="checkoutForm">
        @csrf
        <div class="row">
            <!-- Shipping and Payment Column -->
            <div class="col-lg-8">
                <!-- Shipping Address -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Shipping Address</h5>
                    </div>
                    <div class="card-body">
                        @if($addresses->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label">Select an existing address:</label>
                                @foreach($addresses as $address)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="selected_address" id="address{{ $address->id }}" value="{{ $address->id }}">
                                    <label class="form-check-label" for="address{{ $address->id }}">
                                        <strong>{{ $address->type }}</strong>: {{ $address->address_line_1 }}, {{ $address->city }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <hr>
                            <p class="text-muted text-center">Or enter a new address</p>
                        @endif

                        <div id="address-fields">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="delivery_address[name]" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="delivery_address[phone]" required>
                                </div>
                                <div class="col-12">
                                    <label for="address_line_1" class="form-label">Address Line 1</label>
                                    <input type="text" class="form-control" id="address_line_1" name="delivery_address[address_line_1]" required>
                                </div>
                                <div class="col-12">
                                    <label for="address_line_2" class="form-label">Address Line 2 <span class="text-muted">(Optional)</span></label>
                                    <input type="text" class="form-control" id="address_line_2" name="delivery_address[address_line_2]">
                                </div>
                                <div class="col-md-6">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="delivery_address[city]" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" class="form-control" id="state" name="delivery_address[state]" required>
                                </div>
                                 <div class="col-md-6">
                                    <label for="pincode" class="form-label">Pincode</label>
                                    <input type="text" class="form-control" id="pincode" name="delivery_address[pincode]" required>
                                </div>
                                 <div class="col-md-6">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="delivery_address[country]" value="India" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                            <label class="form-check-label" for="cod">
                                Cash on Delivery (COD)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="online" value="online" disabled>
                            <label class="form-check-label" for="online">
                                Online Payment (Coming Soon)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Column -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        @foreach($cartItems as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ $item->medicine->name }} x {{ $item->quantity }}</span>
                            <span>₹{{ number_format($item->medicine->price * $item->quantity, 2) }}</span>
                        </div>
                        @endforeach
                        <hr>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Subtotal
                                <span>₹{{ number_format($subtotal, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Shipping
                                <span>{{ $subtotal > 500 ? 'Free' : '₹50.00' }}</span>
                            </li>
                             <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Tax (5%)
                                <span>₹{{ number_format($subtotal * 0.05, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                                <strong>Total</strong>
                                @php
                                    $shipping = $subtotal > 500 ? 0 : 50;
                                    $tax = $subtotal * 0.05;
                                    $totalAmount = $subtotal + $shipping + $tax;
                                @endphp
                                <strong>₹{{ number_format($totalAmount, 2) }}</strong>
                            </li>
                        </ul>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Make addresses available in JS
    const addresses = @json($addresses);

    // Populate form with selected address
    $('input[name="selected_address"]').on('change', function() {
        const addressId = $(this).val();
        const selectedAddress = addresses.find(addr => addr.id == addressId);
        
        if (selectedAddress) {
            $('#name').val(selectedAddress.name);
            $('#phone').val(selectedAddress.phone);
            $('#address_line_1').val(selectedAddress.address_line_1);
            $('#address_line_2').val(selectedAddress.address_line_2);
            $('#city').val(selectedAddress.city);
            $('#state').val(selectedAddress.state);
            $('#pincode').val(selectedAddress.pincode);
            $('#country').val(selectedAddress.country);
        }
    });

    // Handle form submission
    $('#checkoutForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '{{ route("customer.order.place") }}', // Corrected to use the named route
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    window.location.href = response.redirect_url;
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = '';
                for (let key in errors) {
                    errorMessage += errors[key][0] + '\n';
                }
                alert(errorMessage || 'An error occurred. Please check your input.');
            }
        });
    });
});
</script>
@endpush
