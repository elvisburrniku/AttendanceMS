@extends('layouts.master')

@section('title', 'NFC Scanner')

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group float-right">
                        <ol class="breadcrumb hide-phone p-0 m-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">NFC Scanner</li>
                        </ol>
                    </div>
                    <h4 class="page-title">NFC Attendance Scanner</h4>
                </div>
            </div>
        </div>

        <!-- Scanner Interface -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            <i class="fas fa-mobile-alt text-primary"></i> 
                            NFC Scanner Interface
                        </h4>
                        
                        <!-- Scanner Status -->
                        <div class="alert alert-info" id="scanner-status">
                            <i class="fas fa-info-circle"></i>
                            Ready to scan NFC cards. Please tap an employee's NFC card or phone.
                        </div>

                        <!-- iOS Help Link -->
                        <div class="ios-help-section" style="display: none;">
                            <div class="alert alert-light border-info">
                                <div class="d-flex align-items-center">
                                    <i class="fab fa-apple fa-2x text-info mr-3"></i>
                                    <div>
                                        <h6 class="mb-1">Using iPhone?</h6>
                                        <p class="mb-2 small">iOS has specific requirements for NFC functionality.</p>
                                        <a href="{{ route('nfc.ios-instructions') }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-book"></i> View iOS Setup Guide
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Preview -->
                        <div class="card border-primary" id="employee-preview" style="display: none;">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-user"></i> Employee Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> <span id="emp-name"></span></p>
                                        <p><strong>Employee Code:</strong> <span id="emp-code"></span></p>
                                        <p><strong>Department:</strong> <span id="emp-department"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Position:</strong> <span id="emp-position"></span></p>
                                        <p><strong>Next Action:</strong> <span id="next-action" class="badge"></span></p>
                                        <p><strong>Last Action:</strong> <span id="last-action-time"></span></p>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-success btn-lg" id="confirm-attendance">
                                        <i class="fas fa-check"></i> Confirm <span id="action-text">Check In</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Manual NFC Input (Fallback) -->
                        <div class="card mt-3">
                            <div class="card-body">
                                <h6 class="card-title">Manual NFC Input (Fallback)</h6>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="manual-nfc-input" 
                                           placeholder="Enter NFC Card ID manually if scanning fails">
                                    <button type="button" class="btn btn-secondary mt-2" id="manual-lookup">
                                        <i class="fas fa-search"></i> Lookup Employee
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            <i class="fas fa-clock text-success"></i> 
                            Recent Activity
                        </h4>
                        <div id="recent-attendance" class="activity-list">
                            <div class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            <i class="fas fa-chart-bar text-info"></i> 
                            Today's Stats
                        </h4>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-right">
                                    <h3 class="text-success" id="checkin-count">0</h3>
                                    <p class="text-muted mb-0">Check-ins</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <h3 class="text-warning" id="checkout-count">0</h3>
                                <p class="text-muted mb-0">Check-outs</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- NFC Registration Modal -->
<div class="modal fade" id="registerNfcModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register New NFC Card</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="register-nfc-form">
                    <div class="form-group">
                        <label>Employee Code</label>
                        <input type="text" class="form-control" id="register-emp-code" required>
                    </div>
                    <div class="form-group">
                        <label>NFC Card ID</label>
                        <input type="text" class="form-control" id="register-nfc-id" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-nfc-registration">
                    <i class="fas fa-save"></i> Register Card
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="/js/nfc-ios-compat.js"></script>
<script>
class NFCScanner {
    constructor() {
        this.currentEmployee = null;
        this.init();
        this.loadRecentAttendance();
        this.setupPeriodicRefresh();
    }

    init() {
        // Check device type and NFC capabilities
        this.detectDevice();
        
        // Check for Web NFC API support
        if ('NDEFReader' in window && !this.isIOS) {
            this.initWebNFC();
        } else {
            this.showIOSCompatibleMode();
        }

        // Setup event listeners
        this.setupEventListeners();
    }

    detectDevice() {
        this.isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        this.isAndroid = /Android/.test(navigator.userAgent);
        this.isMobile = this.isIOS || this.isAndroid;
        
        console.log('Device detected:', {
            isIOS: this.isIOS,
            isAndroid: this.isAndroid,
            isMobile: this.isMobile
        });
        
        // Show iOS help section if on iOS
        if (this.isIOS) {
            $('.ios-help-section').show();
        }
    }

    async initWebNFC() {
        try {
            const ndef = new NDEFReader();
            await ndef.scan();
            
            this.updateStatus('NFC Scanner active. Tap an NFC card or phone.', 'success');

            ndef.addEventListener('reading', ({ message, serialNumber }) => {
                this.handleNFCRead(serialNumber);
            });

            ndef.addEventListener('readingerror', () => {
                this.updateStatus('Error reading NFC card. Please try again.', 'danger');
            });

        } catch (error) {
            console.error('Web NFC Error:', error);
            this.showFallbackMode();
        }
    }

