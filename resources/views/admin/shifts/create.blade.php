@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Create New Shift</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('shifts.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="alias">Shift Name <span class="text-danger">*</span></label>
                            <input type="text" name="alias" id="alias" 
                                   class="form-control @error('alias') is-invalid @enderror" 
                                   value="{{ old('alias') }}" required>
                            @error('alias')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cycle_unit">Cycle Unit <span class="text-danger">*</span></label>
                                    <select name="cycle_unit" id="cycle_unit" class="form-control @error('cycle_unit') is-invalid @enderror" required>
                                        <option value="1" {{ old('cycle_unit') == 1 ? 'selected' : '' }}>Daily</option>
                                        <option value="2" {{ old('cycle_unit') == 2 ? 'selected' : '' }}>Weekly</option>
                                        <option value="3" {{ old('cycle_unit') == 3 ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                    @error('cycle_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shift_cycle">Shift Cycle (Days) <span class="text-danger">*</span></label>
                                    <input type="number" name="shift_cycle" id="shift_cycle" 
                                           class="form-control @error('shift_cycle') is-invalid @enderror" 
                                           value="{{ old('shift_cycle', 7) }}" min="1" required>
                                    @error('shift_cycle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Time Intervals</label>
                            <div class="form-check-list" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                @foreach($timeIntervals as $interval)
                                <div class="form-check">
                                    <input type="checkbox" name="time_intervals[]" value="{{ $interval->id }}" 
                                           id="interval_{{ $interval->id }}" class="form-check-input"
                                           {{ in_array($interval->id, old('time_intervals', [])) ? 'checked' : '' }}>
                                    <label for="interval_{{ $interval->id }}" class="form-check-label">
                                        <strong>{{ $interval->alias }}</strong>
                                        <span class="text-muted">
                                            ({{ $interval->formatted_in_time }} - {{ number_format($interval->duration / 60, 1) }}h)
                                        </span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="work_weekend" id="work_weekend" 
                                               class="form-check-input" value="1" 
                                               {{ old('work_weekend') ? 'checked' : '' }}>
                                        <label for="work_weekend" class="form-check-label">Work on Weekends</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="work_day_off" id="work_day_off" 
                                               class="form-check-input" value="1" 
                                               {{ old('work_day_off') ? 'checked' : '' }}>
                                        <label for="work_day_off" class="form-check-label">Work on Days Off</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="enable_ot_rule" id="enable_ot_rule" 
                                               class="form-check-input" value="1" 
                                               {{ old('enable_ot_rule') ? 'checked' : '' }}>
                                        <label for="enable_ot_rule" class="form-check-label">Enable Overtime Rules</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="frequency">Frequency</label>
                                    <select name="frequency" id="frequency" class="form-control">
                                        <option value="1" {{ old('frequency') == 1 ? 'selected' : '' }}>Daily</option>
                                        <option value="2" {{ old('frequency') == 2 ? 'selected' : '' }}>Weekly</option>
                                        <option value="3" {{ old('frequency') == 3 ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create Shift
                            </button>
                            <a href="{{ route('shifts.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Shift Preview</h5>
                </div>
                <div class="card-body">
                    <div id="shiftPreview">
                        <p class="text-muted">Configure shift settings to see preview</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    updatePreview();
    
    $('input, select').on('change', function() {
        updatePreview();
    });
});

function updatePreview() {
    var alias = $('#alias').val();
    var cycleUnit = $('#cycle_unit option:selected').text();
    var shiftCycle = $('#shift_cycle').val();
    var selectedIntervals = [];
    
    $('input[name="time_intervals[]"]:checked').each(function() {
        var label = $(this).next('label').text();
        selectedIntervals.push(label.trim());
    });
    
    var features = [];
    if ($('#work_weekend').is(':checked')) features.push('Weekend Work');
    if ($('#work_day_off').is(':checked')) features.push('Day Off Work');
    if ($('#enable_ot_rule').is(':checked')) features.push('Overtime Rules');
    
    var preview = '<div class="alert alert-info">';
    if (alias) {
        preview += '<h6>' + alias + '</h6>';
    }
    preview += '<p><strong>Cycle:</strong> ' + cycleUnit + ' (' + shiftCycle + ' days)</p>';
    
    if (selectedIntervals.length > 0) {
        preview += '<p><strong>Time Intervals:</strong></p><ul>';
        selectedIntervals.forEach(function(interval) {
            preview += '<li>' + interval + '</li>';
        });
        preview += '</ul>';
    }
    
    if (features.length > 0) {
        preview += '<p><strong>Features:</strong> ' + features.join(', ') + '</p>';
    }
    
    preview += '</div>';
    
    $('#shiftPreview').html(alias || selectedIntervals.length > 0 ? preview : '<p class="text-muted">Configure shift settings to see preview</p>');
}
</script>
@endsection