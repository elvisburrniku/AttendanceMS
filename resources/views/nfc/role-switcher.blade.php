@extends('layouts.master')

@section('title', 'Role Switcher - Test Different NFC Cards')

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
                        <a href="{{ route('nfc.role-cards') }}" class="btn btn-info">
                            <i class="fas fa-layer-group"></i> All Cards
                        </a>
                    </div>
                    <h4 class="page-title">Role Switcher - Test Different Employee Roles</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Switch Employee Role</h5>
                        <p class="text-muted">Select a different employee role to see how their NFC card appears:</p>
                        
                        <div class="row">
                            <!-- Role Selection -->
                            <div class="col-md-4">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Select Employee Role</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="role-selector">
                                            <div class="form-group">
                                                <label for="role-select">Choose Role:</label>
                                                <select class="form-control" id="role-select">
                                                    <option value="employee">Standard Employee</option>
                                                    <option value="register">Register User</option>
                                                    <option value="supervisor">Supervisor</option>
                                                    <option value="security">Security Officer</option>
                                                    <option value="manager">Manager</option>
                                                    <option value="hr_manager">HR Manager</option>
                                                    <option value="system_admin">System Admin</option>
                                                    <option value="super_admin">Super Admin</option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="employee-select">Choose Employee:</label>
                                                <select class="form-control" id="employee-select">
                                                    <option value="default">Default Employee</option>
                                                    <option value="SA001">Alex Johnson (SA001)</option>
                                                    <option value="HR001">Sarah Wilson (HR001)</option>
                                                    <option value="MGR001">David Martinez (MGR001)</option>
                                                    <option value="EMP001">John Smith (EMP001)</option>
                                                </select>
                                            </div>
                                            
                                            <button type="button" class="btn btn-primary" id="apply-role">
                                                <i class="fas fa-sync-alt"></i> Apply Role
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Current Role Info -->
                                <div class="card border-info mt-3">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Current Role Details</h6>
                                    </div>
                                    <div class="card-body" id="current-role-info">
                                        <p><strong>Role:</strong> <span id="current-role">Employee</span></p>
                                        <p><strong>Access Level:</strong> <span id="current-level">Level 1</span></p>
                                        <p><strong>Card Type:</strong> <span id="current-type">Standard</span></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Live Card Preview -->
                            <div class="col-md-8">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">Live NFC Card Preview</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div id="live-card-container">
                                            <!-- Live card will be rendered here -->
                                            <div class="card role-based-nfc-card" id="preview-nfc-card" data-role="employee">
                                                <div class="card-body text-center" style="background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); color: white;">
                                                    <div class="nfc-card-visual">
                                                        <div class="card-header-info mb-3">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="badge badge-light text-dark" id="preview-card-type">STANDARD</span>
                                                                <i class="fas fa-id-card fa-lg" id="preview-card-icon"></i>
                                                            </div>
                                                            <small class="access-level" id="preview-access-level">LEVEL 1 - EMPLOYEE</small>
                                                        </div>
                                                        
                                                        <div class="nfc-icon">
                                                            <i class="fas fa-wifi fa-3x"></i>
                                                        </div>
                                                        
                                                        <h4 class="mt-3" id="preview-name">John Doe</h4>
                                                        <p class="emp-code" id="preview-emp-code">EMP001</p>
                                                        
                                                        <div class="employee-details mb-3" id="preview-department" style="display: none;">
                                                            <small class="badge badge-light text-dark" id="preview-dept"></small>
                                                        </div>
                                                        
                                                        <div class="nfc-id">
                                                            <small id="preview-nfc-id">NFC ID: EMP001-STD</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="card-footer bg-light">
                                                    <h6 class="text-center mb-2 text-dark">Access Permissions</h6>
                                                    <div class="permissions-list" id="preview-permissions">
                                                        <span class="badge badge-primary badge-sm mr-1 mb-1">Check In/Out</span>
                                                        <span class="badge badge-primary badge-sm mr-1 mb-1">View Schedule</span>
                                                    </div>
                                                    
                                                    <div class="special-features mt-2" id="preview-features" style="display: none;">
                                                        <small class="text-muted">Special Features:</small><br>
                                                        <span id="preview-features-list"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions for this role -->
                                <div class="card border-warning mt-3">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">Available Actions for This Role</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="role-actions">
                                            <p>Loading available actions...</p>
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
<script>
class RoleSwitcher {
    constructor() {
        this.roleConfigs = {
            'super_admin': {
                'card_color': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'access_level': 'LEVEL 5 - SUPER ADMIN',
                'permissions': ['All System Access', 'User Management', 'System Configuration', 'Data Export'],
                'card_type': 'EXECUTIVE',
                'special_features': ['Master Override', 'Audit Logs', 'Emergency Access'],
                'icon': 'fas fa-crown',
                'actions': ['Manage all users', 'System configuration', 'View all reports', 'Emergency overrides']
            },
            'system_admin': {
                'card_color': 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                'access_level': 'LEVEL 4 - SYSTEM ADMIN',
                'permissions': ['System Management', 'Employee Records', 'Reports', 'Device Config'],
                'card_type': 'ADMINISTRATIVE',
                'special_features': ['System Logs', 'Backup Access', 'Settings'],
                'icon': 'fas fa-cogs',
                'actions': ['Manage employees', 'Configure devices', 'Generate reports', 'System maintenance']
            },
            'hr_manager': {
                'card_color': 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                'access_level': 'LEVEL 3 - HR MANAGER',
                'permissions': ['Employee Management', 'Attendance Reports', 'Leave Management', 'Payroll'],
                'card_type': 'MANAGEMENT',
                'special_features': ['HR Dashboard', 'Employee Analytics', 'Report Export'],
                'icon': 'fas fa-users',
                'actions': ['Manage employee records', 'Process leave requests', 'Generate HR reports', 'Payroll management']
            },
            'manager': {
                'card_color': 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                'access_level': 'LEVEL 3 - MANAGER',
                'permissions': ['Team Management', 'Attendance Monitoring', 'Schedule Management'],
                'card_type': 'MANAGEMENT',
                'special_features': ['Team Dashboard', 'Approval Rights', 'Reports'],
                'icon': 'fas fa-user-tie',
                'actions': ['Manage team schedules', 'Approve time-off', 'Monitor team attendance', 'Team reports']
            },
            'supervisor': {
                'card_color': 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                'access_level': 'LEVEL 2 - SUPERVISOR',
                'permissions': ['Team Oversight', 'Attendance Review', 'Basic Reports'],
                'card_type': 'SUPERVISORY',
                'special_features': ['Team View', 'Shift Management'],
                'icon': 'fas fa-clipboard-check',
                'actions': ['Review team attendance', 'Manage shifts', 'Basic team oversight', 'Submit reports']
            },
            'security': {
                'card_color': 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
                'access_level': 'LEVEL 2 - SECURITY',
                'permissions': ['Access Control', 'Visitor Management', 'Security Logs'],
                'card_type': 'SECURITY',
                'special_features': ['Security Dashboard', 'Alert System', 'Access Logs'],
                'icon': 'fas fa-shield-alt',
                'actions': ['Monitor access points', 'Manage visitors', 'Security alerts', 'Access control']
            },
            'register': {
                'card_color': 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                'access_level': 'LEVEL 1 - REGISTER',
                'permissions': ['Attendance Tracking', 'Basic Profile', 'Time Logging'],
                'card_type': 'STANDARD',
                'special_features': ['Quick Check-in', 'Schedule View'],
                'icon': 'fas fa-clock',
                'actions': ['Check in/out', 'View schedule', 'Update basic profile', 'Time tracking']
            },
            'employee': {
                'card_color': 'linear-gradient(135deg, #d299c2 0%, #fef9d7 100%)',
                'access_level': 'LEVEL 1 - EMPLOYEE',
                'permissions': ['Check In/Out', 'View Schedule', 'Personal Profile'],
                'card_type': 'STANDARD',
                'special_features': ['Attendance Only'],
                'icon': 'fas fa-id-card',
                'actions': ['Check in/out', 'View personal schedule', 'Update profile', 'View attendance history']
            }
        };
        
        this.employees = {
            'SA001': { name: 'Alex Johnson', dept: 'Executive', role: 'super_admin' },
            'HR001': { name: 'Sarah Wilson', dept: 'Human Resources', role: 'hr_manager' },
            'MGR001': { name: 'David Martinez', dept: 'Operations', role: 'manager' },
            'EMP001': { name: 'John Smith', dept: 'General', role: 'employee' },
            'default': { name: 'John Doe', dept: '', role: 'employee' }
        };
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.updatePreview();
    }
    