    showIOSCompatibleMode() {
        if (this.isIOS) {
            this.updateStatus('iOS detected. Use camera scan or manual input for NFC cards.', 'info');
            this.initIOSNFCSupport();
        } else {
            this.updateStatus('Web NFC not supported. Use manual input below.', 'warning');
            $('#manual-nfc-input').focus();
        }
    }

    async initIOSNFCSupport() {
        // iOS NFC support through Core NFC (limited to NDEF reading)
        if ('NDEFReader' in window) {
            try {
                const ndef = new NDEFReader();
                
                // Add iOS-specific button to trigger NFC scan
                this.addIOSNFCButton();
                
                this.updateStatus('iOS NFC ready. Tap "Scan NFC" button when ready.', 'success');
                
            } catch (error) {
                console.log('iOS NFC setup error:', error);
                this.updateStatus('iOS NFC not available. Use manual input or camera scan.', 'warning');
                this.initCameraScanner();
            }
        } else {
            this.updateStatus('NFC not supported on this iOS version. Use manual input or camera scan.', 'warning');
            this.initCameraScanner();
        }
    }

    addIOSNFCButton() {
        const buttonHtml = `
            <div class="ios-nfc-section mt-3 mb-3">
                <div class="text-center">
                    <button type="button" class="btn btn-primary btn-lg" id="ios-nfc-scan">
                        <i class="fas fa-mobile-alt"></i> Scan NFC Card (iOS)
                    </button>
                    <p class="text-muted mt-2 small">
                        Tap this button, then hold your iPhone near an NFC card when prompted
                    </p>
                </div>
            </div>
        `;
        $('.card-body').first().append(buttonHtml);
        
        $('#ios-nfc-scan').on('click', () => this.startIOSNFCScan());
    }

    async startIOSNFCScan() {
        try {
            const ndef = new NDEFReader();
            
            this.updateStatus('Hold your iPhone near an NFC card...', 'info');
            
            const abortController = new AbortController();
            
            // Set timeout for scan
            setTimeout(() => {
                abortController.abort();
                this.updateStatus('NFC scan timeout. Please try again.', 'warning');
            }, 10000);
            
            await ndef.scan({ signal: abortController.signal });
            
            ndef.addEventListener('reading', ({ message, serialNumber }) => {
                abortController.abort();
                this.handleNFCRead(serialNumber);
            });
            
        } catch (error) {
            console.error('iOS NFC scan error:', error);
            this.updateStatus('NFC scan failed. Try manual input instead.', 'danger');
        }
    }

    initCameraScanner() {
        // Add camera scanner for QR codes as NFC alternative
        const cameraHtml = `
            <div class="camera-scanner-section mt-3">
                <div class="card border-info">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-camera"></i> Camera Scanner (Alternative)
                        </h6>
                        <p class="small text-muted">
                            Scan QR codes on employee badges as an alternative to NFC
                        </p>
                        <button type="button" class="btn btn-info" id="start-camera-scan">
                            <i class="fas fa-camera"></i> Start Camera Scan
                        </button>
                        <video id="camera-preview" style="display: none; width: 100%; max-width: 300px; margin-top: 10px;"></video>
                    </div>
                </div>
            </div>
        `;
        $('.card-body').first().append(cameraHtml);
        
        $('#start-camera-scan').on('click', () => this.startCameraScanner());
        
        // QR Scanner button
        $('#start-camera-scanner').on('click', () => this.startQRScanner());
        $('#stop-camera-scanner').on('click', () => this.stopQRScanner());
    }

    async startCameraScanner() {
        try {
            const video = document.getElementById('camera-preview');
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' } 
            });
            
            video.srcObject = stream;
            video.style.display = 'block';
            video.play();
            
            this.updateStatus('Camera active. Point at QR code on employee badge.', 'info');
            
