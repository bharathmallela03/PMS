@extends('layouts.supplier')

@section('title', 'Pharmacists')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Pharmacist Directory</h1>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">All Registered Pharmacists</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pharmacists as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('d M, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center p-4">No pharmacists found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pharmacists->hasPages())
            <div class="card-footer bg-white">
                {{ $pharmacists->links() }}
            </div>
        @endif
    </div>
</div>
@endsection