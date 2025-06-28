@extends('layouts.app')

@section('title', 'Dashboard - AttendanceTracker Pro')

@section('breadcrumb')
    <li><a href="{{ route('admin') }}">Dashboard</a></li>
    <li class="active">Overview</li>
@endsection

@section('topbar-actions')
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small">Last updated: {{ now()->format('M d, Y H:i') }}</span>
        <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card primary">
            <div class="stats-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="fw-bold mb-1">{{ $totalEmployees ?? '25' }}</h3>
            <p class="text-muted mb-0">Total Employees</p>
            <small class="text-success">
                <i class="fas fa-arrow-up"></i> 12% from last month
            </small>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card success">
            <div class="stats-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="fw-bold mb-1">{{ $presentToday ?? '18' }}</h3>
            <p class="text-muted mb-0">Present Today</p>
            <small class="text-success">
                <i class="fas fa-arrow-up"></i> 72% attendance rate
            </small>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card warning">
            <div class="stats-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <h3 class="fw-bold mb-1">{{ $lateToday ?? '3' }}</h3>
            <p class="text-muted mb-0">Late Arrivals</p>
            <small class="text-warning">
                <i class="fas fa-exclamation-triangle"></i> Needs attention
            </small>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card info">
            <div class="stats-icon info">
                <i class="fas fa-building"></i>
            </div>
            <h3 class="fw-bold mb-1">{{ $totalDepartments ?? '8' }}</h3>
            <p class="text-muted mb-0">Departments</p>
            <small class="text-info">
                <i class="fas fa-info-circle"></i> Active departments
            </small>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="card-modern">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-600">Recent Attendance Activity</h5>
                <a href="{{ route('attendance') }}" class="btn btn-sm btn-primary-modern">
                    <i class="fas fa-eye"></i> View All
                </a>
            </div>
            <div class="card-body-modern">
                <div class="table-modern">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Time</th>
                                <th>Action</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="profile-avatar me-3" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                            JD
                                        </div>
                                        <div>
                                            <div class="fw-500">John Doe</div>
                                            <small class="text-muted">Software Engineer</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ now()->format('H:i') }}</td>
                                <td><span class="badge bg-success">Check In</span></td>
                                <td><span class="badge bg-success">On Time</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="profile-avatar me-3" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                            SM
                                        </div>
                                        <div>
                                            <div class="fw-500">Sarah Miller</div>
                                            <small class="text-muted">HR Manager</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ now()->subMinutes(15)->format('H:i') }}</td>
                                <td><span class="badge bg-info">Break Out</span></td>
                                <td><span class="badge bg-primary">Active</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="profile-avatar me-3" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                            MJ
                                        </div>
                                        <div>
                                            <div class="fw-500">Mike Johnson</div>
                                            <small class="text-muted">Designer</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ now()->subMinutes(30)->format('H:i') }}</td>
                                <td><span class="badge bg-warning">Late Check In</span></td>
                                <td><span class="badge bg-warning">Late</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5 class="mb-0 fw-600">Quick Actions</h5>
            </div>
            <div class="card-body-modern">
                <div class="d-grid gap-3">
                    <a href="{{ route('employees.create') }}" class="btn btn-modern btn-primary-modern">
                        <i class="fas fa-user-plus"></i>
                        Add New Employee
                    </a>
                    
                    <a href="{{ route('attendance.export') }}" class="btn btn-modern btn-outline-success">
                        <i class="fas fa-download"></i>
                        Export Attendance
                    </a>
                    
                    <a href="{{ route('schedules.create') }}" class="btn btn-modern btn-outline-info">
                        <i class="fas fa-calendar-plus"></i>
                        Create Schedule
                    </a>
                    
                    <a href="{{ route('holiday') }}" class="btn btn-modern btn-outline-warning">
                        <i class="fas fa-calendar-times"></i>
                        Manage Holidays
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-modern mt-4">
            <div class="card-header-modern">
                <h5 class="mb-0 fw-600">System Status</h5>
            </div>
            <div class="card-body-modern">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Database</span>
                    <span class="badge bg-success">Online</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>NFC Scanner</span>
                    <span class="badge bg-success">Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>GPS Tracking</span>
                    <span class="badge bg-success">Enabled</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Backup Status</span>
                    <span class="badge bg-info">Auto</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5 class="mb-0 fw-600">Weekly Overview</h5>
            </div>
            <div class="card-body-modern">
                <canvas id="weeklyChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5 class="mb-0 fw-600">Department Attendance</h5>
            </div>
            <div class="card-body-modern">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Engineering</span>
                        <span class="fw-600">85%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar" style="width: 85%; background: var(--primary-gradient);"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Human Resources</span>
                        <span class="fw-600">92%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-success" style="width: 92%;"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Marketing</span>
                        <span class="fw-600">78%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-warning" style="width: 78%;"></div>
                    </div>
                </div>
                
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Sales</span>
                        <span class="fw-600">89%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-info" style="width: 89%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Weekly attendance chart
const ctx = document.getElementById('weeklyChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Attendance Rate',
            data: [85, 92, 78, 88, 82, 65, 45],
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});
</script>
@endsection

