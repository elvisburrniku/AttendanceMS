<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use App\Services\ShiftValidationService;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_address' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        $employee = Employee::find($request->employee_id);
        $today = Carbon::today();

        // Validate shift-based check-in rules
        $shiftValidationService = new ShiftValidationService();
        $validation = $shiftValidationService->canEmployeeCheckIn($request->employee_id);
        
        if (!$validation['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $validation['reason'],
                'details' => $validation['details'],
                'shift_info' => $validation['shift_details'] ?? null
            ], 403);
        }

        // Create attendance record
        $attendanceId = \DB::table('attendances')->insertGetId([
            'uid' => $request->employee_id . '-' . $today->format('Ymd') . '-checkin',
            'emp_id' => $request->employee_id,
            'state' => 0, // Check-in state
            'attendance_time' => Carbon::now()->format('H:i:s'),
            'attendance_date' => $today->format('Y-m-d'),
            'status' => 1,
            'type' => 'mobile',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $attendance = \DB::table('attendances')->where('id', $attendanceId)->first();

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful',
            'data' => [
                'attendance' => $attendance,
                'employee' => $employee
            ]
        ], 200);
    }

    public function checkOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_address' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        $today = Carbon::today();
        $checkInAttendance = \DB::table('attendances')
            ->where('emp_id', $request->employee_id)
            ->whereDate('attendance_date', $today)
            ->where('state', 0) // Check-in state
            ->first();

        if (!$checkInAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'No check-in record found for today'
            ], 400);
        }

        $checkOutAttendance = \DB::table('attendances')
            ->where('emp_id', $request->employee_id)
            ->whereDate('attendance_date', $today)
            ->where('state', 1) // Check-out state
            ->first();

        if ($checkOutAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked out today'
            ], 400);
        }

        $checkOutTime = Carbon::now();
        $checkInTime = Carbon::parse($checkInAttendance->attendance_time);
        $workHours = $checkOutTime->diffInHours($checkInTime);

        // Create checkout attendance record
        $attendanceId = \DB::table('attendances')->insertGetId([
            'uid' => $request->employee_id . '-' . $today->format('Ymd') . '-checkout',
            'emp_id' => $request->employee_id,
            'state' => 1, // Check-out state
            'attendance_time' => $checkOutTime->format('H:i:s'),
            'attendance_date' => $today->format('Y-m-d'),
            'status' => 1,
            'type' => 'mobile',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $attendance = \DB::table('attendances')->where('id', $attendanceId)->first();

        return response()->json([
            'success' => true,
            'message' => 'Check-out successful',
            'data' => [
                'attendance' => $attendance,
                'work_hours' => $workHours
            ]
        ], 200);
    }

    public function todayAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        $today = Carbon::today();
        $attendances = \DB::table('attendances')
            ->join('employees', 'attendances.emp_id', '=', 'employees.id')
            ->where('attendances.emp_id', $request->employee_id)
            ->whereDate('attendances.attendance_date', $today)
            ->select('attendances.*', 'employees.name as employee_name', 'employees.position')
            ->get();
        
        // Format attendance data for API response
        $checkIn = $attendances->where('state', 0)->first();
        $checkOut = $attendances->where('state', 1)->first();
        
        $attendance = [
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'employee_name' => $checkIn->employee_name ?? null,
            'employee_position' => $checkIn->position ?? null,
            'work_hours' => $checkIn && $checkOut ? 
                Carbon::parse($checkOut->attendance_time)->diffInHours(Carbon::parse($checkIn->attendance_time)) : 0
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'attendance' => $attendance
            ]
        ], 200);
    }

    public function attendanceHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        $query = \DB::table('attendances')
            ->join('employees', 'attendances.emp_id', '=', 'employees.id')
            ->where('attendances.emp_id', $request->employee_id)
            ->select('attendances.*', 'employees.name as employee_name', 'employees.position');

        if ($request->start_date) {
            $query->whereDate('attendances.attendance_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('attendances.attendance_date', '<=', $request->end_date);
        }

        $attendances = $query->orderBy('attendances.attendance_date', 'desc')
            ->orderBy('attendances.attendance_time', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'attendances' => $attendances
            ]
        ], 200);
    }
}