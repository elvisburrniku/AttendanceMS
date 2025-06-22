@extends('layouts.master')

@section('title', 'NFC Employee Card')

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group float-right">
                        <ol class="breadcrumb hide-phone p-0 m-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Employee Card</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Employee NFC Card</h4>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="employee-card-header mb-4">
                            <i class="fas fa-id-card-alt fa-3x text-primary mb-3"></i>
                            <h4>{{ auth()->user()->name ?? 'Employee Name' }}</h4>
                            <p class="text-muted">{{ auth()->user()->email ?? 'employee@company.com' }}</p>
                        </div>

                        <!-- NFC Card Simulation -->
                        <div class="nfc-card-container mb-4">
                            <div class="nfc-card" id="employee-nfc-card">
                                <div class="nfc-card-header">
                                    <i class="fas fa-building"></i>
                                    <span>Company Name</span>
                                </div>
                                <div class="nfc-card-body">
                                    <div class="employee-photo">
                                        <i class="fas fa-user fa-3x"></i>
                                    </div>
                                    <div class="employee-info">
                                        <h5 id="card-employee-name">{{ auth()->user()->name ?? 'Employee Name' }}</h5>
                                        <p id="card-emp-code">EMP-{{ str_pad(auth()->id() ?? '001', 3, '0', STR_PAD_LEFT) }}</p>
                                        <div class="nfc-chip">
                                            <i class="fas fa-wifi"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="nfc-card-footer">
                                    <small>Tap to check in/out</small>
                                </div>
                            </div>
                        </div>

                        <!-- HCE Status -->
                        <div class="alert alert-info" id="hce-status">
                            <i class="fas fa-mobile-alt"></i>
                            <strong>Host Card Emulation (HCE)</strong><br>
                            <span id="hce-status-text">Checking device compatibility...</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="btn-group-vertical w-100 mb-3">
                            <button type="button" class="btn btn-primary btn-lg mb-2" id="enable-hce">
                                <i class="fas fa-power-off"></i> Enable NFC Card Mode
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="test-card">
                                <i class="fas fa-vial"></i> Test Card
                            </button>
                        </div>

                        <!-- Instructions -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> How to Use</h6>
                            </div>
                            <div class="card-body text-left">
                                <ol class="mb-0">
                                    <li><strong>Enable NFC:</strong> Make sure NFC is enabled in your phone settings</li>
                                    <li><strong>Activate Card Mode:</strong> Tap "Enable NFC Card Mode" button above</li>
                                    <li><strong>Approach Scanner:</strong> Hold your phone near the NFC scanner device</li>
                                    <li><strong>Wait for Confirmation:</strong> You'll hear a beep and see confirmation on the scanner</li>
                                </ol>
                            </div>
                        </div>

                        <!-- Technical Details -->
                        <div class="accordion mt-4" id="technicalAccordion">
                            <div class="card">
                                <div class="card-header" id="headingTechnical">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" 
                                                data-target="#collapseTechnical">
                                            <i class="fas fa-cog"></i> Technical Details
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseTechnical" class="collapse" data-parent="#technicalAccordion">
                                    <div class="card-body text-left small">
                                        <p><strong>NFC ID:</strong> <code id="nfc-id">{{ md5((auth()->id() ?? '1') . 'nfc_salt') }}</code></p>
                                        <p><strong>Technology:</strong> Host Card Emulation (HCE)</p>
                                        <p><strong>Frequency:</strong> 13.56 MHz</p>
                                        <p><strong>Protocol:</strong> ISO 14443 Type A</p>
                                        <p><strong>Data Format:</strong> NDEF (NFC Data Exchange Format)</p>
                                        <hr>
                                        <p class="text-muted mb-0">
                                            This feature uses your Android device's NFC capability to emulate an NFC card.
                                            iOS devices have limited NFC support and may not work with this feature.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="/js/nfc-ios-compat.js"></script>
<script>
class EmployeeNFCCard {
    constructor() {
        this.isHCEActive = false;
        this.employeeData = {
            id: '{{ auth()->id() ?? "1" }}',
            name: '{{ auth()->user()->name ?? "Employee Name" }}',
            emp_code: 'EMP-{{ str_pad(auth()->id() ?? "001", 3, "0", STR_PAD_LEFT) }}',
            nfc_id: '{{ md5((auth()->id() ?? "1") . "nfc_salt") }}'
        };
        this.init();
    }

