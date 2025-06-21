<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FingerDevicesControlller;
use App\Providers\RouteServiceProvider;

Route::get('/', function () {
    if(auth()->check() && auth()->user()->hasRole('employee')){
        return redirect(RouteServiceProvider::HOME_EMPLOYEE);
    }
    return view('welcome');
})->name('welcome');
Route::get('attended/{user_id}', '\App\Http\Controllers\AttendanceController@attended' )->name('attended');
Route::get('attended-before/{user_id}', '\App\Http\Controllers\AttendanceController@attendedBefore' )->name('attendedBefore');
Auth::routes(['register' => false, 'reset' => false]);

Route::group(['middleware' => ['auth', 'Role'], 'roles' => ['admin']], function () {
    Route::resource('/employees', '\App\Http\Controllers\EmployeeController');
    Route::resource('/departments', '\App\Http\Controllers\DepartmentController');
    Route::resource('/positions', '\App\Http\Controllers\PositionController');
    Route::resource('/areas', '\App\Http\Controllers\AreaController');
    Route::get('/attendance', '\App\Http\Controllers\AttendanceController@index')->name('attendance');
    Route::put('/attendance/{id}', '\App\Http\Controllers\AttendanceController@update')->name('attendance.update');
    Route::delete('/attendance/{id}', '\App\Http\Controllers\AttendanceController@destroy')->name('attendances.destroy');
    Route::get('/attendances/export', '\App\Http\Controllers\AttendanceController@export')->name('attendance.export');

    Route::get('/attendances/api/documentation', '\App\Http\Controllers\ApiController@attendancesApi')->name('attendance.api');
  
    Route::get('/latetime', '\App\Http\Controllers\AttendanceController@indexLatetime')->name('indexLatetime');
    Route::get('/leave', '\App\Http\Controllers\LeaveController@index')->name('leave');
    Route::post('/leave', '\App\Http\Controllers\LeaveController@store')->name('leaves.store');
    Route::put('/leave/{leave}', '\App\Http\Controllers\LeaveController@update')->name('leaves.update');
    Route::delete('/leave/{leave}', '\App\Http\Controllers\LeaveController@destroy')->name('leaves.destroy');
    Route::get('/overtime', '\App\Http\Controllers\LeaveController@indexOvertime')->name('indexOvertime');

    Route::get('/holiday', '\App\Http\Controllers\HolidayController@index')->name('holiday');
    Route::post('/holiday', '\App\Http\Controllers\HolidayController@store')->name('holiday.store');
    Route::put('/holiday/{holiday}', '\App\Http\Controllers\HolidayController@update')->name('holiday.update');
    Route::delete('/holiday/{holiday}', '\App\Http\Controllers\HolidayController@destroy')->name('holiday.destroy');

    Route::get('/admin', '\App\Http\Controllers\AdminController@index')->name('admin');

    // Schedule and Shift Management Routes
    Route::resource('schedules', '\App\Http\Controllers\ScheduleController');
    Route::get('schedules/bulk/assign', '\App\Http\Controllers\ScheduleController@bulk')->name('schedules.bulk');
    Route::post('schedules/bulk/assign', '\App\Http\Controllers\ScheduleController@bulkStore')->name('schedules.bulk.store');
    Route::get('employees/{employee}/schedules', '\App\Http\Controllers\ScheduleController@employeeSchedules')->name('employees.schedules');
    
    Route::resource('shifts', '\App\Http\Controllers\ShiftController');
    Route::get('shifts/{shift}/copy', '\App\Http\Controllers\ShiftController@copy')->name('shifts.copy');
    Route::post('shifts/{shift}/duplicate', '\App\Http\Controllers\ShiftController@duplicate')->name('shifts.duplicate');
    
    Route::resource('time-intervals', '\App\Http\Controllers\TimeIntervalController');
    Route::patch('time-intervals/{timeInterval}/toggle', '\App\Http\Controllers\TimeIntervalController@toggle')->name('time-intervals.toggle');
    
    // Shift Calendar Routes (Legacy)
    Route::get('calendar', '\App\Http\Controllers\ShiftCalendarController@index')->name('calendar.index');
    Route::post('calendar/update', '\App\Http\Controllers\ShiftCalendarController@updateSchedule')->name('calendar.update');
    Route::post('calendar/create', '\App\Http\Controllers\ShiftCalendarController@createSchedule')->name('calendar.create');
    Route::post('calendar/delete', '\App\Http\Controllers\ShiftCalendarController@deleteSchedule')->name('calendar.delete');
    Route::get('calendar/week-data', '\App\Http\Controllers\ShiftCalendarController@getWeekData')->name('calendar.week-data');
    
    // API Routes for Vue SPA
    Route::prefix('api')->group(function () {
        Route::get('employees', '\App\Http\Controllers\ApiController@employees');
        Route::get('shifts', '\App\Http\Controllers\ApiController@shifts');
        Route::get('time-intervals', '\App\Http\Controllers\ApiController@timeIntervals');
        Route::get('schedules', '\App\Http\Controllers\ApiController@schedules');
        Route::get('calendar/week-data', '\App\Http\Controllers\ApiController@weekData');
        Route::post('calendar/create', '\App\Http\Controllers\ApiController@createSchedule');
        Route::post('calendar/update', '\App\Http\Controllers\ApiController@updateSchedule');
        Route::post('calendar/delete', '\App\Http\Controllers\ApiController@deleteSchedule');
    });
    
    // SPA Route - catch all except login
    Route::get('app/{any?}', function () {
        return view('spa');
    })->where('any', '.*')->name('spa');
    
    // Redirect admin dashboard to SPA
    Route::get('admin', function () {
        return redirect('/app/dashboard');
    });
    
    // Legacy routes (keep for compatibility)
    Route::resource('/schedule', '\App\Http\Controllers\ScheduleController');
    Route::resource('/shift', '\App\Http\Controllers\ShiftController');
    Route::resource('/timetable', '\App\Http\Controllers\TimetableController');

    Route::get('/check', '\App\Http\Controllers\CheckController@index')->name('check');
    Route::get('/check/export', '\App\Http\Controllers\CheckController@export')->name('check.export');
    Route::get('/overtime', '\App\Http\Controllers\EmployeeOvertimeController@index')->name('overtime');
    Route::post('/overtime', '\App\Http\Controllers\EmployeeOvertimeController@store')->name('overtime_store');
    Route::get('/sheet-report', '\App\Http\Controllers\CheckController@sheetReport')->name('sheet-report');
    Route::post('check-store','\App\Http\Controllers\CheckController@CheckStore')->name('check_store');
    
    // Fingerprint Devices
    Route::resource('/finger_device', '\App\Http\Controllers\BiometricDeviceController');

    Route::delete('finger_device/destroy', '\App\Http\Controllers\BiometricDeviceController@massDestroy')->name('finger_device.massDestroy');
    Route::get('finger_device/{fingerDevice}/employees/add', '\App\Http\Controllers\BiometricDeviceController@addEmployee')->name('finger_device.add.employee');
    Route::get('finger_device/{fingerDevice}/get/attendance', '\App\Http\Controllers\BiometricDeviceController@getAttendance')->name('finger_device.get.attendance');
    // Temp Clear Attendance route
    Route::get('finger_device/clear/attendance', function () {
        $midnight = \Carbon\Carbon::createFromTime(23, 50, 00);
        $diff = now()->diffInMinutes($midnight);
        dispatch(new ClearAttendanceJob())->delay(now()->addMinutes($diff));
        toast("Attendance Clearance Queue will run in 11:50 P.M}!", "success");

        return back();
    })->name('finger_device.clear.attendance');
    

});

