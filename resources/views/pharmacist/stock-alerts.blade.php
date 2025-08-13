@extends('layouts.pharmacist')

@section('title', 'Stock Alerts')

@push('styles')
<style>
    .stock-critical { color: #dc3545; font-weight: bold; }
    .stock-low { color: #fd7e14; }
    .table-responsive-sm {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Stock Alerts & Management</h1>
    </div>

    <div class="row">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Low Stock Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockMedicines->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Stock Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stockRequests->where('status', 'pending')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Action Required: Low Stock Medicines</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table table-bordered table-hover" id="lowStockTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Current Stock</th>
                            <th>Min. Stock Level</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockMedicines as $medicine)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <!-- <img src="{{ $medicine->photo_url }}" class="rounded me-3" alt="{{ $medicine->name }}" width="40" height="40"> -->
                                        <div>
                                            <strong>{{ $medicine->name }}</strong>
                                            <div class="text-muted small">{{ $medicine->brand }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="{{ $medicine->quantity == 0 ? 'stock-critical' : 'stock-low' }}">
                                        {{ $medicine->quantity }} Units
                                    </span>
                                </td>
                                <td>{{ $medicine->minimum_stock ?? 'N/A' }} Units</td>
                                <td>
                                    <button class="btn btn-sm btn-warning request-restock-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#restockModal"
                                            data-medicine-id="{{ $medicine->id }}"
                                            data-medicine-name="{{ $medicine->name }}">
                                        <i class="fas fa-plus-circle me-1"></i> Request Restock
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    <i class="fas fa-check-circle text-success fa-2x my-2"></i>
                                    <p class="mb-0">Great! No medicines are currently low on stock.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Stock Requests</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table table-bordered" id="requestHistoryTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Supplier</th>
                            <th>Requested Qty</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockRequests as $request)
                            <tr>
                                <td>{{ $request->medicine->name }}</td>
                                <td>{{ $request->supplier->name }}</td>
                                <td>{{ $request->requested_quantity }}</td>
                                <td>
                                    <span class="badge bg-{{ $request->status_color }}">{{ ucfirst($request->status) }}</span>
                                </td>
                                <td>{{ $request->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">You have not made any stock requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="restockModalLabel">Request Restock</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="restockForm">
        @csrf
        <div class="modal-body">
            <input type="hidden" id="medicine_id" name="medicine_id">
            <div class="mb-3">
                <label class="form-label">Medicine</label>
                <input type="text" id="medicine_name" class="form-control" readonly>
            </div>
            <div class="mb-3">
                <label for="supplier_id" class="form-label">Select Supplier</label>
                <select class="form-select" id="supplier_id" name="supplier_id" required>
                    <option value="">-- Choose a supplier --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity to Request</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes (Optional)</label>
                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Send Request</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 for nice popups --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const restockModal = new bootstrap.Modal(document.getElementById('restockModal'));
    const restockForm = document.getElementById('restockForm');
    const medicineIdInput = document.getElementById('medicine_id');
    const medicineNameInput = document.getElementById('medicine_name');

    // Handle opening the modal
    document.querySelectorAll('.request-restock-btn').forEach(button => {
        button.addEventListener('click', function () {
            const medicineId = this.dataset.medicineId;
            const medicineName = this.dataset.medicineName;

            medicineIdInput.value = medicineId;
            medicineNameInput.value = medicineName;
        });
    });

    // Handle form submission with AJAX
    restockForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch("{{ route('pharmacist.restock.request') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            restockModal.hide();
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                }).then(() => {
                    location.reload(); // Refresh the page to update lists
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message || 'Something went wrong!',
                });
            }
        })
        .catch(error => {
            restockModal.hide();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Could not send the request. Please try again.',
            });
            console.error('Error:', error);
        });
    });
});
</script>
@endpush