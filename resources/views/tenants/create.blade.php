@extends('layouts.modern')

@section('title', 'Create New System - Solar Eagles')
@section('page-title', 'Create New System')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Create New Attendance System</h4>
                <p class="text-muted mb-0">Set up a new isolated attendance management system</p>
            </div>
            <div class="card-body">
                <form action="{{ route('tenants.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">System Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="e.g., HR Department, Branch Office A"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">This will be used to identify your system</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Admin Email *</label>
                                <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                       id="admin_email" name="admin_email" value="{{ old('admin_email') }}" 
                                       placeholder="admin@company.com"
                                       required>
                                @error('admin_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Admin user will be created with this email</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="admin_password" class="form-label">Admin Password *</label>
                        <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                               id="admin_password" name="admin_password" 
                               placeholder="Minimum 8 characters"
                               required>
                        @error('admin_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- System Features -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">What's Included:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Separate database</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Employee management</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Attendance tracking</li>
                                        <li><i class="fas fa-check text-success me-2"></i>NFC support</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Department management</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Reporting tools</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Export capabilities</li>
                                        <li><i class="fas fa-check text-success me-2"></i>14-day free trial</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trial Information -->
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Free Trial:</strong> Your new system will start with a 14-day free trial. 
                        After the trial period, you'll need to subscribe to continue using the system.
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('tenants.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Systems
                        </a>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create System
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .form-control:focus {
        border-color: var(--primary-purple);
        box-shadow: 0 0 0 0.2rem rgba(76, 53, 117, 0.25);
    }

    .btn-primary {
        background: var(--primary-purple);
        border-color: var(--primary-purple);
    }

    .btn-primary:hover {
        background: var(--dark-purple);
        border-color: var(--dark-purple);
    }

    .text-success {
        color: #28a745 !important;
    }
</style>
@endpush