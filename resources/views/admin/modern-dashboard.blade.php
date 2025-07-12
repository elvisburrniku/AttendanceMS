@extends('layouts.modern-master')

@section('title', 'Dashboard - AttendanceFlow')
@section('page-title', 'Dashboard')

@section('page-actions')
    <a href="{{ route('employees.create') }}" class="action-btn">
        <i class="fas fa-plus"></i>
        Add Employee
    </a>
    <a href="{{ route('attendance') }}" class="action-btn btn-secondary">
        <i class="fas fa-chart-bar"></i>
        View Reports
    </a>
@endsection

@section('css')
<style>
    .dashboard-hero {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        margin-bottom: var(--spacing-xl);
        border: 1px solid rgba(102, 126, 234, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    
    .hero-content {
        position: relative;
        z-index: 1;
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-xl);
    }
    
    .quick-action-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid var(--border-color);
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    
    .quick-action-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-xl);
        text-decoration: none;
        color: inherit;
    }
    
    .quick-action-icon {
        width: 60px;
        height: 60px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin: 0 auto var(--spacing-md);
        background: var(--primary-gradient);
    }
    
    .quick-action-card:nth-child(2) .quick-action-icon {
        background: var(--success-gradient);
    }
    
    .quick-action-card:nth-child(3) .quick-action-icon {
        background: var(--warning-gradient);
    }
    
    .quick-action-card:nth-child(4) .quick-action-icon {
        background: var(--danger-gradient);
    }
    
    .activity-feed {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .activity-item {
        display: flex;
        align-items: flex-start;
        padding: var(--spacing-md);
        border-left: 3px solid var(--border-color);
        margin-bottom: var(--spacing-md);
        background: var(--bg-card);
        border-radius: 0 var(--radius-md) var(--radius-md) 0;
        transition: all 0.3s ease;
    }
    
    .activity-item:hover {
        transform: translateX(8px);
        border-left-color: #667eea;
        box-shadow: var(--shadow-md);
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: var(--spacing-md);
        flex-shrink: 0;
        color: white;
        font-size: 0.875rem;
    }
    
    .activity-check-in {
        background: var(--success-gradient);
    }
    
    .activity-check-out {
        background: var(--warning-gradient);
    }
    
    .activity-late {
        background: var(--danger-gradient);
    }
    
    .activity-break {
        background: var(--secondary-gradient);
    }
    
    .chart-container {
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-primary);
        border-radius: var(--radius-md);
        border: 2px dashed var(--border-color);
    }
    
    .attendance-heatmap {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        margin-top: var(--spacing-md);
    }
    
    .heatmap-day {
        aspect-ratio: 1;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .heatmap-day:hover {
        transform: scale(1.1);
        z-index: 1;
    }
    
    .heatmap-excellent {
        background: var(--success-gradient);
    }
    
    .heatmap-good {
        background: var(--warning-gradient);
    }
    
    .heatmap-poor {
        background: var(--danger-gradient);
    }
    
    .heatmap-absent {
        background: #e2e8f0;
        color: var(--text-secondary);
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    <!-- Hero Section -->
    <div class="dashboard-hero">
        <div class="hero-content">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-6 fw-bold mb-3">Welcome back, {{ Auth::user()->name ?? 'Administrator' }}! 👋</h1>
                    <p class="lead mb-4">Here's what's happening with your team today. {{ \Carbon\Carbon::now()->format('l, F j, Y') }}</p>
                    <div class="d-flex gap-3">
                        <span class="badge badge-modern badge-success">
                            <i class="fas fa-check me-1"></i>
                            System Online
                        </span>
                        <span class="badge badge-modern badge-primary">
                            <i class="fas fa-users me-1"></i>
                            {{ $totalEmployees ?? '0' }} Active Employees
                        </span>
                        <span class="badge badge-modern badge-warning">
                            <i class="fas fa-clock me-1"></i>
                            {{ $todayAttendance ?? '0' }} Present Today
                        </span>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="hero-stats">
                        <div class="text-gradient display-4 fw-bold">98.5%</div>
                        <small class="text-muted">Attendance Rate This Month</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <h3 class="stat-value">{{ $totalEmployees ?? '156' }}</h3>
                    <p class="stat-label">Total Employees</p>
                    <p class="stat-change positive">
                        <i class="fas fa-arrow-up me-1"></i>
                        +12% from last month
                    </p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <h3 class="stat-value">{{ $presentToday ?? '142' }}</h3>
                    <p class="stat-label">Present Today</p>
                    <p class="stat-change positive">
                        <i class="fas fa-arrow-up me-1"></i>
                        +5% from yesterday
                    </p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <h3 class="stat-value">{{ $lateToday ?? '8' }}</h3>
                    <p class="stat-label">Late Arrivals</p>
                    <p class="stat-change negative">
                        <i class="fas fa-arrow-down me-1"></i>
                        -23% from yesterday
                    </p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <h3 class="stat-value">{{ $onLeave ?? '6' }}</h3>
                    <p class="stat-label">On Leave</p>
                    <p class="stat-change positive">
                        <i class="fas fa-minus me-1"></i>
                        Normal range
                    </p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-plane-departure"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('employees.create') }}" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h5>Add Employee</h5>
            <p class="text-muted mb-0">Register a new team member</p>
        </a>
        
        <a href="{{ route('attendance') }}" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fas fa-edit"></i>
            </div>
            <h5>Manual Entry</h5>
            <p class="text-muted mb-0">Record attendance manually</p>
        </a>
        
        <a href="{{ route('attendance.export') }}" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fas fa-file-download"></i>
            </div>
            <h5>Export Report</h5>
            <p class="text-muted mb-0">Download attendance data</p>
        </a>
        
        <a href="{{ route('nfc.scanner') }}" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <h5>NFC Scanner</h5>
            <p class="text-muted mb-0">Mobile check-in system</p>
        </a>
    </div>
    
    <!-- Main Content Grid -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock me-2"></i>
                        Recent Activity
                    </h3>
                </div>
                <div class="card-body">
                    <div class="activity-feed">
                        <div class="activity-item">
                            <div class="activity-icon activity-check-in">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Sarah Johnson checked in</h6>
                                <p class="text-muted mb-1">Marketing Department • On time</p>
                                <small class="text-muted">2 minutes ago</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon activity-late">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Mike Chen arrived late</h6>
                                <p class="text-muted mb-1">IT Department • 15 minutes late</p>
                                <small class="text-muted">8 minutes ago</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon activity-break">
                                <i class="fas fa-coffee"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Emma Davis started break</h6>
                                <p class="text-muted mb-1">Sales Department • 15-minute break</p>
                                <small class="text-muted">12 minutes ago</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon activity-check-out">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Alex Rodriguez checked out</h6>
                                <p class="text-muted mb-1">HR Department • Early departure approved</p>
                                <small class="text-muted">25 minutes ago</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon activity-check-in">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Lisa Park checked in</h6>
                                <p class="text-muted mb-1">Finance Department • On time</p>
                                <small class="text-muted">1 hour ago</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('attendance') }}" class="btn-modern btn-outline">
                            View All Activity
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Attendance Overview -->
        <div class="col-lg-4">
            <div class="modern-card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-week me-2"></i>
                        This Week
                    </h3>
                </div>
                <div class="card-body">
                    <div class="attendance-heatmap">
                        <div class="heatmap-day heatmap-excellent">M<br>28</div>
                        <div class="heatmap-day heatmap-excellent">T<br>29</div>
                        <div class="heatmap-day heatmap-good">W<br>30</div>
                        <div class="heatmap-day heatmap-excellent">T<br>31</div>
                        <div class="heatmap-day heatmap-good">F<br>01</div>
                        <div class="heatmap-day heatmap-absent">S<br>02</div>
                        <div class="heatmap-day heatmap-absent">S<br>03</div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Average Check-in</span>
                            <strong>8:42 AM</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Average Check-out</span>
                            <strong>5:18 PM</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Hours</span>
                            <strong>42.5 hrs</strong>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Department Overview -->
            <div class="modern-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building me-2"></i>
                        Departments
                    </h3>
                </div>
                <div class="card-body">
                    <div class="department-list">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">IT Department</h6>
                                <small class="text-muted">25 employees</small>
                            </div>
                            <span class="badge badge-modern badge-success">96%</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">Marketing</h6>
                                <small class="text-muted">18 employees</small>
                            </div>
                            <span class="badge badge-modern badge-success">94%</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">Sales</h6>
                                <small class="text-muted">32 employees</small>
                            </div>
                            <span class="badge badge-modern badge-warning">89%</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">HR</h6>
                                <small class="text-muted">8 employees</small>
                            </div>
                            <span class="badge badge-modern badge-success">100%</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Finance</h6>
                                <small class="text-muted">12 employees</small>
                            </div>
                            <span class="badge badge-modern badge-success">92%</span>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('departments.index') }}" class="btn-modern btn-outline">
                            Manage Departments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Attendance Trends
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div class="text-center">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Attendance Chart</h5>
                            <p class="text-muted">Interactive charts will be displayed here</p>
                            <button class="btn-modern" onclick="loadChart()">
                                <i class="fas fa-sync-alt me-2"></i>
                                Load Chart Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="modern-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Alerts & Issues
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning border-0 rounded-modern mb-3">
                        <i class="fas fa-clock me-2"></i>
                        <strong>3 employees</strong> have exceeded overtime limits this week
                    </div>
                    
                    <div class="alert alert-info border-0 rounded-modern mb-3">
                        <i class="fas fa-calendar me-2"></i>
                        <strong>Holiday reminder:</strong> Christmas Day - Dec 25
                    </div>
                    
                    <div class="alert alert-success border-0 rounded-modern">
                        <i class="fas fa-check me-2"></i>
                        All biometric devices are online and functioning
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function loadChart() {
        showToast('Chart data loading...', 'info');
        
        // Simulate chart loading
        setTimeout(() => {
            showToast('Chart loaded successfully!', 'success');
        }, 2000);
    }
    
    // Real-time updates
    function refreshDashboardData() {
        // Add AJAX calls to refresh dashboard data
        console.log('Refreshing dashboard data...');
    }
    
    // Set up real-time refresh
    window.refreshData = refreshDashboardData;
    
    // Interactive heatmap
    $(document).ready(function() {
        $('.heatmap-day').on('click', function() {
            const date = $(this).text().split('\n')[1];
            showToast(`Viewing attendance for day ${date}`, 'info');
        });
        
        // Animate cards on load
        $('.stat-card, .modern-card').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
            $(this).addClass('animate-fade-in-up');
        });
    });
</script>
@endsection