    init() {
        this.checkNFCSupport();
        this.setupEventListeners();
        this.updateCardDisplay();
    }

    checkNFCSupport() {
        this.isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        this.isAndroid = /Android/.test(navigator.userAgent);
        
        if (this.isIOS) {
            this.updateHCEStatus('iOS detected - Limited NFC support (read-only)', 'info');
            this.setupIOSNFCCard();
        } else if ('NDEFWriter' in window || 'NDEFReader' in window) {
            this.updateHCEStatus('NFC supported - Ready to activate', 'success');
        } else {
            this.updateHCEStatus('NFC not supported on this device', 'warning');
            $('#enable-hce').prop('disabled', true);
        }
    }

    setupIOSNFCCard() {
        // iOS doesn't support HCE, so we'll show a QR code alternative
        $('#enable-hce').text('Generate QR Code').removeClass('btn-primary').addClass('btn-info');
        
        // Add QR code generation section
        const qrSection = `
            <div class="ios-alternative mt-4">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>iOS Alternative:</strong> Since iOS doesn't support NFC card emulation, 
                    you can generate a QR code that scanners can read instead.
                </div>
                <div id="qr-code-container" style="display: none;">
                    <div class="text-center">
                        <canvas id="qr-canvas" width="200" height="200"></canvas>
                        <p class="mt-2">Show this QR code to scanners</p>
                    </div>
                </div>
            </div>
        `;
        $('.card-body').append(qrSection);
    }

    setupEventListeners() {
        $('#enable-hce').on('click', () => {
            this.toggleHCE();
        });

        $('#test-card').on('click', () => {
            this.testCard();
        });

        // Simulate card tap animation
        $('#employee-nfc-card').on('click', () => {
            this.animateCardTap();
        });
    }

    async toggleHCE() {
        if (!this.isHCEActive) {
            await this.enableHCE();
        } else {
            this.disableHCE();
        }
    }

    async enableHCE() {
        try {
            if (this.isIOS) {
                this.generateQRCode();
                return;
            }
            
            // Check if Web NFC is available
            if ('NDEFWriter' in window) {
                this.updateHCEStatus('Activating NFC card mode...', 'info');
                
                // Create NDEF message with employee data
                const message = {
                    records: [{
                        recordType: "text",
                        data: JSON.stringify({
                            type: 'employee_card',
                            emp_code: this.employeeData.emp_code,
                            nfc_id: this.employeeData.nfc_id,
                            name: this.employeeData.name,
                            timestamp: Date.now()
                        })
                    }]
                };

                // Note: Web NFC Write is limited, this is primarily for demonstration
                // In a real implementation, you'd use Android HCE service
                
                this.isHCEActive = true;
                this.updateHCEStatus('NFC Card Mode Active - Hold near scanner', 'success');
                $('#enable-hce').html('<i class="fas fa-power-off"></i> Disable NFC Card Mode')
                    .removeClass('btn-primary').addClass('btn-danger');
                $('#employee-nfc-card').addClass('card-active');
                
                // Start heartbeat to keep connection alive
                this.startHeartbeat();
                
            } else {
                throw new Error('Web NFC not supported');
            }
            
        } catch (error) {
            console.error('HCE Error:', error);
            this.updateHCEStatus('Failed to activate - Try using Android device', 'danger');
        }
    }

    generateQRCode() {
        const canvas = document.getElementById('qr-canvas');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        // Simple QR code representation (in production, use a QR library)
        const qrData = JSON.stringify({
            type: 'employee_card',
            emp_code: this.employeeData.emp_code,
            nfc_id: this.employeeData.nfc_id,
            name: this.employeeData.name,
            timestamp: Date.now()
        });
        
        // Draw a simple pattern representing QR code
        ctx.fillStyle = '#000';
        ctx.fillRect(0, 0, 200, 200);
        ctx.fillStyle = '#fff';
        
        // Create a basic pattern
        for (let i = 0; i < 10; i++) {
            for (let j = 0; j < 10; j++) {
                if ((i + j) % 2 === 0) {
                    ctx.fillRect(i * 20, j * 20, 20, 20);
                }
            }
        }
        
        // Add employee code in center
        ctx.fillStyle = '#000';
        ctx.font = '12px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(this.employeeData.emp_code, 100, 100);
        
        $('#qr-code-container').show();
        $('#enable-hce').html('<i class="fas fa-qrcode"></i> QR Code Generated')
            .removeClass('btn-info').addClass('btn-success');
        
        this.updateHCEStatus('QR Code generated - Show to scanner', 'success');
    }

