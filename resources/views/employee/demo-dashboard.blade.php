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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .demo-banner {
            background-color: #007bff;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .simple-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-working {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-break {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .time-display {
            font-size: 2rem;
            font-weight: bold;
            color: #212529;
            margin: 1rem 0;
        }
        
        .btn-simple {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
        }
        
        .btn-simple:hover {
            background-color: #0056b3;
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .text-muted {
            color: #6c757d;
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

        <!-- Simple Header -->
        <div class="simple-card">
            <h3>Welcome, {{ $user->name }}</h3>
            <p class="text-muted mb-0">{{ now()->format('l, F j, Y') }} • <span id="currentTime"></span></p>
        </div>

        <!-- Simple Status Cards -->
        <div class="row">
            <div class="col-md-6">
                <div class="simple-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Current Status</h5>
                        <span class="status-badge status-working">Working</span>
                    </div>
                    
                    <div class="time-display" id="workTimer">8:45:32</div>
                    
                    <p class="text-muted mb-3">Started at {{ $checkin->punch_time->format('g:i A') }}</p>
                    
                    <button class="btn-simple btn-danger" onclick="showDemo('clock-out')">
                        Clock Out
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <div class="simple-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Break Status</h5>
                        <span class="status-badge status-break">Completed</span>
                    </div>
                    
                    <div class="time-display">0:30:00</div>
                    
                    <p class="text-muted mb-3">{{ $breakin->punch_time->format('g:i A') }} - {{ $breakout->punch_time->format('g:i A') }}</p>
                    
                    <button class="btn-simple" onclick="showDemo('break')">
                        Take Break
                    </button>
                </div>
            </div>
        </div>

        <!-- Simple Summary -->
        <div class="row">
            <div class="col-md-6">
                <div class="simple-card">
                    <h5 class="mb-3">Today's Summary</h5>
                    <div class="info-row">
                        <span>Total Work Time</span>
                        <strong>{{ floor($workTime / 60) }}h {{ $workTime % 60 }}m</strong>
                    </div>
                    <div class="info-row">
                        <span>Break Time</span>
                        <strong>{{ floor($breakTime / 60) }}h {{ $breakTime % 60 }}m</strong>
                    </div>
                    <div class="info-row">
                        <span>Check-in Time</span>
                        <strong>{{ $checkin->punch_time->format('g:i A') }}</strong>
                    </div>
                    <div class="info-row">
                        <span>Status</span>
                        <strong>Working</strong>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="simple-card">
                    <h5 class="mb-3">This Week</h5>
                    <div class="info-row">
                        <span>Monday</span>
                        <strong>9:00 AM</strong>
                    </div>
                    <div class="info-row">
                        <span>Tuesday</span>
                        <strong>9:10 AM</strong>
                    </div>
                    <div class="info-row">
                        <span>Wednesday</span>
                        <strong>9:15 AM</strong>
                    </div>
                    <div class="info-row">
                        <span>Thursday</span>
                        <span class="text-muted">Absent</span>
                    </div>
                    <div class="info-row">
                        <span>Friday</span>
                        <span class="text-muted">Absent</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="simple-card">
            <h5 class="mb-3">Recent Activity</h5>
            @foreach($weeklyAttendances as $attendance)
                <div class="info-row">
                    <span>{{ $attendance->punch_state == 0 ? 'Clock In' : 'Clock Out' }}</span>
                    <strong>{{ $attendance->punch_time->format('M j, g:i A') }}</strong>
                </div>
            @endforeach
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

        // Simple timer update
        function updateTimer() {
            const workTimer = document.getElementById('workTimer');
            const startTime = new Date();
            startTime.setHours(9, 15, 0); // Started at 9:15 AM
            
            const now = new Date();
            const elapsed = now - startTime;
            
            const hours = Math.floor(elapsed / (1000 * 60 * 60));
            const minutes = Math.floor((elapsed % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((elapsed % (1000 * 60)) / 1000);
            
            workTimer.textContent = `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
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
            updateTimer();
            setInterval(updateTimer, 1000);
            
            // Show welcome message
            setTimeout(() => {
                showDemo('welcome');
                document.getElementById('notificationText').innerHTML = 'Welcome to the simple employee dashboard!';
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