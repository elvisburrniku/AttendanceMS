<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Holiday;
use App\Helpers\ApiHelper;
use App\Helpers\ApiUrlHelper;
use App\Exports\ExportCheckins;
use Excel;

class CheckController extends Controller
{
    public function index()
    {
        $today = \Carbon\Carbon::parse(request()->month ?? now()->format('Y-m').'-01');
        $holidays = Holiday::whereYear('date', $today)->get();
        return view('admin.check')->with(['today' => $today, 'employees' => Employee::with([ 'attendances'=> function($query) use ($today) {
            $query->whereMonth('punch_time', $today);
        }, 'overtimes'=> function($query) use ($today) {
            $query->whereMonth('date', $today);
        }, 'leave'=> function($query) use ($today) {
            $query->whereMonth('start_date', $today)->orWhereMonth('end_date', $today);
        }])->get(), 'holidays' => $holidays]);
    }

    public function CheckStore(Request $request)
    {
        if (isset($request->attd)) {
            foreach ($request->attd as $keys => $values) {
                foreach ($values as $key => $value) {
                    if ($employee = Employee::whereId(request('emp_id'))->first()) {
                        if (
                            !Attendance::whereAttendance_date($keys)
                                ->whereEmp_id($key)
                                ->whereType(0)
                                ->first()
                        ) {
                            $data = new Attendance();
                            
                            $data->emp_id = $key;
                            $emp_req = Employee::whereId($data->emp_id)->first();
                            $data->attendance_time = date('H:i:s', strtotime($emp_req->schedules->first()->time_in));
                            $data->attendance_date = $keys;
                            
                            $emps = date('H:i:s', strtotime($employee->schedules->first()->time_in));
                            if (!($emps > $data->attendance_time)) {
                                $data->status = 0;
                           
                            }
                            $data->save();
                        }
                    }
                }
            }
        }
        if (isset($request->leave)) {
            foreach ($request->leave as $keys => $values) {
                foreach ($values as $key => $value) {
                    if ($employee = Employee::whereId(request('emp_id'))->first()) {
                        if (
                            !Leave::whereLeave_date($keys)
                                ->whereEmp_id($key)
                                ->whereType(1)
                                ->first()
                        ) {
                            $data = new Leave();
                            $data->emp_id = $key;
                            $emp_req = Employee::whereId($data->emp_id)->first();
                            $data->leave_time = $emp_req->schedules->first()->time_out;
                            $data->leave_date = $keys;
                            if ($employee->schedules->first()->time_out <= $data->leave_time) {
                                $data->status = 1;
                                
                            }
                            
                            $data->save();
                        }
                    }
                }
            }
        }
        flash()->success('Success', 'You have successfully submited the attendance !');
        return back();
    }
    public function sheetReport()
    {
        $api = new ApiHelper();
        $api->url(ApiUrlHelper::url('Transaction.Report'))->get();
        
        $transactions = $api->getData()->map(function($e) {
            return (object) $e;
        });
        dd($transactions);
        return view('admin.sheet-report')->with(['transactions' => $transactions]);
    }

    public function export() 
    {
        $today = \Carbon\Carbon::parse(request()->month ?? now()->format('Y-m').'-01');
        return Excel::download(new \App\Exports\ExportCheckins($today), "timetable-". $today->format('d-m-Y') .".xlsx");
    }
}
