@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('plugins/chartist/css/chartist.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ URL::asset('css/employee-dashboard.css') }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="theme-color" content="#667eea">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<style>
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.employee-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.employee-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.time-display {
    font-size: 1.1rem;
    opacity: 0.9;
}

.status-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: none;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.status-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.status-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 1rem;
}

.status-working { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.status-break { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.status-complete { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
.status-pending { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }

.clock-display {
    font-family: 'Courier New', monospace;
    font-size: 2.5rem;
    font-weight: bold;
    text-align: center;
    margin: 1rem 0;
    color: #2c3e50;
}

.action-btn {
    width: 100%;
    padding: 1rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 50px;
    border: none;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-clock-in {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-clock-out {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.btn-break-start {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.btn-break-end {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.action-btn:disabled {
    background: #e9ecef;
    color: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.quick-stats {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.stat-item:last-child {
    border-bottom: none;
}

.recent-activity {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-top: 2rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: white;
}

.location-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #6c757d;
    margin-top: 1rem;
}

.pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@media (max-width: 768px) {
    .dashboard-header {
        padding: 1.5rem;
        text-align: center;
    }
    
    .employee-info {
        flex-direction: column;
        text-align: center;
    }
    
    .clock-display {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('breadcrumb')
<div class="col-sm-12">
    <div class="dashboard-header">
        <div class="employee-info">
            <div class="employee-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <h2 class="mb-1">Welcome back, {{ auth()->user()->name ?? 'Employee' }}</h2>
                <div class="time-display">
                    <i class="fas fa-calendar-alt me-2"></i>
                    {{ now()->format('l, F j, Y') }}
                    <span class="ms-3">
                        <i class="fas fa-clock me-2"></i>
                        <span id="currentTime"></span>
                    </span>
                </div>
                <div class="location-status" id="locationStatus">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Detecting location...</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
@php
    $employee = auth()->user()->employee;
    
    // Create employee record if it doesn't exist
    if (!$employee) {
        $employee = \App\Models\Employee::firstOrCreate(
            ['email' => auth()->user()->email],
            [
                'emp_code' => auth()->id(),
                'first_name' => auth()->user()->name ? explode(' ', auth()->user()->name)[0] : 'Employee',
                'last_name' => auth()->user()->name ? (explode(' ', auth()->user()->name)[1] ?? '') : '',
                'nickname' => auth()->user()->name ? explode(' ', auth()->user()->name)[0] : 'Employee',
                'card_no' => 'CARD' . str_pad(auth()->id(), 6, '0', STR_PAD_LEFT),
                'department_id' => 1,
                'position_id' => 1,
                'hire_date' => auth()->user()->created_at ?? now(),
                'gender' => 'M',
                'birthday' => now()->subYears(25),
                'emp_type' => 1,
                'create_time' => now(),
                'create_user' => 'system',
                'change_time' => now(),
                'change_user' => 'system',
                'status' => 1,
                'verify_mode' => 15,
                'city' => '',
                'live_city' => '',
                'province' => '',
                'address' => '',
                'zip_code' => '',
                'office_tel' => '',
                'contact_tel' => '',
                'mobile' => '',
                'national_num' => '',
                'payroll_id' => '',
                'att_bonus' => 0,
                'overtime_policy' => 1,
                'holiday_policy' => 1,
                'att_policy' => 1,
                'app_role' => 1,
                'app_status' => 1,
                'machine_sn' => '',
                'dev_privilege' => 1,
                'fb_palm_vein' => '',
                'fb_face_vein' => '',
                'image_content' => '',
                'first_name_en' => '',
                'last_name_en' => '',
                'emp_code_digits' => 4,
                'pin_code' => '1234'
            ]
        );
    }
    
    $checkin = $employee->attendances()->where('punch_state', 0)->whereDate('punch_time', now()->format('Y-m-d'))->first();
    $checkout = $employee->attendances()->where('punch_state', 1)->whereDate('punch_time', now()->format('Y-m-d'))->first();
    $breakin = $employee->attendances()->where('punch_state', 3)->whereDate('punch_time', now()->format('Y-m-d'))->first();
    $breakout = $employee->attendances()->where('punch_state', 2)->whereDate('punch_time', now()->format('Y-m-d'))->first();
    
    // Calculate work status
    $currentStatus = 'not_started';
    if ($checkin && !$checkout) {
        $currentStatus = ($breakin && !$breakout) ? 'on_break' : 'working';
    } elseif ($checkin && $checkout) {
        $currentStatus = 'completed';
    }
    
    // Calculate today's work time
    $workTime = 0;
    if ($checkin) {
        $endTime = $checkout ? $checkout->punch_time : now();
        $workTime = $checkin->punch_time->diffInMinutes($endTime);
    }
    
    // Calculate break time
    $breakTime = 0;
    if ($breakin) {
        $breakEndTime = $breakout ? $breakout->punch_time : now();
        $breakTime = $breakin->punch_time->diffInMinutes($breakEndTime);
    }
    
    // Recent attendances for this week
    $weeklyAttendances = $employee->attendances()
        ->whereBetween('punch_time', [now()->startOfWeek(), now()->endOfWeek()])
        ->orderBy('punch_time', 'desc')
        ->take(10)
        ->get();
@endphp

<div class="container-fluid">
    <!-- Status Cards Row -->
    <div class="row mb-4">
        <!-- Clock In/Out Card -->
        <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
            <div class="status-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="status-icon {{ $currentStatus === 'working' ? 'status-working' : ($currentStatus === 'completed' ? 'status-complete' : 'status-pending') }}">
                        <i class="fas {{ $currentStatus === 'working' ? 'fa-play' : ($currentStatus === 'completed' ? 'fa-check' : 'fa-clock') }}"></i>
                    </div>
                    <div class="text-end">
                        <h6 class="text-muted mb-1">Work Status</h6>
                        <h5 class="mb-0">
                            @if($currentStatus === 'working')
                                <span class="text-success">Working</span>
                            @elseif($currentStatus === 'completed')
                                <span class="text-info">Day Completed</span>
                            @elseif($currentStatus === 'on_break')
                                <span class="text-warning">On Break</span>
                            @else
                                <span class="text-secondary">Not Started</span>
                            @endif
                        </h5>
                    </div>
                </div>
                
                <div class="clock-display" id="workTimer">
                    <span class="timeel hours">00</span>:
                    <span class="timeel minutes">00</span>:
                    <span class="timeel seconds">00</span>
                </div>
                
                <div class="text-center mb-3">
                    @if($checkin)
                        <small class="text-muted">Started at {{ $checkin->punch_time->format('g:i A') }}</small>
                        @if($checkout)
                            <br><small class="text-muted">Finished at {{ $checkout->punch_time->format('g:i A') }}</small>
                        @endif
                    @endif
                </div>
                
                @if(!$checkin)
                    <button id="toggle-checkin" class="action-btn btn-clock-in pulse">
                        <i class="fas fa-play me-2"></i>Start Work Day
                    </button>
                @elseif($checkin && !$checkout)
                    <button @if($breakin && !$breakout) disabled @endif id="toggle-checkin" class="action-btn btn-clock-out">
                        <i class="fas fa-stop me-2"></i>End Work Day
                    </button>
                @else
                    <div class="text-center text-success">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p class="mb-0">Work day completed!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Break Card -->
        <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
            <div class="status-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="status-icon {{ ($breakin && !$breakout) ? 'status-break' : 'status-pending' }}">
                        <i class="fas {{ ($breakin && !$breakout) ? 'fa-pause' : 'fa-coffee' }}"></i>
                    </div>
                    <div class="text-end">
                        <h6 class="text-muted mb-1">Break Time</h6>
                        <h5 class="mb-0">
                            @if($breakin && !$breakout)
                                <span class="text-warning">On Break</span>
                            @elseif($breakin && $breakout)
                                <span class="text-info">Break Taken</span>
                            @else
                                <span class="text-secondary">Available</span>
                            @endif
                        </h5>
                    </div>
                </div>
                
                <div class="clock-display" id="breakTimer">
                    <span class="timeel hours">00</span>:
                    <span class="timeel minutes">00</span>:
                    <span class="timeel seconds">00</span>
                </div>
                
                <div class="text-center mb-3">
                    @if($breakin)
                        <small class="text-muted">Break started at {{ $breakin->punch_time->format('g:i A') }}</small>
                        @if($breakout)
                            <br><small class="text-muted">Break ended at {{ $breakout->punch_time->format('g:i A') }}</small>
                        @endif
                    @endif
                </div>
                
                @if(!$breakin && $checkin && !$checkout)
                    <button id="toggle-breakin" class="action-btn btn-break-start">
                        <i class="fas fa-pause me-2"></i>Start Break
                    </button>
                @elseif($breakin && !$breakout)
                    <button id="toggle-breakin" class="action-btn btn-break-end">
                        <i class="fas fa-play me-2"></i>End Break
                    </button>
                @elseif(!$checkin)
                    <button disabled class="action-btn">
                        <i class="fas fa-lock me-2"></i>Start work first
                    </button>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-check fa-2x mb-2"></i>
                        <p class="mb-0">Break completed</p>
                    </div>
                @endif
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
                <strong>{{ $checkin ? $checkin->punch_time->format('g:i A') : 'Not started' }}</strong>
            </div>
            <div class="stat-item">
                <span>Status</span>
                <strong class="{{ $currentStatus === 'working' ? 'text-success' : ($currentStatus === 'completed' ? 'text-info' : 'text-secondary') }}">
                    {{ ucfirst(str_replace('_', ' ', $currentStatus)) }}
                </strong>
            </div>
        </div>

        <!-- Weekly Overview -->
        <div class="quick-stats">
            <h5 class="mb-3">
                <i class="fas fa-calendar-week me-2 text-success"></i>This Week
            </h5>
            @php
                $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                $weeklyData = [];
                foreach($weekDays as $day) {
                    $dayAttendance = $employee->attendances()
                        ->whereDate('punch_time', now()->startOfWeek()->addDays(array_search($day, $weekDays)))
                        ->where('punch_state', 0)
                        ->first();
                    $weeklyData[$day] = $dayAttendance;
                }
            @endphp
            @foreach($weeklyData as $day => $attendance)
                <div class="stat-item">
                    <span>{{ $day }}</span>
                    <strong class="{{ $attendance ? 'text-success' : 'text-muted' }}">
                        {{ $attendance ? $attendance->punch_time->format('g:i A') : 'Absent' }}
                    </strong>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="recent-activity">
        <h5 class="mb-3">
            <i class="fas fa-history me-2 text-info"></i>Recent Activity
        </h5>
        @if($weeklyAttendances->count() > 0)
            @foreach($weeklyAttendances->take(5) as $attendance)
                <div class="activity-item">
                    <div class="activity-icon {{ $attendance->punch_state == 0 ? 'status-working' : ($attendance->punch_state == 1 ? 'status-complete' : 'status-break') }}">
                        <i class="fas {{ $attendance->punch_state == 0 ? 'fa-play' : ($attendance->punch_state == 1 ? 'fa-stop' : ($attendance->punch_state == 2 ? 'fa-pause' : 'fa-play')) }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <strong>
                                @if($attendance->punch_state == 0) Clock In
                                @elseif($attendance->punch_state == 1) Clock Out
                                @elseif($attendance->punch_state == 2) Break End
                                @else Break Start
                                @endif
                            </strong>
                            <span class="text-muted">{{ $attendance->punch_time->format('M j, g:i A') }}</span>
                        </div>
                        <small class="text-muted">{{ $attendance->punch_time->diffForHumans() }}</small>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center text-muted py-4">
                <i class="fas fa-clock fa-3x mb-3"></i>
                <p>No activity recorded yet</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('plugins/chartist/js/chartist.min.js') }}"></script>
<script src="{{ URL::asset('plugins/chartist/js/chartist-plugin-tooltip.min.js') }}"></script>
<script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>

<script>
// Enhanced timer functions with better formatting
function updateTimer(startTime, endTime = null, elementId) {
    const start = new Date(startTime).getTime();
    const now = endTime ? new Date(endTime).getTime() : new Date().getTime();
    const timeDifference = now - start;
    
    if (timeDifference < 0) return;
    
    const hours = Math.floor(timeDifference / (1000 * 60 * 60));
    const minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);
    
    const element = document.getElementById(elementId);
    if (element) {
        const hoursEl = element.querySelector('.hours');
        const minutesEl = element.querySelector('.minutes');
        const secondsEl = element.querySelector('.seconds');
        
        if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
        if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
        if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
    }
}

// Real-time clock display
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

// Location management
function updateLocationStatus() {
    const locationStatus = document.getElementById('locationStatus');
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude.toFixed(4);
                const lng = position.coords.longitude.toFixed(4);
                locationStatus.innerHTML = `
                    <i class="fas fa-map-marker-alt text-success"></i>
                    <span class="text-success">Location detected (${lat}, ${lng})</span>
                `;
            },
            function(error) {
                let message = 'Location unavailable';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Location access denied';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'Location unavailable';
                        break;
                    case error.TIMEOUT:
                        message = 'Location timeout';
                        break;
                }
                locationStatus.innerHTML = `
                    <i class="fas fa-map-marker-alt text-warning"></i>
                    <span class="text-warning">${message}</span>
                `;
            }
        );
    } else {
        locationStatus.innerHTML = `
            <i class="fas fa-map-marker-alt text-danger"></i>
            <span class="text-danger">Geolocation not supported</span>
        `;
    }
}

// Notification system
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}

// Enhanced attendance action with better UX
function performAttendanceAction(actionType, buttonElement) {
    // Show loading state
    const originalText = buttonElement.innerHTML;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    buttonElement.disabled = true;
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const data = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    checkType: actionType
                };
                
                axios.post('/attendance-tap', data)
                    .then(function(response) {
                        const data = response.data;
                        
                        // Update global variables
                        if (data.punch_state == '0') {
                            window.checkin = data;
                            showNotification('Successfully clocked in! Have a productive day.', 'success');
                        } else if (data.punch_state == '1') {
                            window.checkout = data;
                            showNotification('Successfully clocked out! Great work today.', 'success');
                        } else if (data.punch_state == '2') {
                            window.breakout = data;
                            showNotification('Break ended. Welcome back!', 'info');
                        } else if (data.punch_state == '3') {
                            window.breakin = data;
                            showNotification('Break started. Enjoy your break!', 'info');
                        }
                        
                        // Refresh page to update UI
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    })
                    .catch(function(error) {
                        console.error('Attendance error:', error);
                        showNotification('Failed to record attendance. Please try again.', 'danger');
                        
                        // Restore button
                        buttonElement.innerHTML = originalText;
                        buttonElement.disabled = false;
                    });
            },
            function(error) {
                console.error('Geolocation error:', error);
                showNotification('Location access required for attendance tracking.', 'warning');
                
                // Restore button
                buttonElement.innerHTML = originalText;
                buttonElement.disabled = false;
            }
        );
    } else {
        showNotification('Geolocation not supported by your browser.', 'danger');
        buttonElement.innerHTML = originalText;
        buttonElement.disabled = false;
    }
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Get attendance data from PHP
    const checkin = @json($checkin);
    const checkout = @json($checkout);
    const breakin = @json($breakin);
    const breakout = @json($breakout);
    
    // Store globally for updates
    window.checkin = checkin;
    window.checkout = checkout;
    window.breakin = breakin;
    window.breakout = breakout;
    
    // Start timers
    if (checkin) {
        updateTimer(checkin.punch_time, checkout?.punch_time, 'workTimer');
        if (!checkout) {
            setInterval(() => {
                updateTimer(checkin.punch_time, null, 'workTimer');
            }, 1000);
        }
    }
    
    if (breakin) {
        updateTimer(breakin.punch_time, breakout?.punch_time, 'breakTimer');
        if (!breakout) {
            setInterval(() => {
                updateTimer(breakin.punch_time, null, 'breakTimer');
            }, 1000);
        }
    }
    
    // Update current time every second
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
    
    // Update location status
    updateLocationStatus();
    
    // Bind event handlers
    const checkinBtn = document.getElementById('toggle-checkin');
    if (checkinBtn) {
        checkinBtn.addEventListener('click', function() {
            performAttendanceAction('checkin', this);
        });
    }
    
    const breakBtn = document.getElementById('toggle-breakin');
    if (breakBtn) {
        breakBtn.addEventListener('click', function() {
            performAttendanceAction('pause', this);
        });
    }
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + Enter for quick clock in/out
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            if (checkinBtn && !checkinBtn.disabled) {
                checkinBtn.click();
            }
        }
        
        // Ctrl + Space for break
        if (e.ctrlKey && e.code === 'Space') {
            e.preventDefault();
            if (breakBtn && !breakBtn.disabled) {
                breakBtn.click();
            }
        }
    });
    
    // Show keyboard shortcuts help
    console.log('Keyboard shortcuts:');
    console.log('Ctrl + Enter: Clock in/out');
    console.log('Ctrl + Space: Start/end break');
});

// Page visibility API for accurate timers
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // Page became visible, refresh timers
        if (window.checkin && !window.checkout) {
            updateTimer(window.checkin.punch_time, null, 'workTimer');
        }
        if (window.breakin && !window.breakout) {
            updateTimer(window.breakin.punch_time, null, 'breakTimer');
        }
    }
});
</script>
@endsection