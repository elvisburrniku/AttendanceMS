
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Check-in/out Kiosk</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .kiosk-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .kiosk-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        
        .kiosk-header {
            margin-bottom: 30px;
        }
        
        .kiosk-header h1 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .current-time {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 15px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-kiosk {
            padding: 15px 30px;
            font-size: 1.2rem;
            border-radius: 10px;
            border: none;
            margin: 10px;
            min-width: 150px;
            transition: all 0.3s ease;
        }
        
        .btn-checkin {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
        }
        
        .btn-checkout {
            background: linear-gradient(45deg, #dc3545, #fd7e14);
            color: white;
        }
        
        .btn-kiosk:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .employee-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            display: none;
        }
        
        .loading {
            display: none;
        }
        
        .loading .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>
<body>
    <div class="kiosk-container">
        <div class="kiosk-card">
            <div class="kiosk-header">
                <h1>🕐 Time Clock</h1>
                <div class="current-time" id="currentTime"></div>
            </div>
            
            <div class="alert alert-info" style="display: none;" id="alertMessage"></div>
            
            <form id="kioskForm">
                <div class="form-group">
                    <label for="emp_code">Employee Code</label>
                    <input type="text" class="form-control" id="emp_code" name="emp_code" placeholder="Enter your employee code" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="pin">PIN (Optional)</label>
                    <input type="password" class="form-control" id="pin" name="pin" placeholder="Enter your PIN">
                </div>
                
                <div class="employee-info" id="employeeInfo">
                    <h5 id="employeeName"></h5>
                    <p id="employeeDetails"></p>
                    <p id="employeeStatus"></p>
                </div>
                
                <div class="loading" id="loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                
                <div class="action-buttons" id="actionButtons" style="display: none;">
                    <button type="button" class="btn btn-kiosk btn-checkin" id="checkinBtn">
                        Check In
                    </button>
                    <button type="button" class="btn btn-kiosk btn-checkout" id="checkoutBtn">
                        Check Out
                    </button>
                </div>
                
                <button type="button" class="btn btn-secondary" id="clearBtn" style="display: none; margin-top: 20px;">
                    Clear
                </button>
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            updateTime();
            setInterval(updateTime, 1000);
            
            $('#emp_code').on('input', function() {
                const empCode = $(this).val();
                if (empCode.length >= 3) {
                    verifyEmployee(empCode);
                } else {
                    resetForm();
                }
            });
            
            $('#checkinBtn').click(function() {
                performAction('checkin');
            });
            
            $('#checkoutBtn').click(function() {
                performAction('checkout');
            });
            
            $('#clearBtn').click(function() {
                resetForm();
                $('#emp_code').val('').focus();
            });
        });
        
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const dateString = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            $('#currentTime').text(dateString + ' - ' + timeString);
        }
        
        function verifyEmployee(empCode) {
            $('#loading').show();
            $('#actionButtons').hide();
            $('#employeeInfo').hide();
            
            $.get(`/kiosk/verify/${empCode}`)
                .done(function(response) {
                    if (response.success) {
                        showEmployeeInfo(response.employee, response.status, response.checkin_time, response.checkout_time);
                    }
                })
                .fail(function() {
                    showAlert('Employee not found', 'danger');
                    resetForm();
                })
                .always(function() {
                    $('#loading').hide();
                });
        }
        
        function showEmployeeInfo(employee, status, checkinTime, checkoutTime) {
            $('#employeeName').text(employee.name);
            $('#employeeDetails').text(`${employee.department} - ${employee.position}`);
            
            let statusText = '';
            let showCheckin = false;
            let showCheckout = false;
            
            switch(status) {
                case 'not_checked_in':
                    statusText = 'Ready to check in';
                    showCheckin = true;
                    break;
                case 'checked_in':
                    statusText = `Checked in at ${new Date(checkinTime).toLocaleTimeString()}`;
                    showCheckout = true;
                    break;
                case 'checked_out':
                    statusText = `Checked out at ${new Date(checkoutTime).toLocaleTimeString()}`;
                    break;
            }
            
            $('#employeeStatus').text(statusText);
            $('#employeeInfo').show();
            
            $('#checkinBtn').toggle(showCheckin);
            $('#checkoutBtn').toggle(showCheckout);
            $('#actionButtons').show();
            $('#clearBtn').show();
        }
        
        function performAction(action) {
            const empCode = $('#emp_code').val();
            const pin = $('#pin').val();
            
            $('#loading').show();
            $('#actionButtons').hide();
            
            const url = action === 'checkin' ? '/kiosk/checkin' : '/kiosk/checkout';
            
            $.post(url, {
                emp_code: empCode,
                pin: pin,
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                if (response.success) {
                    showAlert(`${response.message}. Time: ${response.time}`, 'success');
                    setTimeout(() => {
                        resetForm();
                        $('#emp_code').val('').focus();
                    }, 3000);
                } else {
                    showAlert(response.message, 'danger');
                }
            })
            .fail(function(xhr) {
                const response = xhr.responseJSON;
                showAlert(response.message || 'An error occurred', 'danger');
            })
            .always(function() {
                $('#loading').hide();
            });
        }
        
        function showAlert(message, type) {
            $('#alertMessage')
                .removeClass('alert-success alert-danger alert-info')
                .addClass(`alert-${type}`)
                .text(message)
                .show();
                
            setTimeout(() => {
                $('#alertMessage').fadeOut();
            }, 5000);
        }
        
        function resetForm() {
            $('#employeeInfo').hide();
            $('#actionButtons').hide();
            $('#clearBtn').hide();
            $('#alertMessage').hide();
        }
    </script>
</body>
</html>
