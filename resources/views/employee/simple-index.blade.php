@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('plugins/chartist/css/chartist.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background-color: #f8f9fa;
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
</style>
@endsection

@section('breadcrumb')
<div class="col-sm-12">
    <div class="simple-card">
        <h3>Welcome, {{ auth()->user()->name ?? 'Employee' }}</h3>
        <p class="text-muted mb-0">{{ now()->format('l, F j, Y') }} • <span id="currentTime"></span></p>
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
        $workTime = $endTime->diffInMinutes($checkin->punch_time);
    }
    
    // Calculate break time
    $breakTime = 0;
    if ($breakin && $breakout) {
        $breakTime = $breakout->punch_time->diffInMinutes($breakin->punch_time);
    }
    
    // Get weekly attendances
    $weeklyAttendances = $employee->attendances()
        ->whereBetween('punch_time', [now()->startOfWeek(), now()->endOfWeek()])
        ->orderBy('punch_time', 'desc')
        ->get();
@endphp

<div class="container-fluid">
    <!-- Simple Status Cards -->
    <div class="row">
        <div class="col-md-6">
            <div class="simple-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Current Status</h5>
                    <span class="status-badge {{ $currentStatus === 'working' ? 'status-working' : 'status-break' }}">
                        @if($currentStatus === 'working')
                            Working
                        @elseif($currentStatus === 'completed')
                            Completed
                        @elseif($currentStatus === 'on_break')
                            On Break
                        @else
                            Not Started
                        @endif
                    </span>
                </div>
                
                <div class="time-display" id="workTimer">0:00:00</div>
                
                @if($checkin)
                    <p class="text-muted mb-3">Started at {{ $checkin->punch_time->format('g:i A') }}</p>
                @endif
                
                @if(!$checkin)
                    <button id="toggle-checkin" class="btn-simple">
                        Clock In
                    </button>
                @elseif($checkin && !$checkout)
                    <button @if($breakin && !$breakout) disabled @endif id="toggle-checkin" class="btn-simple btn-danger">
                        Clock Out
                    </button>
                @else
                    <div class="text-center text-success">
                        <p class="mb-0">Work day completed!</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="simple-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Break Status</h5>
                    <span class="status-badge status-break">
                        @if($breakin && !$breakout)
                            On Break
                        @elseif($breakin && $breakout)
                            Completed
                        @else
                            Available
                        @endif
                    </span>
                </div>
                
                <div class="time-display" id="breakTimer">0:00:00</div>
                
                @if($breakin)
                    <p class="text-muted mb-3">
                        @if($breakout)
                            {{ $breakin->punch_time->format('g:i A') }} - {{ $breakout->punch_time->format('g:i A') }}
                        @else
                            Started at {{ $breakin->punch_time->format('g:i A') }}
                        @endif
                    </p>
                @endif
                
                @if(!$breakin && $checkin && !$checkout)
                    <button id="toggle-breakin" class="btn-simple">
                        Take Break
                    </button>
                @elseif($breakin && !$breakout)
                    <button id="toggle-breakin" class="btn-simple">
                        End Break
                    </button>
                @else
                    <div class="text-center text-muted">
                        <p class="mb-0">
                            @if($breakin && $breakout)
                                Break completed
                            @else
                                Clock in first
                            @endif
                        </p>
                    </div>
                @endif
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
                    <strong>{{ $checkin ? $checkin->punch_time->format('g:i A') : 'Not started' }}</strong>
                </div>
                <div class="info-row">
                    <span>Status</span>
                    <strong>{{ ucfirst(str_replace('_', ' ', $currentStatus)) }}</strong>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="simple-card">
                <h5 class="mb-3">This Week</h5>
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
                    <div class="info-row">
                        <span>{{ $day }}</span>
                        <strong>{{ $attendance ? $attendance->punch_time->format('g:i A') : 'Absent' }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="simple-card">
        <h5 class="mb-3">Recent Activity</h5>
        @if($weeklyAttendances->count() > 0)
            @foreach($weeklyAttendances->take(5) as $attendance)
                <div class="info-row">
                    <span>
                        @if($attendance->punch_state == 0) Clock In
                        @elseif($attendance->punch_state == 1) Clock Out
                        @elseif($attendance->punch_state == 2) Break End
                        @else Break Start
                        @endif
                    </span>
                    <strong>{{ $attendance->punch_time->format('M j, g:i A') }}</strong>
                </div>
            @endforeach
        @else
            <div class="text-center text-muted py-4">
                <p>No activity recorded yet</p>
            </div>
        @endif
    </div>

    <!-- NFC & QR Code Actions -->
    <div class="simple-card">
        <h5 class="mb-3">NFC & QR Code Options</h5>
        <div class="row">
            <div class="col-md-4">
                <button class="btn-simple mb-2" id="generate-employee-qr" style="background-color: #28a745;">
                    <i class="fas fa-qrcode"></i> Generate My QR Code
                </button>
            </div>
            <div class="col-md-4">
                <button class="btn-simple mb-2" id="scan-qr-attendance" style="background-color: #17a2b8;">
                    <i class="fas fa-camera"></i> Scan QR for Attendance
                </button>
            </div>
            <div class="col-md-4">
                <a href="{{ route('nfc.employee-card') }}" class="btn-simple mb-2" style="background-color: #6c757d; text-decoration: none; display: block; text-align: center;">
                    <i class="fas fa-id-card"></i> My NFC Card
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #007bff; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode"></i> My QR Code
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: white;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <canvas id="employee-dashboard-qr" width="200" height="200" style="border: 1px solid #ddd; border-radius: 8px;"></canvas>
                </div>
                <p class="text-muted mb-3">Show this QR code to attendance scanners</p>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-success btn-sm" id="download-dashboard-qr">
                        <i class="fas fa-download"></i> Download
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="share-dashboard-qr">
                        <i class="fas fa-share"></i> Share
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Scanner Modal -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #17a2b8; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-camera"></i> QR Code Scanner
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: white;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <button type="button" class="btn-simple" id="start-dashboard-scanner">
                        <i class="fas fa-camera"></i> Start Camera
                    </button>
                    <button type="button" class="btn-simple" id="stop-dashboard-scanner" style="display: none; background-color: #6c757d;">
                        <i class="fas fa-stop"></i> Stop Camera
                    </button>
                </div>
                
                <!-- Camera Preview -->
                <div id="dashboard-camera-container" style="display: none;">
                    <video id="dashboard-camera-preview" autoplay muted playsinline 
                           style="width: 100%; max-height: 400px; border-radius: 8px; border: 2px solid #17a2b8;"></video>
                    <div class="mt-2">
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle"></i>
                            Point camera at QR code for automatic attendance recording
                        </div>
                    </div>
                </div>
                
                <!-- Scanner Status -->
                <div id="dashboard-scanner-status" class="alert alert-secondary small mt-2" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Scanning for QR codes...
                </div>
            </div>
        </div>
    </div>
</div>

@section('script')
<script src="/js/qr-code-generator.js"></script>
<script src="{{ URL::asset('plugins/chartist/js/chartist.min.js') }}"></script>
<script src="{{ URL::asset('plugins/chartist/js/chartist-plugin-tooltip.min.js') }}"></script>

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
    @if($checkin && !$checkout)
        const workTimer = document.getElementById('workTimer');
        const startTime = new Date('{{ $checkin->punch_time->toISOString() }}');
        const now = new Date();
        const elapsed = now - startTime;
        
        const hours = Math.floor(elapsed / (1000 * 60 * 60));
        const minutes = Math.floor((elapsed % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((elapsed % (1000 * 60)) / 1000);
        
        workTimer.textContent = `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    @endif
    
    @if($breakin && !$breakout)
        const breakTimer = document.getElementById('breakTimer');
        const breakStartTime = new Date('{{ $breakin->punch_time->toISOString() }}');
        const now = new Date();
        const elapsed = now - breakStartTime;
        
        const hours = Math.floor(elapsed / (1000 * 60 * 60));
        const minutes = Math.floor((elapsed % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((elapsed % (1000 * 60)) / 1000);
        
        breakTimer.textContent = `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    @endif
}

// Handle attendance actions
document.addEventListener('DOMContentLoaded', function() {
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
    updateTimer();
    setInterval(updateTimer, 1000);
    
    // Clock in/out handler
    const checkinBtn = document.getElementById('toggle-checkin');
    if (checkinBtn) {
        checkinBtn.addEventListener('click', function() {
            const punchState = {{ $checkin && !$checkout ? '1' : '0' }};
            recordAttendance(punchState);
        });
    }
    
    // Break handler
    const breakBtn = document.getElementById('toggle-breakin');
    if (breakBtn) {
        breakBtn.addEventListener('click', function() {
            const punchState = {{ $breakin && !$breakout ? '2' : '3' }};
            recordAttendance(punchState);
        });
    }
});

function recordAttendance(punchState) {
    fetch('/attendance/punch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            punch_state: punchState,
            latitude: null,
            longitude: null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while recording attendance');
    });
}

// QR Code and Scanner functionality
class EmployeeDashboardNFC {
    constructor() {
        this.employeeData = {
            emp_code: '{{ $employee->emp_code ?? "EMP001" }}',
            name: '{{ ($employee->first_name ?? "Employee") . " " . ($employee->last_name ?? "Name") }}',
            role: 'employee'
        };
        this.setupEventListeners();
    }

    setupEventListeners() {
        $('#generate-employee-qr').on('click', () => this.showQRCode());
        $('#scan-qr-attendance').on('click', () => this.showQRScanner());
        $('#download-dashboard-qr').on('click', () => this.downloadQR());
        $('#share-dashboard-qr').on('click', () => this.shareQR());
        $('#start-dashboard-scanner').on('click', () => this.startScanner());
        $('#stop-dashboard-scanner').on('click', () => this.stopScanner());
    }

    showQRCode() {
        this.generateQRCode();
        $('#qrCodeModal').modal('show');
    }

    generateQRCode() {
        const canvas = document.getElementById('employee-dashboard-qr');
        
        // Use QR Code Generator
        QRCodeGenerator.generate(canvas, {
            emp_code: this.employeeData.emp_code,
            name: this.employeeData.name,
            role: this.employeeData.role,
            type: 'employee_attendance',
            scan_method: 'qr_code',
            timestamp: Date.now()
        });
    }

    downloadQR() {
        const canvas = document.getElementById('employee-dashboard-qr');
        const link = document.createElement('a');
        link.download = `${this.employeeData.emp_code}_attendance_qr.png`;
        link.href = canvas.toDataURL();
        link.click();
    }

    shareQR() {
        const canvas = document.getElementById('employee-dashboard-qr');
        
        if (navigator.share && canvas.toBlob) {
            canvas.toBlob(blob => {
                const file = new File([blob], `${this.employeeData.emp_code}_qr.png`, { type: 'image/png' });
                navigator.share({
                    title: 'Employee Attendance QR Code',
                    text: `Attendance QR Code for ${this.employeeData.name}`,
                    files: [file]
                }).catch(err => console.log('Share failed:', err));
            });
        } else {
            alert('Sharing not supported on this device. Use download instead.');
        }
    }

    showQRScanner() {
        $('#qrScannerModal').modal('show');
    }

    async startScanner() {
        try {
            this.cameraStream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            });
            
            const video = document.getElementById('dashboard-camera-preview');
            video.srcObject = this.cameraStream;
            
            $('#dashboard-camera-container').slideDown();
            $('#dashboard-scanner-status').show();
            $('#start-dashboard-scanner').hide();
            $('#stop-dashboard-scanner').show();
            
            // Start QR detection simulation
            this.startQRDetection(video);
            
        } catch (error) {
            console.error('Camera error:', error);
            alert('Could not access camera. Please check permissions.');
        }
    }

    stopScanner() {
        if (this.cameraStream) {
            this.cameraStream.getTracks().forEach(track => track.stop());
            this.cameraStream = null;
        }
        
        if (this.qrDetectionInterval) {
            clearInterval(this.qrDetectionInterval);
            this.qrDetectionInterval = null;
        }
        
        $('#dashboard-camera-container').slideUp();
        $('#dashboard-scanner-status').hide();
        $('#start-dashboard-scanner').show();
        $('#stop-dashboard-scanner').hide();
    }

    startQRDetection(video) {
        this.qrDetectionInterval = setInterval(() => {
            // Simulate QR detection (10% chance per second)
            if (Math.random() < 0.1) {
                this.handleQRDetected();
            }
        }, 1000);
    }

    handleQRDetected() {
        this.stopScanner();
        
        $('#dashboard-scanner-status').removeClass('alert-secondary')
            .addClass('alert-success')
            .html('<i class="fas fa-check"></i> QR Code detected! Processing attendance...');
        
        // Simulate attendance processing
        setTimeout(() => {
            $('#qrScannerModal').modal('hide');
            
            // Trigger attendance action based on current status
            const currentStatus = '{{ $currentStatus }}';
            if (currentStatus === 'not_started') {
                recordAttendance(0); // Check in
            } else if (currentStatus === 'working') {
                recordAttendance(1); // Check out
            } else if (currentStatus === 'on_break') {
                recordAttendance(2); // Break in
            }
            
            setTimeout(() => {
                $('#dashboard-scanner-status').removeClass('alert-success')
                    .addClass('alert-secondary').hide();
            }, 3000);
        }, 2000);
    }
}

// Initialize NFC functionality when page loads
$(document).ready(function() {
    // Initialize existing functionality
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
    updateTimer();
    setInterval(updateTimer, 1000);
    
    // Initialize NFC functionality
    new EmployeeDashboardNFC();
});
</script>
@endsection