<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Latetime;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Support\Facades\Http;
use App\Helpers\ApiHelper;
use App\Helpers\ApiUrlHelper;
use Carbon\Carbon;
use DateTime;

class AdminController extends Controller
{

 
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        $attendances = Attendance::whereDate('punch_time', $today)->get();
        //Dashboard statistics 
        $totalEmp =  Employee::count();
        $AllAttendance = $attendances->count();
        $employees = Employee::with('schedules.shift.timetables.timeInterval')->with(['attendances' => function($query) use ($today) {
            $query->whereDate('punch_time', $today);
        }])->get();
        $ontimeEmp = $employees->where('attendances.punch_state', 0)->filter(function($employee) {
            $schedule = $employee->schedules->first();
            $attendance = $employee->attendances->where('punch_state', 0)->sortBy('punch_time')->first();
            if(!$attendance) {
                return false;
            }
            $dayIndex = now()->dayOfWeek;
            if($schedule) {
                $timetable = optional($schedule)->shift->timetables->where('day_index', $dayIndex)->first();
                $timetableInDateTime = Carbon::parse(now()->format('Y-m-d'). ' ' .optional($timetable)->in_time);
                $newEmployeeCheckinDateTime = Carbon::parse($attendance->punch_time);
                if($timetableInDateTime < $newEmployeeCheckinDateTime) {
                    return false;
                } else{
                    return true;
                }
            }
            return false;
        })->count();
        $latetimeEmp = $employees->where('attendances.punch_state', 0)->filter(function($employee) {
            $schedule = $employee->schedules->first();
            $attendance = $employee->attendances->where('punch_state', 0)->sortBy('punch_time')->first();
            if(!$attendance) {
                return false;
            }
            $dayIndex = now()->dayOfWeek;
            if($schedule) {
                $timetable = $schedule->shift->timetables->where('day_index', $dayIndex)->first();
                $timetableInDateTime = Carbon::parse(now()->format('Y-m-d'). ' ' .optional($timetable)->in_time);
                $newEmployeeCheckinDateTime = Carbon::parse($attendance->punch_time);
                if($timetableInDateTime < $newEmployeeCheckinDateTime) {
                    return true;
                } else{
                    return false;
                }
            }
            return false;
        })->count();
        $absentToday = $employees->where('attendances.*.punch_state', null)->count();
        $totalSchedule =  count(Schedule::all());
            
        if($totalEmp > 0){
                $percentageOntime = str_split(($ontimeEmp/ $totalEmp)*100, 4)[0];
            }else {
                $percentageOntime = 0 ;
            }
        
        $data = [$totalEmp, $ontimeEmp, $latetimeEmp, $percentageOntime, $totalSchedule, $absentToday];
        
        return view('admin.index')->with(['data' => $data]);
    }

}
