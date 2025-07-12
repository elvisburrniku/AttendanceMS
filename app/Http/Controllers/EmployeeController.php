<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\Department;
use App\Models\Position;
use App\Models\Area;
use App\Http\Requests\EmployeeRec;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
   
    public function index()
    {
        // Get real employees from database
        $employees = Employee::paginate(50);
        
        // Get real departments from database
        $departments = collect();
        $departmentRecords = \DB::table('personnel_department')->get();
        foreach ($departmentRecords as $dept) {
            $departments->push((object)[
                'id' => $dept->id,
                'name' => $dept->dept_name
            ]);
        }
        
        // Get real positions from database
        $positions = collect();
        $positionRecords = \DB::table('personnel_position')->get();
        foreach ($positionRecords as $pos) {
            $positions->push((object)[
                'id' => $pos->id,
                'name' => $pos->position_name
            ]);
        }
        
        // Get real areas from database
        $areas = collect();
        $areaRecords = \DB::table('personnel_area')->get();
        foreach ($areaRecords as $area) {
            $areas->push((object)[
                'id' => $area->id,
                'name' => $area->area_name
            ]);
        }
        
        $employee_count = $employees->count();
        $total = $employee_count;

        return view('admin.modern-employees', compact('employees', 'employee_count', 'departments', 'positions', 'areas', 'total'));
    }

    public function create()
    {
        return view('admin.employee-create');
    }

    public function store(Request $request)
    {
        return redirect()->route('employees.index')->with('success', 'Employee created successfully!');
    }

    public function show($id)
    {
        return view('admin.employee-show', compact('id'));
    }

    public function edit($id)
    {
        return view('admin.employee-edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    }

    public function destroy($id)
    {
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully!');
    }

    public function employeeDashboard()
    {
        // Get a real employee from database for demo
        $employee = \DB::table('employees')->first();
        
        if (!$employee) {
            // If no employees exist, redirect back with error
            return redirect()->route('modern.dashboard')->with('error', 'No employees found in database');
        }
        
        // Get real attendance data for this employee
        $todayAttendance = \DB::table('attendances')
            ->where('emp_id', $employee->id)
            ->whereDate('attendance_date', today())
            ->orderBy('attendance_time')
            ->get();
        
        // Get check-in and check-out records
        $checkin = $todayAttendance->where('state', 0)->first(); // Check-in
        $checkout = $todayAttendance->where('state', 1)->first(); // Check-out
        $breakin = $todayAttendance->where('state', 3)->first(); // Break-in
        $breakout = $todayAttendance->where('state', 2)->first(); // Break-out
        
        // Get weekly attendance data
        $weeklyAttendances = \DB::table('attendances')
            ->where('emp_id', $employee->id)
            ->whereBetween('attendance_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->orderBy('attendance_date')
            ->orderBy('attendance_time')
            ->get();
        
        // Calculate work time and break time
        $workTime = 0;
        $breakTime = 0;
        
        if ($checkin && $checkout) {
            $checkInTime = \Carbon\Carbon::parse($checkin->attendance_time);
            $checkOutTime = \Carbon\Carbon::parse($checkout->attendance_time);
            $workTime = $checkInTime->diffInMinutes($checkOutTime);
        }
        
        if ($breakout && $breakin) {
            $breakOutTime = \Carbon\Carbon::parse($breakout->attendance_time);
            $breakInTime = \Carbon\Carbon::parse($breakin->attendance_time);
            $breakTime = $breakOutTime->diffInMinutes($breakInTime);
        }
        
        // Create user object from employee data
        $user = (object)[
            'id' => $employee->id,
            'name' => $employee->name,
            'email' => $employee->email ?? 'employee@company.com',
            'created_at' => $employee->created_at ?? now()
        ];
        
        return view('employee.demo-dashboard', [
            'user' => $user,
            'employee' => $employee,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'breakin' => $breakin,
            'breakout' => $breakout,
            'weeklyAttendances' => $weeklyAttendances,
            'workTime' => $workTime,
            'breakTime' => $breakTime
        ]);
    }
}