<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['employee', 'shift'])
                           ->orderBy('start_date', 'desc')
                           ->paginate(20);

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $employees = Employee::orderBy('first_name')->get();
        $shifts = Shift::orderBy('alias')->get();

        return view('admin.schedules.create', compact('employees', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:att_attshift,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $employee = Employee::find($request->employee_id);
        $shift = Shift::find($request->shift_id);

        $slug = Str::slug($employee->first_name . '-' . $employee->last_name . '-' . $shift->alias . '-' . $request->start_date);

        Schedule::create([
            'slug' => $slug,
            'employee_id' => $request->employee_id,
            'shift_id' => $request->shift_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('schedules.index')
                        ->with('success', 'Schedule assigned successfully!');
    }

    public function show(Schedule $schedule)
    {
        $schedule->load(['employee', 'shift.timeIntervals']);

        return view('admin.schedules.show', compact('schedule'));
    }

    public function edit(Schedule $schedule)
    {
        $employees = Employee::orderBy('first_name')->get();
        $shifts = Shift::orderBy('alias')->get();

        return view('admin.schedules.edit', compact('schedule', 'employees', 'shifts'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:att_attshift,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $employee = Employee::find($request->employee_id);
        $shift = Shift::find($request->shift_id);

        $slug = Str::slug($employee->first_name . '-' . $employee->last_name . '-' . $shift->alias . '-' . $request->start_date);

        $schedule->update([
            'slug' => $slug,
            'employee_id' => $request->employee_id,
            'shift_id' => $request->shift_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('schedules.index')
                        ->with('success', 'Schedule updated successfully!');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('schedules.index')
                        ->with('success', 'Schedule deleted successfully!');
    }

    public function bulk()
    {
        $employees = Employee::orderBy('first_name')->get();
        dd($employees);
        $shifts = Shift::orderBy('alias')->get();

        return view('admin.schedules.bulk', compact('employees', 'shifts'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'required|integer|exists:personnel_employee,id',
            'shift_id' => 'required|exists:att_attshift,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $createdCount = 0;

        foreach ($request->employee_ids as $employeeId) {
            $slug = Str::slug($employeeId . '-' . $request->shift_id . '-' . $request->start_date);

            // Check if schedule already exists
            if (Schedule::where('slug', $slug)->exists()) {
                continue; // Skip if already exists
            }

            Schedule::create([
                'slug' => $slug,
                'employee_id' => $employeeId,
                'shift_id' => $request->shift_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            $createdCount++;
        }

        return redirect()->route('schedules.index')
                        ->with('success', "Successfully assigned schedules to {$createdCount} employees!");
    }

    public function employeeSchedules($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $schedules = Schedule::with('shift')
                           ->where('employee_id', $employeeId)
                           ->orderBy('start_date', 'desc')
                           ->paginate(10);

        return view('admin.schedules.employee', compact('employee', 'schedules'));
    }
}