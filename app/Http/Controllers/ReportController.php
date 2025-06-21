
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\TimeInterval;
use App\Models\Holiday;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function attendance(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $employeeId = $request->input('employee_id');

        $query = Attendance::with('employee')
            ->whereBetween('punch_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($employeeId) {
            $query->where('emp_id', $employeeId);
        }

        $attendances = $query->orderBy('punch_time', 'desc')->paginate(50);
        $employees = Employee::all();

        return view('admin.reports.attendance', compact('attendances', 'employees', 'startDate', 'endDate', 'employeeId'));
    }

    public function exportAttendance(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        return (new \App\Exports\AttendanceExport($startDate, $endDate))->download('attendance-report-' . $startDate . '-to-' . $endDate . '.xlsx');
    }

    public function monthly(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        $employees = Employee::with(['attendances' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('punch_time', [$startDate, $endDate]);
        }])->get();

        $monthlyStats = [];
        foreach ($employees as $employee) {
            $attendances = $employee->attendances->groupBy(function($attendance) {
                return Carbon::parse($attendance->punch_time)->format('Y-m-d');
            });

            $stats = [
                'employee' => $employee,
                'total_days' => $attendances->count(),
                'present_days' => $attendances->filter(function($dayAttendances) {
                    return $dayAttendances->where('punch_state', '0')->isNotEmpty();
                })->count(),
                'late_days' => $attendances->filter(function($dayAttendances) {
                    $checkin = $dayAttendances->where('punch_state', '0')->first();
                    return $checkin && Carbon::parse($checkin->punch_time)->format('H:i') > '09:00';
                })->count(),
                'total_hours' => $this->calculateTotalHours($attendances)
            ];

            $monthlyStats[] = $stats;
        }

        return view('admin.reports.monthly', compact('monthlyStats', 'month'));
    }

    public function daily(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        
        $attendances = Attendance::with('employee')
            ->whereDate('punch_time', $date)
            ->orderBy('punch_time')
            ->get()
            ->groupBy('emp_id');

        $dailyReport = [];
        foreach ($attendances as $empId => $empAttendances) {
            $employee = $empAttendances->first()->employee;
            $checkin = $empAttendances->where('punch_state', '0')->first();
            $checkout = $empAttendances->where('punch_state', '1')->first();
            
            $dailyReport[] = [
                'employee' => $employee,
                'checkin' => $checkin,
                'checkout' => $checkout,
                'hours_worked' => $this->calculateWorkedHours($checkin, $checkout),
                'status' => $this->getAttendanceStatus($checkin, $checkout)
            ];
        }

        return view('admin.reports.daily', compact('dailyReport', 'date'));
    }

    public function employeeReport(Employee $employee, Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $attendances = Attendance::where('emp_id', $employee->id)
            ->whereBetween('punch_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('punch_time')
            ->get()
            ->groupBy(function($attendance) {
                return Carbon::parse($attendance->punch_time)->format('Y-m-d');
            });

        $report = [];
        foreach ($attendances as $date => $dayAttendances) {
            $checkin = $dayAttendances->where('punch_state', '0')->first();
            $checkout = $dayAttendances->where('punch_state', '1')->first();
            
            $report[] = [
                'date' => $date,
                'checkin' => $checkin,
                'checkout' => $checkout,
                'hours_worked' => $this->calculateWorkedHours($checkin, $checkout),
                'status' => $this->getAttendanceStatus($checkin, $checkout)
            ];
        }

        return view('admin.reports.employee', compact('employee', 'report', 'startDate', 'endDate'));
    }

    public function summary(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        $totalEmployees = Employee::count();
        $totalAttendances = Attendance::whereBetween('punch_time', [$startDate, $endDate])
            ->where('punch_state', '0')
            ->distinct('emp_id', DB::raw('DATE(punch_time)'))
            ->count();

        $lateAttendances = Attendance::whereBetween('punch_time', [$startDate, $endDate])
            ->where('punch_state', '0')
            ->whereTime('punch_time', '>', '09:00')
            ->count();

        $avgWorkingHours = $this->calculateAverageWorkingHours($startDate, $endDate);

        $summary = [
            'total_employees' => $totalEmployees,
            'total_attendances' => $totalAttendances,
            'late_attendances' => $lateAttendances,
            'punctuality_rate' => $totalAttendances > 0 ? round((($totalAttendances - $lateAttendances) / $totalAttendances) * 100, 2) : 0,
            'avg_working_hours' => $avgWorkingHours
        ];

        return view('admin.reports.summary', compact('summary', 'month'));
    }

    public function customReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_ids' => 'array',
            'report_type' => 'required|in:attendance,summary,detailed'
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $employeeIds = $request->employee_ids;
        $reportType = $request->report_type;

        $query = Attendance::with('employee')
            ->whereBetween('punch_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($employeeIds) {
            $query->whereIn('emp_id', $employeeIds);
        }

        $data = $query->get();
        
        return view('admin.reports.custom', compact('data', 'startDate', 'endDate', 'reportType'));
    }

    private function calculateTotalHours($attendances)
    {
        $totalSeconds = 0;
        
        foreach ($attendances as $dayAttendances) {
            $checkin = $dayAttendances->where('punch_state', '0')->first();
            $checkout = $dayAttendances->where('punch_state', '1')->first();
            
            if ($checkin && $checkout) {
                $totalSeconds += Carbon::parse($checkout->punch_time)->diffInSeconds(Carbon::parse($checkin->punch_time));
            }
        }
        
        return round($totalSeconds / 3600, 2);
    }

    private function calculateWorkedHours($checkin, $checkout)
    {
        if (!$checkin || !$checkout) {
            return 0;
        }
        
        return Carbon::parse($checkout->punch_time)->diffInHours(Carbon::parse($checkin->punch_time));
    }

    private function getAttendanceStatus($checkin, $checkout)
    {
        if (!$checkin) {
            return 'Absent';
        }
        
        if (!$checkout) {
            return 'Not Checked Out';
        }
        
        if (Carbon::parse($checkin->punch_time)->format('H:i') > '09:00') {
            return 'Late';
        }
        
        return 'Present';
    }

    private function calculateAverageWorkingHours($startDate, $endDate)
    {
        $attendances = Attendance::whereBetween('punch_time', [$startDate, $endDate])
            ->get()
            ->groupBy('emp_id')
            ->map(function($empAttendances) {
                return $empAttendances->groupBy(function($attendance) {
                    return Carbon::parse($attendance->punch_time)->format('Y-m-d');
                });
            });

        $totalHours = 0;
        $workingDays = 0;

        foreach ($attendances as $empDays) {
            foreach ($empDays as $dayAttendances) {
                $checkin = $dayAttendances->where('punch_state', '0')->first();
                $checkout = $dayAttendances->where('punch_state', '1')->first();
                
                if ($checkin && $checkout) {
                    $totalHours += Carbon::parse($checkout->punch_time)->diffInHours(Carbon::parse($checkin->punch_time));
                    $workingDays++;
                }
            }
        }

        return $workingDays > 0 ? round($totalHours / $workingDays, 2) : 0;
    }
}
