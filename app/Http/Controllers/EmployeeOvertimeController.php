<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeOvertime;

class EmployeeOvertimeController extends Controller
{
    public function index()
    {
        return view('admin.overtime')->with(['employees' => Employee::with([ 'overtimes'=> function($query) {
            $query->whereMonth('date', request()->date ?? now());
        }, 'attendances'=> function($query) {
            $query->whereMonth('punch_time', now());
        }])->get()]);
    }

    public function store(Request $request)
    {
        if (isset($request->attd)) {
            $dates_to_be_deleted = [];
            foreach ($request->attd as $date => $values) {
                foreach ($values as $employee_id => $value) {
                    if(array_key_exists('approved', $value)) {
                        EmployeeOvertime::updateOrCreate(['employee_id' => $employee_id, 'date' => $date], ['total_hr' => $value['total_hr'], 'approved' => true]);
                    } else {
                        $dates_to_be_deleted[$employee_id][] = $date;
                    }
                }
            }
            foreach($dates_to_be_deleted as $employee_id => $dates) {
                EmployeeOvertime::where(['employee_id' => $employee_id])->whereIn('date', $dates)->delete();
            }
        }
        flash()->success('Success', 'You have successfully submited the overtime !');
        return back();
    }
}
