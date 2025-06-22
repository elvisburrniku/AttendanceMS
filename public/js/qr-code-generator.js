/**
 * Simple QR Code Generator for Employee Cards
 * Creates visual QR-like patterns for employee identification
 */

class QRCodeGenerator {
    constructor() {
        this.size = 200;
        this.modules = 25;
        this.moduleSize = this.size / this.modules;
    }
    
    generate(canvas, data) {
        const ctx = canvas.getContext('2d');
        
        // Clear canvas
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, this.size, this.size);
        
        // Generate pattern
        this.drawQRPattern(ctx, data);
        
        return canvas.toDataURL();
    }
    
    drawQRPattern(ctx, data) {
        const hash = this.simpleHash(JSON.stringify(data));
        
        ctx.fillStyle = '#000000';
        
        // Draw finder patterns (corners)
        this.drawFinderPattern(ctx, 0, 0);
        this.drawFinderPattern(ctx, (this.modules - 7) * this.moduleSize, 0);
        this.drawFinderPattern(ctx, 0, (this.modules - 7) * this.moduleSize);
        
        // Draw timing patterns
        this.drawTimingPatterns(ctx);
        
        // Draw data modules
        for (let row = 0; row < this.modules; row++) {
            for (let col = 0; col < this.modules; col++) {
                // Skip reserved areas
                if (this.isReservedArea(row, col)) continue;
                
                // Generate module based on data hash
                const moduleValue = this.getModuleValue(hash, row, col, data);
                if (moduleValue) {
                    ctx.fillRect(col * this.moduleSize, row * this.moduleSize, this.moduleSize, this.moduleSize);
                }
            }
        }
        
        // Add employee identifier in center
        this.drawCenterInfo(ctx, data);
    }
    
    drawFinderPattern(ctx, x, y) {
        const moduleSize = this.moduleSize;
        
        // Outer black square
        ctx.fillStyle = '#000000';
        ctx.fillRect(x, y, 7 * moduleSize, 7 * moduleSize);
        
        // Inner white square
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(x + moduleSize, y + moduleSize, 5 * moduleSize, 5 * moduleSize);
        
        // Center black square
        ctx.fillStyle = '#000000';
        ctx.fillRect(x + 2 * moduleSize, y + 2 * moduleSize, 3 * moduleSize, 3 * moduleSize);
    }
    
    drawTimingPatterns(ctx) {
        ctx.fillStyle = '#000000';
        
        // Horizontal timing pattern
        for (let i = 8; i < this.modules - 8; i++) {
            if (i % 2 === 0) {
                ctx.fillRect(i * this.moduleSize, 6 * this.moduleSize, this.moduleSize, this.moduleSize);
            }
        }
        
        // Vertical timing pattern
        for (let i = 8; i < this.modules - 8; i++) {
            if (i % 2 === 0) {
                ctx.fillRect(6 * this.moduleSize, i * this.moduleSize, this.moduleSize, this.moduleSize);
            }
        }
    }
    
    drawCenterInfo(ctx, data) {
        const centerX = this.size / 2;
        const centerY = this.size / 2;
        
        // Clear center area
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(centerX - 25, centerY - 20, 50, 40);
        
        // Draw border
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 1;
        ctx.strokeRect(centerX - 25, centerY - 20, 50, 40);
        
        // Add text
        ctx.fillStyle = '#000000';
        ctx.font = 'bold 8px Arial';
        ctx.textAlign = 'center';
        
        // Employee code
        ctx.fillText(data.emp_code || 'EMP', centerX, centerY - 5);
        
        // Role indicator
        const roleIcon = this.getRoleIcon(data.role);
        ctx.font = '6px Arial';
        ctx.fillText(roleIcon, centerX, centerY + 5);
        
        // Scan text
        ctx.font = '6px Arial';
        ctx.fillText('SCAN', centerX, centerY + 15);
    }
    
    getRoleIcon(role) {
        const icons = {
            'super_admin': '♔',
            'system_admin': '⚙',
            'hr_manager': '👥',
            'manager': '👔',
            'supervisor': '📋',
            'security': '🛡',
            'register': '⏰',
            'employee': '👤'
        };
        return icons[role] || '👤';
    }
    
    isReservedArea(row, col) {
        // Finder patterns
        if ((row < 9 && col < 9) || 
            (row < 9 && col >= this.modules - 8) || 
            (row >= this.modules - 8 && col < 9)) {
            return true;
        }
        
        // Timing patterns
        if (row === 6 || col === 6) {
            return true;
        }
        
        // Center area
        if (row >= 10 && row <= 14 && col >= 10 && col <= 14) {
            return true;
        }
        
        return false;
    }
    
    getModuleValue(hash, row, col, data) {
        // Create pattern based on hash and position
        const seed = hash + row * this.modules + col;
        const empCodeHash = this.simpleHash(data.emp_code || '');
        const roleHash = this.simpleHash(data.role || '');
        
        return (seed + empCodeHash + roleHash) % 3 === 0;
    }
    
    simpleHash(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        return Math.abs(hash);
    }
    
    // Static method for easy use
    static generate(canvas, data) {
        const generator = new QRCodeGenerator();
        return generator.generate(canvas, data);
    }
}

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = QRCodeGenerator;
}

// Make available globally
window.QRCodeGenerator = QRCodeGenerator;