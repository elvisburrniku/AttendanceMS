<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\FingerDevices;
use App\Helpers\FingerHelper;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Http\Requests\AttendanceEmp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('leaveType')->get();

        $leaveTypes = LeaveType::all();

        if (request()->ajax()) {
            return $leaves;
        }

        return view('admin.leave', [
            'leaves' => $leaves,
            'employees' => Employee::all(),
            'leaveTypes' => $leaveTypes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'leave_type_id'=>'required',
        ]);

        $leave = Leave::create([
            'emp_id' => $request->input('emp_id'),
            'type' => $request->input('type'),
            'comment' => $request->input('comment'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'leave_type_id'=>$request->input('leave_type_id'),
        ]);

        if ($request->ajax()) {
            return $leave->load('leaveType','user');
        }

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param SickLeave $sickLeave
     * @return SickLeaves|\Illuminate\Http\Response
     */
    public function update(Request $request, Leave $leave)
    {
        $request->validate([
            'type' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'leave_type_id'=>'required',
        ]);

        $leave->update($request->all());

        if ($request->ajax()) {
            return $leave;
        }    

        return redirect('admin/sickLeave');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SickLeaves $sickLeave
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Leave $leave)
    {
        $leave->delete();

        if (request()->ajax()) {
            return response(['Sick leave was deleted successfully.']);
        }

        return back();
    }

    public function indexOvertime()
    {
        return view('admin.overtime')->with(['overtimes' => Overtime::all()]);
    }


    // public static function overTime(Employee $employee)
    // {
    //     $current_t = new DateTime(date('H:i:s'));
    //     $start_t = new DateTime($employee->schedules->first()->time_out);
    //     $difference = $start_t->diff($current_t)->format('%H:%I:%S');

    //     $overtime = new Overtime();
    //     $overtime->emp_id = $employee->id;
    //     $overtime->duration = $difference;
    //     $overtime->overtime_date = date('Y-m-d');
    //     $overtime->save();
    // }
    public static function overTimeDevice($att_dateTime, Employee $employee)
    {
        
            $attendance_time =new DateTime($att_dateTime);
            $checkout = new DateTime($employee->schedules->first()->time_out);
            $difference = $checkout->diff($attendance_time)->format('%H:%I:%S');

            $overtime = new Overtime();
            $overtime->emp_id = $employee->id;
            $overtime->duration = $difference;
            $overtime->overtime_date = date('Y-m-d', strtotime($att_dateTime));
            $overtime->save();
        
    }
}
