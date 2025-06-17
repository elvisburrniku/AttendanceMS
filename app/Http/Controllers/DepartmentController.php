<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Department;
use App\Http\Requests\DepartmentRec;
use RealRashid\SweetAlert\Facades\Alert;

class DepartmentController extends Controller
{

    public function index()
    {
        $departments = Department::with('parentDepartment')->simplePaginate(100);

        return view('admin.department')->with(['departments' => $departments, 'departments_count' => $departments->count()]);
    }

    public function store(DepartmentRec $request)
    {
        $request->validated();

        $department = new Department();
        $department->dept_code = $request->dept_code;
        $department->dept_name = $request->dept_name;
        $department->parent_dept_id = $request->parent_dept;
        $department->company_id = 1; // Default company ID
        $department->is_default = false;
        $department->save();

        flash()->success('Success','Department Record has been created successfully !');

        return redirect()->route('departments.index');
    }


    public function update(DepartmentRec $request, $id)
    {
        $request->validated();

        $department = Department::findOrFail($id);
        $department->dept_code = $request->dept_code;
        $department->dept_name = $request->dept_name;
        $department->parent_dept_id = $request->parent_dept;
        $department->save();

        flash()->success('Success','Department Record has been Updated successfully !');

        return redirect()->route('departments.index');
    }


    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        flash()->success('Success','Department Record has been Deleted successfully !');
        return redirect()->route('departments.index');
    }
}