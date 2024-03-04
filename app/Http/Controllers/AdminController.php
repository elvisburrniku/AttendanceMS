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
        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Employee'));

        $employee = $api->get();

        $at_api = new ApiHelper();
        $at_api->url(ApiUrlHelper::url('Attendance'))->get();

        //Dashboard statistics 
        $totalEmp =  $employee->get('count');
        $AllAttendance = $at_api->getData()->filter(function ($item) use ($today) {
            $punchTime = Carbon::parse($item['punch_time'])->toDateString();
            return $punchTime == $today;
        })->count();
        $ontimeEmp = $at_api->getData()->filter(function ($item) use ($today) {
            $punchTime = Carbon::parse($item['punch_time'])->toDateString();
            return $punchTime == $today;
        })->where('punch_state', 1)->count();
        $latetimeEmp = $at_api->getData()->filter(function ($item) use ($today) {
            $punchTime = Carbon::parse($item['punch_time'])->toDateString();
            return $punchTime == $today;
        })->where('punch_state', 0)->count();
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
