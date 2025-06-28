@extends('layouts.modern')

@section('title', 'Manage Systems - Solar Eagles')
@section('page-title', 'Manage Systems')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">System Management</h2>
        <p class="text-muted">Manage all attendance systems in your organization</p>
    </div>
    <a href="{{ route('tenants.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create New System
    </a>
</div>

<div class="row">
    @if(isset($tenants) && $tenants->count() > 0)
        @foreach($tenants as $tenant)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ $tenant->name }}</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('tenants.show', $tenant) }}">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('tenants.switch', $tenant) }}">
                                <i class="fas fa-exchange-alt me-2"></i>Switch To
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#">
                                <i class="fas fa-trash me-2"></i>Delete
                            </a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-{{ $tenant->subscription_status === 'active' ? 'success' : ($tenant->subscription_status === 'trial' ? 'warning' : 'danger') }}">
                            {{ ucfirst($tenant->subscription_status) }}
                        </span>
                        @if($tenant->isTrialActive())
                            <span class="badge bg-info ms-1">
                                {{ $tenant->trial_ends_at->diffInDays(now()) }} days left
                            </span>
                        @endif
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-card-value" style="font-size: 24px;">{{ $tenant->users->count() }}</div>
                            <div class="stat-card-title" style="font-size: 12px;">Users</div>
                        </div>
                        <div class="col-4">
                            <div class="stat-card-value" style="font-size: 24px;">0</div>
                            <div class="stat-card-title" style="font-size: 12px;">Employees</div>
                        </div>
                        <div class="col-4">
                            <div class="stat-card-value" style="font-size: 24px;">0</div>
                            <div class="stat-card-title" style="font-size: 12px;">Records</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex gap-2">
                        <a href="{{ route('tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary flex-fill">
                            View Details
                        </a>
                        <a href="{{ route('tenants.switch', $tenant) }}" class="btn btn-sm btn-primary flex-fill">
                            Switch To
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h4>No Systems Created Yet</h4>
                <p class="text-muted mb-4">Create your first attendance management system to get started</p>
                <a href="{{ route('tenants.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create New System
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

@if(isset($tenants) && $tenants->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $tenants->links() }}
</div>
@endif

@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-card-value {
        font-weight: 700;
        color: var(--primary-purple);
    }

    .stat-card-title {
        color: #666;
        font-weight: 500;
    }
</style>
@endpush