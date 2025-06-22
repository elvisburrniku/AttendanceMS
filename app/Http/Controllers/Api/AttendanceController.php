<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

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

        // Check if already checked in today
        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance && $existingAttendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in today'
            ], 400);
        }

        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date' => $today
            ],
            [
                'check_in' => Carbon::now(),
                'latitude_in' => $request->latitude,
                'longitude_in' => $request->longitude,
                'location_in' => $request->location_address,
                'status' => 'present'
            ]
        );

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
        $attendance = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'No check-in record found for today'
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked out today'
            ], 400);
        }

        $checkOutTime = Carbon::now();
        $workHours = $checkOutTime->diffInHours($attendance->check_in);

        $attendance->update([
            'check_out' => $checkOutTime,
            'latitude_out' => $request->latitude,
            'longitude_out' => $request->longitude,
            'location_out' => $request->location_address,
            'work_hours' => $workHours
        ]);

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
        $attendance = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('date', $today)
            ->with('employee')
            ->first();

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

        $query = Attendance::where('employee_id', $request->employee_id);

        if ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data' => [
                'attendances' => $attendances
            ]
        ], 200);
    }
}