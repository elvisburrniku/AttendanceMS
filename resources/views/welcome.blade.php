@extends('layouts.modern')

@section('title', 'Dashboard - Solar Eagles')
@section('page-title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <p class="text-muted mb-4">Monitoring de Asistencia y Marcaciones de Empleados</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <!-- Total Employees Card -->
    <div class="stat-card purple">
        <div class="stat-card-header">
            <div class="stat-card-title">Total Employees</div>
            <div class="stat-card-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['total_employees'] ?? 13 }}</div>
        <div class="stat-card-change">{{ $stats['employees_change'] ?? '+2 this month' }}</div>
    </div>

    <!-- Present Today Card -->
    <div class="stat-card blue">
        <div class="stat-card-header">
            <div class="stat-card-title">Present Today</div>
            <div class="stat-card-icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['present_today'] ?? 0 }}</div>
        <div class="stat-card-change">{{ $stats['attendance_rate'] ?? '0% attendance rate' }}</div>
    </div>

    <!-- Late Arrivals Card -->
    <div class="stat-card orange">
        <div class="stat-card-header">
            <div class="stat-card-title">Late Arrivals</div>
            <div class="stat-card-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['late_arrivals'] ?? 0 }}</div>
        <div class="stat-card-change">{{ $stats['late_change'] ?? 'No late arrivals today' }}</div>
    </div>

    <!-- Departments Card -->
    <div class="stat-card green">
        <div class="stat-card-header">
            <div class="stat-card-title">Departments</div>
            <div class="stat-card-icon">
                <i class="fas fa-building"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['departments'] ?? 13 }}</div>
        <div class="stat-card-change">{{ $stats['dept_change'] ?? 'Active departments' }}</div>
    </div>

    <!-- Working Hours Card -->
    <div class="stat-card red">
        <div class="stat-card-header">
            <div class="stat-card-title">Working Hours</div>
            <div class="stat-card-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['working_hours'] ?? '0%' }}</div>
        <div class="stat-card-change">{{ $stats['hours_change'] ?? 'Average completion' }}</div>
    </div>

    <!-- Check Ins Today Card -->
    <div class="stat-card purple">
        <div class="stat-card-header">
            <div class="stat-card-title">Check Ins Today</div>
            <div class="stat-card-icon">
                <i class="fas fa-sign-in-alt"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['checkins_today'] ?? 12 }}</div>
        <div class="stat-card-change">{{ $stats['checkin_change'] ?? 'Total check-ins' }}</div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="row mt-5">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Attendance Activity</h5>
                <button class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-download me-1"></i>Export
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Action</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($recent_activity) && count($recent_activity) > 0)
                                @foreach($recent_activity as $activity)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                {{ substr($activity['name'], 0, 2) }}
                                            </div>
                                            {{ $activity['name'] }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $activity['action'] === 'Check In' ? 'success' : 'warning' }}">
                                            {{ $activity['action'] }}
                                        </span>
                                    </td>
                                    <td>{{ $activity['time'] }}</td>
                                    <td>{{ $activity['location'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $activity['status'] === 'On Time' ? 'success' : 'danger' }}">
                                            {{ $activity['status'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-clock fa-2x mb-3 d-block"></i>
                                    No attendance records for today
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Add New Employee
                    </a>
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar me-2"></i>Generate Report
                    </a>
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fas fa-cog me-2"></i>System Settings
                    </a>
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fas fa-download me-2"></i>Export Data
                    </a>
                </div>
            </div>
        </div>

        @auth
            @if(auth()->user()->is_super_admin || auth()->user()->role === 'admin')
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">System Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenants.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Create New System
                        </a>
                        <a href="{{ route('tenants.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-building me-2"></i>Manage Systems
                        </a>
                    </div>
                </div>
            </div>
            @endif
        @endauth
    </div>
</div>

@if(session('success'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show" role="alert">
        <div class="toast-header">
            <i class="fas fa-check-circle text-success me-2"></i>
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            {{ session('success') }}
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
    }

    .card-header {
        background: white;
        border-bottom: 1px solid #e9ecef;
        border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #666;
        font-size: 14px;
    }

    .table td {
        border-top: 1px solid #f8f9fa;
        vertical-align: middle;
    }

    .badge {
        font-size: 11px;
        padding: 4px 8px;
    }

    .btn {
        border-radius: 6px;
        font-weight: 500;
    }

    .btn-primary {
        background: var(--primary-purple);
        border-color: var(--primary-purple);
    }

    .btn-primary:hover {
        background: var(--dark-purple);
        border-color: var(--dark-purple);
    }

    .btn-outline-primary {
        color: var(--primary-purple);
        border-color: var(--primary-purple);
    }

    .btn-outline-primary:hover {
        background: var(--primary-purple);
        border-color: var(--primary-purple);
    }
</style>
@endpush