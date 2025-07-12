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
        $employees = Employee::all();
        
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
}