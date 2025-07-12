@extends('layouts.master')

@section('css')
<link href="{{ asset('assets/css/calendar.css') }}" rel="stylesheet" type="text/css">
<style>
/* Additional inline styles for calendar */
.shift-calendar {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.calendar-header {
    background: #f8f9fa;
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
}

.calendar-grid {
    display: table;
    width: 100%;
    border-collapse: collapse;
}

.calendar-row {
    display: table-row;
}

.calendar-cell {
    display: table-cell;
    border: 1px solid #dee2e6;
    min-height: 80px;
    vertical-align: top;
    position: relative;
}

.employee-cell {
    width: 200px;
    background: #f8f9fa;
    padding: 15px 10px;
    font-weight: 600;
    border-right: 2px solid #dee2e6;
}

.day-cell {
    width: calc((100% - 200px) / 7);
    padding: 5px;
    min-height: 80px;
}

.day-header {
    text-align: center;
    padding: 15px 5px;
    background: #6c757d;
    color: white;
    font-weight: 600;
}

.day-header.weekend {
    background: #dc3545;
}

.day-header.today {
    background: #28a745;
}

.shift-block {
    background: #3498db;
    color: white;
    padding: 4px 8px;
    margin: 2px 0;
    border-radius: 4px;
    font-size: 11px;
    cursor: move;
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    transition: all 0.2s ease;
}

.shift-block:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

.shift-block.dragging {
    opacity: 0.7;
    transform: rotate(5deg);
}

.day-cell.drag-over {
    background: #e3f2fd;
    border: 2px dashed #2196f3;
}

.shift-time {
    font-size: 10px;
    opacity: 0.9;
}

.employee-info {
    font-size: 12px;
    color: #6c757d;
    margin-top: 4px;
}

.calendar-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.week-navigation {
    display: flex;
    align-items: center;
    gap: 15px;
}

.week-navigation button {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
}

.week-navigation button:hover {
    background: #0056b3;
}

.current-week {
    font-weight: 600;
    font-size: 16px;
}

.quick-actions {
    display: flex;
    gap: 10px;
}

.add-schedule-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
}

.add-schedule-btn:hover {
    background: #1e7e34;
}

.legend {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-top: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
}

.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 3px;
}

.context-menu {
    position: absolute;
    background: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 1000;
    display: none;
}

