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
        // Get dashboard statistics
        $totalEmployees = User::where('role', 'employee')->count();
        $today = Carbon::today();
        
        // Get today's attendance
        $presentToday = Attendance::whereDate('clock_in', $today)
            ->distinct('user_id')
            ->count();
            
        // Get late arrivals today
        $lateToday = Attendance::whereDate('clock_in', $today)
            ->whereTime('clock_in', '>', '09:00:00') // Assuming 9 AM is the standard start time
            ->count();
            
        // Get employees on leave today
        $onLeave = Leave::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where('status', 'approved')
            ->count();

        // Get department statistics
        $departments = Department::withCount('employees')->get();
        
        // Get recent activity (last 10 attendance records)
        $recentActivity = Attendance::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate attendance rate for current month
        $currentMonth = Carbon::now()->format('Y-m');
        $workingDaysThisMonth = $this->getWorkingDaysInMonth();
        $totalPossibleAttendance = $totalEmployees * $workingDaysThisMonth;
        $actualAttendance = Attendance::whereRaw("DATE_FORMAT(clock_in, '%Y-%m') = ?", [$currentMonth])
            ->distinct('user_id', DB::raw('DATE(clock_in)'))
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
        $recentActivity = Attendance::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'user_name' => $attendance->user->name ?? 'Unknown',
                    'user_department' => $attendance->user->department->name ?? 'No Department',
                    'action' => $this->getActionType($attendance),
                    'time' => $attendance->created_at->diffForHumans(),
                    'status' => $this->getAttendanceStatus($attendance),
                ];
            });

        return response()->json($recentActivity);
    }

    private function getActionType($attendance)
    {
        if ($attendance->clock_in && !$attendance->clock_out) {
            return 'check-in';
        } elseif ($attendance->clock_out) {
            return 'check-out';
        } elseif ($attendance->break_out && !$attendance->break_in) {
            return 'break-start';
        } elseif ($attendance->break_in) {
            return 'break-end';
        }
        
        return 'unknown';
    }

    private function getAttendanceStatus($attendance)
    {
        if (!$attendance->clock_in) {
            return 'absent';
        }

        $standardStartTime = Carbon::parse('09:00:00');
        $clockInTime = Carbon::parse($attendance->clock_in);

        if ($clockInTime->gt($standardStartTime)) {
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
            'total_employees' => User::where('role', 'employee')->count(),
            'present_today' => Attendance::whereDate('clock_in', $today)->distinct('user_id')->count(),
            'late_today' => Attendance::whereDate('clock_in', $today)
                ->whereTime('clock_in', '>', '09:00:00')
                ->count(),
            'on_leave' => Leave::where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->where('status', 'approved')
                ->count(),
            'weekly_attendance' => Attendance::where('clock_in', '>=', $thisWeek)->distinct('user_id')->count(),
            'monthly_attendance' => Attendance::where('clock_in', '>=', $thisMonth)->distinct('user_id')->count(),
        ];

        return response()->json($stats);
    }
}