<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Cancelled - AttendanceTracker Pro</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .cancel-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cancel-card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }
        .cancel-icon {
            font-size: 4rem;
            color: #ffc107;
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
    <div class="cancel-container">
        <div class="cancel-card">
            <i class="fas fa-exclamation-triangle cancel-icon"></i>
            <h1 class="h2 mb-3">Payment Cancelled</h1>
            <p class="text-muted mb-4">
                No worries! Your payment was cancelled and no charges were made to your account.
            </p>
            
            <div class="alert alert-info mb-4">
                <h6><i class="fas fa-gift me-2"></i>Still interested?</h6>
                <p class="mb-0">You can still start your free 14-day trial without any payment information required.</p>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('landing') }}#trial-signup" class="btn btn-primary btn-lg">
                    <i class="fas fa-play me-2"></i>Start Free Trial
                </a>
                <a href="{{ route('landing') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
            </div>

            <div class="mt-4 pt-3 border-top">
                <small class="text-muted">
                    <i class="fas fa-question-circle me-1"></i>Questions about pricing or features?<br>
                    <a href="mailto:support@attendancetracker.pro" class="text-decoration-none">Contact our sales team</a>
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>