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
        $employees = Employee::with('areas', 'department', 'position')->simplePaginate(100);
        $employee_count = ((int) Employee::latest('create_time')->first()->emp_code ?? 0);

        $departments = Department::all();

        $positions = Position::all();

        $areas = Area::all();

        return view('admin.employee')->with(['employees'=> $employees, 'employee_count' => $employee_count, 'departments' => $departments, 'positions' => $positions, 'areas' => $areas]);
    }

    public function store(EmployeeRec $request)
    {
        // $validatedData = $request->validate([
        //     'emp_code' => 'required|string',
        //     'first_name' => 'required|string|max:255',
        //     'last_name' => 'required|string|max:255',
        //     'nickname' => 'nullable|string|max:255',
        //     'card_no' => 'required|string|max:255',
        //     'department' => 'required|integer',
        //     'position' => 'required|integer',
        //     'hire_date' => 'required|date',
        //     'gender' => 'required|string|max:1',
        //     'birthday' => 'required|date',
        //     'emp_type' => 'required|integer',
        //     'contact_tel' => 'nullable|string|max:255',
        //     'office_tel' => 'nullable|string|max:255',
        //     'mobile' => 'nullable|string|max:255',
        //     'national' => 'nullable|string|max:255',
        //     'city' => 'nullable|string|max:255',
        //     'address' => 'nullable|string|max:255',
        //     'postcode' => 'nullable|string|max:255',
        //     'email' => 'required|email|max:255',
        //     'dev_privilege' => 'required|boolean',
        //     'area' => 'required|array',
        //     'area.*' => 'integer',
        //     'app_status' => 'required|boolean',
        //     'app_role' => 'required|integer',
        // ]);

        // $employee = Employee::create([
        //     'emp_code' => $validatedData['emp_code'],
        //     'emp_code_digit' => $validatedData['emp_code'],
        //     'first_name' => $validatedData['first_name'],
        //     'last_name' => $validatedData['last_name'],
        //     'nickname' => $validatedData['nickname'],
        //     'card_no' => $validatedData['card_no'],
        //     'department_id' => $validatedData['department'],
        //     'position_id' => $validatedData['position'],
        //     'hire_date' => $validatedData['hire_date'],
        //     'gender' => $validatedData['gender'],
        //     'birthday' => $validatedData['birthday'],
        //     'emp_type' => $validatedData['emp_type'],
        //     'contact_tel' => $validatedData['contact_tel'],
        //     'office_tel' => $validatedData['office_tel'],
        //     'mobile' => $validatedData['mobile'],
        //     'national' => $validatedData['national'],
        //     'city' => $validatedData['city'],
        //     'address' => $validatedData['address'],
        //     'postcode' => $validatedData['postcode'],
        //     'email' => $validatedData['email'],
        //     'dev_privilege' => $validatedData['dev_privilege'],
        //     'app_status' => $validatedData['app_status'],
        //     'app_role' => $validatedData['app_role'],
        //     'status' => 0,
        //     'company_id' => 1,
        //     'enable_payroll' => 1,
        //     'is_active' => 1,
        //     'verify_mode' => -1,
        //     'create_time' => now(),
        //     'change_time' => now(),
        //     'update_time' => now(),
        // ]);

        // $employee->areas()->attach($validatedData['area']);

        // $employee->attAttemployee()->create([
        //     'create_time' => now(),
        //     'change_time' => now(),
        //     'status' => 0,
        //     'enable_attendance' => 1,
        //     'enable_schedule' => 1,
        //     'enable_overtime' => 1,
        //     'enable_holiday' => 1,
        //     'enable_compensatory' => 0,
        // ]);

        // $employee->employeeProfile()->create([
        //     'id' => $employee->id,
        //     'column_order' => "",
        //     "preferences" => "",
        //     "pwd_update_time" => null,
        //     "disabled_fields" => ""
        // ]);

        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Employee'));

        $employee = $api->post($request->all());

        flash()->success('Success','Employee Record has been created successfully !');

        $user = User::updateOrCreate(['email' => $request['nickname']],[
            'name' => $request['first_name']. ' ' . $request['last_name'],
            'password' => \Hash::make($request['nickname']),
        ]);

        $role = Role::firstOrCreate([
            'slug' => 'employee',
            'name' => 'Employee',
        ]);

        $user->roles()->sync($role->id);

        return redirect()->route('employees.index')->with('success');
    }

 
    public function update(EmployeeRec $request, $id)
    {
        // $employee = Employee::find($id);

        // $validatedData = $request->validate([
        //     'emp_code' => 'required|string',
        //     'first_name' => 'required|string|max:255',
        //     'last_name' => 'required|string|max:255',
        //     'nickname' => 'nullable|string|max:255',
        //     'card_no' => 'required|string|max:255',
        //     'department' => 'required|integer',
        //     'position' => 'required|integer',
        //     'hire_date' => 'required|date',
        //     'gender' => 'required|string|max:1',
        //     'birthday' => 'required|date',
        //     'emp_type' => 'required|integer',
        //     'contact_tel' => 'nullable|string|max:255',
        //     'office_tel' => 'nullable|string|max:255',
        //     'mobile' => 'nullable|string|max:255',
        //     'national' => 'nullable|string|max:255',
        //     'city' => 'nullable|string|max:255',
        //     'address' => 'nullable|string|max:255',
        //     'postcode' => 'nullable|string|max:255',
        //     'email' => 'nullable|email|max:255',
        //     'dev_privilege' => 'nullable|boolean',
        //     'area' => 'required|array',
        //     'area.*' => 'integer',
        //     'app_status' => 'required|boolean',
        //     'app_role' => 'required|integer',
        // ]);

        // $employee->update([
        //     'emp_code' => $validatedData['emp_code'],
        //     'emp_code_digit' => $validatedData['emp_code'],
        //     'first_name' => $validatedData['first_name'],
        //     'last_name' => $validatedData['last_name'],
        //     'nickname' => $validatedData['nickname'],
        //     'card_no' => $validatedData['card_no'],
        //     'department_id' => $validatedData['department'],
        //     'position_id' => $validatedData['position'],
        //     'hire_date' => $validatedData['hire_date'],
        //     'gender' => $validatedData['gender'],
        //     'birthday' => $validatedData['birthday'],
        //     'emp_type' => $validatedData['emp_type'],
        //     'contact_tel' => $validatedData['contact_tel'],
        //     'office_tel' => $validatedData['office_tel'],
        //     'mobile' => $validatedData['mobile'],
        //     'national' => $validatedData['national'],
        //     'city' => $validatedData['city'],
        //     'address' => $validatedData['address'],
        //     'postcode' => $validatedData['postcode'],
        //     'email' => $validatedData['email'],
        //     'dev_privilege' => $validatedData['dev_privilege'],
        //     'app_status' => $validatedData['app_status'],
        //     'app_role' => $validatedData['app_role'],
        //     'verify_mode' => -1,
        //     'status' => 0,
        //     'company_id' => 1,
        //     'enable_payroll' => 1,
        //     'is_active' => 1,
        //     'update_time' => now(),
        // ]);

        // $employee->areas()->sync($validatedData['area']);
        // $employee = Employee::find($id);
       
        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Employee.Update'));

        $employee = $api->put($id, $request->all());

        flash()->success('Success','Employee Record has been Updated successfully !');

        return redirect()->route('employees.index')->with('success');
    }


    public function destroy($id)
    {
        $employee = Employee::find($id);

        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Employee.Update'));

        $api->delete($id);

        flash()->success('Success','Employee Record has been Deleted successfully !');

        User::where(['email' => $employee['nickname']])->delete();

        return redirect()->route('employees.index')->with('success');
    }
}
