<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\TimeInterval;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShiftValidationService
{
    /**
     * Validate if an employee can check in based on their shift and schedule
     */
    public function canEmployeeCheckIn($employeeId, $requestedTime = null)
    {
        $requestedTime = $requestedTime ? Carbon::parse($requestedTime) : Carbon::now();
        $today = $requestedTime->format('Y-m-d');
        $currentTime = $requestedTime->format('H:i:s');
        $dayOfWeek = $requestedTime->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.

        // Get employee's active schedule for today
        $activeSchedule = $this->getEmployeeActiveSchedule($employeeId, $today);
        
        if (!$activeSchedule) {
            return [
                'allowed' => false,
                'reason' => 'No active schedule found for today',
                'details' => 'Employee is not scheduled to work today'
            ];
        }

        // Get shift details with time intervals
        $shiftDetails = $this->getShiftTimeIntervals($activeSchedule->shift_id, $dayOfWeek);
        
        if ($shiftDetails->isEmpty()) {
            return [
                'allowed' => false,
                'reason' => 'No shift time intervals configured',
                'details' => 'The assigned shift has no time intervals configured for today'
            ];
        }

        // Check if current time falls within any allowed check-in window
        $validInterval = $this->findValidCheckInInterval($shiftDetails, $currentTime);
        
        if (!$validInterval) {
            return [
                'allowed' => false,
                'reason' => 'Outside allowed check-in time',
                'details' => $this->getNextAllowedCheckInTime($shiftDetails, $currentTime),
                'shift_details' => $shiftDetails->map(function($detail) {
                    return [
                        'interval_name' => $detail->alias,
                        'start_time' => $detail->in_time,
                        'allowed_early' => $detail->in_ahead_margin . ' minutes',
                        'allowed_late' => $detail->in_above_margin . ' minutes'
                    ];
                })
            ];
        }

        // Check if employee already checked in today
        $existingCheckIn = $this->hasEmployeeCheckedInToday($employeeId, $today);
        
        if ($existingCheckIn) {
            return [
                'allowed' => false,
                'reason' => 'Already checked in today',
                'details' => 'Employee has already checked in at ' . $existingCheckIn->attendance_time
            ];
        }

        return [
            'allowed' => true,
            'reason' => 'Check-in allowed',
            'details' => 'Employee can check in during ' . $validInterval->alias . ' shift',
            'shift_info' => [
                'shift_name' => $activeSchedule->shift->alias ?? 'Unknown Shift',
                'interval_name' => $validInterval->alias,
                'scheduled_start' => $validInterval->in_time,
                'check_in_window' => [
                    'earliest' => $this->calculateEarliestCheckIn($validInterval),
                    'latest' => $this->calculateLatestCheckIn($validInterval)
                ]
            ]
        ];
    }

    /**
     * Get employee's active schedule for a specific date
     */
    private function getEmployeeActiveSchedule($employeeId, $date)
    {
        return Schedule::with('shift')
            ->where('employee_id', $employeeId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();
    }

    /**
     * Get shift time intervals for a specific day of week
     */
    private function getShiftTimeIntervals($shiftId, $dayOfWeek)
    {
        return DB::table('att_shiftdetail')
            ->join('att_timeinterval', 'att_shiftdetail.time_interval_id', '=', 'att_timeinterval.id')
            ->where('att_shiftdetail.shift_id', $shiftId)
            ->where('att_shiftdetail.day_of_week', $dayOfWeek)
            ->select('att_timeinterval.*', 'att_shiftdetail.work_type', 'att_shiftdetail.day_of_week')
            ->get();
    }

    /**
     * Find valid check-in interval for current time
     */
    private function findValidCheckInInterval($shiftDetails, $currentTime)
    {
        $currentMinutes = $this->timeToMinutes($currentTime);
        
        foreach ($shiftDetails as $interval) {
            $startMinutes = $this->timeToMinutes($interval->in_time);
            $earliestCheckIn = $startMinutes - ($interval->in_ahead_margin ?? 0);
            $latestCheckIn = $startMinutes + ($interval->in_above_margin ?? 0);
            
            if ($currentMinutes >= $earliestCheckIn && $currentMinutes <= $latestCheckIn) {
                return $interval;
            }
        }
        
        return null;
    }

    /**
     * Get next allowed check-in time
     */
    private function getNextAllowedCheckInTime($shiftDetails, $currentTime)
    {
        $currentMinutes = $this->timeToMinutes($currentTime);
        $nextInterval = null;
        $nextStartTime = null;
        
        foreach ($shiftDetails as $interval) {
            $startMinutes = $this->timeToMinutes($interval->in_time);
            $earliestCheckIn = $startMinutes - ($interval->in_ahead_margin ?? 0);
            
            if ($earliestCheckIn > $currentMinutes) {
                if ($nextStartTime === null || $earliestCheckIn < $nextStartTime) {
                    $nextStartTime = $earliestCheckIn;
                    $nextInterval = $interval;
                }
            }
        }
        
        if ($nextInterval) {
            $nextTime = $this->minutesToTime($nextStartTime);
            return "Next check-in window opens at {$nextTime} for {$nextInterval->alias}";
        }
        
        return "No more check-in windows available today";
    }

    /**
     * Check if employee has already checked in today
     */
    private function hasEmployeeCheckedInToday($employeeId, $date)
    {
        return DB::table('attendances')
            ->where('emp_id', $employeeId)
            ->whereDate('attendance_date', $date)
            ->where('state', 0) // Check-in state
            ->first();
    }

    /**
     * Calculate earliest check-in time for an interval
     */
    private function calculateEarliestCheckIn($interval)
    {
        $startMinutes = $this->timeToMinutes($interval->in_time);
        $earliestMinutes = $startMinutes - ($interval->in_ahead_margin ?? 0);
        return $this->minutesToTime($earliestMinutes);
    }

    /**
     * Calculate latest check-in time for an interval
     */
    private function calculateLatestCheckIn($interval)
    {
        $startMinutes = $this->timeToMinutes($interval->in_time);
        $latestMinutes = $startMinutes + ($interval->in_above_margin ?? 0);
        return $this->minutesToTime($latestMinutes);
    }

    /**
     * Convert time string to minutes since midnight
     */
    private function timeToMinutes($time)
    {
        $timeParts = explode(':', $time);
        return intval($timeParts[0]) * 60 + intval($timeParts[1]);
    }

    /**
     * Convert minutes since midnight to time string
     */
    private function minutesToTime($minutes)
    {
        $hours = intval($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }

    /**
     * Get comprehensive shift validation report for an employee
     */
    public function getEmployeeShiftReport($employeeId, $date = null)
    {
        $date = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        
        $employee = Employee::find($employeeId);
        if (!$employee) {
            return ['error' => 'Employee not found'];
        }

        $activeSchedule = $this->getEmployeeActiveSchedule($employeeId, $date);
        
        if (!$activeSchedule) {
            return [
                'employee' => $employee->name,
                'date' => $date,
                'status' => 'No active schedule',
                'message' => 'Employee is not scheduled to work on this date'
            ];
        }

        $shiftDetails = $this->getShiftTimeIntervals($activeSchedule->shift_id, $dayOfWeek);
        
        return [
            'employee' => $employee->name,
            'date' => $date,
            'day_of_week' => $dayOfWeek,
            'schedule' => [
                'shift_name' => $activeSchedule->shift->alias ?? 'Unknown',
                'start_date' => $activeSchedule->start_date,
                'end_date' => $activeSchedule->end_date
            ],
            'time_intervals' => $shiftDetails->map(function($detail) {
                return [
                    'name' => $detail->alias,
                    'start_time' => $detail->in_time,
                    'duration' => $detail->duration . ' minutes',
                    'early_margin' => $detail->in_ahead_margin . ' minutes',
                    'late_margin' => $detail->in_above_margin . ' minutes',
                    'check_in_window' => [
                        'earliest' => $this->calculateEarliestCheckIn($detail),
                        'latest' => $this->calculateLatestCheckIn($detail)
                    ]
                ];
            })
        ];
    }
}