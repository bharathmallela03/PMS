@extends('layouts.pharmacist')

@section('title', 'View Bill ' . $bill->bill_number)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">
            <a href="{{ route('pharmacist.billing') }}" class="text-decoration-none text-muted">Bills</a> / 
            {{ $bill->bill_number }}
        </h1>
        <a href="{{ route('pharmacist.billing.print', $bill->id) }}" class="btn btn-primary" target="_blank">
            <i class="fas fa-print me-2"></i>Print / Download PDF
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h2 class="mb-0">INVOICE</h2>
                    <p class="text-muted">#{{ $bill->bill_number }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge fs-6 {{ $bill->status_badge }}">{{ ucfirst($bill->status) }}</span>
                </div>
            </div>

            <hr class="mb-4">

            <div class="row mb-5">
                <div class="col-md-6">
                    <h5 class="text-muted">FROM</h5>
                    <p class="mb-1"><strong>{{ $bill->pharmacist->pharmacy_name ?? 'PharmaCare' }}</strong></p>
                    <p class="mb-1">{{ $bill->pharmacist->name }}</p>
                    <p class="mb-1">{{ $bill->pharmacist->address ?? '123 Pharmacy Lane, Health City' }}</p>
                    <p class="mb-1">Email: {{ $bill->pharmacist->email }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5 class="text-muted">TO</h5>
                    <p class="mb-1"><strong>{{ $bill->patient_name }}</strong></p>
                    <p class="mb-1">{{ $bill->patient_address }}</p>
                    <p class="mb-1">Phone: {{ $bill->patient_phone }}</p>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Item</th>
                            <th scope="col" class="text-end">Quantity</th>
                            <th scope="col" class="text-end">Unit Price</th>
                            <th scope="col" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bill->billItems as $index => $item)
                        <tr>
                            <th scope="row">{{ $index + 1 }}</th>
                            <td>{{ $item->medicine->name }}</td>
                            <td class="text-end">{{ $item->quantity }}</td>
                            <td class="text-end">&#8377;{{ number_format($item->price, 2) }}</td>
                            <td class="text-end">&#8377;{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Subtotal
                            <span>&#8377;{{ number_format($bill->subtotal, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Discount ({{ $bill->discount_percentage }}%)
                            <span>- &#8377;{{ number_format($bill->discount_amount, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                            <strong>Grand Total</strong>
                            <strong>&#8377;{{ number_format($bill->total_amount, 2) }}</strong>
                        </li>
                    </ul>
                </div>
            </div>

            @if($bill->notes)
                <div class="mt-5">
                    <h5>Notes</h5>
                    <p class="text-muted">{{ $bill->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection