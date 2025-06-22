@extends('layouts.master')

@section('title', 'iOS NFC Setup Instructions')

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group float-right">
                        <a href="{{ route('nfc.scanner') }}" class="btn btn-primary">
                            <i class="fas fa-mobile-alt"></i> Go to Scanner
                        </a>
                    </div>
                    <h4 class="page-title">iOS NFC Setup Instructions</h4>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="fab fa-apple fa-3x text-primary mb-3"></i>
                            <h4>Setting up NFC on iPhone</h4>
                            <p class="text-muted">Follow these steps to use NFC features on your iPhone</p>
                        </div>

                        <!-- iOS Version Requirements -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Requirements</h6>
                            <ul class="mb-0">
                                <li><strong>iOS Version:</strong> iOS 11 or later</li>
                                <li><strong>Device:</strong> iPhone 7 or newer</li>
                                <li><strong>Browser:</strong> Safari (recommended)</li>
                                <li><strong>Internet:</strong> Active internet connection</li>
                            </ul>
                        </div>

                        <!-- Step-by-step Instructions -->
                        <div class="instruction-steps">
                            <h5 class="mb-3">Setup Steps</h5>
                            
                            <div class="step-card mb-3">
                                <div class="d-flex">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Enable NFC in Settings</h6>
                                        <p>Go to <strong>Settings → Control Center</strong> and add "NFC Tag Reader" to your Control Center.</p>
                                        <div class="step-image">
                                            <div class="ios-setting-mockup">
                                                <div class="setting-item">
                                                    <i class="fas fa-cog"></i>
                                                    <span>Settings → Control Center → NFC Tag Reader</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-card mb-3">
                                <div class="d-flex">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Open Safari Browser</h6>
                                        <p>Open Safari and navigate to this website. Other browsers may have limited NFC support.</p>
                                        <div class="alert alert-warning small">
                                            <strong>Note:</strong> Chrome and Firefox on iOS have limited NFC capabilities.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-card mb-3">
                                <div class="d-flex">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Add to Home Screen</h6>
                                        <p>For the best experience, add this app to your home screen:</p>
                                        <ol class="small">
                                            <li>Tap the <i class="fas fa-share"></i> Share button in Safari</li>
                                            <li>Select "Add to Home Screen"</li>
                                            <li>Tap "Add" to confirm</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="step-card mb-3">
                                <div class="d-flex">
                                    <div class="step-number">4</div>
                                    <div class="step-content">
                                        <h6>Test NFC Functionality</h6>
                                        <p>Go to the NFC Scanner and test the functionality:</p>
                                        <ul class="small">
                                            <li>Tap "Scan NFC Card (iOS)" button</li>
                                            <li>Hold your iPhone near an NFC tag when prompted</li>
                                            <li>Wait for the scan to complete</li>
                                        </ul>
                                        <a href="{{ route('nfc.scanner') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-mobile-alt"></i> Test Scanner Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- iOS Limitations -->
                        <div class="card mt-4 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> iOS Limitations</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li><strong>Read-Only:</strong> iOS can only read NFC tags, not write to them</li>
                                    <li><strong>No HCE:</strong> iPhones cannot emulate NFC cards like Android devices</li>
                                    <li><strong>User Interaction:</strong> Each NFC scan requires a user tap/interaction</li>
                                    <li><strong>Background Limitations:</strong> NFC scanning doesn't work in background</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Alternative Methods -->
                        <div class="card mt-4 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Alternative Methods for iOS</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <i class="fas fa-qrcode fa-2x text-info mb-2"></i>
                                            <h6>QR Codes</h6>
                                            <p class="small text-muted">Generate QR codes for employee identification</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <i class="fas fa-camera fa-2x text-info mb-2"></i>
                                            <h6>Camera Scanner</h6>
                                            <p class="small text-muted">Use camera to scan employee badges</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <i class="fas fa-keyboard fa-2x text-info mb-2"></i>
                                            <h6>Manual Input</h6>
                                            <p class="small text-muted">Enter employee codes manually</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Troubleshooting -->
                        <div class="accordion mt-4" id="troubleshootingAccordion">
                            <div class="card">
                                <div class="card-header" id="headingTrouble">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" 
                                                data-target="#collapseTrouble">
                                            <i class="fas fa-wrench"></i> Troubleshooting
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapseTrouble" class="collapse" data-parent="#troubleshootingAccordion">
                                    <div class="card-body">
                                        <h6>Common Issues & Solutions:</h6>
                                        
                                        <div class="mb-3">
                                            <strong>NFC not working:</strong>
                                            <ul class="small">
                                                <li>Check if NFC is enabled in Control Center</li>
                                                <li>Ensure you're using Safari browser</li>
                                                <li>Try restarting the browser</li>
                                                <li>Make sure iOS is version 11 or later</li>
                                            </ul>
                                        </div>

                                        <div class="mb-3">
                                            <strong>Scanner button not responding:</strong>
                                            <ul class="small">
                                                <li>Refresh the page and try again</li>
                                                <li>Check internet connection</li>
                                                <li>Clear browser cache</li>
                                            </ul>
                                        </div>

                                        <div class="mb-3">
                                            <strong>App not working after adding to home screen:</strong>
                                            <ul class="small">
                                                <li>Delete and re-add the app to home screen</li>
                                                <li>Open directly in Safari first</li>
                                                <li>Check for iOS updates</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Support -->
                        <div class="text-center mt-4">
                            <p class="text-muted">Still having issues?</p>
                            <a href="mailto:support@company.com" class="btn btn-outline-primary">
                                <i class="fas fa-envelope"></i> Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Auto-detect and show relevant instructions
$(document).ready(function() {
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
    const iOSVersion = getIOSVersion();
    
    if (!isIOS) {
        $('.alert-info').html(`
            <h6><i class="fas fa-info-circle"></i> Device Not Supported</h6>
            <p class="mb-0">These instructions are specifically for iOS devices. You appear to be using a different device type.</p>
        `).removeClass('alert-info').addClass('alert-warning');
    } else if (iOSVersion && iOSVersion < 11) {
        $('.alert-info').html(`
            <h6><i class="fas fa-exclamation-triangle"></i> iOS Version Too Old</h6>
            <p class="mb-0">Your iOS version (${iOSVersion}) is too old for NFC support. Please update to iOS 11 or later.</p>
        `).removeClass('alert-info').addClass('alert-danger');
    }
    
    // Check NFC availability
    if ('NDEFReader' in window) {
        $('.card-body').prepend(`
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> NFC support detected on your device!
            </div>
        `);
    }
});

function getIOSVersion() {
    const match = navigator.userAgent.match(/OS (\d+)_(\d+)_?(\d+)?/);
    if (match) {
        return parseInt(match[1]);
    }
    return null;
}
</script>

<style>
.step-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    background: #f8f9fa;
}

.step-number {
    width: 40px;
    height: 40px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 15px;
    flex-shrink: 0;
}

.step-content {
    flex: 1;
}

.step-content h6 {
    margin-bottom: 8px;
    color: #495057;
}

.ios-setting-mockup {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 10px;
    margin-top: 10px;
}

.setting-item {
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #6c757d;
}

.setting-item i {
    margin-right: 8px;
    color: #007bff;
}

.instruction-steps h5 {
    color: #495057;
    border-bottom: 2px solid #007bff;
    padding-bottom: 8px;
}
</style>
@endsection