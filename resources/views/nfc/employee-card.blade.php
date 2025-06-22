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
                            <i class="{{ $cardConfig['icon'] ?? 'fas fa-id-card-alt' }} fa-3x text-primary mb-3"></i>
                            <h4>
                                @if(isset($employee) && $employee)
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                @else
                                    {{ auth()->user()->name ?? 'Employee Name' }}
                                @endif
                            </h4>
                            <p class="text-muted">{{ auth()->user()->email ?? 'employee@company.com' }}</p>
                            <div class="role-badge">
                                <span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $userRole ?? 'employee')) }}</span>
                            </div>
                        </div>

                        <!-- Role-Based NFC Card -->
                        <div class="nfc-card-container mb-4">
                            <div class="card role-based-nfc-card" id="employee-nfc-card" data-role="{{ $userRole ?? 'employee' }}">
                                <div class="card-body text-center" style="background: {{ $cardConfig['card_color'] ?? 'linear-gradient(135deg, #d299c2 0%, #fef9d7 100%)' }}; color: white;">
                                    <div class="nfc-card-visual">
                                        <!-- Role-specific header -->
                                        <div class="card-header-info mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge badge-light text-dark">{{ $cardConfig['card_type'] ?? 'STANDARD' }}</span>
                                                <i class="{{ $cardConfig['icon'] ?? 'fas fa-id-card' }} fa-lg"></i>
                                            </div>
                                            <small class="access-level">{{ $cardConfig['access_level'] ?? 'LEVEL 1 - EMPLOYEE' }}</small>
                                        </div>
                                        
                                        <!-- NFC Icon -->
                                        <div class="nfc-icon">
                                            <i class="fas fa-wifi fa-3x"></i>
                                        </div>
                                        
                                        <!-- Employee Information -->
                                        <h4 class="mt-3">
                                            @if(isset($employee) && $employee)
                                                {{ $employee->first_name }} {{ $employee->last_name }}
                                            @else
                                                {{ auth()->user()->name ?? 'Employee Name' }}
                                            @endif
                                        </h4>
                                        <p class="emp-code">
                                            @if(isset($employee) && $employee)
                                                {{ $employee->emp_code }}
                                            @else
                                                EMP-{{ str_pad(auth()->id() ?? '001', 3, '0', STR_PAD_LEFT) }}
                                            @endif
                                        </p>
                                        
                                        <!-- Department & Position -->
                                        @if(isset($employee) && $employee && ($employee->dept_name || $employee->position_name))
                                        <div class="employee-details mb-3">
                                            @if($employee->dept_name)
                                                <small class="badge badge-light text-dark mr-1">{{ $employee->dept_name }}</small>
                                            @endif
                                            @if($employee->position_name)
                                                <small class="badge badge-light text-dark">{{ $employee->position_name }}</small>
                                            @endif
                                        </div>
                                        @endif
                                        
                                        <!-- NFC ID -->
                                        <div class="nfc-id">
                                            <small>NFC ID: {{ (isset($employee) && $employee ? $employee->card_no : null) ?? md5((auth()->id() ?? '1') . 'nfc_salt') }}</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Role-specific permissions footer -->
                                <div class="card-footer bg-light">
                                    <h6 class="text-center mb-2 text-dark">Access Permissions</h6>
                                    <div class="permissions-list">
                                        @if(isset($cardConfig['permissions']))
                                            @foreach($cardConfig['permissions'] as $permission)
                                                <span class="badge badge-primary badge-sm mr-1 mb-1">{{ $permission }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge badge-primary badge-sm mr-1 mb-1">Check In/Out</span>
                                            <span class="badge badge-primary badge-sm mr-1 mb-1">View Schedule</span>
                                        @endif
                                    </div>
                                    
                                    @if(isset($cardConfig['special_features']) && !empty($cardConfig['special_features']))
                                    <div class="special-features mt-2">
                                        <small class="text-muted">Special Features:</small><br>
                                        @foreach($cardConfig['special_features'] as $feature)
                                            <small class="badge badge-info badge-sm mr-1">{{ $feature }}</small>
                                        @endforeach
                                    </div>
                                    @endif
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
                            <button type="button" class="btn btn-success mb-2" id="generate-qr">
                                <i class="fas fa-qrcode"></i> Generate QR Code
                            </button>
                            <button type="button" class="btn btn-outline-secondary mb-2" id="test-card">
                                <i class="fas fa-vial"></i> Test Card
                            </button>
                            <a href="{{ route('nfc.role-switcher') }}" class="btn btn-outline-info">
                                <i class="fas fa-exchange-alt"></i> Try Different Roles
                            </a>
                        </div>

                        <!-- QR Code Display -->
                        <div class="qr-code-section mt-4" id="qr-code-section" style="display: none;">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-qrcode"></i> Employee QR Code</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div id="qr-code-display">
                                        <canvas id="employee-qr-code" width="200" height="200"></canvas>
                                    </div>
                                    <p class="mt-2 mb-0 text-muted small">
                                        Show this QR code to scanners for attendance tracking
                                    </p>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-sm btn-outline-success" id="download-qr">
                                            <i class="fas fa-download"></i> Download QR Code
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="share-qr">
                                            <i class="fas fa-share"></i> Share
                                        </button>
                                    </div>
                                </div>
                            </div>
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
<script src="/js/qr-code-generator.js"></script>
<script>
class EmployeeNFCCard {
    constructor() {
        this.isHCEActive = false;
        this.employeeData = {
            id: '{{ auth()->id() ?? "1" }}',
            name: '@if(isset($employee) && $employee){{ $employee->first_name }} {{ $employee->last_name }}@else{{ auth()->user()->name ?? "Employee Name" }}@endif',
            emp_code: '@if(isset($employee) && $employee){{ $employee->emp_code }}@elseEMP-{{ str_pad(auth()->id() ?? "001", 3, "0", STR_PAD_LEFT) }}@endif',
            nfc_id: '{{ (isset($employee) && $employee ? $employee->card_no : null) ?? md5((auth()->id() ?? "1") . "nfc_salt") }}',
            role: '{{ $userRole ?? "employee" }}',
            access_level: '{{ $cardConfig["access_level"] ?? "LEVEL 1 - EMPLOYEE" }}',
            card_type: '{{ $cardConfig["card_type"] ?? "STANDARD" }}'
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

    generateEmployeeQRCode() {
        const canvas = document.getElementById('employee-qr-code');
        const ctx = canvas.getContext('2d');
        
        // Clear canvas
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, 200, 200);
        
        // Employee data for QR code
        const qrData = JSON.stringify({
            type: 'employee_attendance',
            emp_code: this.employeeData.emp_code,
            name: this.employeeData.name,
            nfc_id: this.employeeData.nfc_id,
            role: this.employeeData.role,
            access_level: this.employeeData.access_level,
            timestamp: Date.now(),
            scan_method: 'qr_code'
        });
        
        // Use QR Code Generator
        QRCodeGenerator.generate(canvas, {
            emp_code: this.employeeData.emp_code,
            name: this.employeeData.name,
            role: this.employeeData.role,
            nfc_id: this.employeeData.nfc_id,
            access_level: this.employeeData.access_level
        });
        
        // Show QR code section
        $('#qr-code-section').slideDown();
        
        // Update button state
        $('#generate-qr').html('<i class="fas fa-check"></i> QR Code Generated').removeClass('btn-success').addClass('btn-outline-success');
        
        console.log('QR Code generated with data:', qrData);
    }
    
    drawQRPattern(ctx, data) {
        // Simple QR-like pattern generation
        const size = 200;
        const modules = 25; // 25x25 grid
        const moduleSize = size / modules;
        
        // Create pattern based on data hash
        const hash = this.simpleHash(data);
        
        ctx.fillStyle = '#000000';
        
        // Draw finder patterns (corners)
        this.drawFinderPattern(ctx, 0, 0, moduleSize);
        this.drawFinderPattern(ctx, (modules - 7) * moduleSize, 0, moduleSize);
        this.drawFinderPattern(ctx, 0, (modules - 7) * moduleSize, moduleSize);
        
        // Draw data modules
        for (let row = 0; row < modules; row++) {
            for (let col = 0; col < modules; col++) {
                // Skip finder pattern areas
                if (this.isFinderPatternArea(row, col, modules)) continue;
                
                // Generate module based on hash and position
                const moduleValue = (hash + row * col) % 3;
                if (moduleValue === 0) {
                    ctx.fillRect(col * moduleSize, row * moduleSize, moduleSize, moduleSize);
                }
            }
        }
        
        // Add employee code in center
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(80, 80, 40, 40);
        ctx.fillStyle = '#000000';
        ctx.font = '10px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(this.employeeData.emp_code, 100, 95);
        ctx.fillText('SCAN', 100, 105);
        ctx.fillText('ME', 100, 115);
    }
    
    drawFinderPattern(ctx, x, y, moduleSize) {
        // Draw 7x7 finder pattern
        ctx.fillStyle = '#000000';
        ctx.fillRect(x, y, 7 * moduleSize, 7 * moduleSize);
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(x + moduleSize, y + moduleSize, 5 * moduleSize, 5 * moduleSize);
        ctx.fillStyle = '#000000';
        ctx.fillRect(x + 2 * moduleSize, y + 2 * moduleSize, 3 * moduleSize, 3 * moduleSize);
    }
    
    isFinderPatternArea(row, col, modules) {
        // Top-left
        if (row < 9 && col < 9) return true;
        // Top-right
        if (row < 9 && col >= modules - 8) return true;
        // Bottom-left
        if (row >= modules - 8 && col < 9) return true;
        return false;
    }
    
    simpleHash(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32-bit integer
        }
        return Math.abs(hash);
    }
    
    downloadQRCode() {
        const canvas = document.getElementById('employee-qr-code');
        const link = document.createElement('a');
        link.download = `${this.employeeData.emp_code}_qr_code.png`;
        link.href = canvas.toDataURL();
        link.click();
    }
    
    shareQRCode() {
        const canvas = document.getElementById('employee-qr-code');
        
        if (navigator.share && canvas.toBlob) {
            canvas.toBlob(blob => {
                const file = new File([blob], `${this.employeeData.emp_code}_qr.png`, { type: 'image/png' });
                navigator.share({
                    title: 'Employee QR Code',
                    text: `QR Code for ${this.employeeData.name} (${this.employeeData.emp_code})`,
                    files: [file]
                }).catch(err => console.log('Error sharing:', err));
            });
        } else {
            // Fallback: copy to clipboard
            this.copyQRToClipboard();
        }
    }
    
    copyQRToClipboard() {
        const canvas = document.getElementById('employee-qr-code');
        canvas.toBlob(blob => {
            const item = new ClipboardItem({ 'image/png': blob });
            navigator.clipboard.write([item]).then(() => {
                alert('QR code copied to clipboard!');
            }).catch(err => {
                console.log('Copy failed:', err);
                alert('Could not copy QR code. Try downloading instead.');
            });
        });
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