Route::group(['middleware' => ['auth']], function () {

    // Route::get('/home', 'HomeController@index')->name('home');



    

});

Route::group(['middleware' => ['auth', 'Role'], 'roles' => ['employee']], function () {

    Route::get('/employee', '\App\Http\Controllers\HomeController@index')->name('employee');

    Route::post('/attendance-tap', '\App\Http\Controllers\AttendanceController@startClocking')->name('attendance.tap');
    Route::post('/attendance/punch', '\App\Http\Controllers\AttendanceController@punchAttendance')->name('attendance.punch');
    
    // Employee Dashboard Routes
    Route::get('/employee/dashboard', '\App\Http\Controllers\HomeController@dashboard')->name('employee.dashboard');
    Route::get('/employee/attendance/history', '\App\Http\Controllers\HomeController@attendanceHistory')->name('employee.attendance.history');
    Route::get('/employee/reports', '\App\Http\Controllers\HomeController@reports')->name('employee.reports');
});

// Public Check-in/out Routes (for kiosk mode or public terminals)
Route::group(['prefix' => 'kiosk'], function () {
    Route::get('/', function () {
        return view('kiosk.index');
    })->name('kiosk.index');
    
    Route::post('/checkin', '\App\Http\Controllers\AttendanceController@kioskCheckin')->name('kiosk.checkin');
    Route::post('/checkout', '\App\Http\Controllers\AttendanceController@kioskCheckout')->name('kiosk.checkout');
    Route::get('/verify/{emp_code}', '\App\Http\Controllers\AttendanceController@verifyEmployee')->name('kiosk.verify');
});