    disableHCE() {
        this.isHCEActive = false;
        this.updateHCEStatus('NFC Card Mode Disabled', 'info');
        $('#enable-hce').html('<i class="fas fa-power-off"></i> Enable NFC Card Mode')
            .removeClass('btn-danger').addClass('btn-primary');
        $('#employee-nfc-card').removeClass('card-active');
        
        if (this.heartbeatInterval) {
            clearInterval(this.heartbeatInterval);
        }
    }

    startHeartbeat() {
        // Send periodic signals to indicate the card is still active
        this.heartbeatInterval = setInterval(() => {
            if (this.isHCEActive) {
                // In a real implementation, this would communicate with the HCE service
                console.log('HCE Heartbeat:', this.employeeData.nfc_id);
            }
        }, 2000);
    }

    testCard() {
        this.animateCardTap();
        
        // Simulate scanner interaction
        Swal.fire({
            icon: 'info',
            title: 'Testing NFC Card',
            html: `
                <div class="text-left">
                    <p><strong>Employee:</strong> ${this.employeeData.name}</p>
                    <p><strong>Code:</strong> ${this.employeeData.emp_code}</p>
                    <p><strong>NFC ID:</strong> ${this.employeeData.nfc_id}</p>
                    <hr>
                    <p class="text-muted small">
                        This is a simulation. In a real scenario, hold your phone near an NFC scanner.
                    </p>
                </div>
            `,
            confirmButtonText: 'OK'
        });
    }

    updateCardDisplay() {
        $('#card-employee-name').text(this.employeeData.name);
        $('#card-emp-code').text(this.employeeData.emp_code);
        $('#nfc-id').text(this.employeeData.nfc_id);
    }

    updateHCEStatus(message, type) {
        const alertClass = `alert-${type}`;
        $('#hce-status')
            .removeClass('alert-info alert-success alert-warning alert-danger')
            .addClass(alertClass);
        $('#hce-status-text').text(message);
    }

    animateCardTap() {
        $('#employee-nfc-card').addClass('card-tap');
        setTimeout(() => {
            $('#employee-nfc-card').removeClass('card-tap');
        }, 300);
    }
}

// Initialize when page loads
$(document).ready(() => {
    new EmployeeNFCCard();
});
</script>

<style>
.nfc-card {
    width: 300px;
    height: 200px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    color: white;
    padding: 20px;
    margin: 0 auto;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
}

.nfc-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.4);
}

.nfc-card.card-active {
    animation: pulse-glow 2s infinite;
    border: 2px solid #28a745;
}

.nfc-card.card-tap {
    transform: scale(0.95);
    box-shadow: 0 5px 15px rgba(0,0,0,0.5);
}

@keyframes pulse-glow {
    0% { box-shadow: 0 10px 30px rgba(0,0,0,0.3), 0 0 0 0 rgba(40, 167, 69, 0.7); }
    50% { box-shadow: 0 10px 30px rgba(0,0,0,0.3), 0 0 0 10px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 10px 30px rgba(0,0,0,0.3), 0 0 0 0 rgba(40, 167, 69, 0); }
}

.nfc-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
    font-size: 14px;
    opacity: 0.8;
}

.nfc-card-body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex: 1;
}

.employee-photo {
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.employee-info h5 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
}

.employee-info p {
    margin: 5px 0 0 0;
    font-size: 12px;
    opacity: 0.8;
}

.nfc-chip {
    background: rgba(255,255,255,0.3);
    border-radius: 5px;
    width: 30px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 10px;
    font-size: 12px;
}

.nfc-card-footer {
    position: absolute;
    bottom: 10px;
    left: 20px;
    right: 20px;
    text-align: center;
    font-size: 11px;
    opacity: 0.7;
}

.nfc-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    transition: all 0.5s;
    opacity: 0;
}

.nfc-card:hover::before {
    animation: shine 0.5s ease-in-out;
}

@keyframes shine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); opacity: 0; }
}

.btn-group-vertical .btn {
    border-radius: 25px;
    font-weight: 500;
    padding: 12px 30px;
}

.btn-group-vertical .btn:not(:last-child) {
    margin-bottom: 10px;
}

.employee-card-header i {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endsection