    setupEventListeners() {
        $('#role-select').on('change', () => this.updatePreview());
        $('#employee-select').on('change', () => this.updatePreview());
        $('#apply-role').on('click', () => this.applyRole());
    }
    
    updatePreview() {
        const selectedRole = $('#role-select').val();
        const selectedEmployee = $('#employee-select').val();
        const config = this.roleConfigs[selectedRole];
        const employee = this.employees[selectedEmployee];
        
        // Update current role info
        $('#current-role').text(selectedRole.replace('_', ' ').toUpperCase());
        $('#current-level').text(config.access_level);
        $('#current-type').text(config.card_type);
        
        // Update card preview
        const cardElement = $('#preview-nfc-card');
        cardElement.attr('data-role', selectedRole);
        cardElement.find('.card-body').css('background', config.card_color);
        
        $('#preview-card-type').text(config.card_type);
        $('#preview-card-icon').attr('class', config.icon + ' fa-lg');
        $('#preview-access-level').text(config.access_level);
        $('#preview-name').text(employee.name);
        $('#preview-emp-code').text(selectedEmployee === 'default' ? 'EMP001' : selectedEmployee);
        $('#preview-nfc-id').text(`NFC ID: ${selectedEmployee === 'default' ? 'EMP001-STD' : selectedEmployee + '-' + config.card_type.substring(0,3)}`);
        
        // Update department
        if (employee.dept) {
            $('#preview-dept').text(employee.dept);
            $('#preview-department').show();
        } else {
            $('#preview-department').hide();
        }
        
        // Update permissions
        const permissionsHtml = config.permissions.map(permission => 
            `<span class="badge badge-primary badge-sm mr-1 mb-1">${permission}</span>`
        ).join('');
        $('#preview-permissions').html(permissionsHtml);
        
        // Update special features
        if (config.special_features && config.special_features.length > 0) {
            const featuresHtml = config.special_features.map(feature => 
                `<small class="badge badge-info badge-sm mr-1">${feature}</small>`
            ).join('');
            $('#preview-features-list').html(featuresHtml);
            $('#preview-features').show();
        } else {
            $('#preview-features').hide();
        }
        
        // Update available actions
        const actionsHtml = config.actions.map(action => 
            `<li class="mb-1"><i class="fas fa-check-circle text-success mr-2"></i>${action}</li>`
        ).join('');
        $('#role-actions').html(`<ul class="list-unstyled mb-0">${actionsHtml}</ul>`);
    }
    
    applyRole() {
        const selectedRole = $('#role-select').val();
        const selectedEmployee = $('#employee-select').val();
        
        // Simulate applying the role (in real app, this would update the user session)
        alert(`Role applied successfully!\n\nEmployee: ${this.employees[selectedEmployee].name}\nRole: ${selectedRole.replace('_', ' ').toUpperCase()}\n\nIn a real application, this would update your session and redirect you to your personalized dashboard.`);
        
        // Optional: redirect to employee card page
        // window.location.href = '/nfc/employee-card';
    }
}

$(document).ready(function() {
    new RoleSwitcher();
});
</script>

<style>
.role-based-nfc-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

.permissions-list .badge {
    font-size: 0.7rem;
    margin: 1px;
}

.role-selector .form-group label {
    font-weight: 600;
    color: #495057;
}
</style>
@endsection