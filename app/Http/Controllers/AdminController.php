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

class AdminController extends Controller
{

 
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        $attendances = Attendance::whereDate('punch_time', $today)->get();
        //Dashboard statistics 
        $totalEmp =  Employee::count();
        $AllAttendance = $attendances->unique('punch_state')->count();
        $ontimeEmp = $attendances->where('punch_state', 1)->unique('punch_state')->count();
        $latetimeEmp = $attendances->where('punch_state', 0)->unique('punch_state')->count();
        $totalSchedule =  count(Schedule::all());
            
        if($AllAttendance > 0){
                $percentageOntime = str_split(($ontimeEmp/ $AllAttendance)*100, 4)[0];
            }else {
                $percentageOntime = 0 ;
            }
        
        $data = [$totalEmp, $ontimeEmp, $latetimeEmp, $percentageOntime, $totalSchedule];
        
        return view('admin.index')->with(['data' => $data]);
    }

}
