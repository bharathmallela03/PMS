
@extends('layouts.pharmacist')


@section('title', 'Companies Management')

@section('styles')
<style>
    :root {
        --primary-color: #6366f1;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --dark-color: #1f2937;
        --light-bg: #f8fafc;
        --border-color: #e5e7eb;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    body {
        background-color: var(--light-bg);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #8b5cf6 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: var(--shadow);
    }

    .page-title {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .breadcrumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        margin-bottom: 0;
    }

    .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: white;
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: var(--shadow);
        background: white;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-bottom: none;
        border-radius: 16px 16px 0 0;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, #8b5cf6 100%);
        box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(99, 102, 241, 0.4);
    }

    .btn-outline-primary {
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        background: transparent;
    }

    .btn-outline-primary:hover {
        background: var(--primary-color);
        color: white;
    }

    .btn-outline-secondary {
        border: 2px solid #6b7280;
        color: #6b7280;
        background: transparent;
    }

    .btn-outline-secondary:hover {
        background: #6b7280;
        color: white;
    }

    .form-control, .form-select {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 0.75rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    .table {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow);
    }

    .table-dark {
        background: linear-gradient(135deg, var(--dark-color) 0%, #374151 100%);
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(99, 102, 241, 0.02);
    }

    .table tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.05);
        transform: scale(1.001);
        transition: all 0.2s ease;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.8rem;
    }

    .bg-success {
        background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%) !important;
    }

    .bg-danger {
        background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%) !important;
    }

    .company-card {
        transition: all 0.3s ease;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .company-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .company-logo {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        object-fit: cover;
        border: 3px solid var(--border-color);
    }

    .search-box {
        position: relative;
    }

    .search-box .form-control {
        padding-left: 3rem;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
    }

    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: var(--shadow);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">🏢 Companies Management</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('pharmacist.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Companies</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">{{ $companies->count() }}</div>
                <div>Total Companies</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">{{ $companies->where('is_active', true)->count() }}</div>
                <div>Active Companies</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">{{ $companies->where('is_active', false)->count() }}</div>
                <div>Inactive Companies</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">{{ $companies->where('created_at', '>=', now()->subMonth())->count() }}</div>
                <div>New This Month</div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row">
            <div class="col-md-6">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" id="searchCompanies" placeholder="Search companies by name, email, or phone...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="sortBy">
                    <option value="name">Sort by Name</option>
                    <option value="created_at">Sort by Date</option>
                    <option value="email">Sort by Email</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Companies Grid -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">🏭 Company Directory</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                        <i class="fas fa-plus"></i> Add New Company
                    </button>
                </div>
                <div class="card-body">
                    @if($companies->count() > 0)
                        <div class="row" id="companiesGrid">
                            @foreach($companies as $company)
                            <div class="col-lg-4 col-md-6 mb-4 company-item" 
                                 data-name="{{ strtolower($company->name) }}" 
                                 data-email="{{ strtolower($company->email) }}" 
                                 data-phone="{{ $company->phone }}"
                                 data-status="{{ $company->is_active ? 'active' : 'inactive' }}">
                                <div class="company-card card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            @if($company->logo)
                                                <img src="{{ asset('storage/companies/' . $company->logo) }}" 
                                                     alt="{{ $company->name }}" class="company-logo me-3">
                                            @else
                                                <div class="company-logo me-3 d-flex align-items-center justify-content-center bg-light">
                                                    <i class="fas fa-building text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-1">{{ $company->name }}</h5>
                                                <span class="badge {{ $company->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $company->is_active ? '✅ Active' : '❌ Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="company-info">
                                            <div class="info-item mb-2">
                                                <i class="fas fa-envelope text-primary me-2"></i>
                                                <small>{{ $company->email }}</small>
                                            </div>
                                            <div class="info-item mb-2">
                                                <i class="fas fa-phone text-success me-2"></i>
                                                <small>{{ $company->phone }}</small>
                                            </div>
                                            @if($company->address)
                                            <div class="info-item mb-2">
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                <small>{{ Str::limit($company->address, 50) }}</small>
                                            </div>
                                            @endif
                                            @if($company->website)
                                            <div class="info-item mb-2">
                                                <i class="fas fa-globe text-info me-2"></i>
                                                <small><a href="{{ $company->website }}" target="_blank" class="text-decoration-none">{{ $company->website }}</a></small>
                                            </div>
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                {{ $company->created_at->format('M d, Y') }}
                                            </small>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="viewCompany({{ $company->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="editCompany({{ $company->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-5x text-muted mb-3"></i>
                            <h4 class="text-muted">No Companies Found</h4>
                            <p class="text-muted">Start by adding your first company to the system.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                                <i class="fas fa-plus"></i> Add First Company
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Company Modal -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-labelledby="addCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCompanyModalLabel">🏢 Add New Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pharmacist.companies.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Company Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website" placeholder="https://...">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="logo" class="form-label">Company Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Company
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchCompanies');
    const statusFilter = document.getElementById('statusFilter');
    const sortBy = document.getElementById('sortBy');
    const companyItems = document.querySelectorAll('.company-item');

    function filterCompanies() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        companyItems.forEach(item => {
            const name = item.dataset.name;
            const email = item.dataset.email;
            const phone = item.dataset.phone;
            const status = item.dataset.status;

            const matchesSearch = name.includes(searchTerm) || 
                                email.includes(searchTerm) || 
                                phone.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue;

            if (matchesSearch && matchesStatus) {
                item.style.display = 'block';
                item.classList.add('fadeIn');
            } else {
                item.style.display = 'none';
                item.classList.remove('fadeIn');
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterCompanies);
    if (statusFilter) statusFilter.addEventListener('change', filterCompanies);
});

// Company actions
function viewCompany(companyId) {
    window.location.href = `/pharmacist/companies/${companyId}`;
}

function editCompany(companyId) {
    window.location.href = `/pharmacist/companies/${companyId}/edit`;
}

// Form validation
document.querySelector('#addCompanyModal form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    
    if (!name || !email) {
        e.preventDefault();
        alert('Please fill in all required fields (Name and Email).');
        return false;
    }
});
</script>
@endsection