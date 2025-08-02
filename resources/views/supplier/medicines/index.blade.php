@extends('layouts.supplier') {{-- Or your main supplier layout file, e.g., layouts.app --}}

@section('title', 'My Medicines')

@push('styles')
<style>
    .table-responsive-sm {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .card-header .btn {
        float: right;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Medicine Inventory</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary d-inline-block">Medicines List</h6>
            <a href="{{ route('supplier.medicines.bulk-upload') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-file-excel fa-sm text-white-50"></i> Bulk Upload
                </a>
            <a href="{{ route('supplier.medicines.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add New Medicine
            </a>
        </div>
        <div class="card-body">
            <!-- Search Form -->
            <div class="mb-3">
                <form action="{{ route('supplier.medicines') }}" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search by name or brand..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>

            <div class="table-responsive-sm">
                <table class="table table-bordered table-hover" id="medicinesTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Company</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicines as $medicine)
                            <tr>
                                <td>{{ $medicine->name }}</td>
                                <td>{{ $medicine->brand }}</td>
                                <td>{{ $medicine->category }}</td>
                                <td>${{ number_format($medicine->price, 2) }}</td>
                                <td>{{ $medicine->company->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('supplier.medicines.edit', $medicine->id) }}" class="btn btn-sm btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $medicine->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $medicine->id }}" action="{{ route('supplier.medicines.delete', $medicine->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No medicines found. <a href="{{ route('supplier.medicines.create') }}">Add one now!</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $medicines->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 for confirmation dialogs --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const medicineId = this.dataset.id;
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + medicineId).submit();
                }
            })
        });
    });
});
</script>
@endpush
