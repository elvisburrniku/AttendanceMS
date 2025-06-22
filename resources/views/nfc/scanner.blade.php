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
<script>
class NFCScanner {
    constructor() {
        this.currentEmployee = null;
        this.init();
        this.loadRecentAttendance();
        this.setupPeriodicRefresh();
    }

    init() {
        // Check for Web NFC API support
        if ('NDEFReader' in window) {
            this.initWebNFC();
        } else {
            this.showFallbackMode();
        }

        // Setup event listeners
        this.setupEventListeners();
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

    showFallbackMode() {
        this.updateStatus('Web NFC not supported. Use manual input below.', 'warning');
        $('#manual-nfc-input').focus();
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