// Reports Routes for Admin
Route::group(['middleware' => ['auth', 'Role'], 'roles' => ['admin']], function () {
    Route::get('/reports', '\App\Http\Controllers\ReportController@index')->name('reports.index');
    Route::get('/reports/attendance', '\App\Http\Controllers\ReportController@attendance')->name('reports.attendance');
    Route::get('/reports/attendance/export', '\App\Http\Controllers\ReportController@exportAttendance')->name('reports.attendance.export');
    Route::get('/reports/monthly', '\App\Http\Controllers\ReportController@monthly')->name('reports.monthly');
    Route::get('/reports/daily', '\App\Http\Controllers\ReportController@daily')->name('reports.daily');
    Route::get('/reports/employee/{employee}', '\App\Http\Controllers\ReportController@employeeReport')->name('reports.employee');
    Route::get('/reports/summary', '\App\Http\Controllers\ReportController@summary')->name('reports.summary');
    Route::post('/reports/custom', '\App\Http\Controllers\ReportController@customReport')->name('reports.custom');
});

// Route::get('/attendance/assign', function () {
//     return view('attendance_leave_login');
// })->name('attendance.login');

// Route::post('/attendance/assign', '\App\Http\Controllers\AttendanceController@assign')->name('attendance.assign');


// Route::get('/leave/assign', function () {
//     return view('attendance_leave_login');
// })->name('leave.login');

// Route::post('/leave/assign', '\App\Http\Controllers\LeaveController@assign')->name('leave.assign');


// Demo route for showcasing redesigned employee dashboard
Route::get('/demo/employee-dashboard', function () {
    // Create mock data for demo
    $mockUser = (object)[
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john.doe@company.com',
        'created_at' => now()->subMonths(3)
    ];
    
    $mockEmployee = (object)[
        'id' => 1,
        'emp_code' => '001',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ];
    
    $mockCheckin = (object)[
        'punch_time' => now()->setTime(9, 15, 0),
        'punch_state' => '0',
    ];
    
    $mockBreakin = (object)[
        'punch_time' => now()->setTime(12, 30, 0),
        'punch_state' => '3',
    ];
    
    $mockBreakout = (object)[
        'punch_time' => now()->setTime(13, 0, 0),
        'punch_state' => '2',
    ];
    
    $mockWeeklyAttendances = collect([
        (object)['punch_time' => now()->subDays(1)->setTime(9, 0, 0), 'punch_state' => '0'],
        (object)['punch_time' => now()->subDays(1)->setTime(17, 30, 0), 'punch_state' => '1'],
        (object)['punch_time' => now()->subDays(2)->setTime(9, 10, 0), 'punch_state' => '0'],
        (object)['punch_time' => now()->subDays(2)->setTime(17, 45, 0), 'punch_state' => '1']
    ]);
    
    return view('employee.demo-dashboard', [
        'user' => $mockUser,
        'employee' => $mockEmployee,
        'checkin' => $mockCheckin,
        'checkout' => null,
        'breakin' => $mockBreakin,
        'breakout' => $mockBreakout,
        'weeklyAttendances' => $mockWeeklyAttendances,
        'workTime' => 480,
        'breakTime' => 30
    ]);
})->name('demo.employee.dashboard');

// Route::get('{any}', 'App\http\controllers\VeltrixController@index');