.context-menu-item {
    padding: 8px 15px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

.context-menu-item:hover {
    background: #f8f9fa;
}

.context-menu-item:last-child {
    border-bottom: none;
}

.empty-day {
    min-height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-style: italic;
    font-size: 12px;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    display: none;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Shift Calendar</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Shift Calendar</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Calendar Controls -->
            <div class="calendar-controls">
                <div class="week-navigation">
                    <button type="button" id="prevWeek">
                        <i class="fa fa-chevron-left"></i> Previous
                    </button>
                    <div class="current-week" id="currentWeek">
                        {{ $startDate->format('M j') }} - {{ $endDate->format('M j, Y') }}
                    </div>
                    <button type="button" id="nextWeek">
                        Next <i class="fa fa-chevron-right"></i>
                    </button>
                    <button type="button" id="todayBtn" class="btn btn-outline-primary btn-sm">
                        Today
                    </button>
                </div>
                
                <div class="quick-actions">
                    <button type="button" class="add-schedule-btn" data-toggle="modal" data-target="#addScheduleModal">
                        <i class="fa fa-plus"></i> Add Schedule
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="refreshCalendar()">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="shift-calendar">
                <div class="calendar-wrapper">
                    <div class="calendar-grid" id="calendarGrid">
                        <!-- Calendar will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="legend">
                <strong>Legend:</strong>
                @foreach($shifts as $shift)
                    <div class="legend-item">
                        <div class="legend-color" style="background: {{ ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#34495e', '#e67e22'][$loop->index % 8] }};"></div>
                        <span>{{ $shift->alias }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Schedule</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addScheduleForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Employee</label>
                        <select name="employee_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->emp_code ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Shift</label>
                        <select name="shift_id" class="form-control" required>
                            <option value="">Select Shift</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->alias }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Duration (days)</label>
                        <input type="number" name="duration" class="form-control" value="1" min="1" max="365">
                        <small class="form-text text-muted">Number of consecutive days for this schedule</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Context Menu -->
<div class="context-menu" id="contextMenu">
    <div class="context-menu-item" onclick="editSchedule()">
        <i class="fa fa-edit"></i> Edit Schedule
    </div>
    <div class="context-menu-item" onclick="deleteSchedule()">
        <i class="fa fa-trash"></i> Delete Schedule
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>
@endsection

@section('script')
<script>
let currentDate = '{{ $currentDate }}';
let calendarData = {!! json_encode($calendarData) !!};
let shifts = {!! json_encode($shifts) !!};
let contextMenuTarget = null;
let draggedElement = null;

console.log('Initial calendar data:', calendarData);

$(document).ready(function() {
    renderCalendar();
    initializeEventListeners();
});

function renderCalendar() {
    const grid = document.getElementById('calendarGrid');
    grid.innerHTML = '';
    
    // Validate calendar data
    if (!calendarData || !calendarData.weekDays || !calendarData.employees) {
        console.error('Invalid calendar data:', calendarData);
        grid.innerHTML = '<div class="alert alert-danger">Invalid calendar data</div>';
        return;
    }
    
    // Debug calendar structure for drag-drop analysis
    console.log('Calendar Week Structure:', {
        weekDays: calendarData.weekDays.map(day => ({
            date: day.date,
            dayNumber: day.dayNumber,
            day: day.day
        })),
        totalEmployees: calendarData.employees.length
    });
    
    // Create header row
    const headerRow = document.createElement('div');
    headerRow.className = 'calendar-row';
    
    // Empty cell for employee column
    const employeeHeader = document.createElement('div');
    employeeHeader.className = 'calendar-cell employee-cell day-header';
    employeeHeader.textContent = 'Employee';
    headerRow.appendChild(employeeHeader);
    
    // Day headers
    calendarData.weekDays.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-cell day-header';
        if (day.isWeekend) dayHeader.classList.add('weekend');
        if (day.isToday) dayHeader.classList.add('today');
        
        dayHeader.innerHTML = `
            <div>${day.day}</div>
            <div style="font-size: 12px; font-weight: normal;">${day.dayNumber}</div>
        `;
        headerRow.appendChild(dayHeader);
    });
    
    grid.appendChild(headerRow);
    
    // Create employee rows
    calendarData.employees.forEach(employeeData => {
        const employeeRow = document.createElement('div');
        employeeRow.className = 'calendar-row';
        
        // Employee cell
        const employeeCell = document.createElement('div');
        employeeCell.className = 'calendar-cell employee-cell';
        employeeCell.innerHTML = `
            <div>${employeeData.employee.name}</div>
            <div class="employee-info">${employeeData.employee.emp_code}</div>
        `;
        employeeRow.appendChild(employeeCell);
        
        // Day cells
        calendarData.weekDays.forEach(day => {
            const dayCell = document.createElement('div');
            dayCell.className = 'calendar-cell day-cell';
            dayCell.dataset.date = day.date;
            dayCell.dataset.employeeId = employeeData.employee.id;
            
            const schedules = employeeData.schedules[day.date] || [];
            
            if (!Array.isArray(schedules) || schedules.length === 0) {
                dayCell.innerHTML = '<div class="empty-day">No shifts</div>';
            } else {
                schedules.forEach(schedule => {
                    const shiftBlock = createShiftBlock(schedule);
                    dayCell.appendChild(shiftBlock);
                });
            }
            
            // Make droppable
            makeDroppable(dayCell);
            employeeRow.appendChild(dayCell);
        });
        
        grid.appendChild(employeeRow);
    });
}

