@extends('layouts.supplier')

@section('title', 'Bulk Upload Medicines')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bulk Upload Medicines</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Import from Excel</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <h5 class="alert-heading">Instructions</h5>
                <p>Please follow these steps to bulk upload medicines:</p>
                <ol>
                    <li>Download the template file using the button below.</li>
                    <li>Fill in the medicine details. Do not change the column headers.</li>
                    <li>The required columns are: <strong>name, brand, generic_name, category, price, company, quantity</strong>.</li>
                    <li>Save the file and upload it using the form below.</li>
                </ol>
                <a href="{{ route('supplier.medicines.download-template') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-download me-2"></i>Download Template
                </a>
            </div>

            @if (session('import_errors'))
                <div class="alert alert-danger">
                    <h5 class="alert-heading">Import Errors</h5>
                    <p>The following rows had errors and were not imported:</p>
                    <ul>
                        @foreach (session('import_errors') as $failure)
                            <li>
                                <strong>Row {{ $failure->row() }}:</strong> {{ $failure->errors()[0] }}
                                (Value provided: '{{ $failure->values()[$failure->attribute()] ?? '' }}')
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('supplier.medicines.process-bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="medicines_file" class="form-label">Select Excel File</label>
                    <input class="form-control" type="file" id="medicines_file" name="medicines_file" required accept=".xlsx, .xls, .csv">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload me-2"></i>Upload and Import
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
