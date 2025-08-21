@extends('layouts.pharmacist')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Import Medicine Inventory</h1>
        <a href="{{ route('pharmacist.medicines') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Back to Medicines
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{!! session('error') !!}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Upload Excel File</h6>
        </div>
        <div class="card-body">
            <p>
                Download the template file to ensure your data is in the correct format.
                All columns in the template are required. The 'expiry_date' column must be in a date format (e.g., YYYY-MM-DD).
            </p>
            <a href="{{ route('pharmacist.medicines.import.template') }}" class="btn btn-info mb-4">
                <i class="fas fa-download fa-sm"></i> Download Template
            </a>

            <form action="{{ route('pharmacist.medicines.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="import_file">Choose Excel File (.xlsx, .xls, .csv)</label>
                    <input type="file" class="form-control-file" id="import_file" name="import_file" required accept=".xlsx,.xls,.csv">
                    @error('import_file')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload fa-sm"></i> Upload and Import
                </button>
            </form>
        </div>
    </div>
</div>
@endsection