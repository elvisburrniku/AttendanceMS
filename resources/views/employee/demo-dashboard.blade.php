<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#667eea">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Employee Dashboard - Attendance Management</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ URL::asset('css/employee-dashboard.css') }}">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .demo-banner {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Demo Banner -->
        <div class="demo-banner">
            <h4 class="mb-1"><i class="fas fa-eye me-2"></i>Demo: Redesigned Employee Dashboard</h4>
            <small>Interactive preview of the modern attendance management interface</small>
        </div>

        <!-- Dashboard Header -->
        <div class="col-sm-12">
            <div class="dashboard-header">
                <div class="employee-info">
                    <div class="employee-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">Welcome back, {{ $user->name }}</h2>
                        <div class="time-display">
                            <i class="fas fa-calendar-alt me-2"></i>
                            {{ now()->format('l, F j, Y') }}
                            <span class="ms-3">
                                <i class="fas fa-clock me-2"></i>
                                <span id="currentTime"></span>
                            </span>
                        </div>
                        <div class="location-status" id="locationStatus">
                            <i class="fas fa-map-marker-alt text-success"></i>
                            <span class="text-success">New York, NY (40.7128, -74.0060)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Cards Row -->
        <div class="row mb-4">
            <!-- Clock In/Out Card -->
            <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                <div class="status-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="status-icon status-working">
                            <i class="fas fa-play"></i>
                        </div>
                        <div class="text-end">
                            <h6 class="text-muted mb-1">Work Status</h6>
                            <h5 class="mb-0">
                                <span class="text-success">Working</span>
                            </h5>
                        </div>
                    </div>
                    
                    <div class="clock-display" id="workTimer">
                        <span class="timeel hours">08</span>:
                        <span class="timeel minutes">45</span>:
                        <span class="timeel seconds">32</span>
                    </div>
                    
                    <div class="text-center mb-3">
                        <small class="text-muted">Started at {{ $checkin->punch_time->format('g:i A') }}</small>
                    </div>
                    
                    <button class="action-btn btn-clock-out" onclick="showDemo('clock-out')">
                        <i class="fas fa-stop me-2"></i>End Work Day
                    </button>
                </div>
            </div>

            <!-- Break Card -->
            <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                <div class="status-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="status-icon status-pending">
                            <i class="fas fa-coffee"></i>
                        </div>
                        <div class="text-end">
                            <h6 class="text-muted mb-1">Break Time</h6>
                            <h5 class="mb-0">
                                <span class="text-info">Break Taken</span>
                            </h5>
                        </div>
                    </div>
                    
                    <div class="clock-display" id="breakTimer">
                        <span class="timeel hours">00</span>:
                        <span class="timeel minutes">30</span>:
                        <span class="timeel seconds">00</span>
                    </div>
                    
                    <div class="text-center mb-3">
                        <small class="text-muted">Break: {{ $breakin->punch_time->format('g:i A') }} - {{ $breakout->punch_time->format('g:i A') }}</small>
                    </div>
                    
                    <div class="text-center text-muted">
                        <i class="fas fa-check fa-2x mb-2"></i>
                        <p class="mb-0">Break completed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <!-- Today's Summary -->
            <div class="quick-stats">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i>Today's Summary
                </h5>
                <div class="stat-item">
                    <span>Total Work Time</span>
                    <strong>{{ floor($workTime / 60) }}h {{ $workTime % 60 }}m</strong>
                </div>
                <div class="stat-item">
                    <span>Break Time</span>
                    <strong>{{ floor($breakTime / 60) }}h {{ $breakTime % 60 }}m</strong>
                </div>
                <div class="stat-item">
                    <span>Check-in Time</span>
                    <strong>{{ $checkin->punch_time->format('g:i A') }}</strong>
                </div>
                <div class="stat-item">
                    <span>Status</span>
                    <strong class="text-success">Working</strong>
                </div>
            </div>

            <!-- Weekly Overview -->
            <div class="quick-stats">
                <h5 class="mb-3">
                    <i class="fas fa-calendar-week me-2 text-success"></i>This Week
                </h5>
                <div class="stat-item">
                    <span>Monday</span>
                    <strong class="text-success">9:00 AM</strong>
                </div>
                <div class="stat-item">
                    <span>Tuesday</span>
                    <strong class="text-success">9:10 AM</strong>
                </div>
                <div class="stat-item">
                    <span>Wednesday</span>
                    <strong class="text-success">9:15 AM</strong>
                </div>
                <div class="stat-item">
                    <span>Thursday</span>
                    <strong class="text-muted">Absent</strong>
                </div>
                <div class="stat-item">
                    <span>Friday</span>
                    <strong class="text-muted">Absent</strong>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <h5 class="mb-3">
                <i class="fas fa-history me-2 text-info"></i>Recent Activity
            </h5>
            @foreach($weeklyAttendances as $attendance)
                <div class="activity-item">
                    <div class="activity-icon {{ $attendance->punch_state == 0 ? 'status-working' : 'status-complete' }}">
                        <i class="fas {{ $attendance->punch_state == 0 ? 'fa-play' : 'fa-stop' }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <strong>
                                {{ $attendance->punch_state == 0 ? 'Clock In' : 'Clock Out' }}
                            </strong>
                            <span class="text-muted">{{ $attendance->punch_time->format('M j, g:i A') }}</span>
                        </div>
                        <small class="text-muted">{{ $attendance->punch_time->diffForHumans() }}</small>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Demo Features Info -->
        <div class="recent-activity mt-4">
            <h5 class="mb-3">
                <i class="fas fa-info-circle me-2 text-primary"></i>Dashboard Features
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <strong><i class="fas fa-mobile-alt me-2 text-success"></i>Mobile Responsive</strong>
                        <p class="mb-2 text-muted small">Optimized for all device sizes with touch-friendly interfaces</p>
                    </div>
                    <div class="mb-3">
                        <strong><i class="fas fa-map-marker-alt me-2 text-warning"></i>GPS Tracking</strong>
                        <p class="mb-2 text-muted small">Automatic location detection for attendance verification</p>
                    </div>
                    <div class="mb-3">
                        <strong><i class="fas fa-clock me-2 text-info"></i>Real-time Timers</strong>
                        <p class="mb-2 text-muted small">Live countdown and work duration tracking</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <strong><i class="fas fa-keyboard me-2 text-primary"></i>Keyboard Shortcuts</strong>
                        <p class="mb-2 text-muted small">Ctrl+Enter (Clock in/out), Ctrl+Space (Break)</p>
                    </div>
                    <div class="mb-3">
                        <strong><i class="fas fa-bell me-2 text-danger"></i>Smart Notifications</strong>
                        <p class="mb-2 text-muted small">Toast notifications for all attendance actions</p>
                    </div>
                    <div class="mb-3">
                        <strong><i class="fas fa-chart-bar me-2 text-success"></i>Analytics</strong>
                        <p class="mb-2 text-muted small">Weekly overview and performance tracking</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Demo Notification -->
    <div id="demoNotification" class="alert alert-success alert-dismissible position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; display: none;">
        <span id="notificationText"></span>
        <button type="button" class="btn-close" onclick="hideNotification()"></button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Real-time clock
        function updateCurrentTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour12: true, 
                hour: 'numeric', 
                minute: '2-digit', 
                second: '2-digit' 
            });
            
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }

        // Animate work timer
        function animateTimer() {
            const workTimer = document.getElementById('workTimer');
            const seconds = workTimer.querySelector('.seconds');
            const minutes = workTimer.querySelector('.minutes');
            const hours = workTimer.querySelector('.hours');
            
            let currentSeconds = parseInt(seconds.textContent);
            let currentMinutes = parseInt(minutes.textContent);
            let currentHours = parseInt(hours.textContent);
            
            setInterval(() => {
                currentSeconds++;
                
                if (currentSeconds >= 60) {
                    currentSeconds = 0;
                    currentMinutes++;
                    
                    if (currentMinutes >= 60) {
                        currentMinutes = 0;
                        currentHours++;
                    }
                }
                
                seconds.textContent = String(currentSeconds).padStart(2, '0');
                minutes.textContent = String(currentMinutes).padStart(2, '0');
                hours.textContent = String(currentHours).padStart(2, '0');
            }, 1000);
        }

        // Demo notification system
        function showDemo(action) {
            const notification = document.getElementById('demoNotification');
            const text = document.getElementById('notificationText');
            
            let message = '';
            switch(action) {
                case 'clock-out':
                    message = '<i class="fas fa-check me-2"></i>Demo: Clock out successful! Great work today.';
                    break;
                default:
                    message = '<i class="fas fa-info me-2"></i>Demo mode - Click actions show notifications';
            }
            
            text.innerHTML = message;
            notification.style.display = 'block';
            
            setTimeout(() => {
                hideNotification();
            }, 4000);
        }

        function hideNotification() {
            document.getElementById('demoNotification').style.display = 'none';
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCurrentTime();
            setInterval(updateCurrentTime, 1000);
            animateTimer();
            
            // Show welcome message
            setTimeout(() => {
                showDemo('welcome');
                document.getElementById('notificationText').innerHTML = '<i class="fas fa-rocket me-2"></i>Welcome to the redesigned employee dashboard!';
                document.getElementById('demoNotification').style.display = 'block';
            }, 1000);
        });

        // Add click handlers for demo interactions
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.textContent.toLowerCase().includes('end') ? 'clock-out' : 'clock-in';
                showDemo(action);
            });
        });
    </script>
</body>
</html>