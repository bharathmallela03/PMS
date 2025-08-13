@extends('layouts.supplier')

@section('title', 'Supplier Billing')

@push('styles')
    {{-- We will use a simple autocomplete CSS --}}
    <style>
        .autocomplete-suggestions { border: 1px solid #999; background: #FFF; overflow: auto; }
        .autocomplete-suggestion { padding: 5px 10px; white-space: nowrap; overflow: hidden; cursor: pointer; }
        .autocomplete-selected { background: #F0F0F0; }
        .autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Billing</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newBillModal">
            <i class="fas fa-plus me-2"></i> Create New Bill
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white"><h5 class="mb-0">Billing History</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Bill #</th>
                            <th>Pharmacist</th>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $bill)
                        <tr>
                            <td><strong>{{ $bill->bill_number }}</strong></td>
                            <td>{{ $bill->pharmacist->user->name ?? 'N/A' }}</td>
                            <td>{{ $bill->created_at->format('d M, Y') }}</td>
                            <td class="text-end">₹{{ number_format($bill->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center p-4">No billing history found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($bills->hasPages())
        <div class="card-footer bg-white">{{ $bills->links() }}</div>
        @endif
    </div>
</div>

@include('supplier.billing._new_bill_modal')

@endsection

@push('scripts')
{{-- We'll use jQuery Autocomplete plugin for suggestions --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.devbridge-autocomplete/1.4.11/jquery.autocomplete.min.js"></script>
<script>
$(document).ready(function() {
    let billItems = [];
    
    // --- Autocomplete for Pharmacist Name ---
    $('#pharmacist_name').autocomplete({
        serviceUrl: '{{ route("supplier.pharmacists.search") }}',
        dataType: 'json',
        onSelect: function (suggestion) {
            $('#pharmacist_id').val(suggestion.pharmacist_id);
        },
        transformResult: function(response) {
            return {
                suggestions: $.map(response, function(dataItem) {
                    return { value: dataItem.name, pharmacist_id: dataItem.pharmacist_id };
                })
            };
        }
    });

    // --- Autocomplete for Medicine Search ---
    $('#medicine_search').autocomplete({
        serviceUrl: '{{ route("supplier.medicines.search") }}',
        dataType: 'json',
        onSelect: function (suggestion) {
            addItemToBill(suggestion);
            $(this).val(''); // Clear search input
        },
         transformResult: function(response) {
            return {
                suggestions: $.map(response, function(dataItem) {
                    return { value: dataItem.name, id: dataItem.id, price: dataItem.price };
                })
            };
        }
    });
    
    // Add item to bill table
    function addItemToBill(item) {
        // Check if item already exists
        const existingItem = billItems.find(i => i.medicine_id === item.id);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            billItems.push({
                medicine_id: item.id,
                name: item.value,
                quantity: 1,
                price: parseFloat(item.price)
            });
        }
        renderBillItems();
    }
    
    // Render items in the bill
    function renderBillItems() {
        const tbody = $('#bill-items-tbody');
        tbody.empty();
        let subtotal = 0;

        if (billItems.length === 0) {
            tbody.html('<tr><td colspan="5" class="text-center">No items added.</td></tr>');
        } else {
            billItems.forEach((item, index) => {
                const total = item.quantity * item.price;
                subtotal += total;
                const row = `
                    <tr>
                        <td>${item.name}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm item-quantity" value="${item.quantity}" data-index="${index}" style="width: 70px;">
                        </td>
                        <td>₹${item.price.toFixed(2)}</td>
                        <td class="item-total">₹${total.toFixed(2)}</td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-item" data-index="${index}">&times;</button></td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
        updateTotals(subtotal);
    }
    
    // Update totals
    function updateTotals(subtotal) {
        $('#bill-subtotal').text('₹' + subtotal.toFixed(2));
        $('#bill-total').text('₹' + subtotal.toFixed(2));
        $('#total_amount_input').val(subtotal.toFixed(2));
    }

    // Handle quantity change
    $('#bill-items-tbody').on('change', '.item-quantity', function() {
        const index = $(this).data('index');
        const newQty = parseInt($(this).val());
        if (newQty > 0) {
            billItems[index].quantity = newQty;
            renderBillItems();
        }
    });

    // Handle item removal
    $('#bill-items-tbody').on('click', '.remove-item', function() {
        const index = $(this).data('index');
        billItems.splice(index, 1);
        renderBillItems();
    });
    
    // Handle form submission
    $('#createBillForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            _token: '{{ csrf_token() }}',
            pharmacist_id: $('#pharmacist_id').val(),
            pharmacist_name: $('#pharmacist_name').val(),
            total_amount: $('#total_amount_input').val(),
            items: billItems.map(item => ({
                medicine_id: item.medicine_id,
                quantity: item.quantity,
                price: item.price,
                total: item.quantity * item.price
            }))
        };
        
        // Basic validation
        if (!formData.pharmacist_id || formData.items.length === 0) {
            alert('Please select a pharmacist and add at least one item.');
            return;
        }

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    alert(response.message);
                    window.location.reload();
                }
            },
            error: function(xhr) {
                alert('An error occurred. Please check the console for details.');
                console.log(xhr.responseText);
            }
        });
    });

    // Reset modal on close
    $('#newBillModal').on('hidden.bs.modal', function () {
        $('#createBillForm')[0].reset();
        billItems = [];
        renderBillItems();
    });
});
</script>
@endpush