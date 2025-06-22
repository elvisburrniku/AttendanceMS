@extends('layouts.master')

@section('title', 'NFC Scanner')

@push('head')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#4285f4">
<link rel="manifest" href="{{ asset('nfc-manifest.json') }}">
<style>
    .scanner-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }
    .scanner-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        margin-bottom: 20px;
    }
    .scan-area {
        background: rgba(255,255,255,0.1);
        border: 3px dashed rgba(255,255,255,0.5);
        border-radius: 15px;
        padding: 40px;
        margin: 20px 0;
        transition: all 0.3s ease;
    }
    .scan-area.scanning {
        border-color: #00ff00;
        background: rgba(0,255,0,0.1);
        animation: pulse 2s infinite;
    }
    .scan-area.error {
        border-color: #ff0000;
        background: rgba(255,0,0,0.1);
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    .nfc-icon {
        font-size: 4rem;
        margin-bottom: 15px;
        opacity: 0.8;
    }
    .status-message {
        font-size: 1.2rem;
        margin: 15px 0;
        font-weight: 500;
    }
    .employee-info {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin: 20px 0;
        color: #333;
        display: none;
    }
    .attendance-log {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
    }
    .log-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    .log-item:last-child {
        border-bottom: none;
    }
    .action-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    .check-in { background: #e8f5e8; color: #2e7d32; }
    .check-out { background: #fff3e0; color: #f57c00; }
    .control-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    .btn-nfc {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-primary { background: #4285f4; color: white; }
    .btn-secondary { background: #6c757d; color: white; }
    .btn-success { background: #28a745; color: white; }
    .btn-warning { background: #ffc107; color: #212529; }
    .btn-nfc:hover { transform: translateY(-2px); }
    .btn-nfc:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
    }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="scanner-container">
                <!-- Scanner Header -->
                <div class="scanner-card">
                    <div class="nfc-icon">📱</div>
                    <h2>NFC Attendance Scanner</h2>
                    <p class="status-message" id="statusMessage">Ready to scan NFC cards</p>
                </div>

                <!-- NFC Support Check -->
                <div class="alert alert-warning" id="nfcUnsupported" style="display: none;">
                    <strong>NFC Not Supported</strong><br>
                    Your browser doesn't support Web NFC. Please use Chrome on Android or enable the feature in chrome://flags
                </div>

                <!-- Scan Area -->
                <div class="scan-area" id="scanArea">
                    <div class="nfc-icon">🔍</div>
                    <h4>Tap NFC Card Here</h4>
                    <p>Bring your NFC card close to the device</p>
                </div>

                <!-- Employee Information -->
                <div class="employee-info" id="employeeInfo">
                    <h5>Employee Information</h5>
                    <div id="employeeDetails"></div>
                    <div class="control-buttons">
                        <button class="btn-nfc btn-success" id="confirmAction">Confirm Check-in</button>
                        <button class="btn-nfc btn-secondary" id="cancelAction">Cancel</button>
                    </div>
                </div>

                <!-- Control Buttons -->
                <div class="control-buttons">
                    <button class="btn-nfc btn-primary" id="startScanBtn">Start NFC Scan</button>
                    <button class="btn-nfc btn-secondary" id="manualEntryBtn">Manual Entry</button>
                    <button class="btn-nfc btn-secondary" id="refreshBtn">Refresh</button>
                </div>

                <!-- Recent Attendance Log -->
                <div class="attendance-log">
                    <h5>Today's Attendance</h5>
                    <div id="attendanceList">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manual Entry Modal -->
<div class="modal fade" id="manualEntryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manual NFC Entry</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="manualEntryForm">
                    <div class="form-group">
                        <label for="manualEmpCode">Employee Code</label>
                        <input type="text" class="form-control" id="manualEmpCode" required>
                    </div>
                    <div class="form-group">
                        <label for="manualNfcId">NFC Card ID</label>
                        <input type="text" class="form-control" id="manualNfcId" required>
                    </div>
                    <div class="form-group">
                        <label for="terminalAlias">Terminal Location</label>
                        <input type="text" class="form-control" id="terminalAlias" value="NFC Scanner" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitManualEntry">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
class NFCScanner {
    constructor() {
        this.isScanning = false;
        this.currentEmployee = null;
        this.init();
    }

    init() {
        this.checkNFCSupport();
        this.bindEvents();
        this.loadRecentAttendance();
        this.requestLocationPermission();
    }

    checkNFCSupport() {
        if (!('NDEFReader' in window)) {
            document.getElementById('nfcUnsupported').style.display = 'block';
            document.getElementById('startScanBtn').disabled = true;
            return false;
        }
        return true;
    }

    bindEvents() {
        document.getElementById('startScanBtn').addEventListener('click', () => this.startNFCScanner());
        document.getElementById('manualEntryBtn').addEventListener('click', () => $('#manualEntryModal').modal('show'));
        document.getElementById('refreshBtn').addEventListener('click', () => this.loadRecentAttendance());
        document.getElementById('confirmAction').addEventListener('click', () => this.confirmAttendance());
        document.getElementById('cancelAction').addEventListener('click', () => this.cancelAction());
        document.getElementById('submitManualEntry').addEventListener('click', () => this.submitManualEntry());
    }

    async startNFCScanner() {
        if (!this.checkNFCSupport()) return;

        try {
            this.isScanning = true;
            this.updateStatus('Scanning for NFC cards...', 'scanning');
            document.getElementById('startScanBtn').disabled = true;

            const ndef = new NDEFReader();
            await ndef.scan();

            ndef.addEventListener("reading", event => {
                this.handleNFCRead(event);
            });

            ndef.addEventListener("readingerror", event => {
                this.updateStatus('Error reading NFC card. Please try again.', 'error');
                this.resetScanner();
            });

        } catch (error) {
            console.error('NFC Error:', error);
            this.updateStatus('Failed to start NFC scanner: ' + error.message, 'error');
            this.resetScanner();
        }
    }

    async handleNFCRead(event) {
        try {
            let nfcId = '';
            
            if (event.serialNumber) {
                nfcId = event.serialNumber;
            } else if (event.message && event.message.records.length > 0) {
                const record = event.message.records[0];
                if (record.recordType === "text") {
                    const textDecoder = new TextDecoder(record.encoding || "utf-8");
                    nfcId = textDecoder.decode(record.data);
                } else {
                    nfcId = Array.from(new Uint8Array(record.data))
                        .map(b => b.toString(16).padStart(2, '0'))
                        .join('');
                }
            }

            if (!nfcId) {
                throw new Error('Could not read NFC ID');
            }

            this.updateStatus('NFC card detected. Looking up employee...', 'scanning');
            await this.lookupEmployee(nfcId);

        } catch (error) {
            console.error('Error processing NFC data:', error);
            this.updateStatus('Error processing NFC card: ' + error.message, 'error');
            this.resetScanner();
        }
    }

    async lookupEmployee(nfcId) {
        try {
            const response = await fetch('/nfc/employee-info', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ nfc_id: nfcId })
            });

            const data = await response.json();

            if (data.success) {
                this.showEmployeeInfo(data.data, nfcId);
            } else {
                this.updateStatus(data.message, 'error');
                this.resetScanner();
            }

        } catch (error) {
            console.error('Error looking up employee:', error);
            this.updateStatus('Failed to lookup employee information', 'error');
            this.resetScanner();
        }
    }

    showEmployeeInfo(employee, nfcId) {
        this.currentEmployee = { ...employee, nfc_id: nfcId };
        
        const actionText = employee.next_action === 'check_in' ? 'Check In' : 'Check Out';
        const actionClass = employee.next_action === 'check_in' ? 'check-in' : 'check-out';
        
        document.getElementById('employeeDetails').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Name:</strong> ${employee.name}<br>
                    <strong>Code:</strong> ${employee.emp_code}<br>
                    <strong>Department:</strong> ${employee.department || 'N/A'}
                </div>
                <div class="col-md-6">
                    <strong>Position:</strong> ${employee.position || 'N/A'}<br>
                    <strong>Next Action:</strong> <span class="action-badge ${actionClass}">${actionText}</span><br>
                    <strong>Last Action:</strong> ${employee.last_action_time || 'Never'}
                </div>
            </div>
        `;

        document.getElementById('confirmAction').textContent = `Confirm ${actionText}`;
        document.getElementById('confirmAction').className = `btn-nfc ${employee.next_action === 'check_in' ? 'btn-success' : 'btn-warning'}`;
        
        document.getElementById('employeeInfo').style.display = 'block';
        this.updateStatus(`Employee found: ${employee.name}`, 'success');
    }

    async confirmAttendance() {
        if (!this.currentEmployee) return;

        try {
            document.getElementById('confirmAction').disabled = true;
            this.updateStatus('Processing attendance...', 'scanning');

            const location = await this.getCurrentLocation();
            
            const response = await fetch('/nfc/attendance', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    emp_code: this.currentEmployee.emp_code,
                    nfc_id: this.currentEmployee.nfc_id,
                    location: location,
                    terminal_alias: 'NFC Scanner',
                    area_alias: 'Office'
                })
            });

            const data = await response.json();

            if (data.success) {
                this.updateStatus(data.message, 'success');
                this.showSuccessAlert(data.message);
                await this.loadRecentAttendance();
            } else {
                this.updateStatus(data.message, 'error');
                this.showErrorAlert(data.message);
            }

        } catch (error) {
            console.error('Error processing attendance:', error);
            this.updateStatus('Failed to process attendance', 'error');
            this.showErrorAlert('Failed to process attendance');
        } finally {
            this.resetScanner();
        }
    }

    cancelAction() {
        this.currentEmployee = null;
        document.getElementById('employeeInfo').style.display = 'none';
        this.resetScanner();
    }

    async submitManualEntry() {
        const empCode = document.getElementById('manualEmpCode').value;
        const nfcId = document.getElementById('manualNfcId').value;
        const terminalAlias = document.getElementById('terminalAlias').value;

        if (!empCode || !nfcId) {
            this.showErrorAlert('Please fill in all required fields');
            return;
        }

        try {
            await this.lookupEmployee(nfcId);
            $('#manualEntryModal').modal('hide');
            document.getElementById('manualEntryForm').reset();
        } catch (error) {
            this.showErrorAlert('Failed to process manual entry');
        }
    }

    async loadRecentAttendance() {
        try {
            const response = await fetch('/nfc/recent-attendance');
            const data = await response.json();

            if (data.success) {
                this.displayAttendanceLog(data.data);
            }
        } catch (error) {
            console.error('Error loading attendance:', error);
        }
    }

    displayAttendanceLog(records) {
        const container = document.getElementById('attendanceList');
        
        if (records.length === 0) {
            container.innerHTML = '<p class="text-center text-muted">No attendance records for today</p>';
            return;
        }

        const html = records.map(record => `
            <div class="log-item">
                <div>
                    <strong>${record.name}</strong> (${record.emp_code})<br>
                    <small class="text-muted">${record.terminal} - ${record.area}</small>
                </div>
                <div class="text-right">
                    <span class="action-badge ${record.action.toLowerCase().replace(' ', '-')}">${record.action}</span><br>
                    <small class="text-muted">${record.time}</small>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    async getCurrentLocation() {
        return new Promise((resolve) => {
            if (!navigator.geolocation) {
                resolve(null);
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    resolve({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    });
                },
                () => resolve(null),
                { timeout: 5000, enableHighAccuracy: false }
            );
        });
    }

    async requestLocationPermission() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(() => {}, () => {});
        }
    }

    updateStatus(message, status = '') {
        document.getElementById('statusMessage').textContent = message;
        const scanArea = document.getElementById('scanArea');
        scanArea.className = `scan-area ${status}`;
    }

    resetScanner() {
        this.isScanning = false;
        document.getElementById('startScanBtn').disabled = false;
        document.getElementById('confirmAction').disabled = false;
        document.getElementById('employeeInfo').style.display = 'none';
        this.currentEmployee = null;
        
        setTimeout(() => {
            this.updateStatus('Ready to scan NFC cards', '');
        }, 3000);
    }

    showSuccessAlert(message) {
        this.showAlert(message, 'success');
    }

    showErrorAlert(message) {
        this.showAlert(message, 'danger');
    }

    showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        const container = document.querySelector('.scanner-container');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new NFCScanner();
});
</script>
@endpush