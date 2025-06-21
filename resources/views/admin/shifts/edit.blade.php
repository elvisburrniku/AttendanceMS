@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Edit Shift: {{ $shift->alias }}</h4>
                        <a href="{{ route('shifts.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Shifts
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

                    <form method="POST" action="{{ route('shifts.update', $shift) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="alias">Shift Name <span class="text-danger">*</span></label>
                                    <input type="text" name="alias" id="alias" class="form-control" 
                                           value="{{ old('alias', $shift->alias) }}" required placeholder="e.g., Day Shift">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cycle_unit">Cycle Unit (Days) <span class="text-danger">*</span></label>
                                    <select name="cycle_unit" id="cycle_unit" class="form-control" required>
                                        <option value="1" {{ old('cycle_unit', $shift->cycle_unit) == 1 ? 'selected' : '' }}>Daily</option>
                                        <option value="7" {{ old('cycle_unit', $shift->cycle_unit) == 7 ? 'selected' : '' }}>Weekly</option>
                                        <option value="14" {{ old('cycle_unit', $shift->cycle_unit) == 14 ? 'selected' : '' }}>Bi-weekly</option>
                                        <option value="30" {{ old('cycle_unit', $shift->cycle_unit) == 30 ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="shift_cycle">Shift Cycle <span class="text-danger">*</span></label>
                                    <input type="number" name="shift_cycle" id="shift_cycle" class="form-control" 
                                           value="{{ old('shift_cycle', $shift->shift_cycle) }}" required min="1" max="365">
                                    <small class="form-text text-muted">Number of cycles</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="work_weekend" 
                                           id="work_weekend" {{ old('work_weekend', $shift->work_weekend) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="work_weekend">
                                        Work on Weekends
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="weekend_type">Weekend Type</label>
                                    <select name="weekend_type" id="weekend_type" class="form-control">
                                        <option value="0" {{ old('weekend_type', $shift->weekend_type) == 0 ? 'selected' : '' }}>No weekend work</option>
                                        <option value="1" {{ old('weekend_type', $shift->weekend_type) == 1 ? 'selected' : '' }}>Saturday only</option>
                                        <option value="2" {{ old('weekend_type', $shift->weekend_type) == 2 ? 'selected' : '' }}>Both Saturday and Sunday</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="work_day_off" 
                                           id="work_day_off" {{ old('work_day_off', $shift->work_day_off) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="work_day_off">
                                        Work on Day Off
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="day_off_type">Day Off Type</label>
                                    <select name="day_off_type" id="day_off_type" class="form-control">
                                        <option value="0" {{ old('day_off_type', $shift->day_off_type) == 0 ? 'selected' : '' }}>No day off work</option>
                                        <option value="1" {{ old('day_off_type', $shift->day_off_type) == 1 ? 'selected' : '' }}>Compensatory day off</option>
                                        <option value="2" {{ old('day_off_type', $shift->day_off_type) == 2 ? 'selected' : '' }}>Overtime pay</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="auto_shift">Auto Shift</label>
                                    <select name="auto_shift" id="auto_shift" class="form-control">
                                        <option value="0" {{ old('auto_shift', $shift->auto_shift) == 0 ? 'selected' : '' }}>Manual</option>
                                        <option value="1" {{ old('auto_shift', $shift->auto_shift) == 1 ? 'selected' : '' }}>Auto by time</option>
                                        <option value="2" {{ old('auto_shift', $shift->auto_shift) == 2 ? 'selected' : '' }}>Auto by schedule</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="frequency">Frequency</label>
                                    <select name="frequency" id="frequency" class="form-control">
                                        <option value="1" {{ old('frequency', $shift->frequency) == 1 ? 'selected' : '' }}>Daily</option>
                                        <option value="2" {{ old('frequency', $shift->frequency) == 2 ? 'selected' : '' }}>Every 2 days</option>
                                        <option value="3" {{ old('frequency', $shift->frequency) == 3 ? 'selected' : '' }}>Every 3 days</option>
                                        <option value="7" {{ old('frequency', $shift->frequency) == 7 ? 'selected' : '' }}>Weekly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="enable_ot_rule" 
                                           id="enable_ot_rule" {{ old('enable_ot_rule', $shift->enable_ot_rule) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_ot_rule">
                                        Enable Overtime Rules
                                    </label>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="ot_rule" id="ot_rule" class="form-control" 
                                           value="{{ old('ot_rule', $shift->ot_rule) }}" placeholder="Overtime rule code">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Assign Time Intervals</label>
                            <div class="row">
                                @foreach($timeIntervals as $interval)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="time_intervals[]" 
                                                   value="{{ $interval->id }}" id="interval_{{ $interval->id }}"
                                                   {{ $shift->timeIntervals->contains($interval->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="interval_{{ $interval->id }}">
                                                <strong>{{ $interval->alias }}</strong>
                                                <br><small class="text-muted">
                                                    {{ $interval->formatted_in_time }} - {{ $interval->duration_in_hours }}h
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($timeIntervals->isEmpty())
                                <div class="alert alert-info">
                                    No time intervals available. <a href="{{ route('time-intervals.create') }}">Create one first</a>.
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Shift
                            </button>
                            <a href="{{ route('shifts.index') }}" class="btn btn-secondary">
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
    // Enable/disable overtime rule input
    $('#enable_ot_rule').on('change', function() {
        $('#ot_rule').prop('disabled', !$(this).is(':checked'));
    });
    
    // Initialize overtime rule state
    $('#ot_rule').prop('disabled', !$('#enable_ot_rule').is(':checked'));
});
</script>
@endsection