function createShiftBlock(schedule) {
    const shiftBlock = document.createElement('div');
    shiftBlock.className = 'shift-block';
    shiftBlock.style.background = schedule.shift.color;
    shiftBlock.dataset.scheduleId = schedule.id;
    shiftBlock.draggable = true;
    
    const timeIntervals = Array.isArray(schedule.shift.time_intervals) ? 
        schedule.shift.time_intervals.map(interval => 
            `${interval.in_time} (${interval.duration}h)`
        ).join(', ') : 'No time info';
    
    shiftBlock.innerHTML = `
        <div>${schedule.shift.alias}</div>
        <div class="shift-time">${timeIntervals}</div>
    `;
    
    // Add drag events
    shiftBlock.addEventListener('dragstart', handleDragStart);
    shiftBlock.addEventListener('dragend', handleDragEnd);
    
    // Add context menu
    shiftBlock.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        showContextMenu(e, schedule);
    });
    
    return shiftBlock;
}

function makeDroppable(element) {
    element.addEventListener('dragover', handleDragOver);
    element.addEventListener('drop', handleDrop);
    element.addEventListener('dragenter', handleDragEnter);
    element.addEventListener('dragleave', handleDragLeave);
}

function handleDragStart(e) {
    draggedElement = e.target;
    e.target.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', e.target.outerHTML);
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
    draggedElement = null;
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
}

function handleDragEnter(e) {
    e.preventDefault();
    let targetCell = e.target;
    while (targetCell && !targetCell.classList.contains('day-cell')) {
        targetCell = targetCell.parentElement;
    }
    if (targetCell) {
        targetCell.classList.add('drag-over');
    }
}

function handleDragLeave(e) {
    // Only remove drag-over if we're actually leaving the cell
    let targetCell = e.target;
    while (targetCell && !targetCell.classList.contains('day-cell')) {
        targetCell = targetCell.parentElement;
    }
    if (targetCell && !targetCell.contains(e.relatedTarget)) {
        targetCell.classList.remove('drag-over');
    }
}

function handleDrop(e) {
    e.preventDefault();
    
    // Find the actual day cell (might be nested)
    let targetCell = e.target;
    while (targetCell && !targetCell.classList.contains('day-cell')) {
        targetCell = targetCell.parentElement;
    }
    
    if (targetCell) {
        targetCell.classList.remove('drag-over');
    }
    
    if (!draggedElement || !targetCell) {
        console.warn('Drop failed: missing draggedElement or targetCell', {
            draggedElement: !!draggedElement,
            targetCell: !!targetCell
        });
        return;
    }
    
    const scheduleId = draggedElement.dataset.scheduleId;
    const newDate = targetCell.dataset.date;
    const newEmployeeId = targetCell.dataset.employeeId;
    
    // Enhanced debugging for drag-drop issues
    console.log('Drag and Drop Debug:', {
        scheduleId: scheduleId,
        newDate: newDate,
        newEmployeeId: newEmployeeId,
        draggedElement: draggedElement,
        targetCell: targetCell,
        targetCellDataset: targetCell.dataset,
        dropEvent: {
            clientX: e.clientX,
            clientY: e.clientY,
            target: e.target
        }
    });
    
    // Get original date from parent cell for comparison
    const originalCell = draggedElement.closest('.day-cell');
    const originalDate = originalCell ? originalCell.dataset.date : 'unknown';
    
    console.log('Moving from', originalDate, 'to', newDate);
    
    // Visual feedback - temporarily move the element
    if (draggedElement && targetCell) {
        const clone = draggedElement.cloneNode(true);
        clone.style.opacity = '0.5';
        clone.style.border = '2px dashed #007bff';
        targetCell.appendChild(clone);
        draggedElement.style.display = 'none';
    }
    
    updateSchedule(scheduleId, newDate, newEmployeeId);
}

function updateSchedule(scheduleId, newDate, employeeId) {
    showLoading();
    
    $.ajax({
        url: '{{ route("calendar.update") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            schedule_id: scheduleId,
            new_date: newDate,
            employee_id: employeeId
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                refreshCalendar();
            } else {
                showAlert('error', 'Failed to update schedule');
                refreshCalendar(); // Refresh to reset any visual changes
            }
        },
        error: function(xhr, status, error) {
            console.error('Update error:', error);
            showAlert('error', 'Error updating schedule: ' + error);
            refreshCalendar(); // Refresh to reset any visual changes
        },
        complete: function() {
            hideLoading();
        }
    });
}

function initializeEventListeners() {
    // Week navigation
    $('#prevWeek').click(function() {
        changeWeek(-7);
    });
    
    $('#nextWeek').click(function() {
        changeWeek(7);
    });
    
    $('#todayBtn').click(function() {
        currentDate = new Date().toISOString().split('T')[0];
        loadWeekData();
    });
    
    // Add schedule form
    $('#addScheduleForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("calendar.create") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                employee_id: formData.get('employee_id'),
                shift_id: formData.get('shift_id'),
                date: formData.get('date'),
                duration: formData.get('duration')
            },
            success: function(response) {
                if (response.success) {
                    $('#addScheduleModal').modal('hide');
                    showAlert('success', response.message);
                    refreshCalendar();
                    document.getElementById('addScheduleForm').reset();
                }
            },
            error: function() {
                showAlert('error', 'Error creating schedule');
            }
        });
    });
    
    // Hide context menu on click
    $(document).click(function() {
        $('#contextMenu').hide();
    });
}

function changeWeek(days) {
    const date = new Date(currentDate);
    date.setDate(date.getDate() + days);
    currentDate = date.toISOString().split('T')[0];
    loadWeekData();
}

function loadWeekData() {
    showLoading();
    
    $.ajax({
        url: '{{ route("calendar.week-data") }}',
        method: 'GET',
        data: { date: currentDate },
        success: function(response) {
            if (response.success && response.calendarData) {
                calendarData = response.calendarData;
                updateWeekDisplay(response.startDate, response.endDate);
                renderCalendar();
            } else {
                showAlert('error', 'Invalid calendar data received');
            }
        },
        error: function(xhr, status, error) {
            console.error('Calendar load error:', error);
            showAlert('error', 'Error loading calendar data: ' + error);
        },
        complete: function() {
            hideLoading();
        }
    });
}

function updateWeekDisplay(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const options = { month: 'short', day: 'numeric' };
    
    $('#currentWeek').text(
        start.toLocaleDateString('en-US', options) + ' - ' + 
        end.toLocaleDateString('en-US', { ...options, year: 'numeric' })
    );
}

function refreshCalendar() {
    loadWeekData();
}

function showContextMenu(e, schedule) {
    contextMenuTarget = schedule;
    const menu = $('#contextMenu');
    menu.css({
        left: e.pageX + 'px',
        top: e.pageY + 'px'
    }).show();
}

function editSchedule() {
    if (contextMenuTarget) {
        // Implementation for edit functionality
        showAlert('info', 'Edit functionality coming soon');
    }
    $('#contextMenu').hide();
}

function deleteSchedule() {
    if (contextMenuTarget && confirm('Are you sure you want to delete this schedule?')) {
        $.ajax({
            url: '{{ route("calendar.delete") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                schedule_id: contextMenuTarget.id
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    refreshCalendar();
                }
            },
            error: function() {
                showAlert('error', 'Error deleting schedule');
            }
        });
    }
    $('#contextMenu').hide();
}

function showLoading() {
    $('#loadingOverlay').show();
}

function hideLoading() {
    $('#loadingOverlay').hide();
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 
                     type === 'error' ? 'alert-danger' : 'alert-info';
    
    const alert = `
        <div class="alert ${alertClass} alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            ${message}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    `;
    
    $('body').append(alert);
    
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 3000);
}
</script>
@endsection