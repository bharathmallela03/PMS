
@extends('layouts.pharmacist')


@section('title', 'Billing Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/billing.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">💊 Billing Management</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <!-- <li class="breadcrumb-item"><a href="{{ route('pharmacist.dashboard') }}">Dashboard</a></li> -->
                        <li class="breadcrumb-item active" aria-current="page">Billing</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">📋 Billing Records</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newBillModal">
                        <i class="fas fa-plus"></i> New Bill
                    </button>
                </div>
                <div class="card-body">
                    <!-- Enhanced Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="dateFrom" class="form-label">📅 From Date</label>
                            <input type="date" class="form-control" id="dateFrom">
                        </div>
                        <div class="col-md-3">
                            <label for="dateTo" class="form-label">📅 To Date</label>
                            <input type="date" class="form-control" id="dateTo">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">📊 Status</label>
                            <select class="form-select" id="status">
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">🔍 Search</label>
                            <input type="text" class="form-control" id="search" placeholder="Search by patient name or bill #">
                        </div>
                    </div>

                    <!-- Enhanced Bills Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>📄 Bill #</th>
                                    <th>👤 Patient Name</th>
                                    <th>📅 Date</th>
                                    <th>📦 Items</th>
                                    <th>💰 Amount</th>
                                    <th>📊 Status</th>
                                    <th>⚡ Actions</th>
                                </tr>
                            </thead>
                            <tbody id="billsTableBody">
                                @forelse($bills ?? [] as $bill)
                                <tr>
                                    <td><strong>{{ $bill->bill_number }}</strong></td>
                                    <td>{{ $bill->patient_name }}</td>
                                    <td>{{ $bill->created_at->format('M d, Y') }}</td>
                                    <td><span class="badge bg-info">{{ $bill->items_count ?? 0 }} items</span></td>
                                    <td><strong>₹{{ number_format($bill->total_amount, 2) }}</strong></td>
                                    <td>
                                        @if($bill->status == 'paid')
                                            <span class="badge bg-success">✅ Paid</span>
                                        @elseif($bill->status == 'pending')
                                            <span class="badge bg-warning">⏳ Pending</span>
                                        @else
                                            <span class="badge bg-danger">⚠️ Overdue</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewBill({{ $bill->id }})">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="printBill({{ $bill->id }})">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center" style="padding: 3rem;">
                                        <div style="opacity: 0.6;">
                                            <i class="fas fa-file-invoice fa-3x mb-3" style="color: #6b7280;"></i>
                                            <p>No bills found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($bills) && $bills->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $bills->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced New Bill Modal -->
<div class="modal fade" id="newBillModal" tabindex="-1" aria-labelledby="newBillModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newBillModalLabel">🧾 Create New Bill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newBillForm" action="{{ route('pharmacist.billing.store') }}" method="POST">
                    @csrf
                    <!-- Patient Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 style="color: #6366f1; font-weight: 600; margin-bottom: 1rem;">👤 Patient Information</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="patientName" class="form-label">Patient Name</label>
                                <input type="text" class="form-control" id="patientName" name="patient_name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="patientPhone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="patientPhone" name="patient_phone">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="patientAge" class="form-label">Age</label>
                                <input type="number" class="form-control" id="patientAge" name="patient_age" min="1" max="120">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="patientAddress" class="form-label">Address</label>
                                <textarea class="form-control" id="patientAddress" name="patient_address" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <hr style="border-color: #e5e7eb; margin: 2rem 0;">
                    
                    <!-- Medicine Items -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 style="color: #6366f1; font-weight: 600; margin-bottom: 1rem;">💊 Medicine Items</h6>
                        </div>
                    </div>
                    
                    <div id="billItems">
                        <div class="bill-item fadeIn">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Medicine Name</label>
                                        <div class="medicine-search-container">
                                            <input type="text" class="form-control medicine-search" name="medicine_name[]" placeholder="Type medicine name..." autocomplete="off" required>
                                            <input type="hidden" name="medicine_id[]" class="medicine-id">
                                            <div class="medicine-suggestions"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" class="form-control quantity-input" name="quantity[]" min="1" value="1" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">Unit Price</label>
                                        <input type="number" class="form-control price-input" name="price[]" step="0.01" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">Total</label>
                                        <input type="number" class="form-control total-input" name="total[]" step="0.01" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeItem(this)">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary mb-4" onclick="addItem()">
                        <i class="fas fa-plus"></i> Add Another Medicine
                    </button>
                    
                    <hr style="border-color: #e5e7eb; margin: 2rem 0;">
                    
                    <!-- Total Calculation -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="discount" class="form-label">💸 Discount (%)</label>
                                        <input type="number" class="form-control" id="discount" name="discount" step="0.01" min="0" max="100" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tax" class="form-label">📊 Tax (%)</label>
                                        <input type="number" class="form-control" id="tax" name="tax" step="0.01" min="0" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="total-section">
                                <div class="total-row">
                                    <span>Subtotal:</span>
                                    <span id="subtotalAmount">₹0.00</span>
                                </div>
                                <div class="total-row">
                                    <span>Discount:</span>
                                    <span id="discountAmount">₹0.00</span>
                                </div>
                                <div class="total-row">
                                    <span>Tax:</span>
                                    <span id="taxAmount">₹0.00</span>
                                </div>
                                <div class="total-row">
                                    <strong>Grand Total:</strong>
                                    <strong id="grandTotalAmount">₹0.00</strong>
                                </div>
                            </div>
                            <input type="hidden" id="grandTotal" name="grand_total">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" onclick="saveBill()">
                    <i class="fas fa-save"></i> Save Bill
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
class MedicineBilling {
    constructor() {
        this.searchTimeout = {};
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        console.log('Initializing Medicine Billing...');
        
        // Wait for DOM to be ready
        setTimeout(() => {
            const firstItem = document.querySelector('.bill-item');
            if (firstItem) {
                this.attachItemEvents(firstItem);
                console.log('First item events attached');
            }
        }, 100);
        
        // Discount and tax calculation
        const discountInput = document.getElementById('discount');
        const taxInput = document.getElementById('tax');
        
        if (discountInput) {
            discountInput.addEventListener('input', () => this.calculateGrandTotal());
        }
        if (taxInput) {
            taxInput.addEventListener('input', () => this.calculateGrandTotal());
        }
    }

    attachItemEvents(item) {
        if (!item) {
            console.error('No item provided to attachItemEvents');
            return;
        }

        console.log('Attaching events to item:', item);
        
        const searchInput = item.querySelector('.medicine-search');
        const quantityInput = item.querySelector('.quantity-input');
        const priceInput = item.querySelector('.price-input');
        const suggestionsDiv = item.querySelector('.medicine-suggestions');

        if (!searchInput) {
            console.error('Search input not found in item');
            return;
        }

        // Generate unique ID for this input
        const inputId = 'search_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        searchInput.setAttribute('data-id', inputId);

        console.log('Attaching search events with ID:', inputId);

        // Medicine search functionality
        searchInput.addEventListener('input', (e) => {
            console.log('Search input changed:', e.target.value);
            const query = e.target.value.trim();

            clearTimeout(this.searchTimeout[inputId]);

            if (query.length >= 2) {
                console.log('Searching for:', query);
                this.searchTimeout[inputId] = setTimeout(() => {
                    this.searchMedicines(query, suggestionsDiv, searchInput);
                }, 300);
            } else {
                this.hideSuggestions(suggestionsDiv);
            }
        });

        searchInput.addEventListener('blur', () => {
            setTimeout(() => this.hideSuggestions(suggestionsDiv), 200);
        });

        // Calculation events
        if (quantityInput) {
            quantityInput.addEventListener('input', () => this.calculateItemTotal(item));
        }
        if (priceInput) {
            priceInput.addEventListener('input', () => this.calculateItemTotal(item));
        }
    }

    async searchMedicines(query, suggestionsDiv, searchInput) {
        console.log('Making API call for:', query);
        
        try {
            if (!suggestionsDiv) {
                console.error('Suggestions div not found');
                return;
            }

            suggestionsDiv.innerHTML = '<div class="loading">🔍 Searching medicines...</div>';
            suggestionsDiv.style.display = 'block';

            const response = await fetch(`/search-medicines?query=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const medicines = await response.json();
            console.log('Medicines received:', medicines);

            if (medicines && medicines.length > 0) {
                this.displaySuggestions(medicines, suggestionsDiv, searchInput);
            } else {
                suggestionsDiv.innerHTML = '<div class="loading">❌ No medicines found</div>';
            }
        } catch (error) {
            console.error('Search error:', error);
            suggestionsDiv.innerHTML = '<div class="loading">⚠️ Error searching medicines</div>';
        }
    }

    displaySuggestions(medicines, suggestionsDiv, searchInput) {
        console.log('Displaying suggestions');
        
        const inputId = searchInput.getAttribute('data-id');

        suggestionsDiv.innerHTML = medicines.map(medicine => `
            <div class="suggestion-item" onclick="window.medicineBilling.selectMedicine(${medicine.id}, '${medicine.name.replace(/'/g, "\\'")}', ${medicine.price}, ${medicine.quantity}, '${inputId}')">
                <div class="medicine-name">${medicine.name}</div>
                <div class="medicine-info">
                    <span class="medicine-price">₹${medicine.price}</span>
                    <span class="medicine-stock">Stock: ${medicine.quantity}</span>
                </div>
            </div>
        `).join('');

        suggestionsDiv.style.display = 'block';
    }

    selectMedicine(id, name, price, stock, inputId) {
        console.log('Selecting medicine:', { id, name, price, stock, inputId });
        
        const searchInput = document.querySelector(`[data-id="${inputId}"]`);
        if (!searchInput) {
            console.error('Search input not found for ID:', inputId);
            return;
        }

        const item = searchInput.closest('.bill-item');
        const priceInput = item.querySelector('.price-input');
        const medicineIdInput = item.querySelector('.medicine-id');
        const suggestionsDiv = item.querySelector('.medicine-suggestions');

        searchInput.value = name;
        if (priceInput) priceInput.value = price;
        if (medicineIdInput) medicineIdInput.value = id;
        
        // Add visual feedback
        item.classList.add('selected-item');
        setTimeout(() => item.classList.remove('selected-item'), 2000);

        this.calculateItemTotal(item);
        this.hideSuggestions(suggestionsDiv);
        
        console.log('Medicine selected successfully');
    }

    hideSuggestions(suggestionsDiv) {
        if (suggestionsDiv) {
            suggestionsDiv.style.display = 'none';
        }
    }

    calculateItemTotal(item) {
        const quantityInput = item.querySelector('.quantity-input');
        const priceInput = item.querySelector('.price-input');
        const totalInput = item.querySelector('.total-input');

        if (!quantityInput || !priceInput || !totalInput) {
            console.error('Required inputs not found for calculation');
            return;
        }

        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = quantity * price;

        totalInput.value = total.toFixed(2);
        this.calculateGrandTotal();
    }

    calculateGrandTotal() {
        const totalInputs = document.querySelectorAll('.total-input');
        let subtotal = 0;

        totalInputs.forEach(input => {
            subtotal += parseFloat(input.value) || 0;
        });

        const discount = parseFloat(document.getElementById('discount')?.value) || 0;
        const tax = parseFloat(document.getElementById('tax')?.value) || 0;

        const discountAmount = (subtotal * discount) / 100;
        const taxableAmount = subtotal - discountAmount;
        const taxAmount = (taxableAmount * tax) / 100;
        const grandTotal = taxableAmount + taxAmount;

        // Update display
        const subtotalEl = document.getElementById('subtotalAmount');
        const discountEl = document.getElementById('discountAmount');
        const taxEl = document.getElementById('taxAmount');
        const grandTotalEl = document.getElementById('grandTotalAmount');
        const grandTotalInput = document.getElementById('grandTotal');

        if (subtotalEl) subtotalEl.textContent = `₹${subtotal.toFixed(2)}`;
        if (discountEl) discountEl.textContent = `₹${discountAmount.toFixed(2)}`;
        if (taxEl) taxEl.textContent = `₹${taxAmount.toFixed(2)}`;
        if (grandTotalEl) grandTotalEl.textContent = `₹${grandTotal.toFixed(2)}`;
        if (grandTotalInput) grandTotalInput.value = grandTotal.toFixed(2);
    }
}

// Initialize global variable
let medicineBilling = null;

// Enhanced functions
function addItem() {
    console.log('Adding new item');
    const billItems = document.getElementById('billItems');
    const newItem = document.createElement('div');
    newItem.className = 'bill-item fadeIn';
    newItem.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Medicine Name</label>
                    <div class="medicine-search-container">
                        <input type="text" class="form-control medicine-search" name="medicine_name[]" placeholder="Type medicine name..." autocomplete="off" required>
                        <input type="hidden" name="medicine_id[]" class="medicine-id">
                        <div class="medicine-suggestions"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control quantity-input" name="quantity[]" min="1" value="1" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Unit Price</label>
                    <input type="number" class="form-control price-input" name="price[]" step="0.01" required readonly>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Total</label>
                    <input type="number" class="form-control total-input" name="total[]" step="0.01" readonly>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-outline-danger w-100" onclick="removeItem(this)">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    billItems.appendChild(newItem);
    
    // Attach events to the new item
    if (window.medicineBilling) {
        window.medicineBilling.attachItemEvents(newItem);
    }
}

// Remove item functionality
function removeItem(button) {
    const item = button.closest('.bill-item');
    if (document.querySelectorAll('.bill-item').length > 1) {
        item.remove();
        if (window.medicineBilling) {
            window.medicineBilling.calculateGrandTotal();
        }
    } else {
        alert('At least one medicine item is required!');
    }
}

// View bill functionality
function viewBill(billId) {
    window.location.href = `/pharmacist/billing/${billId}`;
}

// Print bill functionality
function printBill(billId) {
    window.open(`/pharmacist/billing/${billId}/print`, '_blank');
}

// Save bill functionality
function saveBill() {
    const form = document.getElementById('newBillForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const patientName = document.getElementById('patientName').value.trim();
    if (!patientName) {
        alert('Please enter patient name');
        return;
    }
    
    // Check if at least one medicine is selected
    const medicineInputs = document.querySelectorAll('.medicine-search');
    let hasValidMedicine = false;
    
    medicineInputs.forEach(input => {
        if (input.value.trim() !== '') {
            hasValidMedicine = true;
        }
    });
    
    if (!hasValidMedicine) {
        alert('Please select at least one medicine');
        return;
    }
    
    // Show loading state
    const saveButton = document.querySelector('[onclick="saveBill()"]');
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    saveButton.disabled = true;
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Bill saved successfully!');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('newBillModal'));
            modal.hide();
            
            // Reset form
            resetBillForm();
            
            // Reload page to show new bill
            location.reload();
        } else {
            alert('❌ Error saving bill: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error saving bill. Please try again.');
    })
    .finally(() => {
        // Reset button state
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
    });
}

// Reset bill form
function resetBillForm() {
    const form = document.getElementById('newBillForm');
    form.reset();
    
    // Reset bill items to just one
    const billItems = document.getElementById('billItems');
    billItems.innerHTML = `
        <div class="bill-item fadeIn">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Medicine Name</label>
                        <div class="medicine-search-container">
                            <input type="text" class="form-control medicine-search" name="medicine_name[]" placeholder="Type medicine name..." autocomplete="off" required>
                            <input type="hidden" name="medicine_id[]" class="medicine-id">
                            <div class="medicine-suggestions"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control quantity-input" name="quantity[]" min="1" value="1" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Unit Price</label>
                        <input type="number" class="form-control price-input" name="price[]" step="0.01" required readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Total</label>
                        <input type="number" class="form-control total-input" name="total[]" step="0.01" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeItem(this)">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Reinitialize events for the new item
    if (window.medicineBilling) {
        window.medicineBilling.attachItemEvents(document.querySelector('.bill-item'));
        window.medicineBilling.calculateGrandTotal();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing billing system');
    
    // Make sure CSRF token is available
    if (!document.querySelector('meta[name="csrf-token"]')) {
        console.warn('CSRF token not found in meta tags');
    }
    
    // Initialize the billing system
    window.medicineBilling = new MedicineBilling();
    
    // Filter functionality
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('status');
    const dateFromFilter = document.getElementById('dateFrom');
    const dateToFilter = document.getElementById('dateTo');
    
    function filterBills() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const statusValue = statusFilter?.value || '';
        
        const rows = document.querySelectorAll('#billsTableBody tr');
        
        rows.forEach(row => {
            const billNumber = row.cells[0]?.textContent.toLowerCase() || '';
            const patientName = row.cells[1]?.textContent.toLowerCase() || '';
            const status = row.querySelector('.badge')?.textContent.toLowerCase() || '';
            
            const matchesSearch = billNumber.includes(searchTerm) || patientName.includes(searchTerm);
            const matchesStatus = !statusValue || status.includes(statusValue);
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Attach filter events
    if (searchInput) searchInput.addEventListener('input', filterBills);
    if (statusFilter) statusFilter.addEventListener('change', filterBills);
    if (dateFromFilter) dateFromFilter.addEventListener('change', filterBills);
    if (dateToFilter) dateToFilter.addEventListener('change', filterBills);
});

// Reinitialize when modal is shown
document.addEventListener('shown.bs.modal', function (event) {
    if (event.target.id === 'newBillModal') {
        console.log('Modal shown, reinitializing');
        if (!window.medicineBilling) {
            window.medicineBilling = new MedicineBilling();
        } else {
            // Reattach events to existing items
            const billItems = document.querySelectorAll('.bill-item');
            billItems.forEach(item => {
                window.medicineBilling.attachItemEvents(item);
            });
        }
    }
});
</script>
@endsection