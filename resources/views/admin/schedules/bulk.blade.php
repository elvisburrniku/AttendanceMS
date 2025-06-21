@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Bulk Schedule Assignment</h4>
                        <a href="{{ route('schedules.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Schedules
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

                    <form method="POST" action="{{ route('schedules.bulk.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shift_id">Select Shift <span class="text-danger">*</span></label>
                                    <select name="shift_id" id="shift_id" class="form-control" required>
                                        <option value="">Choose a shift...</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->alias }} ({{ $shift->working_hours }}h)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" 
                                           value="{{ old('start_date', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" 
                                           value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Select Employees <span class="text-danger">*</span></label>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                        <i class="fa fa-check-square"></i> Select All
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                        <i class="fa fa-square"></i> Deselect All
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" id="employeeSearch" class="form-control form-control-sm" 
                                           placeholder="Search employees...">
                                </div>
                            </div>
                            
                            <div class="employee-list" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                @foreach($employees as $employee)
                                    <div class="form-check employee-item">
                                        <input class="form-check-input employee-checkbox" type="checkbox" 
                                               name="employee_ids[]" value="{{ $employee->id }}" 
                                               id="employee_{{ $employee->id }}"
                                               {{ in_array($employee->id, old('employee_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="employee_{{ $employee->id }}">
                                            <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>
                                            <br><small class="text-muted">
                                                {{ $employee->employee_id }} - {{ $employee->department->name ?? 'N/A' }}
                                            </small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">
                                Selected: <span id="selectedCount">0</span> employees
                            </small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Assign Schedules
                            </button>
                            <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
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
    updateSelectedCount();
    
    // Employee search functionality
    $('#employeeSearch').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        $('.employee-item').each(function() {
            var employeeName = $(this).find('label').text().toLowerCase();
            if (employeeName.indexOf(searchTerm) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Update count when checkboxes change
    $('.employee-checkbox').on('change', function() {
        updateSelectedCount();
    });
});

function selectAll() {
    $('.employee-item:visible .employee-checkbox').prop('checked', true);
    updateSelectedCount();
}

function deselectAll() {
    $('.employee-checkbox').prop('checked', false);
    updateSelectedCount();
}

function updateSelectedCount() {
    var count = $('.employee-checkbox:checked').length;
    $('#selectedCount').text(count);
}
</script>
@endsection