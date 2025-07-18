<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FingerDevicesControlller;
use App\Providers\RouteServiceProvider;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/dashboard', function () {
    return view('welcome');
})->name('welcome');
// Trial signup and payment routes
Route::post('/trial-signup', '\App\Http\Controllers\PaymentController@trialSignup')->name('trial-signup');
Route::post('/create-checkout-session', '\App\Http\Controllers\PaymentController@createCheckoutSession')->name('create-checkout-session');
Route::get('/success', '\App\Http\Controllers\PaymentController@success')->name('payment.success');
Route::get('/cancel', '\App\Http\Controllers\PaymentController@cancel')->name('payment.cancel');

// Tenant management routes
Route::middleware(['auth'])->group(function () {
    Route::resource('tenants', \App\Http\Controllers\TenantController::class);
    Route::get('/tenants/{tenant}/switch', '\App\Http\Controllers\TenantController@switch')->name('tenants.switch');
});

Route::get('attended/{user_id}', '\App\Http\Controllers\AttendanceController@attended' )->name('attended');
Route::get('attended-before/{user_id}', '\App\Http\Controllers\AttendanceController@attendedBefore' )->name('attendedBefore');
Auth::routes(['register' => false, 'reset' => false]);

// Modern UI Routes
Route::get('/modern-login', '\App\Http\Controllers\ModernDashboardController@showLogin')->name('modern.login');
Route::get('/modern-dashboard', '\App\Http\Controllers\ModernDashboardController@index')->name('modern.dashboard')->middleware('auth');
Route::get('/modern-employees', '\App\Http\Controllers\EmployeeController@index')->name('modern.employees')->middleware('auth');
Route::get('/api/dashboard-stats', '\App\Http\Controllers\ModernDashboardController@getDashboardStats')->name('api.dashboard.stats')->middleware('auth');
Route::get('/api/activity-data', '\App\Http\Controllers\ModernDashboardController@getActivityData')->name('api.activity.data')->middleware('auth');

