@extends('layouts.master')

@section('title')
    Shift Validation Management
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h4 class="card-title">Shift Validation Management</h4>
                        <p class="card-subtitle mb-2 text-muted">Manage shift-based check-in rules and time intervals</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-primary" id="createSampleDataBtn">
                            <i class="fas fa-plus"></i> Create Sample Data
                        </button>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Employees</h5>
                                <h2 id="totalEmployees">{{ $employees->count() }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Active Schedules</h5>
                                <h2 id="activeSchedules">{{ $activeSchedules->count() }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Shifts</h5>
                                <h2 id="totalShifts">{{ $shifts->count() }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Time Intervals</h5>
                                <h2 id="timeIntervals">{{ $timeIntervals->count() }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Test Shift Validation -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Test Shift Validation</h5>
                                <form id="testValidationForm">
                                    <div class="mb-3">
                                        <label for="employee_id" class="form-label">Employee</label>
                                        <select class="form-select" id="employee_id" name="employee_id" required>
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->emp_code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="test_time" class="form-label">Test Time (Optional)</label>
                                        <input type="datetime-local" class="form-control" id="test_time" name="test_time" 
                                               value="{{ now()->format('Y-m-d\TH:i') }}">
                                        <small class="form-text text-muted">Leave empty to test current time</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Test Validation</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Validation Result</h5>
                                <div id="validationResult">
                                    <p class="text-muted">Select an employee and click "Test Validation" to see results.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs for different sections -->
                <ul class="nav nav-tabs" id="shiftValidationTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="schedules-tab" data-bs-toggle="tab" data-bs-target="#schedules" type="button" role="tab">
                            Employee Schedules
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="shifts-tab" data-bs-toggle="tab" data-bs-target="#shifts" type="button" role="tab">
                            Shifts
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="intervals-tab" data-bs-toggle="tab" data-bs-target="#intervals" type="button" role="tab">
                            Time Intervals
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="shiftValidationTabsContent">
                    <!-- Employee Schedules Tab -->
                    <div class="tab-pane fade show active" id="schedules" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Shift</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeSchedules as $schedule)
                                    <tr>
                                        <td>
                                            <strong>{{ $schedule->employee->name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $schedule->employee->emp_code ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ $schedule->shift->alias ?? 'N/A' }}</td>
                                        <td>{{ $schedule->start_date }}</td>
                                        <td>{{ $schedule->end_date }}</td>
                                        <td>
                                            @if($schedule->end_date >= now()->format('Y-m-d'))
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Expired</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="getEmployeeReport({{ $schedule->employee->id ?? 0 }})">
                                                <i class="fas fa-eye"></i> View Report
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Shifts Tab -->
                    <div class="tab-pane fade" id="shifts" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Shift Name</th>
                                        <th>Cycle</th>
                                        <th>Weekend Work</th>
                                        <th>Auto Shift</th>
                                        <th>Time Intervals</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shifts as $shift)
                                    <tr>
                                        <td><strong>{{ $shift->alias }}</strong></td>
                                        <td>{{ $shift->cycle_unit }} ({{ $shift->shift_cycle }})</td>
                                        <td>
                                            @if($shift->work_weekend)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($shift->auto_shift)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $shift->timeIntervals->count() }} intervals</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Time Intervals Tab -->
                    <div class="tab-pane fade" id="intervals" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Start Time</th>
                                        <th>Duration</th>
                                        <th>Early Margin</th>
                                        <th>Late Margin</th>
                                        <th>Check-in Window</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($timeIntervals as $interval)
                                    <tr>
                                        <td><strong>{{ $interval->alias }}</strong></td>
                                        <td>{{ $interval->in_time }}</td>
                                        <td>{{ $interval->duration }} minutes</td>
                                        <td>{{ $interval->in_ahead_margin }} minutes</td>
                                        <td>{{ $interval->in_above_margin }} minutes</td>
                                        <td>
                                            @if($interval->in_time)
                                                @php
                                                    $startTime = \Carbon\Carbon::parse($interval->in_time);
                                                    $earliest = $startTime->copy()->subMinutes($interval->in_ahead_margin ?? 0);
                                                    $latest = $startTime->copy()->addMinutes($interval->in_above_margin ?? 0);
                                                @endphp
                                                <small>{{ $earliest->format('H:i') }} - {{ $latest->format('H:i') }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Employee Report -->
<div class="modal fade" id="employeeReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee Shift Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="employeeReportContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test validation form
    document.getElementById('testValidationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        fetch('{{ route("shift-validation.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            displayValidationResult(data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('validationResult').innerHTML = 
                '<div class="alert alert-danger">Error testing validation</div>';
        });
    });

    // Create sample data button
    document.getElementById('createSampleDataBtn').addEventListener('click', function() {
        if (confirm('This will create sample shift data. Continue?')) {
            fetch('{{ route("shift-validation.create-sample-data") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Sample data created successfully!');
                    location.reload();
                } else {
                    alert('Error creating sample data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating sample data');
            });
        }
    });
});

