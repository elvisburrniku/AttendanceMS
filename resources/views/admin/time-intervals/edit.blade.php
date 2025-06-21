@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Edit Time Interval: {{ $timeInterval->alias }}</h4>
                        <a href="{{ route('time-intervals.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Time Intervals
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('time-intervals.update', $timeInterval) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="alias">Time Interval Name <span class="text-danger">*</span></label>
                                    <input type="text" name="alias" id="alias" class="form-control" 
                                           value="{{ old('alias', $timeInterval->alias) }}" required placeholder="e.g., Morning Shift">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="in_time">Start Time <span class="text-danger">*</span></label>
                                    <input type="time" name="in_time" id="in_time" class="form-control" 
                                           value="{{ old('in_time', $timeInterval->in_time ? $timeInterval->in_time->format('H:i') : '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="duration">Duration (minutes) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration" id="duration" class="form-control" 
                                           value="{{ old('duration', $timeInterval->duration) }}" required min="1" max="1440">
                                    <small class="form-text text-muted">{{ $timeInterval->duration }} minutes = {{ $timeInterval->duration_in_hours }} hours</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="in_ahead_margin">Early Clock-in (minutes)</label>
                                    <input type="number" name="in_ahead_margin" id="in_ahead_margin" class="form-control" 
                                           value="{{ old('in_ahead_margin', $timeInterval->in_ahead_margin) }}" min="0">
                                    <small class="form-text text-muted">How early employees can clock in</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="in_above_margin">Late Clock-in Grace (minutes)</label>
                                    <input type="number" name="in_above_margin" id="in_above_margin" class="form-control" 
                                           value="{{ old('in_above_margin', $timeInterval->in_above_margin) }}" min="0">
                                    <small class="form-text text-muted">Grace period for late clock-in</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="out_ahead_margin">Early Clock-out (minutes)</label>
                                    <input type="number" name="out_ahead_margin" id="out_ahead_margin" class="form-control" 
                                           value="{{ old('out_ahead_margin', $timeInterval->out_ahead_margin) }}" min="0">
                                    <small class="form-text text-muted">How early employees can clock out</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="out_above_margin">Late Clock-out (minutes)</label>
                                    <input type="number" name="out_above_margin" id="out_above_margin" class="form-control" 
                                           value="{{ old('out_above_margin', $timeInterval->out_above_margin) }}" min="0">
                                    <small class="form-text text-muted">How late employees can clock out</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="allow_late">Late Arrival Tolerance (minutes)</label>
                                    <input type="number" name="allow_late" id="allow_late" class="form-control" 
                                           value="{{ old('allow_late', $timeInterval->allow_late) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="allow_leave_early">Early Leave Tolerance (minutes)</label>
                                    <input type="number" name="allow_leave_early" id="allow_leave_early" class="form-control" 
                                           value="{{ old('allow_leave_early', $timeInterval->allow_leave_early) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="work_day">Work Day Credit</label>
                                    <input type="number" name="work_day" id="work_day" class="form-control" 
                                           value="{{ old('work_day', $timeInterval->work_day) }}" min="0" max="1" step="0.1">
                                    <small class="form-text text-muted">1.0 = Full day, 0.5 = Half day</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h5>Advanced Options</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="enable_early_in" 
                                           id="enable_early_in" {{ old('enable_early_in', $timeInterval->enable_early_in) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_early_in">
                                        Enable early clock-in tracking
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="enable_late_out" 
                                           id="enable_late_out" {{ old('enable_late_out', $timeInterval->enable_late_out) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_late_out">
                                        Enable late clock-out tracking
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="enable_overtime" 
                                           id="enable_overtime" {{ old('enable_overtime', $timeInterval->enable_overtime) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_overtime">
                                        Enable overtime calculation
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="overtime_settings" style="{{ old('enable_overtime', $timeInterval->enable_overtime) ? '' : 'display: none;' }}">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="enable_max_ot_limit" 
                                           id="enable_max_ot_limit" {{ old('enable_max_ot_limit', $timeInterval->enable_max_ot_limit) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_max_ot_limit">
                                        Set maximum overtime limit
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="max_ot_limit">Max Overtime (minutes)</label>
                                    <input type="number" name="max_ot_limit" id="max_ot_limit" class="form-control" 
                                           value="{{ old('max_ot_limit', $timeInterval->max_ot_limit) }}" min="0"
                                           {{ old('enable_max_ot_limit', $timeInterval->enable_max_ot_limit) ? '' : 'disabled' }}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="count_early_in_interval" 
                                           id="count_early_in_interval" {{ old('count_early_in_interval', $timeInterval->count_early_in_interval) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="count_early_in_interval">
                                        Count early clock-in as overtime
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="count_late_out_interval" 
                                           id="count_late_out_interval" {{ old('count_late_out_interval', $timeInterval->count_late_out_interval) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="count_late_out_interval">
                                        Count late clock-out as overtime
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Time Interval
                            </button>
                            <a href="{{ route('time-intervals.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Show/hide overtime settings
    $('#enable_overtime').on('change', function() {
        if ($(this).is(':checked')) {
            $('#overtime_settings').show();
        } else {
            $('#overtime_settings').hide();
        }
    });
    
    // Enable/disable max overtime limit input
    $('#enable_max_ot_limit').on('change', function() {
        $('#max_ot_limit').prop('disabled', !$(this).is(':checked'));
    });
    
    // Convert duration to hours display
    $('#duration').on('input', function() {
        var minutes = parseInt($(this).val()) || 0;
        var hours = (minutes / 60).toFixed(2);
        $(this).siblings('.form-text').text(minutes + ' minutes = ' + hours + ' hours');
    });
});
</script>
@endsection