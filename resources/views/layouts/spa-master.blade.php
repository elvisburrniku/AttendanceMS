<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Schedule Manager - SPA</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <style>
        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.8) !important;
            transition: color 0.2s ease;
            margin: 0 5px;
            padding: 8px 12px !important;
            border-radius: 4px;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.router-link-active {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
        }
        
        .page-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .page-header h2 {
            color: #2c3e50;
            margin-bottom: 0;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .btn {
            transition: all 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 250px;
        }
        
        /* Calendar specific styles */
        .shift-calendar {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .calendar-wrapper {
            overflow-x: auto;
        }
        
        .calendar-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            min-width: 800px;
        }
        
        .calendar-row {
            display: table-row;
        }
        
        .calendar-cell {
            display: table-cell;
            border: 1px solid #dee2e6;
            min-height: 80px;
            vertical-align: top;
            position: relative;
            padding: 4px;
        }
        
        .employee-cell {
            width: 200px;
            background: #f8f9fa;
            padding: 15px 10px;
            font-weight: 600;
            border-right: 2px solid #dee2e6;
            position: sticky;
            left: 0;
            z-index: 10;
        }
        
        .day-cell {
            width: calc((100% - 200px) / 7);
            padding: 5px;
            min-height: 80px;
            transition: background-color 0.2s ease;
        }
        
        .day-header {
            text-align: center;
            padding: 15px 5px;
            background: #6c757d;
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 5;
        }
        
        .day-header.weekend {
            background: #dc3545;
        }
        
        .day-header.today {
            background: #28a745;
        }
        
        .shift-block {
            background: #3498db;
            color: white;
            padding: 4px 8px;
            margin: 2px 0;
            border-radius: 4px;
            font-size: 11px;
            cursor: move;
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            transition: all 0.2s ease;
            user-select: none;
            word-wrap: break-word;
        }
        
        .shift-block:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            z-index: 1;
        }
        
        .shift-block.dragging {
            opacity: 0.7;
            transform: rotate(5deg) scale(1.05);
            z-index: 1000;
        }
        
        .day-cell.drag-over {
            background: #e3f2fd !important;
            border: 2px dashed #2196f3 !important;
        }
        
        .empty-day {
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-style: italic;
            font-size: 12px;
            text-align: center;
        }
        
        /* Modal styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 20px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .modal-header h5 {
            margin: 0;
            flex: 1;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            margin-left: 15px;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-footer {
            padding: 0 20px 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .context-menu {
            position: absolute;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            min-width: 150px;
        }
        
        .context-menu-item {
            padding: 8px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .context-menu-item:hover {
            background: #f8f9fa;
        }
        
        .context-menu-item:last-child {
            border-bottom: none;
        }
        
        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
            }
            
            .navbar-nav .nav-link {
                margin: 2px 0;
            }
            
            .calendar-grid {
                font-size: 12px;
            }
            
            .employee-cell {
                width: 150px;
                font-size: 12px;
            }
            
            .shift-block {
                font-size: 10px;
                padding: 3px 6px;
            }
        }
    </style>
</head>
<body>
    <div id="app"></div>
    
    <!-- Toast Container -->
    <div id="toast-container"></div>
    
    <!-- Vue App Bundle -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    <!-- Toast Helper -->
    <script>
        // Simple toast implementation
        window.showToast = function(message, type = 'info') {
            const container = document.getElementById('toast-container') || document.body;
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0 show`;
            toast.setAttribute('role', 'alert');
            toast.style.position = 'fixed';
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '1050';
            toast.style.minWidth = '250px';
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Auto hide after 3 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 3000);
        };
        
        // Make toast available to Vue
        if (typeof Vue !== 'undefined') {
            Vue.prototype.$toast = {
                success: (msg) => showToast(msg, 'success'),
                error: (msg) => showToast(msg, 'danger'),
                info: (msg) => showToast(msg, 'info'),
                warning: (msg) => showToast(msg, 'warning')
            };
        }
    </script>
</body>
</html>