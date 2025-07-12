
@extends('layouts.master')

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group float-right">
                        <ol class="breadcrumb hide-phone p-0 m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('schedules.index') }}">Schedules</a></li>
                            <li class="breadcrumb-item active">Employee Schedules</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $employee->first_name }} {{ $employee->last_name }} - Schedules</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h4 class="card-title">Employee Information</h4>
                                <p><strong>Employee Code:</strong> {{ $employee->emp_code }}</p>
                                <p><strong>Name:</strong> {{ $employee->first_name }} {{ $employee->last_name }}</p>
                                @if($employee->department)
                                    <p><strong>Department:</strong> {{ $employee->department->dept_name }}</p>
                                @endif
                                @if($employee->position)
                                    <p><strong>Position:</strong> {{ $employee->position->position_name }}</p>
                                @endif
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Back to Schedules
                                </a>
                                <a href="{{ route('schedules.create') }}?employee_id={{ $employee->id }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add New Schedule
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="employeeSchedulesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Shift</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->id }}</td>
                                        <td>
                                            @if($schedule->shift)
                                                <span class="badge badge-primary">{{ $schedule->shift->alias }}</span>
                                                @if($schedule->shift->timeIntervals->count() > 0)
                                                    <br>
                                                    <small class="text-muted">
                                                        @foreach($schedule->shift->timeIntervals as $interval)
                                                            {{ $interval->alias }}: {{ $interval->formatted_in_time ?? $interval->in_time }}
                                                            @if(!$loop->last), @endif
                                                        @endforeach
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">Shift not found</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->start_date)->format('M d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->end_date)->format('M d, Y') }}</td>
                                        <td>
                                            @php
                                                $now = \Carbon\Carbon::now();
                                                $startDate = \Carbon\Carbon::parse($schedule->start_date);
                                                $endDate = \Carbon\Carbon::parse($schedule->end_date);
                                            @endphp
                                            @if($now->lt($startDate))
                                                <span class="badge badge-info">Upcoming</span>
                                            @elseif($now->between($startDate, $endDate))
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('schedules.show', $schedule) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteSchedule({{ $schedule->id }})" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($schedules->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $schedules->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this schedule? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#employeeSchedulesTable').DataTable({
        "responsive": true,
        "order": [[2, "desc"]], // Order by start date descending
        "pageLength": 25
    });
});

function deleteSchedule(id) {
    $('#deleteForm').attr('action', '{{ url("schedules") }}/' + id);
    $('#deleteModal').modal('show');
}
</script>
@endsection
