<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Schedule;
use App\Http\Requests\EmployeeRec;
use RealRashid\SweetAlert\Facades\Alert;
use App\Helpers\ApiHelper;
use App\Helpers\ApiUrlHelper;

class EmployeeController extends Controller
{
   
    public function index()
    {
        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Employee'));

        $employee = $api->get();
        
        $employee_count = $employee['count'];

        $employees = $api->getData()->map(function($e) {
            return (object) $e;
        });

        $dep_api = new ApiHelper();

        $dep_api->url(ApiUrlHelper::url('Department'))->get();
        
        $departments = $dep_api->getData()->map(function($e) {
            return (object) $e;
        });

        $pos_api = new ApiHelper();

        $pos_api->url(ApiUrlHelper::url('Position'))->get();
        
        $positions = $pos_api->getData()->map(function($e) {
            return (object) $e;
        });

        $area_api = new ApiHelper();

        $area_api->url(ApiUrlHelper::url('Area'))->get();
        
        $areas = $area_api->getData()->map(function($e) {
            return (object) $e;
        });

        return view('admin.employee')->with(['employees'=> $employees, 'employee_count' => $employee_count, 'departments' => $departments, 'positions' => $positions, 'areas' => $areas, 'schedules'=>Schedule::all()]);
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
