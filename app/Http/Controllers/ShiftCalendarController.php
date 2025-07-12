<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\TimeInterval;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ShiftCalendarController extends Controller
{
    public function index(Request $request)
    {
        $currentDate = $request->get('date', now()->format('Y-m-d'));
        $startDate = Carbon::parse($currentDate)->startOfWeek();
        $endDate = Carbon::parse($currentDate)->endOfWeek();

        // Get all employees
        $employees = Employee::orderBy('first_name')->get();
        
        // Get all shifts with time intervals
        $shifts = Shift::with('timeIntervals')->orderBy('alias')->get();
        
        // Get schedules for the week
        $schedules = Schedule::with(['employee', 'shift.timeIntervals'])
            ->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orWhere(function($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate->format('Y-m-d'))
                      ->where('end_date', '>=', $endDate->format('Y-m-d'));
            })
            ->get();

        // Build calendar data
        $calendarData = $this->buildCalendarData($employees, $schedules, $startDate, $endDate);

        return view('admin.calendar.index', compact(
            'employees', 
            'shifts', 
            'schedules', 
            'calendarData', 
            'startDate', 
            'endDate',
            'currentDate'
        ));
    }

    public function updateSchedule(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:att_attschedule,id',
            'new_date' => 'required|date',
            'employee_id' => 'required|exists:personnel_employee,id'
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);
        $newDate = Carbon::parse($request->new_date);
        $employee = Employee::findOrFail($request->employee_id);
        
        // Calculate the duration of the original schedule (inclusive days)
        $originalStartDate = Carbon::parse($schedule->start_date);
        $originalEndDate = Carbon::parse($schedule->end_date);
        
        // For single-day schedules (start_date = end_date), duration is 0 days difference
        // For multi-day schedules, we need the actual number of days between start and end
        $originalDuration = $originalStartDate->diffInDays($originalEndDate);
        
        // Update the schedule maintaining the same duration
        // If it was a single day schedule (duration = 0), keep it single day
        // If it was multi-day, maintain the same duration
        $endDate = $originalDuration === 0 ? $newDate->copy() : $newDate->copy()->addDays($originalDuration);
        
        // Debug logging to help identify drag-drop issues
        \Log::info('Schedule Update:', [
            'original_start' => $schedule->start_date,
            'original_end' => $schedule->end_date,
            'original_duration' => $originalDuration,
            'new_date' => $newDate->format('Y-m-d'),
            'calculated_end_date' => $endDate->format('Y-m-d'),
            'request_data' => $request->all()
        ]);
        
        $schedule->update([
            'employee_id' => $request->employee_id,
            'start_date' => $newDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'slug' => Str::slug($employee->first_name . '-' . $employee->last_name . '-' . $schedule->shift->alias . '-' . $newDate->format('Y-m-d'))
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully',
            'schedule' => $schedule->load(['employee', 'shift.timeIntervals']),
            'debug' => [
                'original_duration' => $originalDuration,
                'new_start' => $newDate->format('Y-m-d'),
                'new_end' => $endDate->format('Y-m-d')
            ]
        ]);
    }

    public function createSchedule(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:personnel_employee,id',
            'shift_id' => 'required|exists:att_attshift,id',
            'date' => 'required|date',
            'duration' => 'integer|min:1|max:365'
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $shift = Shift::findOrFail($request->shift_id);
        $startDate = Carbon::parse($request->date);
        
        // Calculate end date correctly (duration - 1 days for single day schedules)
        $duration = $request->duration ?? 1;
        $endDate = $duration === 1 ? $startDate->copy() : $startDate->copy()->addDays($duration - 1);

        $slug = Str::slug($employee->first_name . '-' . $employee->last_name . '-' . $shift->alias . '-' . $startDate->format('Y-m-d'));

        $schedule = Schedule::create([
            'slug' => $slug,
            'employee_id' => $request->employee_id,
            'shift_id' => $request->shift_id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule created successfully',
            'schedule' => $schedule->load(['employee', 'shift.timeIntervals'])
        ]);
    }

    public function deleteSchedule(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:att_attschedule,id'
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    }

    public function getWeekData(Request $request)
    {
        $currentDate = $request->get('date', now()->format('Y-m-d'));
        $startDate = Carbon::parse($currentDate)->startOfWeek();
        $endDate = Carbon::parse($currentDate)->endOfWeek();

        $employees = Employee::orderBy('first_name')->get();
        
        $schedules = Schedule::with(['employee', 'shift.timeIntervals'])
            ->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orWhere(function($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate->format('Y-m-d'))
                      ->where('end_date', '>=', $endDate->format('Y-m-d'));
            })
            ->get();

        $calendarData = $this->buildCalendarData($employees, $schedules, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'calendarData' => $calendarData,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d')
        ]);
    }

    private function buildCalendarData($employees, $schedules, $startDate, $endDate)
    {
        $calendarData = [];
        $weekDays = [];
        
        // Build week days array
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $weekDays[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'dayNumber' => $date->format('j'),
                'isToday' => $date->isToday(),
                'isWeekend' => $date->isWeekend()
            ];
        }

        // Build employee schedule data
        foreach ($employees as $employee) {
            $employeeSchedules = [];
            
            foreach ($weekDays as $day) {
                $daySchedules = $schedules->filter(function($schedule) use ($employee, $day) {
                    return $schedule->employee_id == $employee->id &&
                           $schedule->start_date <= $day['date'] &&
                           $schedule->end_date >= $day['date'];
                });
                
                $employeeSchedules[$day['date']] = $daySchedules->map(function($schedule) {
                    $timeIntervals = $schedule->shift && $schedule->shift->timeIntervals ? 
                        $schedule->shift->timeIntervals->map(function($interval) {
                            return [
                                'alias' => $interval->alias,
                                'in_time' => $interval->formatted_in_time ?? $interval->in_time,
                                'duration' => $interval->duration_in_hours ?? round($interval->duration / 60, 2)
                            ];
                        })->toArray() : [];
                        
                    return [
                        'id' => $schedule->id,
                        'slug' => $schedule->slug,
                        'shift' => [
                            'id' => $schedule->shift->id,
                            'alias' => $schedule->shift->alias,
                            'color' => $this->getShiftColor($schedule->shift->id),
                            'time_intervals' => $timeIntervals
                        ],
                        'start_date' => $schedule->start_date,
                        'end_date' => $schedule->end_date
                    ];
                })->values()->toArray();
            }
            
            $calendarData[] = [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'emp_code' => $employee->emp_code ?? 'N/A'
                ],
                'schedules' => $employeeSchedules
            ];
        }

        return [
            'weekDays' => $weekDays,
            'employees' => $calendarData
        ];
    }

    private function getShiftColor($shiftId)
    {
        $colors = [
            '#3498db', '#e74c3c', '#2ecc71', '#f39c12', 
            '#9b59b6', '#1abc9c', '#34495e', '#e67e22'
        ];
        return $colors[$shiftId % count($colors)];
    }
}