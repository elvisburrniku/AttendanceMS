
@extends('layouts.master')

@section('css')
<style>
.report-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 25px;
    margin-bottom: 20px;
    transition: transform 0.3s ease;
}

.report-card:hover {
    transform: translateY(-5px);
}

.report-icon {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #007bff;
}

.report-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 10px;
}

.report-description {
    color: #6c757d;
    margin-bottom: 20px;
}

.stats-card {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: bold;
}

.stats-label {
    font-size: 0.9rem;
    opacity: 0.8;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Reports Dashboard</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="stats-number">{{ \App\Models\Employee::count() }}</div>
                <div class="stats-label">Total Employees</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="stats-number">{{ \App\Models\Attendance::whereDate('punch_time', today())->where('punch_state', '0')->distinct('emp_id')->count() }}</div>
                <div class="stats-label">Present Today</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="stats-number">{{ \App\Models\Attendance::whereDate('punch_time', today())->where('punch_state', '0')->whereTime('punch_time', '>', '09:00')->count() }}</div>
                <div class="stats-label">Late Today</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="stats-number">{{ \App\Models\Attendance::whereMonth('punch_time', now()->month)->where('punch_state', '0')->distinct('emp_id', \DB::raw('DATE(punch_time)'))->count() }}</div>
                <div class="stats-label">This Month Attendance</div>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="report-card text-center">
                <div class="report-icon">📊</div>
                <div class="report-title">Attendance Report</div>
                <div class="report-description">View detailed attendance records with filters</div>
                <a href="{{ route('reports.attendance') }}" class="btn btn-primary">View Report</a>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="report-card text-center">
                <div class="report-icon">📅</div>
                <div class="report-title">Daily Report</div>
                <div class="report-description">Daily attendance summary and status</div>
                <a href="{{ route('reports.daily') }}" class="btn btn-primary">View Report</a>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="report-card text-center">
                <div class="report-icon">📈</div>
                <div class="report-title">Monthly Report</div>
                <div class="report-description">Monthly attendance statistics and trends</div>
                <a href="{{ route('reports.monthly') }}" class="btn btn-primary">View Report</a>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="report-card text-center">
                <div class="report-icon">👤</div>
                <div class="report-title">Employee Reports</div>
                <div class="report-description">Individual employee attendance reports</div>
                <a href="{{ route('employees.index') }}" class="btn btn-primary">View Employees</a>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="report-card text-center">
                <div class="report-icon">📋</div>
                <div class="report-title">Summary Report</div>
                <div class="report-description">Overall attendance summary and metrics</div>
                <a href="{{ route('reports.summary') }}" class="btn btn-primary">View Summary</a>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="report-card text-center">
                <div class="report-icon">⚙️</div>
                <div class="report-title">Custom Report</div>
                <div class="report-description">Create custom reports with specific criteria</div>
                <button class="btn btn-primary" data-toggle="modal" data-target="#customReportModal">Create Report</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Report Modal -->
<div class="modal fade" id="customReportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Custom Report</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('reports.custom') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Employees (Optional)</label>
                        <select name="employee_ids[]" class="form-control" multiple>
                            @foreach(\App\Models\Employee::all() as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Report Type</label>
                        <select name="report_type" class="form-control" required>
                            <option value="attendance">Attendance Details</option>
                            <option value="summary">Summary</option>
                            <option value="detailed">Detailed Analysis</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
