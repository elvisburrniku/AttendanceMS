@extends('layouts.master')

@section('title', 'NFC Employee Card')

@push('head')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#4285f4">
<link rel="manifest" href="{{ asset('nfc-manifest.json') }}">
<style>
    .card-container {
        max-width: 400px;
        margin: 0 auto;
        padding: 20px;
    }
    .employee-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        margin-bottom: 20px;
        position: relative;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    }
    .nfc-chip {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 40px;
        height: 30px;
        background: linear-gradient(45deg, #ffd700, #ffed4e);
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        color: #333;
        font-weight: bold;
    }
    .employee-photo {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
    }
    .employee-name {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .employee-details {
        opacity: 0.9;
        margin-bottom: 20px;
    }
    .card-id {
        background: rgba(255,255,255,0.1);
        padding: 10px;
        border-radius: 10px;
        font-family: monospace;
        font-size: 1.1rem;
        letter-spacing: 2px;
    }
    .status-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }
    .status-active { background: #00ff00; }
    .status-inactive { background: #ff4444; }
    .control-panel {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin: 20px 0;
        color: #333;
    }
    .btn-card {
        width: 100%;
        padding: 15px;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        margin: 10px 0;
        transition: all 0.3s ease;
    }
    .btn-primary { background: #4285f4; color: white; }
    .btn-success { background: #28a745; color: white; }
    .btn-warning { background: #ffc107; color: #212529; }
    .btn-card:hover { transform: translateY(-2px); }
    .btn-card:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    .nfc-broadcast {
        background: rgba(0,255,0,0.1);
        border: 2px solid #00ff00;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        margin: 20px 0;
        animation: broadcast 2s infinite;
    }
    @keyframes broadcast {
        0% { box-shadow: 0 0 0 0 rgba(0,255,0,0.4); }
        70% { box-shadow: 0 0 0 20px rgba(0,255,0,0); }
        100% { box-shadow: 0 0 0 0 rgba(0,255,0,0); }
    }
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
    }
    .alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-container">
                <!-- Employee Card -->
                <div class="employee-card" id="employeeCard">
                    <div class="nfc-chip">NFC</div>
                    <div class="employee-photo" id="employeePhoto">👤</div>
                    <div class="employee-name" id="employeeName">Employee Name</div>
                    <div class="employee-details" id="employeeDetails">
                        <div><strong>Code:</strong> <span id="empCode">-</span></div>
                        <div><strong>Department:</strong> <span id="department">-</span></div>
                        <div><strong>Position:</strong> <span id="position">-</span></div>
                    </div>
                    <div class="card-id" id="cardId">
                        <div style="font-size: 0.8rem; margin-bottom: 5px;">CARD ID</div>
                        <span id="nfcId">-</span>
                    </div>
                </div>

                <!-- NFC Not Supported -->
                <div class="alert alert-danger" id="nfcUnsupported" style="display: none;">
                    <strong>NFC Not Supported</strong><br>
                    Your device doesn't support Web NFC. This feature requires Chrome on Android.
                </div>

                <!-- Broadcasting Status -->
                <div class="nfc-broadcast" id="broadcastStatus" style="display: none;">
                    <h5>📡 Broadcasting NFC Signal</h5>
                    <p>Your employee card is now active. Tap on a scanner to check in/out.</p>
                    <button class="btn-card btn-warning" id="stopBroadcast">Stop Broadcasting</button>
                </div>

                <!-- Control Panel -->
                <div class="control-panel">
                    <h5>NFC Controls</h5>
                    
                    <div class="form-group">
                        <label for="employeeSelect">Select Employee</label>
                        <select class="form-control" id="employeeSelect">
                            <option value="">Loading employees...</option>
                        </select>
                    </div>

                    <button class="btn-card btn-primary" id="loadEmployeeBtn">Load Employee Card</button>
                    <button class="btn-card btn-success" id="startBroadcastBtn" disabled>Start NFC Broadcasting</button>
                    
                    <hr>
                    
                    <h6>Quick Actions</h6>
                    <button class="btn-card btn-success" id="quickCheckinBtn" disabled>Quick Check-in</button>
                    <button class="btn-card btn-warning" id="quickCheckoutBtn" disabled>Quick Check-out</button>
                </div>

                <!-- Status Panel -->
                <div class="control-panel">
                    <h6>Status</h6>
                    <div id="statusPanel">
                        <div>Status: <span class="status-indicator status-inactive"></span>Not Active</div>
                        <div>Last Action: <span id="lastAction">Never</span></div>
                        <div>Location: <span id="currentLocation">Unknown</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
class NFCEmployeeCard {
    constructor() {
        this.currentEmployee = null;
        this.isBroadcasting = false;
        this.ndefWriter = null;
        this.init();
    }

    init() {
        this.checkNFCSupport();
        this.bindEvents();
        this.loadEmployeeList();
        this.requestLocationPermission();
    }

    checkNFCSupport() {
        if (!('NDEFWriter' in window)) {
            document.getElementById('nfcUnsupported').style.display = 'block';
            document.getElementById('startBroadcastBtn').disabled = true;
            return false;
        }
        return true;
    }

    bindEvents() {
        document.getElementById('employeeSelect').addEventListener('change', (e) => this.onEmployeeSelect(e));
        document.getElementById('loadEmployeeBtn').addEventListener('click', () => this.loadEmployeeCard());
        document.getElementById('startBroadcastBtn').addEventListener('click', () => this.startBroadcasting());
        document.getElementById('stopBroadcast').addEventListener('click', () => this.stopBroadcasting());
        document.getElementById('quickCheckinBtn').addEventListener('click', () => this.quickAction('check_in'));
        document.getElementById('quickCheckoutBtn').addEventListener('click', () => this.quickAction('check_out'));
    }

    async loadEmployeeList() {
        try {
            // Load employee list from the test data we created
            const employees = [
                { emp_code: 'EMP001', name: 'John Smith', card_no: 'C001' },
                { emp_code: 'EMP002', name: 'Sarah Johnson', card_no: 'C002' },
                { emp_code: 'EMP003', name: 'Michael Davis', card_no: 'C003' },
                { emp_code: 'EMP004', name: 'Emily Brown', card_no: 'C004' },
                { emp_code: 'EMP005', name: 'David Wilson', card_no: 'C005' },
                { emp_code: 'EMP006', name: 'Jennifer Garcia', card_no: 'C006' },
                { emp_code: 'EMP007', name: 'Robert Martinez', card_no: 'C007' },
                { emp_code: 'EMP008', name: 'Lisa Anderson', card_no: 'C008' },
                { emp_code: 'EMP009', name: 'James Taylor', card_no: 'C009' },
                { emp_code: 'EMP010', name: 'Maria Rodriguez', card_no: 'C010' },
                { emp_code: 'EMP011', name: 'Daniel Lee', card_no: 'C011' },
                { emp_code: 'EMP012', name: 'Amanda White', card_no: 'C012' }
            ];

            const select = document.getElementById('employeeSelect');
            select.innerHTML = '<option value="">Select an employee...</option>';
            
            employees.forEach(emp => {
                const option = document.createElement('option');
                option.value = emp.emp_code;
                option.textContent = `${emp.name} (${emp.emp_code})`;
                option.dataset.cardNo = emp.card_no;
                select.appendChild(option);
            });

        } catch (error) {
            console.error('Error loading employees:', error);
        }
    }

    onEmployeeSelect(event) {
        const empCode = event.target.value;
        const loadBtn = document.getElementById('loadEmployeeBtn');
        loadBtn.disabled = !empCode;
    }

    async loadEmployeeCard() {
        const select = document.getElementById('employeeSelect');
        const empCode = select.value;
        const cardNo = select.selectedOptions[0]?.dataset.cardNo;

        if (!empCode || !cardNo) {
            this.showAlert('Please select an employee', 'danger');
            return;
        }

        try {
            const response = await fetch('/nfc/employee-info', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ nfc_id: cardNo })
            });

            const data = await response.json();

            if (data.success) {
                this.currentEmployee = data.data;
                this.currentEmployee.card_no = cardNo;
                this.updateEmployeeCard();
                this.updateStatus();
                document.getElementById('startBroadcastBtn').disabled = false;
                document.getElementById('quickCheckinBtn').disabled = false;
                document.getElementById('quickCheckoutBtn').disabled = false;
            } else {
                this.showAlert(data.message, 'danger');
            }

        } catch (error) {
            console.error('Error loading employee:', error);
            this.showAlert('Failed to load employee information', 'danger');
        }
    }

    updateEmployeeCard() {
        if (!this.currentEmployee) return;

        document.getElementById('employeeName').textContent = this.currentEmployee.name;
        document.getElementById('empCode').textContent = this.currentEmployee.emp_code;
        document.getElementById('department').textContent = this.currentEmployee.department || 'N/A';
        document.getElementById('position').textContent = this.currentEmployee.position || 'N/A';
        document.getElementById('nfcId').textContent = this.currentEmployee.card_no;

        // Update photo with emoji based on name
        const firstLetter = this.currentEmployee.name.charAt(0).toUpperCase();
        document.getElementById('employeePhoto').textContent = firstLetter;
    }

    updateStatus() {
        if (!this.currentEmployee) return;

        const statusPanel = document.getElementById('statusPanel');
        const statusClass = this.isBroadcasting ? 'status-active' : 'status-inactive';
        const statusText = this.isBroadcasting ? 'Broadcasting' : 'Not Active';

        statusPanel.innerHTML = `
            <div>Status: <span class="status-indicator ${statusClass}"></span>${statusText}</div>
            <div>Last Action: <span id="lastAction">${this.currentEmployee.last_action_time || 'Never'}</span></div>
            <div>Location: <span id="currentLocation">Office</span></div>
        `;
    }

    async startBroadcasting() {
        if (!this.checkNFCSupport() || !this.currentEmployee) return;

        try {
            this.ndefWriter = new NDEFWriter();
            
            // Create NFC message with employee data
            const message = {
                records: [{
                    recordType: "text",
                    data: JSON.stringify({
                        emp_code: this.currentEmployee.emp_code,
                        card_no: this.currentEmployee.card_no,
                        name: this.currentEmployee.name
                    })
                }]
            };

            await this.ndefWriter.write(message);
            
            this.isBroadcasting = true;
            document.getElementById('broadcastStatus').style.display = 'block';
            document.getElementById('startBroadcastBtn').disabled = true;
            this.updateStatus();
            
            this.showAlert('NFC broadcasting started successfully!', 'success');

        } catch (error) {
            console.error('NFC Broadcasting Error:', error);
            this.showAlert('Failed to start NFC broadcasting: ' + error.message, 'danger');
        }
    }

    stopBroadcasting() {
        this.isBroadcasting = false;
        this.ndefWriter = null;
        document.getElementById('broadcastStatus').style.display = 'none';
        document.getElementById('startBroadcastBtn').disabled = false;
        this.updateStatus();
        
        this.showAlert('NFC broadcasting stopped', 'info');
    }

    async quickAction(action) {
        if (!this.currentEmployee) return;

        try {
            const location = await this.getCurrentLocation();
            
            const response = await fetch('/nfc/attendance', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    emp_code: this.currentEmployee.emp_code,
                    nfc_id: this.currentEmployee.card_no,
                    location: location,
                    terminal_alias: 'Mobile NFC Card',
                    area_alias: 'Mobile Check-in'
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert(data.message, 'success');
                // Update last action time
                this.currentEmployee.last_action_time = new Date().toLocaleString();
                this.updateStatus();
            } else {
                this.showAlert(data.message, 'danger');
            }

        } catch (error) {
            console.error('Error processing quick action:', error);
            this.showAlert('Failed to process attendance', 'danger');
        }
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

    showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        const container = document.querySelector('.card-container');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new NFCEmployeeCard();
});
</script>
@endpush