<?php

use Illuminate\Support\Facades\Route;

// Demo route to showcase the redesigned employee dashboard
Route::get('/demo/employee-dashboard', function () {
    // Create a mock authenticated user for demo purposes
    $mockUser = new \App\Models\User([
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john.doe@company.com',
        'created_at' => now()->subMonths(3)
    ]);
    
    // Create mock employee data
    $mockEmployee = new \App\Models\Employee([
        'id' => 1,
        'emp_code' => '001',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@company.com',
        'nickname' => 'John',
        'department_id' => 1,
        'position_id' => 1,
        'hire_date' => now()->subMonths(3),
        'status' => 1
    ]);
    
    // Mock today's attendance data
    $mockCheckin = new \App\Models\Attendance([
        'id' => 1,
        'emp_id' => 1,
        'emp_code' => '001',
        'punch_time' => now()->setTime(9, 15, 0),
        'punch_state' => '0',
        'latitude' => 40.7128,
        'longitude' => -74.0060,
        'gps_location' => true,
        'source' => 'web'
    ]);
    
    // Mock break time
    $mockBreakin = new \App\Models\Attendance([
        'id' => 2,
        'emp_id' => 1,
        'emp_code' => '001',
        'punch_time' => now()->setTime(12, 30, 0),
        'punch_state' => '3',
        'source' => 'web'
    ]);
    
    $mockBreakout = new \App\Models\Attendance([
        'id' => 3,
        'emp_id' => 1,
        'emp_code' => '001',
        'punch_time' => now()->setTime(13, 0, 0),
        'punch_state' => '2',
        'source' => 'web'
    ]);
    
    // Mock weekly attendance data
    $mockWeeklyAttendances = collect([
        new \App\Models\Attendance([
            'punch_time' => now()->subDays(1)->setTime(9, 0, 0),
            'punch_state' => '0'
        ]),
        new \App\Models\Attendance([
            'punch_time' => now()->subDays(1)->setTime(17, 30, 0),
            'punch_state' => '1'
        ]),
        new \App\Models\Attendance([
            'punch_time' => now()->subDays(2)->setTime(9, 10, 0),
            'punch_state' => '0'
        ]),
        new \App\Models\Attendance([
            'punch_time' => now()->subDays(2)->setTime(17, 45, 0),
            'punch_state' => '1'
        ])
    ]);
    
    // Set up the view with mock data
    return view('employee.demo-dashboard', [
        'user' => $mockUser,
        'employee' => $mockEmployee,
        'checkin' => $mockCheckin,
        'checkout' => null,
        'breakin' => $mockBreakin,
        'breakout' => $mockBreakout,
        'weeklyAttendances' => $mockWeeklyAttendances,
        'currentStatus' => 'working',
        'workTime' => 480, // 8 hours in minutes
        'breakTime' => 30   // 30 minutes
    ]);
})->name('demo.employee.dashboard');