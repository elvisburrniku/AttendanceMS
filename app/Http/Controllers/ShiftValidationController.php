<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\TimeInterval;
use App\Services\ShiftValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftValidationController extends Controller
{
    protected $shiftValidationService;

    public function __construct(ShiftValidationService $shiftValidationService)
    {
        $this->shiftValidationService = $shiftValidationService;
    }

    /**
     * Display shift validation dashboard
     */
    public function index()
    {
        $employees = Employee::with('schedules.shift')->get();
        $shifts = Shift::with('timeIntervals')->get();
        $timeIntervals = TimeInterval::all();
        $activeSchedules = Schedule::with(['employee', 'shift'])
            ->where('end_date', '>=', now()->format('Y-m-d'))
            ->get();

        return view('admin.shift-validation.index', compact('employees', 'shifts', 'timeIntervals', 'activeSchedules'));
    }

    /**
     * Test shift validation for an employee
     */
    public function testValidation(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:personnel_employee,id',
            'test_time' => 'nullable|date_format:Y-m-d H:i'
        ]);

        $employeeId = $request->employee_id;
        $testTime = $request->test_time ? Carbon::parse($request->test_time) : null;

        $validation = $this->shiftValidationService->canEmployeeCheckIn($employeeId, $testTime);
        $report = $this->shiftValidationService->getEmployeeShiftReport($employeeId, $testTime ? $testTime->format('Y-m-d') : null);

        return response()->json([
            'validation' => $validation,
            'report' => $report
        ]);
    }

    /**
     * Get employee shift report
     */
    public function getEmployeeReport(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:personnel_employee,id',
            'date' => 'nullable|date'
        ]);

        $report = $this->shiftValidationService->getEmployeeShiftReport(
            $request->employee_id,
            $request->date
        );

        return response()->json($report);
    }

    /**
     * Create sample shift data for testing
     */
    public function createSampleData()
    {
        DB::transaction(function () {
            // Create sample time intervals
            $morningShift = TimeInterval::create([
                'alias' => 'Morning Shift',
                'use_mode' => 1,
                'in_time' => '09:00:00',
                'in_ahead_margin' => 30, // 30 minutes early
                'in_above_margin' => 15, // 15 minutes late
                'out_ahead_margin' => 0,
                'out_above_margin' => 0,
                'duration' => 480, // 8 hours
                'in_required' => true,
                'out_required' => true,
                'allow_late' => true,
                'allow_leave_early' => false,
                'work_day' => 1,
                'work_type' => 'normal',
                'company_id' => 1
            ]);

            $afternoonShift = TimeInterval::create([
                'alias' => 'Afternoon Shift',
                'use_mode' => 1,
                'in_time' => '14:00:00',
                'in_ahead_margin' => 20, // 20 minutes early
                'in_above_margin' => 10, // 10 minutes late
                'out_ahead_margin' => 0,
                'out_above_margin' => 0,
                'duration' => 480, // 8 hours
                'in_required' => true,
                'out_required' => true,
                'allow_late' => true,
                'allow_leave_early' => false,
                'work_day' => 1,
                'work_type' => 'normal',
                'company_id' => 1
            ]);

            $nightShift = TimeInterval::create([
                'alias' => 'Night Shift',
                'use_mode' => 1,
                'in_time' => '22:00:00',
                'in_ahead_margin' => 45, // 45 minutes early
                'in_above_margin' => 20, // 20 minutes late
                'out_ahead_margin' => 0,
                'out_above_margin' => 0,
                'duration' => 480, // 8 hours
                'in_required' => true,
                'out_required' => true,
                'allow_late' => true,
                'allow_leave_early' => false,
                'work_day' => 1,
                'work_type' => 'normal',
                'company_id' => 1
            ]);

            // Create sample shifts
            $dayShift = Shift::create([
                'alias' => 'Day Shift (9 AM - 5 PM)',
                'cycle_unit' => 'week',
                'shift_cycle' => 1,
                'work_weekend' => false,
                'work_day_off' => false,
                'auto_shift' => true,
                'enable_ot_rule' => false,
                'company_id' => 1
            ]);

            $eveningShift = Shift::create([
                'alias' => 'Evening Shift (2 PM - 10 PM)',
                'cycle_unit' => 'week',
                'shift_cycle' => 1,
                'work_weekend' => false,
                'work_day_off' => false,
                'auto_shift' => true,
                'enable_ot_rule' => false,
                'company_id' => 1
            ]);

            $graveShift = Shift::create([
                'alias' => 'Night Shift (10 PM - 6 AM)',
                'cycle_unit' => 'week',
                'shift_cycle' => 1,
                'work_weekend' => false,
                'work_day_off' => false,
                'auto_shift' => true,
                'enable_ot_rule' => false,
                'company_id' => 1
            ]);

            // Create shift details (assign time intervals to shifts for each day of week)
            $weekDays = [1, 2, 3, 4, 5]; // Monday to Friday

            foreach ($weekDays as $dayOfWeek) {
                // Day shift - Morning intervals
                DB::table('att_shiftdetail')->insert([
                    'shift_id' => $dayShift->id,
                    'time_interval_id' => $morningShift->id,
                    'work_type' => 'normal',
                    'day_of_week' => $dayOfWeek,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Evening shift - Afternoon intervals
                DB::table('att_shiftdetail')->insert([
                    'shift_id' => $eveningShift->id,
                    'time_interval_id' => $afternoonShift->id,
                    'work_type' => 'normal',
                    'day_of_week' => $dayOfWeek,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Night shift - Night intervals
                DB::table('att_shiftdetail')->insert([
                    'shift_id' => $graveShift->id,
                    'time_interval_id' => $nightShift->id,
                    'work_type' => 'normal',
                    'day_of_week' => $dayOfWeek,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Create sample schedules for employees
            $employees = Employee::take(5)->get();
            
            foreach ($employees as $index => $employee) {
                $shiftId = match($index % 3) {
                    0 => $dayShift->id,
                    1 => $eveningShift->id,
                    2 => $graveShift->id
                };

                Schedule::create([
                    'slug' => 'schedule-' . $employee->id . '-' . now()->format('Y-m-d'),
                    'start_date' => now()->format('Y-m-d'),
                    'end_date' => now()->addMonth()->format('Y-m-d'),
                    'employee_id' => $employee->id,
                    'shift_id' => $shiftId
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Sample shift data created successfully',
            'data' => [
                'time_intervals' => 3,
                'shifts' => 3,
                'schedules' => Employee::count(),
                'shift_details' => 15 // 3 shifts × 5 weekdays
            ]
        ]);
    }

    /**
     * Get shift validation statistics
     */
    public function getValidationStats()
    {
        $stats = [
            'total_employees' => Employee::count(),
            'employees_with_schedules' => Employee::whereHas('schedules', function($q) {
                $q->where('end_date', '>=', now()->format('Y-m-d'));
            })->count(),
            'total_shifts' => Shift::count(),
            'total_time_intervals' => TimeInterval::count(),
            'active_schedules' => Schedule::where('end_date', '>=', now()->format('Y-m-d'))->count()
        ];

        return response()->json($stats);
    }
}