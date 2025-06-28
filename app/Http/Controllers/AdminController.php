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
        
        // Get basic statistics with fallback values
        $totalEmployees = 156; // Sample data for demo
        $presentToday = 142;
        $lateToday = 8;
        $onLeave = 6;
        
        return view('admin.modern-dashboard', compact(
            'totalEmployees',
            'presentToday', 
            'lateToday',
            'onLeave'
        ));
    }

}