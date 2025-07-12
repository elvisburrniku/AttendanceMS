<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ModernDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get dashboard statistics using real database tables
        $totalEmployees = DB::table('employees')->count();
        $today = Carbon::today();
        
        // Get today's attendance from real attendance table
        $presentToday = DB::table('attendances')
            ->whereDate('attendance_date', $today)
            ->distinct('emp_id')
            ->count();
            
        // Get late arrivals today (attendance after 9:00 AM)
        $lateToday = DB::table('attendances')
            ->whereDate('attendance_date', $today)
            ->where('attendance_time', '>', '09:00:00')
            ->where('state', 0) // Check-in state
            ->count();
            
        // Get employees on leave today (if leaves table exists)
        $onLeave = 0;
        if (DB::getSchemaBuilder()->hasTable('leaves')) {
            $onLeave = DB::table('leaves')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->where('status', 'approved')
                ->count();
        }

        // Get department statistics from real database
        $departments = DB::table('personnel_department')->get();
        
        // Get recent activity (last 10 attendance records)
        $recentActivity = DB::table('attendances')
            ->join('employees', 'attendances.emp_id', '=', 'employees.id')
            ->select('attendances.*', 'employees.name as employee_name', 'employees.position')
            ->orderBy('attendances.created_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate attendance rate for current month
        $currentMonth = Carbon::now()->format('Y-m');
        $workingDaysThisMonth = $this->getWorkingDaysInMonth();
        $totalPossibleAttendance = $totalEmployees * $workingDaysThisMonth;
        $actualAttendance = DB::table('attendances')
            ->whereRaw("strftime('%Y-%m', attendance_date) = ?", [$currentMonth])
            ->distinct('emp_id')
            ->count();
        
        $attendanceRate = $totalPossibleAttendance > 0 ? 
            round(($actualAttendance / $totalPossibleAttendance) * 100, 1) : 0;

        return view('admin.modern-dashboard', compact(
            'totalEmployees',
            'presentToday',
            'lateToday',
            'onLeave',
            'departments',
            'recentActivity',
            'attendanceRate'
        ));
    }

    public function showLogin()
    {
        return view('auth.modern-login');
    }

    private function getWorkingDaysInMonth()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $workingDays = 0;

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            // Count weekdays only (Monday to Friday)
            if ($date->isWeekday()) {
                $workingDays++;
            }
        }

        return $workingDays;
    }

    public function getActivityData()
    {
        $recentActivity = DB::table('attendances')
            ->join('employees', 'attendances.emp_id', '=', 'employees.id')
            ->select('attendances.*', 'employees.name as employee_name', 'employees.position')
            ->orderBy('attendances.created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'user_name' => $attendance->employee_name ?? 'Unknown',
                    'user_department' => $attendance->position ?? 'No Department',
                    'action' => $this->getActionType($attendance),
                    'time' => Carbon::parse($attendance->created_at)->diffForHumans(),
                    'status' => $this->getAttendanceStatus($attendance),
                ];
            });

        return response()->json($recentActivity);
    }

    private function getActionType($attendance)
    {
        switch ($attendance->state) {
            case 0:
                return 'check-in';
            case 1:
                return 'check-out';
            case 2:
                return 'break-start';
            case 3:
                return 'break-end';
            default:
                return 'unknown';
        }
    }

    private function getAttendanceStatus($attendance)
    {
        if (!$attendance->attendance_time) {
            return 'absent';
        }

        $standardStartTime = Carbon::parse('09:00:00');
        $clockInTime = Carbon::parse($attendance->attendance_time);

        if ($clockInTime->gt($standardStartTime) && $attendance->state == 0) {
            return 'late';
        }

        return 'on-time';
    }

    public function getDashboardStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'total_employees' => DB::table('employees')->count(),
            'present_today' => DB::table('attendances')
                ->whereDate('attendance_date', $today)
                ->distinct('emp_id')
                ->count(),
            'late_today' => DB::table('attendances')
                ->whereDate('attendance_date', $today)
                ->where('attendance_time', '>', '09:00:00')
                ->where('state', 0)
                ->count(),
            'on_leave' => DB::getSchemaBuilder()->hasTable('leaves') ? 
                DB::table('leaves')
                    ->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today)
                    ->where('status', 'approved')
                    ->count() : 0,
            'weekly_attendance' => DB::table('attendances')
                ->where('attendance_date', '>=', $thisWeek)
                ->distinct('emp_id')
                ->count(),
            'monthly_attendance' => DB::table('attendances')
                ->where('attendance_date', '>=', $thisMonth)
                ->distinct('emp_id')
                ->count(),
        ];

        return response()->json($stats);
    }
}