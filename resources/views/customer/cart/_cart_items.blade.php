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
                                    <button class="btn btn-outline-secondary btn-sm update-quantity" data-id="{{ $item->id }}" data-change="-1" @if($item->quantity <= 1) disabled @endif>-</button>
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