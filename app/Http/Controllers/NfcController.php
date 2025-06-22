<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class NfcController extends Controller
{
    /**
     * Display the NFC scanner interface
     */
    public function scanner()
    {
        return view('nfc.scanner');
    }

    /**
     * Display the NFC employee card interface
     */
    public function employeeCard()
    {
        return view('nfc.employee-card');
    }

    /**
     * Process NFC check-in/checkout
     */
    public function processAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emp_code' => 'required|string',
            'nfc_id' => 'required|string',
            'location' => 'nullable|array',
            'location.latitude' => 'nullable|numeric',
            'location.longitude' => 'nullable|numeric',
            'terminal_alias' => 'nullable|string',
            'area_alias' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Verify employee exists and NFC ID matches
            $employee = DB::table('personnel_employee')
                ->where('emp_code', $request->emp_code)
                ->where('card_no', $request->nfc_id)
                ->where('is_active', true)
                ->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found or NFC card not registered'
                ], 404);
            }

            // Get last attendance record to determine punch state
            $lastAttendance = DB::table('iclock_transaction')
                ->where('emp_code', $request->emp_code)
                ->orderBy('punch_time', 'desc')
                ->first();

            // Determine punch state (0 = check-in, 1 = check-out)
            $punchState = '0'; // Default to check-in
            if ($lastAttendance) {
                $punchState = $lastAttendance->punch_state === '0' ? '1' : '0';
            }

            // Create attendance transaction
            $attendanceData = [
                'emp_code' => $request->emp_code,
                'punch_time' => Carbon::now(),
                'punch_state' => $punchState,
                'verify_type' => 2, // NFC verification
                'work_code' => '',
                'terminal_sn' => 'NFC-' . substr(md5($request->ip()), 0, 8),
                'terminal_alias' => $request->terminal_alias ?? 'NFC Scanner',
                'area_alias' => $request->area_alias ?? 'Office',
                'longitude' => $request->location['longitude'] ?? null,
                'latitude' => $request->location['latitude'] ?? null,
                'gps_location' => $this->formatGpsLocation($request->location),
                'mobile' => $employee->mobile,
                'source' => 2, // NFC source
                'purpose' => 0
            ];

            DB::table('iclock_transaction')->insert($attendanceData);

            // Get employee name for response
            $employeeName = trim($employee->first_name . ' ' . $employee->last_name);
            $actionText = $punchState === '0' ? 'checked in' : 'checked out';

            return response()->json([
                'success' => true,
                'message' => "{$employeeName} successfully {$actionText}",
                'data' => [
                    'employee_name' => $employeeName,
                    'emp_code' => $request->emp_code,
                    'action' => $punchState === '0' ? 'check_in' : 'check_out',
                    'time' => Carbon::now()->format('Y-m-d H:i:s'),
                    'location' => $this->formatGpsLocation($request->location)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee info by NFC ID
     */
    public function getEmployeeByNfc(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nfc_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'NFC ID is required'
            ], 400);
        }

        try {
            $employee = DB::table('personnel_employee as pe')
                ->leftJoin('personnel_department as pd', 'pe.department_id', '=', 'pd.id')
                ->leftJoin('personnel_position as pp', 'pe.position_id', '=', 'pp.id')
                ->select([
                    'pe.emp_code',
                    'pe.first_name',
                    'pe.last_name',
                    'pe.nickname',
                    'pe.email',
                    'pe.mobile',
                    'pe.card_no',
                    'pd.dept_name',
                    'pp.position_name'
                ])
                ->where('pe.card_no', $request->nfc_id)
                ->where('pe.is_active', true)
                ->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found with this NFC card'
                ], 404);
            }

            // Get last attendance record
            $lastAttendance = DB::table('iclock_transaction')
                ->where('emp_code', $employee->emp_code)
                ->orderBy('punch_time', 'desc')
                ->first();

            $nextAction = 'check_in';
            $lastActionTime = null;

            if ($lastAttendance) {
                $nextAction = $lastAttendance->punch_state === '0' ? 'check_out' : 'check_in';
                $lastActionTime = Carbon::parse($lastAttendance->punch_time)->format('Y-m-d H:i:s');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'emp_code' => $employee->emp_code,
                    'name' => trim($employee->first_name . ' ' . $employee->last_name),
                    'nickname' => $employee->nickname,
                    'department' => $employee->dept_name,
                    'position' => $employee->position_name,
                    'email' => $employee->email,
                    'mobile' => $employee->mobile,
                    'next_action' => $nextAction,
                    'last_action_time' => $lastActionTime
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get employee info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent attendance records for dashboard
     */
    public function getRecentAttendance()
    {
        try {
            $recentAttendance = DB::table('iclock_transaction as it')
                ->leftJoin('personnel_employee as pe', 'it.emp_code', '=', 'pe.emp_code')
                ->select([
                    'it.emp_code',
                    'pe.first_name',
                    'pe.last_name',
                    'pe.nickname',
                    'it.punch_time',
                    'it.punch_state',
                    'it.terminal_alias',
                    'it.area_alias'
                ])
                ->where('it.punch_time', '>=', Carbon::today())
                ->orderBy('it.punch_time', 'desc')
                ->limit(20)
                ->get();

            $formattedRecords = $recentAttendance->map(function ($record) {
                return [
                    'emp_code' => $record->emp_code,
                    'name' => trim($record->first_name . ' ' . $record->last_name),
                    'nickname' => $record->nickname,
                    'action' => $record->punch_state === '0' ? 'Check In' : 'Check Out',
                    'time' => Carbon::parse($record->punch_time)->format('H:i:s'),
                    'terminal' => $record->terminal_alias,
                    'area' => $record->area_alias
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedRecords
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recent attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register new NFC card for employee
     */
    public function registerNfcCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emp_code' => 'required|string|exists:personnel_employee,emp_code',
            'nfc_id' => 'required|string|unique:personnel_employee,card_no'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::table('personnel_employee')
                ->where('emp_code', $request->emp_code)
                ->update([
                    'card_no' => $request->nfc_id,
                    'change_time' => Carbon::now()
                ]);

            $employee = DB::table('personnel_employee')
                ->where('emp_code', $request->emp_code)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'NFC card registered successfully',
                'data' => [
                    'emp_code' => $request->emp_code,
                    'name' => trim($employee->first_name . ' ' . $employee->last_name),
                    'nfc_id' => $request->nfc_id
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register NFC card: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format GPS location for storage
     */
    private function formatGpsLocation($location)
    {
        if (!$location || !isset($location['latitude']) || !isset($location['longitude'])) {
            return 'Office Location';
        }

        return "Lat: {$location['latitude']}, Lng: {$location['longitude']}";
    }
}