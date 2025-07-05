<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        $employees = Employee::all();
        
        // Generate attendance records for the past 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Skip weekends for most employees
            if ($date->isWeekend()) {
                continue;
            }
            
            foreach ($employees as $employee) {
                // 90% attendance rate - some employees might miss days
                if (rand(1, 100) <= 90) {
                    $this->createAttendanceRecord($employee, $date);
                }
            }
        }
        
        // Generate today's attendance for active employees
        $today = Carbon::now();
        foreach ($employees->take(6) as $employee) {
            $this->createTodayAttendance($employee, $today);
        }
    }
    
    private function createAttendanceRecord($employee, $date)
    {
        // Morning check-in (8:30 - 9:30 AM)
        $checkinTime = $date->copy()->setTime(8, 30)->addMinutes(rand(0, 60));
        
        // Afternoon check-out (4:30 - 6:00 PM)
        $checkoutTime = $date->copy()->setTime(16, 30)->addMinutes(rand(0, 90));
        
        // Check-in record
        Attendance::create([
            'emp_id' => $employee->id,
            'emp_code' => $employee->emp_code,
            'punch_state' => 0, // Check-in
            'punch_time' => $checkinTime,
            'verify_type' => 1,
            'terminal_sn' => 'MOBILE_APP',
            'terminal_alias' => 'Mobile Application',
            'area_alias' => 'Mobile Check-in',
            'longitude' => $this->generateRandomLongitude(),
            'latitude' => $this->generateRandomLatitude(),
            'gps_location' => $this->generateRandomAddress(),
            'work_code' => null,
            'is_attendance' => 1,
            'upload_time' => $checkinTime,
        ]);
        
        // Check-out record
        Attendance::create([
            'emp_id' => $employee->id,
            'emp_code' => $employee->emp_code,
            'punch_state' => 1, // Check-out
            'punch_time' => $checkoutTime,
            'verify_type' => 1,
            'terminal_sn' => 'MOBILE_APP',
            'terminal_alias' => 'Mobile Application',
            'area_alias' => 'Mobile Check-out',
            'longitude' => $this->generateRandomLongitude(),
            'latitude' => $this->generateRandomLatitude(),
            'gps_location' => $this->generateRandomAddress(),
            'work_code' => null,
            'is_attendance' => 1,
            'upload_time' => $checkoutTime,
        ]);
        
        // Occasionally add break records (30% chance)
        if (rand(1, 100) <= 30) {
            $breakOutTime = $checkinTime->copy()->addHours(3)->addMinutes(rand(-30, 30));
            $breakInTime = $breakOutTime->copy()->addMinutes(rand(30, 60));
            
            // Break out
            Attendance::create([
                'emp_id' => $employee->id,
                'emp_code' => $employee->emp_code,
                'punch_state' => 2, // Break out
                'punch_time' => $breakOutTime,
                'verify_type' => 1,
                'terminal_sn' => 'MOBILE_APP',
                'terminal_alias' => 'Mobile Application',
                'area_alias' => 'Mobile Break',
                'longitude' => $this->generateRandomLongitude(),
                'latitude' => $this->generateRandomLatitude(),
                'gps_location' => $this->generateRandomAddress(),
                'work_code' => null,
                'is_attendance' => 1,
                'upload_time' => $breakOutTime,
            ]);
            
            // Break in
            Attendance::create([
                'emp_id' => $employee->id,
                'emp_code' => $employee->emp_code,
                'punch_state' => 3, // Break in
                'punch_time' => $breakInTime,
                'verify_type' => 1,
                'terminal_sn' => 'MOBILE_APP',
                'terminal_alias' => 'Mobile Application',
                'area_alias' => 'Mobile Break',
                'longitude' => $this->generateRandomLongitude(),
                'latitude' => $this->generateRandomLatitude(),
                'gps_location' => $this->generateRandomAddress(),
                'work_code' => null,
                'is_attendance' => 1,
                'upload_time' => $breakInTime,
            ]);
        }
    }
    
    private function createTodayAttendance($employee, $date)
    {
        // Create check-in for today (8:30 - 9:30 AM)
        $checkinTime = $date->copy()->setTime(8, 30)->addMinutes(rand(0, 60));
        
        Attendance::create([
            'emp_id' => $employee->id,
            'emp_code' => $employee->emp_code,
            'punch_state' => 0, // Check-in
            'punch_time' => $checkinTime,
            'verify_type' => 1,
            'terminal_sn' => 'MOBILE_APP',
            'terminal_alias' => 'Mobile Application',
            'area_alias' => 'Mobile Check-in',
            'longitude' => $this->generateRandomLongitude(),
            'latitude' => $this->generateRandomLatitude(),
            'gps_location' => $this->generateRandomAddress(),
            'work_code' => null,
            'is_attendance' => 1,
            'upload_time' => $checkinTime,
        ]);
    }
    
    private function generateRandomLongitude()
    {
        // Generate longitude for a typical city (e.g., around New York City)
        return number_format(-74.006 + (rand(-1000, 1000) / 10000), 6);
    }
    
    private function generateRandomLatitude()
    {
        // Generate latitude for a typical city (e.g., around New York City)
        return number_format(40.7128 + (rand(-1000, 1000) / 10000), 6);
    }
    
    private function generateRandomAddress()
    {
        $streets = ['Main St', 'Broadway', 'Park Ave', 'Oak St', 'First Ave', 'Market St', 'Center St'];
        $street = $streets[array_rand($streets)];
        $number = rand(100, 9999);
        
        return "$number $street, New York, NY";
    }
}