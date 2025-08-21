{{-- resources/views/pharmacist/medicines/edit.blade.php --}}

@extends('layouts.pharmacist') {{-- Assuming you have a layout file --}}

@section('content')
<div class="container">
    <h1>Edit Medicine: {{ $medicine->name }}</h1>

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- The 'enctype' is important for file uploads --}}
    <form action="{{ route('pharmacist.medicines.update', $medicine->id) }}" method="POST" enctype="multipart/form-data">
        @csrf  {{-- CSRF Token --}}
        @method('PUT') {{-- Method spoofing for UPDATE request --}}

        <div class="row">
            {{-- Medicine Name --}}
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Medicine Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $medicine->name) }}" required>
            </div>

            {{-- Brand --}}
            <div class="col-md-6 mb-3">
                <label for="brand" class="form-label">Brand</label>
                <input type="text" class="form-control" id="brand" name="brand" value="{{ old('brand', $medicine->brand) }}" required>
            </div>

             {{-- Quantity --}}
            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity', $medicine->quantity) }}" required>
            </div>{{-- Generic Name --}}
            <div class="col-md-6 mb-3">
                <label for="generic_name" class="form-label">Generic Name (Optional)</label>
                <input type="text" class="form-control" id="generic_name" name="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}">
            </div>

            {{-- Category --}}
            <div class="col-md-6 mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $medicine->category) }}" required>
            </div>

            {{-- Cost Price --}}
            <div class="col-md-6 mb-3">
                <label for="cost_price" class="form-label">Cost Price ($)</label>
                <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price" value="{{ old('cost_price', $medicine->cost_price) }}" required>
            </div>

            {{-- Batch Number --}}
            <div class="col-md-6 mb-3">
                <label for="batch_number" class="form-label">Batch Number</label>
                <input type="text" class="form-control" id="batch_number" name="batch_number" value="{{ old('batch_number', $medicine->batch_number) }}" required>
            </div>
            
            {{-- Company --}}
            <div class="col-md-6 mb-3">
                <label for="company_id" class="form-label">Company</label>
                <select class="form-control" id="company_id" name="company_id" required>
                    <option value="">Select a Company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $medicine->company_id) == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Minimum Stock --}}
            <div class="col-md-6 mb-3">
                <label for="minimum_stock" class="form-label">Minimum Stock Alert</label>
                <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', $medicine->minimum_stock) }}">
            </div>

            

             {{-- Price --}}
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Price ($)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price', $medicine->price) }}" required>
            </div>
            
            {{-- Expiry Date --}}
            <div class="col-md-6 mb-3">
                <label for="expiry_date" class="form-label">Expiry Date</label>
                <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $medicine->expiry_date->format('Y-m-d')) }}" required>
            </div>
    {{-- Description --}}
            <div class="col-12 mb-3">
                <label for="description" class="form-label">Description (Optional)</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $medicine->description) }}</textarea>
            </div>
            {{-- Photo Upload --}}
            <div class="col-md-6 mb-3">
                <label for="photo" class="form-label">Medicine Photo</label>
                <input type="file" class="form-control" id="photo" name="photo">
                @if($medicine->photo)
                    <img src="{{ asset('storage/medicines/' . $medicine->photo) }}" alt="{{ $medicine->name }}" class="img-thumbnail mt-2" width="100">
                @endif
            </div>

            {{-- Add other fields like category, description, company_id, etc. --}}

        </div>

        <button type="submit" class="btn btn-primary">Update Medicine</button>
        <a href="{{ route('pharmacist.medicines') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection