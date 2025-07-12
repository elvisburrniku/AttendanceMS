<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Successful - AttendanceTracker Pro</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .success-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            padding: 12px 30px;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <i class="fas fa-check-circle success-icon"></i>
            <h1 class="h2 mb-3">Payment Successful!</h1>
            <p class="text-muted mb-4">
                Thank you for subscribing to AttendanceTracker Pro! Your account has been activated and you now have access to all premium features.
            </p>
            
            <div class="alert alert-success mb-4">
                <h6><i class="fas fa-crown me-2"></i>What's included in your subscription:</h6>
                <ul class="list-unstyled mb-0 mt-2">
                    <li><i class="fas fa-check text-success me-2"></i>Unlimited employees</li>
                    <li><i class="fas fa-check text-success me-2"></i>NFC check-in/out system</li>
                    <li><i class="fas fa-check text-success me-2"></i>GPS location tracking</li>
                    <li><i class="fas fa-check text-success me-2"></i>Advanced reporting & analytics</li>
                    <li><i class="fas fa-check text-success me-2"></i>Priority customer support</li>
                </ul>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('admin') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                </a>
                <a href="{{ route('landing') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
            </div>

            <div class="mt-4 pt-3 border-top">
                <small class="text-muted">
                    <i class="fas fa-envelope me-1"></i>A confirmation email has been sent to your inbox<br>
                    <i class="fas fa-headset me-1"></i>Need help? Contact our support team 24/7
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>