// Test route for modern admin dashboard (no auth required)
Route::get('/admin-demo', '\App\Http\Controllers\AdminController@index')->name('admin.demo');

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
    Route::delete('schedules/{schedule}', '\App\Http\Controllers\ScheduleController@destroy')->name('schedules.destroy');

    // NFC Attendance System Routes
    Route::prefix('nfc')->name('nfc.')->group(function () {
        Route::get('/dashboard', '\App\Http\Controllers\NfcController@dashboard')->name('dashboard');
        Route::get('/scanner', '\App\Http\Controllers\NfcController@scanner')->name('scanner');
        Route::get('/devices', '\App\Http\Controllers\NfcController@devices')->name('devices');
        Route::get('/analytics', '\App\Http\Controllers\NfcController@analytics')->name('analytics');
        Route::post('/attendance', '\App\Http\Controllers\NfcController@processAttendance')->name('attendance');
        Route::post('/employee-info', '\App\Http\Controllers\NfcController@getEmployeeByNfc')->name('employee-info');
        Route::get('/recent-attendance', '\App\Http\Controllers\NfcController@getRecentAttendance')->name('recent-attendance');
        Route::post('/register-card', '\App\Http\Controllers\NfcController@registerNfcCard')->name('register-card');
        Route::post('/bulk-register', '\App\Http\Controllers\NfcController@bulkRegister')->name('bulk-register');
    });
    
    // Employee NFC Card (accessible to employees)
    Route::get('/nfc/employee-card', '\App\Http\Controllers\NfcController@employeeCard')->name('nfc.employee-card');
    
    // iOS-specific routes
    Route::get('/nfc/ios-instructions', function() {
        return view('nfc.ios-instructions');
    })->name('nfc.ios-instructions');
    
    // Role Cards Showcase
    Route::get('/nfc/role-cards', function() {
        return view('nfc.role-cards-showcase');
    })->name('nfc.role-cards');
    
    // Role Switcher for Testing
    Route::get('/nfc/role-switcher', function() {
        return view('nfc.role-switcher');
    })->name('nfc.role-switcher');
    
    Route::resource('shifts', '\App\Http\Controllers\ShiftController');
    Route::get('shifts/{shift}/copy', '\App\Http\Controllers\ShiftController@copy')->name('shifts.copy');
    Route::post('shifts/{shift}/duplicate', '\App\Http\Controllers\ShiftController@duplicate')->name('shifts.duplicate');
    
    Route::resource('time-intervals', '\App\Http\Controllers\TimeIntervalController');
    Route::patch('time-intervals/{timeInterval}/toggle', '\App\Http\Controllers\TimeIntervalController@toggle')->name('time-intervals.toggle');
    
    // Shift Calendar Routes
    Route::get('calendar', '\App\Http\Controllers\ShiftCalendarController@index')->name('calendar.index');
    Route::post('calendar/update', '\App\Http\Controllers\ShiftCalendarController@updateSchedule')->name('calendar.update');
    Route::post('calendar/create', '\App\Http\Controllers\ShiftCalendarController@createSchedule')->name('calendar.create');
    Route::post('calendar/delete', '\App\Http\Controllers\ShiftCalendarController@deleteSchedule')->name('calendar.delete');
    Route::get('calendar/week-data', '\App\Http\Controllers\ShiftCalendarController@getWeekData')->name('calendar.week-data');
    
    // Shift Validation Routes
    Route::prefix('shift-validation')->name('shift-validation.')->group(function () {
        Route::get('/', '\App\Http\Controllers\ShiftValidationController@index')->name('index');
        Route::post('/test', '\App\Http\Controllers\ShiftValidationController@testValidation')->name('test');
        Route::get('/employee-report', '\App\Http\Controllers\ShiftValidationController@getEmployeeReport')->name('employee-report');
        Route::post('/create-sample-data', '\App\Http\Controllers\ShiftValidationController@createSampleData')->name('create-sample-data');
        Route::get('/stats', '\App\Http\Controllers\ShiftValidationController@getValidationStats')->name('stats');
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
    // Temp Clear Attendance route (temporarily disabled due to missing dependencies)
    // Route::get('finger_device/clear/attendance', function () {
    //     $midnight = \Carbon\Carbon::createFromTime(23, 50, 00);
    //     $diff = now()->diffInMinutes($midnight);
    //     dispatch(new ClearAttendanceJob())->delay(now()->addMinutes($diff));
    //     toast("Attendance Clearance Queue will run in 11:50 P.M}!", "success");
    //     return back();
    // })->name('finger_device.clear.attendance');
    

});

Route::group(['middleware' => ['auth']], function () {

    // Route::get('/home', 'HomeController@index')->name('home');



    

});

Route::group(['middleware' => ['auth', 'Role'], 'roles' => ['employee']], function () {

    Route::get('/employee', '\App\Http\Controllers\HomeController@index')->name('employee');

    Route::post('/attendance-tap', '\App\Http\Controllers\AttendanceController@startClocking')->name('attendance.tap');
    Route::post('/attendance/punch', '\App\Http\Controllers\AttendanceController@punchAttendance')->name('attendance.punch');
});

// Route::get('/attendance/assign', function () {
//     return view('attendance_leave_login');
// })->name('attendance.login');

// Route::post('/attendance/assign', '\App\Http\Controllers\AttendanceController@assign')->name('attendance.assign');


// Route::get('/leave/assign', function () {
//     return view('attendance_leave_login');
// })->name('leave.login');

// Route::post('/leave/assign', '\App\Http\Controllers\LeaveController@assign')->name('leave.assign');


// Employee dashboard route using real database data
Route::get('/demo/employee-dashboard', '\App\Http\Controllers\EmployeeController@employeeDashboard')->name('demo.employee.dashboard');

// Route::get('{any}', 'App\http\controllers\VeltrixController@index');