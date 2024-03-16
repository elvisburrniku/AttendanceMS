<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Schedule;
use App\Http\Requests\DepartmentRec;
use RealRashid\SweetAlert\Facades\Alert;
use App\Helpers\ApiHelper;
use App\Helpers\ApiUrlHelper;

class DepartmentController extends Controller
{
   
    public function index()
    {
        $dep_api = new ApiHelper();

        $dep_api->url(ApiUrlHelper::url('Department'))->get();
        
        $departments = $dep_api->getData()->map(function($e) {
            return (object) $e;
        });

        return view('admin.department')->with(['departments' => $departments, 'departments_count' => $dep_api->response->get('count')]);
    }

    public function store(DepartmentRec $request)
    {
        $request->validated();

        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Department'));

        $employee = $api->post($request->all());

        // $employee = new Employee;
        // $employee->name = $request->name;
        // $employee->position = $request->position;
        // $employee->email = $request->email;
        // $employee->pin_code = bcrypt($request->pin_code);
        // $employee->save();

        // if($request->schedule){

        //     $schedule = Schedule::whereSlug($request->schedule)->first();

        //     $employee->schedules()->attach($schedule);
        // }

        // $role = Role::whereSlug('emp')->first();

        // $employee->roles()->attach($role);

        flash()->success('Success','Department Record has been created successfully !');

        return redirect()->route('departments.index')->with('success');
    }

 
    public function update(DepartmentRec $request, $id)
    {
        $request->validated();

        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Department.Update'));

        $employee = $api->put($id, $request->all());

        flash()->success('Success','Department Record has been Updated successfully !');

        return redirect()->route('departments.index')->with('success');
    }


    public function destroy($id)
    {
        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Department.Update'));

        $api->delete($id);

        flash()->success('Success','Department Record has been Deleted successfully !');
        return redirect()->route('departments.index')->with('success');
    }
}
