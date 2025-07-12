<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AttendanceTracker Pro - Smart Employee Attendance Management</title>
    <meta name="description" content="Professional attendance management system with NFC support, real-time tracking, and comprehensive reporting. Start your free trial today!">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 120px 0;
        }
        .feature-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .pricing-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .pricing-card.featured {
            border-color: #667eea;
            transform: scale(1.05);
        }
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #764ba2, #667eea);
        }
        .testimonial-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 2rem;
            margin: 1rem 0;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .section-padding {
            padding: 80px 0;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary" href="#">
                <i class="fas fa-clock me-2"></i>AttendanceTracker Pro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Reviews</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white px-3 ms-2" href="{{ route('login') }}">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Smart Employee Attendance Management Made Simple</h1>
                    <p class="lead mb-4">
                        Transform your workforce management with our comprehensive attendance tracking system. 
                        Features NFC check-ins, real-time monitoring, GPS tracking, and detailed reporting.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#trial-signup" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-play me-2"></i>Start Free Trial
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                    <div class="mt-4">
                        <small class="opacity-75">
                            <i class="fas fa-check me-2"></i>14-day free trial
                            <i class="fas fa-check me-2 ms-3"></i>No credit card required
                            <i class="fas fa-check me-2 ms-3"></i>Cancel anytime
                        </small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%23f8f9fa' rx='10'/%3E%3Ccircle cx='200' cy='100' r='40' fill='%23667eea'/%3E%3Crect x='150' y='160' width='100' height='8' fill='%23dee2e6' rx='4'/%3E%3Crect x='120' y='180' width='160' height='6' fill='%23dee2e6' rx='3'/%3E%3Crect x='140' y='200' width='120' height='6' fill='%23dee2e6' rx='3'/%3E%3Ctext x='200' y='250' text-anchor='middle' fill='%23667eea' font-family='Arial, sans-serif' font-size='14'%3EAttendance Dashboard%3C/text%3E%3C/svg%3E" 
                             alt="Attendance Dashboard Preview" class="img-fluid rounded shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Powerful Features for Modern Workplaces</h2>
                <p class="lead text-muted">Everything you need to manage employee attendance efficiently</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <i class="fas fa-mobile-alt feature-icon"></i>
                        <h4>NFC Check-in/Out</h4>
                        <p class="text-muted">Modern contactless attendance tracking with NFC technology. Quick, secure, and hygienic.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <i class="fas fa-map-marker-alt feature-icon"></i>
                        <h4>GPS Location Tracking</h4>
                        <p class="text-muted">Verify employee locations during check-ins with accurate GPS coordinates and location validation.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <i class="fas fa-chart-line feature-icon"></i>
                        <h4>Real-time Dashboard</h4>
                        <p class="text-muted">Monitor attendance patterns, track late arrivals, and view comprehensive analytics in real-time.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <i class="fas fa-users feature-icon"></i>
                        <h4>Multi-level Management</h4>
                        <p class="text-muted">Organize employees by departments, positions, and areas with hierarchical access control.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <i class="fas fa-file-excel feature-icon"></i>
                        <h4>Advanced Reporting</h4>
                        <p class="text-muted">Generate detailed attendance reports with Excel export functionality for payroll integration.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <i class="fas fa-calendar-alt feature-icon"></i>
                        <h4>Schedule Management</h4>
                        <p class="text-muted">Define work schedules, manage shifts, and track overtime with automated calculations.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="section-padding bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Simple, Transparent Pricing</h2>
                <p class="lead text-muted">Start with a free trial, then just $29/month for unlimited features</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="pricing-card h-100 p-4 bg-white">
                        <div class="text-center">
                            <h4>Free Trial</h4>
                            <div class="display-4 fw-bold text-primary mb-3">$0</div>
                            <p class="text-muted">14 days free</p>
                            <a href="#trial-signup" class="btn btn-outline-primary btn-lg w-100 mb-4">Start Free Trial</a>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Up to 10 employees</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Basic attendance tracking</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Simple reporting</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email support</li>
                                <li class="mb-2"><i class="fas fa-times text-muted me-2"></i>NFC integration</li>
                                <li class="mb-2"><i class="fas fa-times text-muted me-2"></i>GPS tracking</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="pricing-card featured h-100 p-4 bg-white position-relative">
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <span class="badge bg-primary px-3 py-2">Most Popular</span>
                        </div>
                        <div class="text-center">
                            <h4>Pro Plan</h4>
                            <div class="display-4 fw-bold text-primary mb-3">$29</div>
                            <p class="text-muted">per month</p>
                            <form action="{{ route('create-checkout-session') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-4">
                                    <i class="fas fa-credit-card me-2"></i>Subscribe Now
                                </button>
                            </form>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Unlimited employees</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>NFC check-in/out</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>GPS location tracking</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Advanced reporting</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Excel export</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Multi-level management</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Schedule management</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Priority support</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-lock me-2"></i>Secure payment powered by Stripe
                    <i class="fas fa-shield-alt me-2 ms-3"></i>Cancel anytime
                    <i class="fas fa-headset me-2 ms-3"></i>24/7 support included
                </small>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">What Our Customers Say</h2>
                <p class="lead text-muted">Join hundreds of satisfied businesses worldwide</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-3">"AttendanceTracker Pro transformed our HR operations. The NFC feature is fantastic and employees love how easy it is to use."</p>
                        <div class="d-flex align-items-center">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%23667eea'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-family='Arial, sans-serif' font-size='16' font-weight='bold'%3ES%3C/text%3E%3C/svg%3E" 
                                 alt="Sarah Johnson" class="rounded-circle me-3" width="40">
                            <div>
                                <strong>Sarah Johnson</strong>
                                <small class="text-muted d-block">HR Manager, TechCorp</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-3">"The reporting features are incredibly detailed. We can now track attendance patterns and optimize our workforce management."</p>
                        <div class="d-flex align-items-center">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%23764ba2'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-family='Arial, sans-serif' font-size='16' font-weight='bold'%3EM%3C/text%3E%3C/svg%3E" 
                                 alt="Michael Chen" class="rounded-circle me-3" width="40">
                            <div>
                                <strong>Michael Chen</strong>
                                <small class="text-muted d-block">Operations Director, RetailPlus</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-3">"Setup was incredibly easy and the support team is outstanding. Worth every penny for the time it saves us."</p>
                        <div class="d-flex align-items-center">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%23667eea'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-family='Arial, sans-serif' font-size='16' font-weight='bold'%3EE%3C/text%3E%3C/svg%3E" 
                                 alt="Emily Rodriguez" class="rounded-circle me-3" width="40">
                            <div>
                                <strong>Emily Rodriguez</strong>
                                <small class="text-muted d-block">CEO, StartupHub</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trial Signup Section -->
    <section id="trial-signup" class="section-padding bg-primary text-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-5 fw-bold mb-4">Ready to Transform Your Attendance Management?</h2>
                    <p class="lead mb-4">Start your 14-day free trial today. No credit card required, no setup fees.</p>
                    <form action="{{ route('trial-signup') }}" method="POST" class="row g-3 justify-content-center">
                        @csrf
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-lg" name="company_name" placeholder="Company Name" required>
                        </div>
                        <div class="col-md-4">
                            <input type="email" class="form-control form-control-lg" name="email" placeholder="Work Email" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-light btn-lg w-100">
                                <i class="fas fa-rocket me-2"></i>Start Free Trial
                            </button>
                        </div>
                    </form>
                    <small class="d-block mt-3 opacity-75">
                        By signing up, you agree to our Terms of Service and Privacy Policy
                    </small>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <h5><i class="fas fa-clock me-2"></i>AttendanceTracker Pro</h5>
                    <p class="text-muted">Modern attendance management for the digital workplace.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="text-muted"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h6>Product</h6>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="text-muted text-decoration-none">Features</a></li>
                        <li><a href="#pricing" class="text-muted text-decoration-none">Pricing</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Demo</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6>Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Contact Us</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Status</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6>Company</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">About</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Privacy</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Terms</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center text-muted">
                <small>&copy; 2025 AttendanceTracker Pro. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Smooth Scrolling -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>