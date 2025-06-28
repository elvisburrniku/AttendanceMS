<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Solar Eagles - Attendance Management')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-purple: #4c3575;
            --secondary-purple: #6b46a8;
            --dark-purple: #3a2859;
            --light-gray: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-gray);
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-header .logo {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: bold;
        }

        .sidebar-header .title {
            color: white;
            font-size: 18px;
            font-weight: 600;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .title {
            opacity: 0;
            display: none;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-section {
            margin-bottom: 30px;
        }

        .menu-section-title {
            color: rgba(255, 255, 255, 0.7);
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 20px;
            margin-bottom: 10px;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .menu-section-title {
            opacity: 0;
            display: none;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
        }

        .menu-item.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .menu-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: white;
        }

        .menu-item .icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 12px;
        }

        .sidebar.collapsed .menu-item .icon {
            margin-right: 0;
        }

        .sidebar.collapsed .menu-item span {
            display: none;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        /* Header */
        .header {
            background: white;
            padding: 16px 24px;
            box-shadow: var(--card-shadow);
            display: flex;
            justify-content: between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 20px;
            color: #666;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: background 0.2s ease;
        }

        .sidebar-toggle:hover {
            background: var(--light-gray);
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-left: auto;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: var(--light-gray);
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-menu:hover {
            background: #e9ecef;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--secondary-purple);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        /* Content Area */
        .content {
            padding: 24px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 24px;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .stat-card-title {
            font-size: 14px;
            font-weight: 500;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .stat-card-value {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }

        .stat-card-change {
            font-size: 14px;
            color: #666;
        }

        /* Card Colors */
        .stat-card.purple .stat-card-icon {
            background: rgba(76, 53, 117, 0.1);
            color: var(--primary-purple);
        }

        .stat-card.blue .stat-card-icon {
            background: rgba(54, 162, 235, 0.1);
            color: #36a2eb;
        }

        .stat-card.green .stat-card-icon {
            background: rgba(75, 192, 192, 0.1);
            color: #4bc0c0;
        }

        .stat-card.orange .stat-card-icon {
            background: rgba(255, 159, 64, 0.1);
            color: #ff9f40;
        }

        .stat-card.red .stat-card-icon {
            background: rgba(255, 99, 132, 0.1);
            color: #ff6384;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Custom Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">SE</div>
            <div class="title">SOLAR EAGLES</div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">Main</div>
                <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <div class="icon"><i class="fas fa-tachometer-alt"></i></div>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="menu-item">
                    <div class="icon"><i class="fas fa-filter"></i></div>
                    <span>Filters</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Management</div>
                <a href="#" class="menu-item">
                    <div class="icon"><i class="fas fa-calendar-check"></i></div>
                    <span>Schedule & Shifts</span>
                </a>
                <a href="#" class="menu-item">
                    <div class="icon"><i class="fas fa-clipboard-check"></i></div>
                    <span>Report Overtime</span>
                </a>
                <a href="#" class="menu-item">
                    <div class="icon"><i class="fas fa-user-clock"></i></div>
                    <span>NFC Attendance</span>
                </a>
                <a href="#" class="menu-item">
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                    <span>Report Overtime</span>
                </a>
                <a href="#" class="menu-item">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <span>Check In/Out</span>
                </a>
                <a href="#" class="menu-item">
                    <div class="icon"><i class="fas fa-file-invoice"></i></div>
                    <span>Payslips</span>
                </a>
                <a href="#" class="menu-item">
                    <div class="icon"><i class="fas fa-calendar-times"></i></div>
                    <span>Festas</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Tools</div>
                <a href="#" class="menu-item">
                    <div class="icon"><i class="fas fa-cog"></i></div>
                    <span>Pàngek</span>
                </a>
            </div>

            @auth
                @if(auth()->user()->is_super_admin || auth()->user()->role === 'admin')
                <div class="menu-section">
                    <div class="menu-section-title">System</div>
                    <a href="{{ route('tenants.index') }}" class="menu-item {{ request()->routeIs('tenants.*') ? 'active' : '' }}">
                        <div class="icon"><i class="fas fa-building"></i></div>
                        <span>Manage Systems</span>
                    </a>
                </div>
                @endif
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            
            <div class="header-right">
                @auth
                    <div class="user-menu" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </div>
                        <span>{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @endauth
            </div>
        </header>

        <!-- Content -->
        <main class="content">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });

            // Mobile sidebar toggle
            if (window.innerWidth <= 768) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>