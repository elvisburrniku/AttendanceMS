/**
 * iOS NFC Compatibility Layer
 * Provides fallback methods for NFC functionality on iOS devices
 */

class IOSNFCCompat {
    constructor() {
        this.isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        this.isAndroid = /Android/.test(navigator.userAgent);
        this.nfcSupported = 'NDEFReader' in window;
        
        this.init();
    }
    
    init() {
        if (this.isIOS) {
            this.setupIOSNFC();
        }
        
        // Add iOS-specific CSS
        this.addIOSStyles();
    }
    
    setupIOSNFC() {
        // iOS NFC is available starting iOS 11, but with limitations
        // - Only NDEF reading (no writing or HCE)
        // - Requires user interaction to start scan
        // - Limited to Safari browser
        
        console.log('iOS NFC Compatibility initialized');
        
        // Check for iOS version
        const iOSVersion = this.getIOSVersion();
        if (iOSVersion && iOSVersion < 11) {
            console.warn('iOS version too old for NFC support');
            return false;
        }
        
        return true;
    }
    
    getIOSVersion() {
        const match = navigator.userAgent.match(/OS (\d+)_(\d+)_?(\d+)?/);
        if (match) {
            return parseInt(match[1]);
        }
        return null;
    }
    
    async scanNFC(options = {}) {
        if (!this.isIOS || !this.nfcSupported) {
            throw new Error('NFC not supported on this device');
        }
        
        try {
            const ndef = new NDEFReader();
            
            // iOS requires explicit user permission
            await ndef.scan(options);
            
            return new Promise((resolve, reject) => {
                const timeout = setTimeout(() => {
                    reject(new Error('NFC scan timeout'));
                }, options.timeout || 10000);
                
                ndef.addEventListener('reading', (event) => {
                    clearTimeout(timeout);
                    resolve({
                        serialNumber: event.serialNumber,
                        message: event.message
                    });
                });
                
                ndef.addEventListener('readingerror', (error) => {
                    clearTimeout(timeout);
                    reject(error);
                });
            });
            
        } catch (error) {
            throw new Error(`iOS NFC scan failed: ${error.message}`);
        }
    }
    
    generateQRCodeAlternative(data) {
        // Generate QR code as alternative to NFC for iOS
        const qrData = typeof data === 'string' ? data : JSON.stringify(data);
        
        // In production, use a proper QR code library like qrcode.js
        return {
            data: qrData,
            url: `data:image/svg+xml;base64,${btoa(this.generateSimpleQR(qrData))}`
        };
    }
    
    generateSimpleQR(data) {
        // Simple SVG QR code placeholder
        // In production, replace with actual QR code generation
        return `
            <svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                <rect width="200" height="200" fill="white"/>
                <rect x="10" y="10" width="180" height="180" fill="black"/>
                <rect x="20" y="20" width="160" height="160" fill="white"/>
                <text x="100" y="100" text-anchor="middle" fill="black" font-size="12">${data.substring(0, 20)}</text>
            </svg>
        `;
    }
    
    initCameraScanner() {
        // Camera-based QR code scanner for iOS
        return new Promise(async (resolve, reject) => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment', // Back camera
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                });
                
                resolve(stream);
                
            } catch (error) {
                reject(new Error(`Camera access failed: ${error.message}`));
            }
        });
    }
    
    addIOSStyles() {
        if (!this.isIOS) return;
        
        const style = document.createElement('style');
        style.textContent = `
            /* iOS-specific NFC styles */
            .ios-nfc-button {
                -webkit-appearance: none;
                border-radius: 12px;
                background: linear-gradient(135deg, #007AFF 0%, #0051D5 100%);
                color: white;
                border: none;
                padding: 12px 24px;
                font-size: 16px;
                font-weight: 600;
                box-shadow: 0 4px 16px rgba(0, 122, 255, 0.3);
                transition: all 0.2s ease;
            }
            
            .ios-nfc-button:active {
                transform: scale(0.96);
                box-shadow: 0 2px 8px rgba(0, 122, 255, 0.4);
            }
            
            .ios-nfc-button:disabled {
                opacity: 0.5;
                background: #8E8E93;
            }
            
            .ios-camera-preview {
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            }
            
            .ios-qr-code {
                border-radius: 12px;
                padding: 16px;
                background: white;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
            
            .ios-nfc-instructions {
                background: rgba(0, 122, 255, 0.1);
                border-radius: 8px;
                padding: 12px;
                margin: 16px 0;
                font-size: 14px;
                color: #007AFF;
            }
            
            @supports (-webkit-touch-callout: none) {
                /* iOS Safari specific styles */
                .nfc-scanner-container {
                    -webkit-user-select: none;
                    user-select: none;
                }
            }
        `;
        
        document.head.appendChild(style);
    }
    
    showIOSInstructions() {
        return `
            <div class="ios-nfc-instructions">
                <strong>iOS NFC Instructions:</strong>
                <ol>
                    <li>Ensure you're using Safari browser</li>
                    <li>Make sure NFC is enabled in Settings</li>
                    <li>Tap the "Scan NFC" button below</li>
                    <li>Hold your iPhone near the NFC tag when prompted</li>
                    <li>Wait for the scan to complete</li>
                </ol>
                <p><small>Note: iOS only supports reading NFC tags, not emulating them.</small></p>
            </div>
        `;
    }
    
    isNFCAvailable() {
        return this.isIOS && this.nfcSupported && this.getIOSVersion() >= 11;
    }
    
    getCompatibilityInfo() {
        return {
            isIOS: this.isIOS,
            isAndroid: this.isAndroid,
            nfcSupported: this.nfcSupported,
            iOSVersion: this.getIOSVersion(),
            canReadNFC: this.isNFCAvailable(),
            canWriteNFC: false, // iOS doesn't support NFC writing
            canEmulateNFC: false, // iOS doesn't support HCE
            alternativeMethods: ['qr_code', 'camera_scan', 'manual_input']
        };
    }
}

// Auto-initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    window.iOSNFCCompat = new IOSNFCCompat();
});

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = IOSNFCCompat;
}