            // In a real implementation, you'd use a QR code library here
            // For now, just show instructions
            setTimeout(() => {
                this.updateStatus('Camera scanner ready. Manual input available below.', 'success');
            }, 2000);
            
        } catch (error) {
            console.error('Camera access error:', error);
            this.updateStatus('Camera access denied. Use manual input instead.', 'warning');
        }
    }

    async startQRScanner() {
        try {
            this.cameraStream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            });
            
            const video = document.getElementById('camera-preview');
            video.srcObject = this.cameraStream;
            
            // Show camera interface
            $('#camera-container').slideDown();
            $('#scanner-status').show();
            $('#start-camera-scanner').hide();
            $('#stop-camera-scanner').show();
            
            this.updateStatus('Camera scanner active. Point at QR codes to scan.', 'info');
            
            // Start QR detection
            this.startQRDetection(video);
            
        } catch (error) {
            console.error('Camera scanner error:', error);
            this.updateStatus('Could not start camera scanner. Check permissions.', 'danger');
        }
    }
    
    stopQRScanner() {
        if (this.cameraStream) {
            this.cameraStream.getTracks().forEach(track => track.stop());
            this.cameraStream = null;
        }
        
        if (this.qrDetectionInterval) {
            clearInterval(this.qrDetectionInterval);
            this.qrDetectionInterval = null;
        }
        
        $('#camera-container').slideUp();
        $('#scanner-status').hide();
        $('#start-camera-scanner').show();
        $('#stop-camera-scanner').hide();
        
        this.updateStatus('Camera scanner stopped.', 'info');
    }
    
    startQRDetection(video) {
        // Simple QR detection simulation
        // In production, use a library like jsQR or ZXing
        this.qrDetectionInterval = setInterval(() => {
            this.detectQRFromVideo(video);
        }, 1000);
    }
    
    detectQRFromVideo(video) {
        // Simulate QR detection
        // In real implementation, capture frame and process with QR library
        
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        if (canvas.width > 0 && canvas.height > 0) {
            ctx.drawImage(video, 0, 0);
            
            // Simulate finding QR code (random chance for demo)
            if (Math.random() < 0.1) { // 10% chance per second
                const mockQRData = this.generateMockQRData();
                this.handleQRCodeDetected(mockQRData);
            }
        }
    }
    
    generateMockQRData() {
        const employees = [
            { emp_code: 'EMP001', name: 'John Smith', role: 'employee' },
            { emp_code: 'SA001', name: 'Alex Johnson', role: 'super_admin' },
            { emp_code: 'HR001', name: 'Sarah Wilson', role: 'hr_manager' },
            { emp_code: 'MGR001', name: 'David Martinez', role: 'manager' }
        ];
        
        const employee = employees[Math.floor(Math.random() * employees.length)];
        
        return JSON.stringify({
            type: 'employee_attendance',
            emp_code: employee.emp_code,
            name: employee.name,
            nfc_id: employee.emp_code + '-QR',
            role: employee.role,
            timestamp: Date.now(),
            scan_method: 'qr_code'
        });
    }
    
    handleQRCodeDetected(qrDataString) {
        try {
            const qrData = JSON.parse(qrDataString);
            
            if (qrData.type === 'employee_attendance') {
                this.stopQRScanner();
                
                $('#scanner-status').removeClass('alert-secondary').addClass('alert-success')
                    .html('<i class="fas fa-check"></i> QR Code detected successfully!');
                
                // Simulate employee lookup
                this.handleNFCRead(qrData.nfc_id);
                
                setTimeout(() => {
                    $('#scanner-status').removeClass('alert-success').addClass('alert-secondary').hide();
                }, 3000);
            }
            
        } catch (error) {
            console.error('Invalid QR code data:', error);
        }
    }

    setupEventListeners() {
        $('#manual-lookup').on('click', () => {
            const nfcId = $('#manual-nfc-input').val().trim();
            if (nfcId) {
                this.handleNFCRead(nfcId);
            }
        });

        $('#manual-nfc-input').on('keypress', (e) => {
            if (e.which === 13) { // Enter key
                const nfcId = $('#manual-nfc-input').val().trim();
                if (nfcId) {
                    this.handleNFCRead(nfcId);
                }
            }
        });

        $('#confirm-attendance').on('click', () => {
            this.processAttendance();
        });

        $('#save-nfc-registration').on('click', () => {
            this.registerNfcCard();
        });
    }

    async handleNFCRead(nfcId) {
        this.updateStatus('Reading NFC card...', 'info');
        
        try {
            const response = await fetch('{{ route("nfc.employee-info") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({ nfc_id: nfcId })
            });

            const data = await response.json();

            if (data.success) {
                this.showEmployeePreview(data.data, nfcId);
            } else {
                this.handleUnknownCard(nfcId);
            }

        } catch (error) {
            console.error('Error:', error);
            this.updateStatus('Error processing NFC card. Please try again.', 'danger');
        }
    }

    showEmployeePreview(employee, nfcId) {
        this.currentEmployee = { ...employee, nfc_id: nfcId };
        
        $('#emp-name').text(employee.name);
        $('#emp-code').text(employee.emp_code);
        $('#emp-department').text(employee.department || 'N/A');
        $('#emp-position').text(employee.position || 'N/A');
        
        const nextAction = employee.next_action;
        const actionBadge = nextAction === 'check_in' ? 'badge-success' : 'badge-warning';
        const actionText = nextAction === 'check_in' ? 'Check In' : 'Check Out';
        
        $('#next-action').removeClass('badge-success badge-warning').addClass(actionBadge).text(actionText);
        $('#action-text').text(actionText);
        $('#last-action-time').text(employee.last_action_time || 'No previous record');
        
        $('#employee-preview').show();
        this.updateStatus(`Employee found: ${employee.name}`, 'success');
    }

    handleUnknownCard(nfcId) {
        this.updateStatus('NFC card not registered. Would you like to register it?', 'warning');
        $('#register-nfc-id').val(nfcId);
        $('#registerNfcModal').modal('show');
    }

    async processAttendance() {
        if (!this.currentEmployee) return;

        const attendanceData = {
            emp_code: this.currentEmployee.emp_code,
            nfc_id: this.currentEmployee.nfc_id,
            location: await this.getCurrentLocation(),
            terminal_alias: 'Web NFC Scanner',
            area_alias: 'Office'
        };

        try {
            const response = await fetch('{{ route("nfc.attendance") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(attendanceData)
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccessMessage(data.message);
                this.resetScanner();
                this.loadRecentAttendance();
            } else {
                this.updateStatus('Error: ' + data.message, 'danger');
            }

        } catch (error) {
            console.error('Error:', error);
            this.updateStatus('Error processing attendance. Please try again.', 'danger');
        }
    }

    async getCurrentLocation() {
        return new Promise((resolve) => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        resolve({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        });
                    },
                    () => resolve(null),
                    { timeout: 5000 }
                );
            } else {
                resolve(null);
            }
        });
    }

    async registerNfcCard() {
        const empCode = $('#register-emp-code').val().trim();
        const nfcId = $('#register-nfc-id').val().trim();

        if (!empCode || !nfcId) {
            alert('Please fill in all fields');
            return;
        }

        try {
            const response = await fetch('{{ route("nfc.register-card") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({ emp_code: empCode, nfc_id: nfcId })
            });

            const data = await response.json();

            if (data.success) {
                $('#registerNfcModal').modal('hide');
                this.updateStatus('NFC card registered successfully!', 'success');
                $('#register-nfc-form')[0].reset();
            } else {
                alert('Error: ' + data.message);
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Error registering NFC card. Please try again.');
        }
    }

    async loadRecentAttendance() {
        try {
            const response = await fetch('{{ route("nfc.recent-attendance") }}');
            const data = await response.json();

            if (data.success) {
                this.displayRecentAttendance(data.data);
                this.updateStats(data.data);
            }

        } catch (error) {
            console.error('Error loading recent attendance:', error);
        }
    }

    displayRecentAttendance(records) {
        const container = $('#recent-attendance');
        
        if (records.length === 0) {
            container.html('<div class="text-center text-muted">No recent activity</div>');
            return;
        }

        const html = records.map(record => `
            <div class="activity-item mb-3">
                <div class="d-flex align-items-center">
                    <div class="activity-icon ${record.action === 'Check In' ? 'bg-success' : 'bg-warning'}">
                        <i class="fas ${record.action === 'Check In' ? 'fa-sign-in-alt' : 'fa-sign-out-alt'}"></i>
                    </div>
                    <div class="activity-content ml-3">
                        <h6 class="mb-1">${record.name}</h6>
                        <p class="text-muted mb-0 small">
                            ${record.action} at ${record.time}
                            <br><small>${record.terminal}</small>
                        </p>
                    </div>
                </div>
            </div>
        `).join('');

        container.html(html);
    }

    updateStats(records) {
        const checkins = records.filter(r => r.action === 'Check In').length;
        const checkouts = records.filter(r => r.action === 'Check Out').length;
        
        $('#checkin-count').text(checkins);
        $('#checkout-count').text(checkouts);
    }

    updateStatus(message, type) {
        const alertClass = `alert-${type}`;
        $('#scanner-status')
            .removeClass('alert-info alert-success alert-warning alert-danger')
            .addClass(alertClass)
            .html(`<i class="fas fa-info-circle"></i> ${message}`);
    }

    showSuccessMessage(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }

    resetScanner() {
        this.currentEmployee = null;
        $('#employee-preview').hide();
        $('#manual-nfc-input').val('');
        this.updateStatus('Ready to scan next NFC card.', 'info');
    }

    setupPeriodicRefresh() {
        // Refresh recent attendance every 30 seconds
        setInterval(() => {
            this.loadRecentAttendance();
        }, 30000);
    }
}

// Initialize scanner when page loads
$(document).ready(() => {
    new NFCScanner();
});
</script>

<style>
.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.activity-item {
    padding: 10px;
    border-left: 3px solid #e9ecef;
    border-radius: 4px;
    background: #f8f9fa;
}

#employee-preview {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.btn-lg {
    padding: 12px 30px;
    font-size: 18px;
}
</style>
@endsection