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

class AdminController extends Controller
{

 
    public function index()
    {
        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Employee'));

        $employee = $api->get();

        //Dashboard statistics 
        $totalEmp =  $employee->get('count');
        $AllAttendance = $api->getData()->where('attendance_date', now())->count();
        $ontimeEmp = $api->getData()->where('attendance_date', now())->where('status', 1)->count();
        $latetimeEmp = $api->getData()->where('attendance_date', now())->where('status', 0)->count();
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
