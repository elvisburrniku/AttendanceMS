<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'AttendanceTracker Pro')</title>
    <meta name="description" content="Professional attendance management system">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --border-color: #e9ecef;
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            --border-radius: 12px;
            --border-radius-lg: 20px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--light-bg);
            color: var(--dark-text);
            line-height: 1.6;
        }
        
        /* Modern Sidebar */
        .sidebar {
            background: white;
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: var(--shadow-md);
            z-index: 1000;
            transition: all 0.3s ease;
            border-right: 1px solid var(--border-color);
        }
        
        .sidebar.collapsed {
            width: 80px;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--primary-gradient);
            color: white;
        }
        
        .sidebar-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white;
        }
        
        .sidebar-brand i {
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .sidebar-brand h4 {
            margin: 0;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
            height: calc(100vh - 100px);
            overflow-y: auto;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: var(--dark-text);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0;
            position: relative;
        }
        
        .nav-link:hover {
            background: rgba(102, 126, 234, 0.1);
            color: var(--primary-color);
        }
        
        .nav-link.active {
            background: var(--primary-gradient);
            color: white;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 1rem;
            font-size: 1.1rem;
        }
        
        .nav-link span {
            font-weight: 500;
            white-space: nowrap;
        }
        
        /* Modern Topbar */
        .topbar {
            background: white;
            height: 70px;
            margin-left: 280px;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 999;
            transition: margin-left 0.3s ease;
        }
        
        .topbar.expanded {
            margin-left: 80px;
        }
        
        .topbar-left {
            display: flex;
            align-items: center;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--dark-text);
            margin-right: 1rem;
            padding: 0.5rem;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background: var(--light-bg);
            color: var(--primary-color);
        }
        
        .breadcrumb-modern {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .breadcrumb-modern li {
            display: flex;
            align-items: center;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .breadcrumb-modern li:not(:last-child)::after {
            content: '/';
            margin: 0 0.75rem;
            color: #dee2e6;
        }
        
        .breadcrumb-modern .active {
            color: var(--dark-text);
            font-weight: 500;
        }
        
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .profile-dropdown {
            position: relative;
        }
        
        .profile-btn {
            display: flex;
            align-items: center;
            background: none;
            border: none;
            padding: 0.5rem;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
        }
        
        .profile-btn:hover {
            background: var(--light-bg);
        }
        
        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 0.75rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: calc(100vh - 70px);
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }
        
        .main-content.expanded {
            margin-left: 80px;
        }
        
        /* Modern Cards */
        .card-modern {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .card-modern:hover {
            box-shadow: var(--shadow-md);
        }
        
        .card-header-modern {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        .card-body-modern {
            padding: 1.5rem;
        }
        
        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-gradient);
        }
        
        .stats-card.success::before {
            background: var(--success-color);
        }
        
        .stats-card.warning::before {
            background: var(--warning-color);
        }
        
        .stats-card.danger::before {
            background: var(--danger-color);
        }
        
        .stats-card.info::before {
            background: var(--info-color);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stats-icon.primary {
            background: rgba(102, 126, 234, 0.1);
            color: var(--primary-color);
        }
        
        .stats-icon.success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
        }
        
        .stats-icon.warning {
            background: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }
        
        .stats-icon.danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }
        
        /* Modern Buttons */
        .btn-modern {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-modern:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        
        .btn-primary-modern {
            background: var(--primary-gradient);
            color: white;
        }
        
        .btn-primary-modern:hover {
            background: var(--primary-gradient);
            opacity: 0.9;
        }
        
        /* Modern Tables */
        .table-modern {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .table-modern .table {
            margin: 0;
        }
        
        .table-modern .table thead th {
            background: var(--light-bg);
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            color: var(--dark-text);
            padding: 1rem;
        }
        
        .table-modern .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
        }
        
        .table-modern .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .topbar {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
        
        /* Sidebar collapsed styles */
        .sidebar.collapsed .sidebar-brand h4,
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-brand i {
            margin-right: 0;
        }
        
        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 0.875rem;
        }
        
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin') }}" class="sidebar-brand">
                <i class="fas fa-clock"></i>
                <h4>AttendanceTracker Pro</h4>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('admin') }}" class="nav-link {{ request()->routeIs('admin') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Employees</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('attendance') }}" class="nav-link {{ request()->routeIs('attendance*') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i>
                        <span>Attendance</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                        <i class="fas fa-building"></i>
                        <span>Departments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('positions.index') }}" class="nav-link {{ request()->routeIs('positions.*') ? 'active' : '' }}">
                        <i class="fas fa-briefcase"></i>
                        <span>Positions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('areas.index') }}" class="nav-link {{ request()->routeIs('areas.*') ? 'active' : '' }}">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Areas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('schedules.index') }}" class="nav-link {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Schedules</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('holiday') }}" class="nav-link {{ request()->routeIs('holiday*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-times"></i>
                        <span>Holidays</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('leave') }}" class="nav-link {{ request()->routeIs('leave*') ? 'active' : '' }}">
                        <i class="fas fa-user-times"></i>
                        <span>Leave Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexLatetime') }}" class="nav-link {{ request()->routeIs('indexLatetime') ? 'active' : '' }}">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Late Arrivals</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Topbar -->
    <div class="topbar" id="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <nav>
                <ol class="breadcrumb-modern">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        
        <div class="topbar-right">
            @yield('topbar-actions')
            
            <div class="profile-dropdown">
                <button class="profile-btn" id="profileDropdown" data-bs-toggle="dropdown">
                    <div class="profile-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="d-none d-md-block">
                        <div class="fw-600">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <small class="text-muted">{{ auth()->user()->email ?? 'admin@example.com' }}</small>
                    </div>
                    <i class="fas fa-chevron-down ms-2"></i>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const topbar = document.getElementById('topbar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            topbar.classList.toggle('expanded');
            mainContent.classList.toggle('expanded');
        });
        
        // Mobile sidebar toggle
        if (window.innerWidth <= 768) {
            document.getElementById('sidebarToggle').addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('show');
            });
        }
    </script>
    
    @yield('scripts')
</body>
</html>