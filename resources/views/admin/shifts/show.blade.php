@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ trans('global.show') }} Shift Details</h4>
                <a href="{{ route('shifts.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back to Shifts
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="form-group">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{ $shift->id }}</td>
                        </tr>
                        <tr>
                            <th>Shift Name</th>
                            <td>{{ $shift->alias }}</td>
                        </tr>
                        <tr>
                            <th>Working Hours</th>
                            <td>{{ $shift->working_hours }} hours</td>
                        </tr>
                        <tr>
                            <th>Color</th>
                            <td>
                                <span class="badge" style="background-color: {{ $shift->color }}; color: white;">
                                    {{ $shift->color }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($shift->is_active ?? true)
                                    <div class="badge badge-success">Active</div>
                                @else
                                    <div class="badge badge-danger">Inactive</div>
                                @endif
                            </td>
                        </tr>
                        @if($shift->time_intervals && $shift->time_intervals->count() > 0)
                        <tr>
                            <th>Time Intervals</th>
                            <td>
                                @foreach($shift->time_intervals as $interval)
                                    <div class="mb-2">
                                        <strong>{{ $interval->alias }}</strong><br>
                                        Start: {{ $interval->in_time }}<br>
                                        Duration: {{ $interval->duration }} hours
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Created At</th>
                            <td>{{ $shift->created_at ? $shift->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $shift->updated_at ? $shift->updated_at->format('M d, Y h:i A') : 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-3">
                    <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-info">
                        <i class="fa fa-edit"></i> Edit Shift
                    </a>
                    <a href="{{ route('shifts.index') }}" class="btn btn-secondary">
                        <i class="fa fa-list"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection