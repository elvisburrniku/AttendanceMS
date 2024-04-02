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
use RealRashid\SweetAlert\Facades\Alert;
use App\Helpers\ApiHelper;
use App\Helpers\ApiUrlHelper;

class EmployeeController extends Controller
{
   
    public function index()
    {
        $employees = Employee::with('areas', 'department')->simplePaginate(100);
        $employee_count = $employees->count();

        $departments = Department::all();

        $positions = Position::all();

        $areas = Area::all();

        return view('admin.employee')->with(['employees'=> $employees, 'employee_count' => $employee_count, 'departments' => $departments, 'positions' => $positions, 'areas' => $areas]);
    }

    public function store(EmployeeRec $request)
    {
        $request->validated();

        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Employee'));

        $employee = $api->post($request->all());

        flash()->success('Success','Employee Record has been created successfully !');

        return redirect()->route('employees.index')->with('success');
    }

 
    public function update(EmployeeRec $request, $id)
    {
        $request->validated();
       
        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Employee.Update'));

        $employee = $api->put($id, $request->all());

        flash()->success('Success','Employee Record has been Updated successfully !');

        return redirect()->route('employees.index')->with('success');
    }


    public function destroy($id)
    {
        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Employee.Update'));

        $api->delete($id);

        flash()->success('Success','Employee Record has been Deleted successfully !');
        return redirect()->route('employees.index')->with('success');
    }
}
