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