function displayValidationResult(data) {
    const validation = data.validation;
    const report = data.report;
    
    let html = '';
    
    if (validation.allowed) {
        html += '<div class="alert alert-success">';
        html += '<h6><i class="fas fa-check-circle"></i> Check-in Allowed</h6>';
        html += '<p>' + validation.reason + '</p>';
        if (validation.shift_info) {
            html += '<p><strong>Shift:</strong> ' + validation.shift_info.shift_name + '</p>';
            html += '<p><strong>Interval:</strong> ' + validation.shift_info.interval_name + '</p>';
            html += '<p><strong>Window:</strong> ' + validation.shift_info.check_in_window.earliest + ' - ' + validation.shift_info.check_in_window.latest + '</p>';
        }
        html += '</div>';
    } else {
        html += '<div class="alert alert-danger">';
        html += '<h6><i class="fas fa-times-circle"></i> Check-in Not Allowed</h6>';
        html += '<p>' + validation.reason + '</p>';
        html += '<p>' + validation.details + '</p>';
        html += '</div>';
    }
    
    if (report && report.time_intervals) {
        html += '<div class="mt-3">';
        html += '<h6>Available Time Intervals:</h6>';
        html += '<div class="table-responsive">';
        html += '<table class="table table-sm">';
        html += '<thead><tr><th>Name</th><th>Start</th><th>Window</th></tr></thead>';
        html += '<tbody>';
        
        report.time_intervals.forEach(function(interval) {
            html += '<tr>';
            html += '<td>' + interval.name + '</td>';
            html += '<td>' + interval.start_time + '</td>';
            html += '<td>' + interval.check_in_window.earliest + ' - ' + interval.check_in_window.latest + '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div></div>';
    }
    
    document.getElementById('validationResult').innerHTML = html;
}

function getEmployeeReport(employeeId) {
    fetch(`{{ route("shift-validation.employee-report") }}?employee_id=${employeeId}`)
        .then(response => response.json())
        .then(data => {
            displayEmployeeReport(data);
            new bootstrap.Modal(document.getElementById('employeeReportModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading employee report');
        });
}

function displayEmployeeReport(data) {
    let html = '';
    
    if (data.error) {
        html = '<div class="alert alert-danger">' + data.error + '</div>';
    } else {
        html += '<div class="row">';
        html += '<div class="col-md-6">';
        html += '<h6>Employee Information</h6>';
        html += '<p><strong>Name:</strong> ' + data.employee + '</p>';
        html += '<p><strong>Date:</strong> ' + data.date + '</p>';
        html += '<p><strong>Day of Week:</strong> ' + data.day_of_week + '</p>';
        html += '</div>';
        
        if (data.schedule) {
            html += '<div class="col-md-6">';
            html += '<h6>Schedule Information</h6>';
            html += '<p><strong>Shift:</strong> ' + data.schedule.shift_name + '</p>';
            html += '<p><strong>Period:</strong> ' + data.schedule.start_date + ' to ' + data.schedule.end_date + '</p>';
            html += '</div>';
        }
        
        html += '</div>';
        
        if (data.time_intervals && data.time_intervals.length > 0) {
            html += '<div class="mt-3">';
            html += '<h6>Time Intervals for This Day</h6>';
            html += '<div class="table-responsive">';
            html += '<table class="table table-sm">';
            html += '<thead><tr><th>Name</th><th>Start</th><th>Duration</th><th>Check-in Window</th></tr></thead>';
            html += '<tbody>';
            
            data.time_intervals.forEach(function(interval) {
                html += '<tr>';
                html += '<td>' + interval.name + '</td>';
                html += '<td>' + interval.start_time + '</td>';
                html += '<td>' + interval.duration + '</td>';
                html += '<td>' + interval.check_in_window.earliest + ' - ' + interval.check_in_window.latest + '</td>';
                html += '</tr>';
            });
            
            html += '</tbody></table></div></div>';
        } else {
            html += '<div class="alert alert-info mt-3">No time intervals configured for this day.</div>';
        }
    }
    
    document.getElementById('employeeReportContent').innerHTML = html;
}
</script>
@endsection