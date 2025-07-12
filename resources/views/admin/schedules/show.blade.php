
@extends('layouts.master')

@section('title', 'Schedule Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('schedules.index') }}">Schedules</a></li>
                        <li class="breadcrumb-item active">Schedule Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Schedule Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Schedule Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Employee:</label>
                                <p class="mb-0">
                                    @if($schedule->employee)
                                        {{ $schedule->employee->first_name }} {{ $schedule->employee->last_name }}
                                        <br><small class="text-muted">{{ $schedule->employee->emp_code }}</small>
                                    @else
                                        <span class="text-danger">Employee not found</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Shift:</label>
                                <p class="mb-0">
                                    @if($schedule->shift)
                                        {{ $schedule->shift->alias }}
                                        @if($schedule->shift->working_hours)
                                            <br><small class="text-muted">{{ $schedule->shift->working_hours }} hours</small>
                                        @endif
                                    @else
                                        <span class="text-danger">Shift not found</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Start Date:</label>
                                <p class="mb-0">{{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">End Date:</label>
                                <p class="mb-0">{{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Duration:</label>
                                <p class="mb-0">
                                    {{ \Carbon\Carbon::parse($schedule->start_date)->diffInDays(\Carbon\Carbon::parse($schedule->end_date)) + 1 }} days
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Status:</label>
                                <p class="mb-0">
                                    @php
                                        $now = \Carbon\Carbon::now();
                                        $startDate = \Carbon\Carbon::parse($schedule->start_date);
                                        $endDate = \Carbon\Carbon::parse($schedule->end_date);
                                    @endphp
                                    @if($now->lt($startDate))
                                        <span class="badge badge-warning">Upcoming</span>
                                    @elseif($now->between($startDate, $endDate))
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Completed</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Schedule Slug:</label>
                        <p class="mb-0"><code>{{ $schedule->slug }}</code></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if($schedule->shift && $schedule->shift->timeIntervals->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Shift Time Intervals</h5>
                </div>
                <div class="card-body">
                    @foreach($schedule->shift->timeIntervals as $interval)
                    <div class="border rounded p-3 mb-2">
                        <h6 class="mb-1">{{ $interval->alias }}</h6>
                        <p class="mb-1">
                            <i class="mdi mdi-clock-outline"></i>
                            Start: {{ \Carbon\Carbon::parse($interval->in_time)->format('h:i A') }}
                        </p>
                        <p class="mb-0">
                            <i class="mdi mdi-timer-outline"></i>
                            Duration: {{ $interval->duration }} hours
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit Schedule
                        </a>
                        <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                        @if($schedule->employee)
                        <a href="{{ route('employees.schedules', $schedule->employee->id) }}" class="btn btn-info">
                            <i class="fa fa-calendar"></i> View Employee Schedules
                        </a>
                        @endif
                        <button type="button" class="btn btn-danger" onclick="deleteSchedule({{ $schedule->id }})">
                            <i class="fa fa-trash"></i> Delete Schedule
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this schedule assignment?
                <br><br>
                <strong>Employee:</strong> 
                @if($schedule->employee)
                    {{ $schedule->employee->first_name }} {{ $schedule->employee->last_name }}
                @else
                    Unknown
                @endif
                <br>
                <strong>Shift:</strong> 
                @if($schedule->shift)
                    {{ $schedule->shift->alias }}
                @else
                    Unknown
                @endif
                <br>
                <strong>Period:</strong> {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}
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
function deleteSchedule(id) {
    $('#deleteForm').attr('action', '{{ url("schedules") }}/' + id);
    $('#deleteModal').modal('show');
}
</script>
@endsection
