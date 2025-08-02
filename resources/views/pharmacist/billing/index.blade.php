@extends('layouts.pharmacist')

@section('title', 'Billing Management')

@push('styles')
<style>
    /* Custom styles for an enhanced UI */
    .page-header h1 {
        font-weight: 700;
        color: #111827;
    }

    .card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    .table thead {
        background-color: #f3f4f6;
    }
    
    .table th {
        font-weight: 600;
        color: #374151;
    }

    .modal-header {
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .medicine-search-container {
        position: relative;
    }

    .medicine-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #d1d5db;
        border-top: none;
        border-radius: 0 0 0.5rem 0.5rem;
        z-index: 1050;
        max-height: 200px;
        overflow-y: auto;
        display: none;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .suggestion-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .suggestion-item:hover {
        background-color: #f3f4f6;
    }
    
    .suggestion-item .medicine-name {
        font-weight: 600;
    }
    
    .suggestion-item .medicine-info {
        font-size: 0.875rem;
        color: #6b7280;
        display: flex;
        justify-content: space-between;
    }

    .total-section {
        background-color: #f9fafb;
        padding: 1.5rem;
        border-radius: 0.5rem;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        font-size: 1rem;
    }
    
    .total-row:not(:last-child) {
        border-bottom: 1px solid #e5e7eb;
    }
    
    .total-row strong {
        font-weight: 600;
        color: #111827;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <h1 class="page-title">💊 Billing Management</h1>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h3 class="card-title mb-0">Billing Records</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newBillModal">
                <i class="fas fa-plus me-2"></i> New Bill
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Bill #</th>
                            <th>Patient Name</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills ?? [] as $bill)
                        <tr>
                            <td><strong>{{ $bill->bill_number }}</strong></td>
                            <td>{{ $bill->patient_name }}</td>
                            <td>{{ $bill->created_at->format('M d, Y') }}</td>
                            <td><strong>&#8377;{{ number_format($bill->total_amount, 2) }}</strong></td>
                            <td>
                                <span class="badge {{ $bill->status_badge }}">{{ ucfirst($bill->status) }}</span>
                            </td>
                            <td>
                                {{-- === SIDE-BY-SIDE ACTION BUTTONS START === --}}
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('pharmacist.billing.show', $bill->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('pharmacist.billing.print', $bill->id) }}" class="btn btn-sm btn-outline-secondary" title="Print/PDF" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    <form action="{{ route('pharmacist.billing.delete', $bill->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this bill? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                                {{-- === SIDE-BY-SIDE ACTION BUTTONS END === --}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <p class="text-muted">No bills found. Create a new bill to get started.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             @if(isset($bills) && $bills->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $bills->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- New Bill Modal -->
<div class="modal fade" id="newBillModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Bill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newBillForm">
                    @csrf
                    <h6>Patient Information</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Patient Name</label>
                            <input type="text" class="form-control" name="patient_name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="patient_phone">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="patient_address">
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6>Medicine Items</h6>
                    <div id="billItems">
                        <!-- Bill items will be added here by JavaScript -->
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary mt-2" id="addItemBtn">
                        <i class="fas fa-plus me-2"></i>Add Item
                    </button>
                    
                    <hr class="my-4">

                    <div class="row justify-content-end">
                        <div class="col-lg-5">
                            <div class="total-section">
                                <div class="total-row">
                                    <span>Subtotal:</span>
                                    <span id="subtotalAmount">&#8377;0.00</span>
                                </div>
                                <div class="total-row">
                                    <span>Discount (%):</span>
                                    <input type="number" class="form-control form-control-sm" style="width: 80px;" id="discount" name="discount_percentage" value="0" min="0">
                                </div>
                                <div class="total-row">
                                    <strong>Grand Total:</strong>
                                    <strong id="grandTotalAmount">&#8377;0.00</strong>
                                </div>
                            </div>
                            <input type="hidden" id="grandTotal" name="total_amount">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveBillBtn">Save Bill</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const billItemsContainer = document.getElementById('billItems');
    const addItemBtn = document.getElementById('addItemBtn');
    let searchTimeout;

    const addItem = () => {
        const itemIndex = Date.now();
        const itemHtml = `
            <div class="row align-items-center bill-item mb-2">
                <div class="col-md-4">
                    <div class="medicine-search-container">
                        <input type="text" class="form-control medicine-search" name="items[${itemIndex}][name]" placeholder="Search medicine..." required>
                        <input type="hidden" class="medicine-id" name="items[${itemIndex}][medicine_id]">
                        <div class="medicine-suggestions"></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control quantity-input" name="items[${itemIndex}][quantity]" value="1" min="1" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control price-input" name="items[${itemIndex}][price]" readonly required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control total-input" name="items[${itemIndex}][total]" readonly>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn w-100">Remove</button>
                </div>
            </div>`;
        billItemsContainer.insertAdjacentHTML('beforeend', itemHtml);
    };

    const calculateItemTotal = (item) => {
        const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(item.querySelector('.price-input').value) || 0;
        item.querySelector('.total-input').value = (quantity * price).toFixed(2);
        calculateGrandTotal();
    };

    const calculateGrandTotal = () => {
        let subtotal = 0;
        document.querySelectorAll('.total-input').forEach(input => {
            subtotal += parseFloat(input.value) || 0;
        });

        const discountPercent = parseFloat(document.getElementById('discount').value) || 0;
        const discountAmount = (subtotal * discountPercent) / 100;
        const grandTotal = subtotal - discountAmount;

        document.getElementById('subtotalAmount').textContent = `₹${subtotal.toFixed(2)}`;
        document.getElementById('grandTotalAmount').textContent = `₹${grandTotal.toFixed(2)}`;
        document.getElementById('grandTotal').value = grandTotal.toFixed(2);
    };

    billItemsContainer.addEventListener('input', e => {
        const target = e.target;
        if (target.classList.contains('quantity-input') || target.classList.contains('price-input')) {
            calculateItemTotal(target.closest('.bill-item'));
        }
        if (target.classList.contains('medicine-search')) {
            const query = target.value.trim();
            const suggestionsDiv = target.closest('.medicine-search-container').querySelector('.medicine-suggestions');
            clearTimeout(searchTimeout);
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => searchMedicines(query, suggestionsDiv), 300);
            } else {
                suggestionsDiv.style.display = 'none';
            }
        }
    });
    
    document.getElementById('discount').addEventListener('input', calculateGrandTotal);

    const searchMedicines = async (query, suggestionsDiv) => {
        try {
            const response = await fetch(`{{ route('search.medicines') }}?query=${encodeURIComponent(query)}`);
            const medicines = await response.json();
            suggestionsDiv.innerHTML = medicines.map(med => `
                <div class="suggestion-item" data-id="${med.id}" data-name="${med.name}" data-price="${med.price}" data-quantity="${med.quantity}">
                    <div class="medicine-name">${med.name}</div>
                    <div class="medicine-info"><span>₹${med.price}</span><span>Stock: ${med.quantity}</span></div>
                </div>
            `).join('');
            suggestionsDiv.style.display = 'block';
        } catch (error) {
            console.error('Search error:', error);
        }
    };

    billItemsContainer.addEventListener('click', e => {
        if (e.target.closest('.suggestion-item')) {
            const suggestion = e.target.closest('.suggestion-item');
            const item = suggestion.closest('.bill-item');
            item.querySelector('.medicine-search').value = suggestion.dataset.name;
            item.querySelector('.medicine-id').value = suggestion.dataset.id;
            item.querySelector('.price-input').value = suggestion.dataset.price;
            item.querySelector('.quantity-input').setAttribute('max', suggestion.dataset.quantity);
            suggestion.closest('.medicine-suggestions').style.display = 'none';
            calculateItemTotal(item);
        }
        if (e.target.classList.contains('remove-item-btn')) {
            if (billItemsContainer.children.length > 1) {
                e.target.closest('.bill-item').remove();
                calculateGrandTotal();
            } else {
                alert('At least one item is required.');
            }
        }
    });

    addItemBtn.addEventListener('click', addItem);
    addItem(); // Initialize with one item

    document.getElementById('saveBillBtn').addEventListener('click', async () => {
        const form = document.getElementById('newBillForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('{{ route("pharmacist.billing.store") }}', {
                method: 'POST',
                body: new URLSearchParams(formData),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await response.json();
            if (data.success) {
                alert('Bill saved successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Save error:', error);
            alert('An error occurred while saving the bill.');
        }
    });
});
</script>
@endpush
