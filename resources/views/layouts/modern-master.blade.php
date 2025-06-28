<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>@yield('title', 'AttendanceFlow - Modern Attendance Management')</title>
    <meta content="Modern Attendance Management System" name="description">
    <meta content="AttendanceFlow" name="author">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Modern Design System -->
    <link href="{{ URL::asset('css/modern-style.css') }}" rel="stylesheet" type="text/css">
    
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.min.css" rel="stylesheet">
    
    @yield('css')
    
    <style>
        /* Custom overrides for this specific layout */
        .page-wrapper {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
        }
        
        .page-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="1000" height="1000" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }
        
        .content-overlay {
            position: relative;
            z-index: 1;
        }
        
        /* Sidebar animations */
        .nav-link {
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 20px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%) scale(0);
            transition: transform 0.3s ease;
        }
        
        .nav-link.active::after,
        .nav-link:hover::after {
            transform: translateY(-50%) scale(1);
        }
        
        /* Enhanced card hover effects */
        .modern-card {
            position: relative;
            overflow: hidden;
        }
        
        .modern-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.6s ease;
        }
        
        .modern-card:hover::before {
            left: 100%;
        }
        
        /* Notification badge */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <div class="content-overlay">
            <div id="wrapper" class="{{ request()->cookie('sidebar_collapsed') ? 'sidebar-collapsed' : '' }}">
                <!-- Sidebar -->
                <div class="modern-sidebar">
                    <div class="sidebar-logo">
                        <h2><i class="fas fa-user-clock me-2"></i>AttendanceFlow</h2>
                    </div>
                    
                    <nav class="sidebar-nav">
                        <div class="nav-item">
                            <a href="{{ route('modern.dashboard') }}" class="nav-link {{ request()->routeIs('modern.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a href="{{ route('modern.employees') }}" class="nav-link {{ request()->routeIs('modern.employees') ? 'active' : '' }}">
                                <i class="fas fa-users"></i>
                                <span>Employees</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a href="{{ route('admin.attendances') }}" class="nav-link {{ request()->routeIs('admin.attendances') ? 'active' : '' }}">
                                <i class="fas fa-clock"></i>
                                <span>Attendance</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a href="{{ route('admin.departments') }}" class="nav-link {{ request()->routeIs('admin.departments') ? 'active' : '' }}">
                                <i class="fas fa-building"></i>
                                <span>Departments</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a href="{{ route('admin.positions') }}" class="nav-link {{ request()->routeIs('admin.positions') ? 'active' : '' }}">
                                <i class="fas fa-briefcase"></i>
                                <span>Positions</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a href="{{ route('admin.schedules') }}" class="nav-link {{ request()->routeIs('admin.schedules') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Schedules</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a href="{{ route('admin.holidays') }}" class="nav-link {{ request()->routeIs('admin.holidays') ? 'active' : '' }}">
                                <i class="fas fa-calendar-day"></i>
                                <span>Holidays</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a href="{{ route('admin.leaves') }}" class="nav-link {{ request()->routeIs('admin.leaves') ? 'active' : '' }}">
                                <i class="fas fa-plane-departure"></i>
                                <span>Leave Management</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a href="{{ route('nfc.dashboard') }}" class="nav-link {{ request()->routeIs('nfc.*') ? 'active' : '' }}">
                                <i class="fas fa-mobile-alt"></i>
                                <span>NFC System</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                                <i class="fas fa-chart-bar"></i>
                                <span>Reports</span>
                            </a>
                        </div>
                        
                        <div class="nav-item mt-4">
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                               class="nav-link">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </nav>
                </div>
                
                <!-- Main Content -->
                <div class="main-content">
                    <!-- Top Bar -->
                    <div class="modern-topbar glass">
                        <div class="topbar-left">
                            <button class="sidebar-toggle" onclick="toggleSidebar()">
                                <i class="fas fa-bars"></i>
                            </button>
                            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                        </div>
                        
                        <div class="topbar-right">
                            <div class="topbar-actions">
                                @yield('page-actions')
                            </div>
                            
                            <!-- Notifications -->
                            <div class="position-relative">
                                <button class="action-btn" onclick="showNotifications()">
                                    <i class="fas fa-bell"></i>
                                    <span class="notification-badge">3</span>
                                </button>
                            </div>
                            
                            <!-- User Profile -->
                            <div class="user-profile" onclick="toggleUserMenu()">
                                <div class="user-avatar">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="user-info">
                                    <h6>{{ Auth::user()->name ?? 'User' }}</h6>
                                    <small>{{ Auth::user()->role ?? 'Administrator' }}</small>
                                </div>
                                <i class="fas fa-chevron-down ms-2"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content Area -->
                    <div class="content-area">
                        <!-- Page Breadcrumb -->
                        @if(isset($breadcrumbs))
                        <nav aria-label="breadcrumb" class="mb-4">
                            <ol class="breadcrumb bg-white rounded-modern p-3 shadow-sm">
                                @foreach($breadcrumbs as $breadcrumb)
                                    @if($loop->last)
                                        <li class="breadcrumb-item active">{{ $breadcrumb['title'] }}</li>
                                    @else
                                        <li class="breadcrumb-item">
                                            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ol>
                        </nav>
                        @endif
                        
                        <!-- Flash Messages -->
                        @if(session('success'))
                        <div class="alert alert-success border-0 rounded-modern shadow-sm mb-4 animate-fade-in">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                        @endif
                        
                        @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-modern shadow-sm mb-4 animate-fade-in">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                        </div>
                        @endif
                        
                        @if(session('warning'))
                        <div class="alert alert-warning border-0 rounded-modern shadow-sm mb-4 animate-fade-in">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('warning') }}
                        </div>
                        @endif
                        
                        <!-- Main Content -->
                        <div class="animate-fade-in-up">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.all.min.js"></script>
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script>
        // Sidebar toggle functionality
        function toggleSidebar() {
            const wrapper = document.getElementById('wrapper');
            wrapper.classList.toggle('sidebar-collapsed');
            
            // Save preference to cookie
            const isCollapsed = wrapper.classList.contains('sidebar-collapsed');
            document.cookie = `sidebar_collapsed=${isCollapsed}; path=/; max-age=31536000`;
        }
        
        // User menu toggle
        function toggleUserMenu() {
            // Add user menu dropdown logic here
            console.log('User menu toggled');
        }
        
        // Notifications
        function showNotifications() {
            Swal.fire({
                title: 'Notifications',
                html: `
                    <div class="text-start">
                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                            <i class="fas fa-user-plus text-primary me-3"></i>
                            <div>
                                <strong>New Employee Added</strong><br>
                                <small class="text-muted">John Doe joined the team</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                            <i class="fas fa-clock text-warning me-3"></i>
                            <div>
                                <strong>Late Arrival Alert</strong><br>
                                <small class="text-muted">3 employees arrived late today</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <i class="fas fa-chart-line text-success me-3"></i>
                            <div>
                                <strong>Weekly Report Ready</strong><br>
                                <small class="text-muted">Attendance report for this week</small>
                            </div>
                        </div>
                    </div>
                `,
                showCloseButton: true,
                showConfirmButton: false,
                width: '500px'
            });
        }
        
        // Initialize DataTables with modern styling
        $(document).ready(function() {
            if ($.fn.DataTable) {
                $('.modern-table').DataTable({
                    responsive: true,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                    language: {
                        search: "",
                        searchPlaceholder: "Search records...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            first: '<i class="fas fa-angle-double-left"></i>',
                            previous: '<i class="fas fa-angle-left"></i>',
                            next: '<i class="fas fa-angle-right"></i>',
                            last: '<i class="fas fa-angle-double-right"></i>'
                        }
                    },
                    pageLength: 10,
                    order: [[0, 'asc']],
                    columnDefs: [
                        {
                            targets: 'no-sort',
                            orderable: false
                        }
                    ]
                });
            }
        });
        
        // Modern form validation
        function showLoading(button) {
            const spinner = '<span class="loading-spinner me-2"></span>';
            button.innerHTML = spinner + 'Processing...';
            button.disabled = true;
        }
        
        function hideLoading(button, originalText) {
            button.innerHTML = originalText;
            button.disabled = false;
        }
        
        // Toast notifications
        function showToast(message, type = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            Toast.fire({
                icon: type,
                title: message
            });
        }
        
        // Auto-refresh functionality for real-time data
        function startAutoRefresh(interval = 30000) {
            setInterval(() => {
                // Add auto-refresh logic for live data
                if (typeof window.refreshData === 'function') {
                    window.refreshData();
                }
            }, interval);
        }
        
        // Initialize app
        $(document).ready(function() {
            // Add any initialization code here
            console.log('AttendanceFlow Modern UI Loaded');
            
            // Start auto-refresh for dashboard
            if (window.location.pathname.includes('dashboard')) {
                startAutoRefresh();
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>