@extends('layouts.supplier') {{-- Or your main supplier layout file --}}

@section('title', 'Stock Requests')

@push('styles')
<style>
    .table-responsive-sm {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .badge-pending { background-color: #ffc107; color: #000; }
    .badge-fulfilled { background-color: #198754; }
    .badge-rejected { background-color: #dc3545; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pharmacist Stock Requests</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Incoming Requests</h6>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <div class="mb-3">
                <form action="{{ route('supplier.stock-requests') }}" method="GET" class="d-flex">
                    <select name="status" class="form-select me-2" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>
            </div>

            <div class="table-responsive-sm">
                <table class="table table-bordered table-hover" id="requestsTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Pharmacist</th>
                            <th>Medicine</th>
                            <th>Requested Qty</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockRequests as $request)
                            <tr>
                                <td>{{ $request->pharmacist->user->name ?? 'N/A' }}</td>
                                <td>{{ $request->medicine->name }}</td>
                                <td>{{ $request->requested_quantity }}</td>
                                <td>
                                    <span class="badge badge-{{$request->status}}">{{ ucfirst($request->status) }}</span>
                                </td>
                                <td>{{ $request->created_at->format('d M Y') }}</td>
                                <td>
                                    @if($request->status == 'pending')
                                        <button class="btn btn-sm btn-success action-btn" data-id="{{ $request->id }}" data-action="fulfill">
                                            <i class="fas fa-check"></i> Fulfill
                                        </button>
                                        <button class="btn btn-sm btn-danger action-btn" data-id="{{ $request->id }}" data-action="reject">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    @else
                                        <span class="text-muted">Processed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No stock requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $stockRequests->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Action Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="actionModalLabel">Process Request</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="actionForm" method="POST">
        @csrf
        <div class="modal-body">
            <input type="hidden" id="action_type" name="action">
            <p>You are about to <strong id="action-text"></strong> this request. Are you sure?</p>
            <div class="mb-3">
                <label for="supplier_notes" class="form-label">Notes (Optional)</label>
                <textarea class="form-control" id="supplier_notes" name="supplier_notes" rows="3" placeholder="Add notes for the pharmacist..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="confirm-action-btn">Confirm</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const actionModal = new bootstrap.Modal(document.getElementById('actionModal'));
    const actionForm = document.getElementById('actionForm');
    const actionTypeInput = document.getElementById('action_type');
    const actionText = document.getElementById('action-text');
    const confirmBtn = document.getElementById('confirm-action-btn');

    document.querySelectorAll('.action-btn').forEach(button => {
        button.addEventListener('click', function () {
            const requestId = this.dataset.id;
            const action = this.dataset.action;

            const url = `/supplier/stock-requests/${requestId}/fulfill`;
            actionForm.setAttribute('action', url);
            
            actionTypeInput.value = action;
            actionText.textContent = action;

            if(action === 'fulfill') {
                confirmBtn.className = 'btn btn-success';
                confirmBtn.textContent = 'Yes, Fulfill';
            } else {
                confirmBtn.className = 'btn btn-danger';
                confirmBtn.textContent = 'Yes, Reject';
            }

            actionModal.show();
        });
    });
});
</script>
@endpush
