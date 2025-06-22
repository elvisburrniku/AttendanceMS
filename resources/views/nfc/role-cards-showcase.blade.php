@extends('layouts.master')

@section('title', 'NFC Role Cards Showcase')

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group float-right">
                        <a href="{{ route('nfc.employee-card') }}" class="btn btn-primary">
                            <i class="fas fa-id-card"></i> My Card
                        </a>
                        <a href="{{ route('nfc.scanner') }}" class="btn btn-success">
                            <i class="fas fa-mobile-alt"></i> Scanner
                        </a>
                    </div>
                    <h4 class="page-title">NFC Role Cards Showcase</h4>
                </div>
            </div>
        </div>

        <!-- Role Cards Display -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Different NFC Cards by Employee Role</h5>
                        <p class="text-muted">Each employee role gets a unique NFC card design with specific permissions and access levels.</p>
                        
                        <div class="row">
                            <!-- Super Admin Card -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="role-card-demo">
                                    <div class="card role-based-nfc-card" data-role="super_admin">
                                        <div class="card-body text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                            <div class="card-header-info mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-light text-dark">EXECUTIVE</span>
                                                    <i class="fas fa-crown fa-lg"></i>
                                                </div>
                                                <small class="access-level">LEVEL 5 - SUPER ADMIN</small>
                                            </div>
                                            <div class="nfc-icon">
                                                <i class="fas fa-wifi fa-2x"></i>
                                            </div>
                                            <h5 class="mt-2">Alex Johnson</h5>
                                            <p class="emp-code">SA001</p>
                                            <div class="nfc-id">
                                                <small>NFC ID: SA001-EXEC</small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <small class="text-center d-block mb-2"><strong>Access Permissions</strong></small>
                                            <div class="permissions-compact">
                                                <span class="badge badge-primary badge-sm">All System Access</span>
                                                <span class="badge badge-primary badge-sm">User Management</span>
                                                <span class="badge badge-info badge-sm">Master Override</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- HR Manager Card -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="role-card-demo">
                                    <div class="card role-based-nfc-card" data-role="hr_manager">
                                        <div class="card-body text-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                            <div class="card-header-info mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-light text-dark">MANAGEMENT</span>
                                                    <i class="fas fa-users fa-lg"></i>
                                                </div>
                                                <small class="access-level">LEVEL 3 - HR MANAGER</small>
                                            </div>
                                            <div class="nfc-icon">
                                                <i class="fas fa-wifi fa-2x"></i>
                                            </div>
                                            <h5 class="mt-2">Sarah Wilson</h5>
                                            <p class="emp-code">HR001</p>
                                            <div class="employee-details mb-2">
                                                <small class="badge badge-light text-dark">Human Resources</small>
                                            </div>
                                            <div class="nfc-id">
                                                <small>NFC ID: HR001-MGT</small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <small class="text-center d-block mb-2"><strong>Access Permissions</strong></small>
                                            <div class="permissions-compact">
                                                <span class="badge badge-primary badge-sm">Employee Management</span>
                                                <span class="badge badge-primary badge-sm">Reports</span>
                                                <span class="badge badge-info badge-sm">HR Dashboard</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Manager Card -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="role-card-demo">
                                    <div class="card role-based-nfc-card" data-role="manager">
                                        <div class="card-body text-center" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                                            <div class="card-header-info mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-light text-dark">MANAGEMENT</span>
                                                    <i class="fas fa-user-tie fa-lg"></i>
                                                </div>
                                                <small class="access-level">LEVEL 3 - MANAGER</small>
                                            </div>
                                            <div class="nfc-icon">
                                                <i class="fas fa-wifi fa-2x"></i>
                                            </div>
                                            <h5 class="mt-2">David Martinez</h5>
                                            <p class="emp-code">MGR001</p>
                                            <div class="employee-details mb-2">
                                                <small class="badge badge-light text-dark">Operations</small>
                                            </div>
                                            <div class="nfc-id">
                                                <small>NFC ID: MGR001-OPS</small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <small class="text-center d-block mb-2"><strong>Access Permissions</strong></small>
                                            <div class="permissions-compact">
                                                <span class="badge badge-primary badge-sm">Team Management</span>
                                                <span class="badge badge-primary badge-sm">Attendance Monitoring</span>
                                                <span class="badge badge-info badge-sm">Team Dashboard</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Supervisor Card -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="role-card-demo">
                                    <div class="card role-based-nfc-card" data-role="supervisor">
                                        <div class="card-body text-center" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                                            <div class="card-header-info mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-light text-dark">SUPERVISORY</span>
                                                    <i class="fas fa-clipboard-check fa-lg"></i>
                                                </div>
                                                <small class="access-level">LEVEL 2 - SUPERVISOR</small>
                                            </div>
                                            <div class="nfc-icon">
                                                <i class="fas fa-wifi fa-2x"></i>
                                            </div>
                                            <h5 class="mt-2">Lisa Chen</h5>
                                            <p class="emp-code">SUP001</p>
                                            <div class="employee-details mb-2">
                                                <small class="badge badge-light text-dark">Production</small>
                                            </div>
                                            <div class="nfc-id">
                                                <small>NFC ID: SUP001-PROD</small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <small class="text-center d-block mb-2"><strong>Access Permissions</strong></small>
                                            <div class="permissions-compact">
                                                <span class="badge badge-primary badge-sm">Team Oversight</span>
                                                <span class="badge badge-primary badge-sm">Attendance Review</span>
                                                <span class="badge badge-info badge-sm">Team View</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Card -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="role-card-demo">
                                    <div class="card role-based-nfc-card" data-role="security">
                                        <div class="card-body text-center" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: white;">
                                            <div class="card-header-info mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-light text-dark">SECURITY</span>
                                                    <i class="fas fa-shield-alt fa-lg"></i>
                                                </div>
                                                <small class="access-level">LEVEL 2 - SECURITY</small>
                                            </div>
                                            <div class="nfc-icon">
                                                <i class="fas fa-wifi fa-2x"></i>
                                            </div>
                                            <h5 class="mt-2">Mike Rodriguez</h5>
                                            <p class="emp-code">SEC001</p>
                                            <div class="employee-details mb-2">
                                                <small class="badge badge-light text-dark">Security</small>
                                            </div>
                                            <div class="nfc-id">
                                                <small>NFC ID: SEC001-GUARD</small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <small class="text-center d-block mb-2"><strong>Access Permissions</strong></small>
                                            <div class="permissions-compact">
                                                <span class="badge badge-primary badge-sm">Access Control</span>
                                                <span class="badge badge-primary badge-sm">Security Logs</span>
                                                <span class="badge badge-info badge-sm">Alert System</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Card -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="role-card-demo">
                                    <div class="card role-based-nfc-card" data-role="employee">
                                        <div class="card-body text-center" style="background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); color: white;">
                                            <div class="card-header-info mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-light text-dark">STANDARD</span>
                                                    <i class="fas fa-id-card fa-lg"></i>
                                                </div>
                                                <small class="access-level">LEVEL 1 - EMPLOYEE</small>
                                            </div>
                                            <div class="nfc-icon">
                                                <i class="fas fa-wifi fa-2x"></i>
                                            </div>
                                            <h5 class="mt-2">John Smith</h5>
                                            <p class="emp-code">EMP001</p>
                                            <div class="employee-details mb-2">
                                                <small class="badge badge-light text-dark">General</small>
                                            </div>
                                            <div class="nfc-id">
                                                <small>NFC ID: EMP001-STD</small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <small class="text-center d-block mb-2"><strong>Access Permissions</strong></small>
                                            <div class="permissions-compact">
                                                <span class="badge badge-primary badge-sm">Check In/Out</span>
                                                <span class="badge badge-primary badge-sm">View Schedule</span>
                                                <span class="badge badge-info badge-sm">Attendance Only</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Role Access Matrix -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-table"></i> Role Access Matrix</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr class="bg-light">
                                                        <th>Role</th>
                                                        <th>Level</th>
                                                        <th>NFC Scanner</th>
                                                        <th>Employee Mgmt</th>
                                                        <th>Reports</th>
                                                        <th>System Config</th>
                                                        <th>Special Features</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><span class="badge badge-purple">Super Admin</span></td>
                                                        <td>5</td>
                                                        <td><i class="fas fa-check text-success"></i></td>
                                                        <td><i class="fas fa-check text-success"></i></td>
                                                        <td><i class="fas fa-check text-success"></i></td>
                                                        <td><i class="fas fa-check text-success"></i></td>
                                                        <td>Master Override, Audit Logs</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge badge-info">HR Manager</span></td>
                                                        <td>3</td>
                                                        <td><i class="fas fa-check text-success"></i></td>
                                                        <td><i class="fas fa-check text-success"></i></td>
                                                        <td><i class="fas fa-check text-success"></i></td>
                                                        <td><i class="fas fa-times text-danger"></i></td>
                                                        <td>HR Dashboard, Employee Analytics</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge badge-success">Manager</span></td>
                                                        <td>3</td>
                                                        <td><i class="fas fa-check text-success"></i></td>
                                                        <td><i class="fas fa-check text-warning"></i></td>
                                                        <td><i class="fas fa-check text-success"></i></td>
                                                        <td><i class="fas fa-times text-danger"></i></td>
                                                        <td>Team Dashboard, Approval Rights</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge badge-warning">Supervisor</span></td>
                                                        <td>2</td>
                                                        <td><i class="fas fa-check text-success"></i></td>
                                                        <td><i class="fas fa-check text-warning"></i></td>
                                                        <td><i class="fas fa-check text-warning"></i></td>
                                                        <td><i class="fas fa-times text-danger"></i></td>
                                                        <td>Team View, Shift Management</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge badge-secondary">Employee</span></td>
                                                        <td>1</td>
                                                        <td><i class="fas fa-check text-warning"></i></td>
                                                        <td><i class="fas fa-times text-danger"></i></td>
                                                        <td><i class="fas fa-times text-danger"></i></td>
                                                        <td><i class="fas fa-times text-danger"></i></td>
                                                        <td>Attendance Only</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-check text-success"></i> Full Access &nbsp;&nbsp;
                                                <i class="fas fa-check text-warning"></i> Limited Access &nbsp;&nbsp;
                                                <i class="fas fa-times text-danger"></i> No Access
                                            </small>
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
</div>
@endsection

@section('script')
<style>
.role-based-nfc-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.role-based-nfc-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.card-header-info {
    font-size: 0.8rem;
}

.access-level {
    font-weight: bold;
    letter-spacing: 0.5px;
}

.nfc-icon {
    opacity: 0.9;
}

.emp-code {
    font-family: 'Courier New', monospace;
    font-weight: bold;
    margin: 0;
}

.permissions-compact .badge {
    font-size: 0.7rem;
    margin: 1px;
}

.badge-purple {
    background-color: #667eea;
    color: white;
}

.table th {
    font-size: 0.85rem;
    border-top: none;
}

.table td {
    font-size: 0.8rem;
    vertical-align: middle;
}

.role-card-demo {
    height: 100%;
}

.role-card-demo .card {
    height: 100%;
}
</style>
@endsection