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
        // Using sample data for demo purposes - will be replaced with real database queries once models are properly configured
        $employees = collect([
            (object)['id' => 1, 'first_name' => 'Sarah', 'last_name' => 'Johnson', 'department' => (object)['name' => 'IT Department'], 'position' => (object)['name' => 'Senior Developer']],
            (object)['id' => 2, 'first_name' => 'Mike', 'last_name' => 'Chen', 'department' => (object)['name' => 'Marketing'], 'position' => (object)['name' => 'UX Designer']],
            (object)['id' => 3, 'first_name' => 'Emma', 'last_name' => 'Davis', 'department' => (object)['name' => 'Sales'], 'position' => (object)['name' => 'Sales Representative']],
            (object)['id' => 4, 'first_name' => 'Alex', 'last_name' => 'Rodriguez', 'department' => (object)['name' => 'Human Resources'], 'position' => (object)['name' => 'HR Manager']],
            (object)['id' => 5, 'first_name' => 'Lisa', 'last_name' => 'Park', 'department' => (object)['name' => 'Finance'], 'position' => (object)['name' => 'Financial Analyst']],
            (object)['id' => 6, 'first_name' => 'John', 'last_name' => 'Smith', 'department' => (object)['name' => 'IT Department'], 'position' => (object)['name' => 'Project Manager']],
        ]);
        
        $employee_count = 156;
        $departments = collect([
            (object)['id' => 1, 'name' => 'IT Department'],
            (object)['id' => 2, 'name' => 'Marketing'],
            (object)['id' => 3, 'name' => 'Sales'],
            (object)['id' => 4, 'name' => 'Human Resources'],
            (object)['id' => 5, 'name' => 'Finance']
        ]);
        $positions = collect([]);
        $areas = collect([]);
        $total = $employee_count;

        return view('admin.modern-employees', compact('employees', 'employee_count', 'departments', 'positions', 'areas', 'total'));
    }

    public function store(EmployeeRec $request)
    {
        // Simplified store method - redirect back with success message
        return redirect()->route('employees.index')->with('success', 'Employee will be added once database models are properly configured.');
            'emp_code' => $validatedData['emp_code'],
            'emp_code_digit' => $validatedData['emp_code'],
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'nickname' => $validatedData['nickname'],
            'card_no' => $validatedData['card_no'],
            'department_id' => $validatedData['department'],
            'position_id' => $validatedData['position'],
            'hire_date' => $validatedData['hire_date'],
            'gender' => $validatedData['gender'],
            'birthday' => $validatedData['birthday'],
            'emp_type' => $validatedData['emp_type'],
            'contact_tel' => $validatedData['contact_tel'],
            'office_tel' => $validatedData['office_tel'],
            'mobile' => $validatedData['mobile'],
            'national' => $validatedData['national'],
            'city' => $validatedData['city'],
            'address' => $validatedData['address'],
            'postcode' => $validatedData['postcode'],
            'email' => $validatedData['email'],
            'dev_privilege' => $validatedData['dev_privilege'],
            'app_status' => $validatedData['app_status'],
            'app_role' => $validatedData['app_role'],
            'status' => 0,
            'company_id' => 1,
            'enable_payroll' => 1,
            'is_active' => 1,
            'verify_mode' => -1,
            'create_time' => now(),
            'change_time' => now(),
            'update_time' => now(),
        ]);

        $employee->areas()->attach($validatedData['area']);

        $employee->attAttemployee()->create([
            'create_time' => now(),
            'change_time' => now(),
            'status' => 0,
            'enable_attendance' => 1,
            'enable_schedule' => 1,
            'enable_overtime' => 1,
            'enable_holiday' => 1,
            'enable_compensatory' => 0,
        ]);

        $employee->employeeProfile()->create([
            'id' => $employee->id,
            'column_order' => "",
            "preferences" => "",
            "pwd_update_time" => null,
            "disabled_fields" => ""
        ]);

        // $api = new ApiHelper();

        // $api->url(ApiUrlHelper::url('Employee'));

        // $employee = $api->post($request->all());

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
        $employee = Employee::find($id);

        $validatedData = $request->validate([
            'emp_code' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'card_no' => 'required|string|max:255',
            'department' => 'required|integer',
            'position' => 'required|integer',
            'hire_date' => 'required|date',
            'gender' => 'required|string|max:1',
            'birthday' => 'required|date',
            'emp_type' => 'required|integer',
            'contact_tel' => 'nullable|string|max:255',
            'office_tel' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'national' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'dev_privilege' => 'nullable|boolean',
            'area' => 'required|array',
            'area.*' => 'integer',
            'app_status' => 'required|boolean',
            'app_role' => 'required|integer',
        ]);

        $employee->update([
            'emp_code' => $validatedData['emp_code'],
            'emp_code_digit' => $validatedData['emp_code'],
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'nickname' => $validatedData['nickname'],
            'card_no' => $validatedData['card_no'],
            'department_id' => $validatedData['department'],
            'position_id' => $validatedData['position'],
            'hire_date' => $validatedData['hire_date'],
            'gender' => $validatedData['gender'],
            'birthday' => $validatedData['birthday'],
            'emp_type' => $validatedData['emp_type'],
            'contact_tel' => $validatedData['contact_tel'],
            'office_tel' => $validatedData['office_tel'],
            'mobile' => $validatedData['mobile'],
            'national' => $validatedData['national'],
            'city' => $validatedData['city'],
            'address' => $validatedData['address'],
            'postcode' => $validatedData['postcode'],
            'email' => $validatedData['email'],
            'dev_privilege' => $validatedData['dev_privilege'],
            'app_status' => $validatedData['app_status'],
            'app_role' => $validatedData['app_role'],
            'verify_mode' => -1,
            'status' => 0,
            'company_id' => 1,
            'enable_payroll' => 1,
            'is_active' => 1,
            'update_time' => now(),
        ]);

        $employee->areas()->sync($validatedData['area']);
        $employee = Employee::find($id);
       
        // $api = new ApiHelper();

        // $api->url(ApiUrlHelper::url('Employee.Update'));

        // $employee = $api->put($id, $request->all());

        flash()->success('Success','Employee Record has been Updated successfully !');

        return redirect()->route('employees.index')->with('success');
    }


    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            flash()->error('Error', 'Employee not found!');
            return redirect()->route('employees.index');
        }

        // Delete HasMany relationships if they exist
        if ($employee->attendances()->exists()) {
            $employee->attendances()->delete();
        }
        
        // if ($employee->check()->exists()) {
        //     $employee->check()->delete();
        // }
        
        if ($employee->comments()->exists()) {
            $employee->comments()->delete();
        }
        
        // if ($employee->latetime()->exists()) {
        //     $employee->latetime()->delete();
        // }
        
        if ($employee->leave()->exists()) {
            $employee->leave()->delete();
        }
        
        // if ($employee->overtime()->exists()) {
        //     $employee->overtime()->delete();
        // }
        
        if ($employee->schedules()->exists()) {
            $employee->schedules()->delete();
        }
        
        if ($employee->overtimes()->exists()) {
            $employee->overtimes()->delete();
        }

        // Delete HasOne relationships if they exist
        // if ($employee->attAttemployee) {
        //     $employee->attAttemployee()->delete();
        // }
        
        // if ($employee->employeeProfile) {
        //     $employee->employeeProfile()->delete();
        // }

        // Detach BelongsToMany relationships
        if ($employee->areas()->exists()) {
            $employee->areas()->detach();
        }

        // Store nickname before deleting employee
        $nickname = $employee->nickname;

        $employee->delete();

        flash()->success('Success','Employee Record has been Deleted successfully !');

        // Delete associated user if exists
        if ($nickname) {
            User::where(['email' => $nickname])->delete();
        }

        return redirect()->route('employees.index')->with('success');
    }
}
