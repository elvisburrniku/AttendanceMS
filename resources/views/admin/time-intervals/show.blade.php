
@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Time Interval Details</h4>
                        <div>
                            <a href="{{ route('time-intervals.edit', $timeInterval) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('time-intervals.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th width="30%">ID</th>
                                        <td>{{ $timeInterval->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $timeInterval->alias }}</td>
                                    </tr>
                                    <tr>
                                        <th>Start Time</th>
                                        <td>{{ $timeInterval->formatted_in_time }}</td>
                                    </tr>
                                    <tr>
                                        <th>Duration</th>
                                        <td>
                                            {{ $timeInterval->duration }} minutes
                                            <span class="text-muted">({{ $timeInterval->duration_in_hours }} hours)</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Work Time Duration</th>
                                        <td>{{ $timeInterval->work_time_duration }} minutes</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($timeInterval->use_mode > 0)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Work Day</th>
                                        <td>{{ $timeInterval->work_day }}</td>
                                    </tr>
                                    <tr>
                                        <th>Color Setting</th>
                                        <td>
                                            <span class="badge" style="background-color: {{ $timeInterval->color_setting }}; color: white;">
                                                {{ $timeInterval->color_setting }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Time Settings</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Check-in Margins:</strong><br>
                                        <small>Ahead: {{ $timeInterval->in_ahead_margin }} min</small><br>
                                        <small>Above: {{ $timeInterval->in_above_margin }} min</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Check-out Margins:</strong><br>
                                        <small>Ahead: {{ $timeInterval->out_ahead_margin }} min</small><br>
                                        <small>Above: {{ $timeInterval->out_above_margin }} min</small>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Allowances:</strong><br>
                                        <small>Allow Late: {{ $timeInterval->allow_late }} min</small><br>
                                        <small>Allow Leave Early: {{ $timeInterval->allow_leave_early }} min</small>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Features:</strong><br>
                                        <div class="form-check-inline">
                                            <span class="badge {{ $timeInterval->enable_early_in ? 'badge-success' : 'badge-secondary' }}">
                                                Early In: {{ $timeInterval->enable_early_in ? 'Yes' : 'No' }}
                                            </span>
                                        </div><br>
                                        <div class="form-check-inline mt-1">
                                            <span class="badge {{ $timeInterval->enable_late_out ? 'badge-success' : 'badge-secondary' }}">
                                                Late Out: {{ $timeInterval->enable_late_out ? 'Yes' : 'No' }}
                                            </span>
                                        </div><br>
                                        <div class="form-check-inline mt-1">
                                            <span class="badge {{ $timeInterval->enable_overtime ? 'badge-success' : 'badge-secondary' }}">
                                                Overtime: {{ $timeInterval->enable_overtime ? 'Yes' : 'No' }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($timeInterval->enable_max_ot_limit)
                                    <div class="mb-3">
                                        <strong>Max OT Limit:</strong><br>
                                        <small>{{ $timeInterval->max_ot_limit }} minutes</small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($timeInterval->shifts && $timeInterval->shifts->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Used in Shifts</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Shift Name</th>
                                                    <th>Color</th>
                                                    <th>Working Hours</th>
                                                    <th>Work Type</th>
                                                    <th>Day of Week</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($timeInterval->shifts as $shift)
                                                <tr>
                                                    <td>{{ $shift->alias }}</td>
                                                    <td>
                                                        <span class="badge" style="background-color: {{ $shift->color }}; color: white;">
                                                            {{ $shift->color }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $shift->working_hours }} hours</td>
                                                    <td>{{ $shift->pivot->work_type ?? 'N/A' }}</td>
                                                    <td>{{ $shift->pivot->day_of_week ?? 'N/A' }}</td>
                                                    <td>
                                                        <a href="{{ route('shifts.show', $shift) }}" class="btn btn-sm btn-info">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
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
                    @endif

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">
                                        Created: {{ $timeInterval->created_at ? $timeInterval->created_at->format('M d, Y h:i A') : 'N/A' }}
                                    </small><br>
                                    <small class="text-muted">
                                        Updated: {{ $timeInterval->updated_at ? $timeInterval->updated_at->format('M d, Y h:i A') : 'N/A' }}
                                    </small>
                                </div>
                                <div>
                                    <a href="{{ route('time-intervals.edit', $timeInterval) }}" class="btn btn-warning">
                                        <i class="fa fa-edit"></i> Edit Time Interval
                                    </a>
                                    <a href="{{ route('time-intervals.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-list"></i> Back to List
                                    </a>
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
