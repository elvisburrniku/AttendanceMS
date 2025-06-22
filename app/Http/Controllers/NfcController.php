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
    /**
     * Employee NFC Card Interface - Role-based display
     */
    public function employeeCard()
    {
        $user = auth()->user();
        $employee = null;
        $userRole = 'employee'; // default role
        
        // Get employee data from personnel_employee table
        if ($user) {
            $employee = DB::table('personnel_employee as pe')
                ->leftJoin('personnel_department as pd', 'pe.department_id', '=', 'pd.id')
                ->leftJoin('personnel_position as pp', 'pe.position_id', '=', 'pp.id')
                ->select([
                    'pe.*',
                    'pd.dept_name',
                    'pp.position_name'
                ])
                ->where('pe.email', $user->email)
                ->orWhere('pe.emp_code', 'EMP' . str_pad($user->id, 3, '0', STR_PAD_LEFT))
                ->first();
            
            // Determine user role based on various factors
            $userRole = $this->determineUserRole($user, $employee);
        }
        
        // Get role-specific card configurations
        $cardConfig = $this->getRoleBasedCardConfig($userRole);
        
        return view('nfc.employee-card', [
            'employee' => $employee,
            'userRole' => $userRole,
            'cardConfig' => $cardConfig,
            'user' => $user
        ]);
    }
    
    /**
     * Determine user role based on user data and employee record
     */
    private function determineUserRole($user, $employee)
    {
        // Check if user is admin
        if ($user->email === 'admin@company.com' || str_contains($user->email, 'admin')) {
            return 'admin';
        }
        
        // Check employee privilege level if available
        if ($employee && isset($employee->dev_privilege)) {
            switch ($employee->dev_privilege) {
                case 14: return 'super_admin';
                case 6: return 'system_admin';
                case 1: return 'register';
                case 10: return 'user_defined';
                default: return 'employee';
            }
        }
        
        // Check based on department
        if ($employee && $employee->dept_name) {
            if (str_contains(strtolower($employee->dept_name), 'hr')) return 'hr_manager';
            if (str_contains(strtolower($employee->dept_name), 'management')) return 'manager';
            if (str_contains(strtolower($employee->dept_name), 'security')) return 'security';
        }
        
        // Check based on position
        if ($employee && $employee->position_name) {
            if (str_contains(strtolower($employee->position_name), 'manager')) return 'manager';
            if (str_contains(strtolower($employee->position_name), 'supervisor')) return 'supervisor';
            if (str_contains(strtolower($employee->position_name), 'admin')) return 'admin';
        }
        
        return 'employee';
    }
    
    /**
     * Get role-based card configuration
     */
    private function getRoleBasedCardConfig($role)
    {
        $configs = [
            'super_admin' => [
                'card_color' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'access_level' => 'LEVEL 5 - SUPER ADMIN',
                'permissions' => ['All System Access', 'User Management', 'System Configuration', 'Data Export'],
                'card_type' => 'EXECUTIVE',
                'special_features' => ['Master Override', 'Audit Logs', 'Emergency Access'],
                'icon' => 'fas fa-crown'
            ],
            'system_admin' => [
                'card_color' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                'access_level' => 'LEVEL 4 - SYSTEM ADMIN',
                'permissions' => ['System Management', 'Employee Records', 'Reports', 'Device Config'],
                'card_type' => 'ADMINISTRATIVE',
                'special_features' => ['System Logs', 'Backup Access', 'Settings'],
                'icon' => 'fas fa-cogs'
            ],
            'hr_manager' => [
                'card_color' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                'access_level' => 'LEVEL 3 - HR MANAGER',
                'permissions' => ['Employee Management', 'Attendance Reports', 'Leave Management', 'Payroll'],
                'card_type' => 'MANAGEMENT',
                'special_features' => ['HR Dashboard', 'Employee Analytics', 'Report Export'],
                'icon' => 'fas fa-users'
            ],
            'manager' => [
                'card_color' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                'access_level' => 'LEVEL 3 - MANAGER',
                'permissions' => ['Team Management', 'Attendance Monitoring', 'Schedule Management'],
                'card_type' => 'MANAGEMENT',
                'special_features' => ['Team Dashboard', 'Approval Rights', 'Reports'],
                'icon' => 'fas fa-user-tie'
            ],
            'supervisor' => [
                'card_color' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                'access_level' => 'LEVEL 2 - SUPERVISOR',
                'permissions' => ['Team Oversight', 'Attendance Review', 'Basic Reports'],
                'card_type' => 'SUPERVISORY',
                'special_features' => ['Team View', 'Shift Management'],
                'icon' => 'fas fa-clipboard-check'
            ],
            'security' => [
                'card_color' => 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
                'access_level' => 'LEVEL 2 - SECURITY',
                'permissions' => ['Access Control', 'Visitor Management', 'Security Logs'],
                'card_type' => 'SECURITY',
                'special_features' => ['Security Dashboard', 'Alert System', 'Access Logs'],
                'icon' => 'fas fa-shield-alt'
            ],
            'register' => [
                'card_color' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                'access_level' => 'LEVEL 1 - REGISTER',
                'permissions' => ['Attendance Tracking', 'Basic Profile', 'Time Logging'],
                'card_type' => 'STANDARD',
                'special_features' => ['Quick Check-in', 'Schedule View'],
                'icon' => 'fas fa-clock'
            ],
            'employee' => [
                'card_color' => 'linear-gradient(135deg, #d299c2 0%, #fef9d7 100%)',
                'access_level' => 'LEVEL 1 - EMPLOYEE',
                'permissions' => ['Check In/Out', 'View Schedule', 'Personal Profile'],
                'card_type' => 'STANDARD',
                'special_features' => ['Attendance Only'],
                'icon' => 'fas fa-id-card'
            ]
        ];
        
        return $configs[$role] ?? $configs['employee'];
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

    /**
     * NFC Management Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_nfc_cards' => DB::table('personnel_employee')->whereNotNull('card_no')->count(),
            'active_employees' => DB::table('personnel_employee')->where('is_active', true)->count(),
            'today_scans' => DB::table('iclock_transaction')
                ->where('punch_time', '>=', Carbon::today())
                ->where('verify_type', 2)
                ->count(),
            'unique_scanners' => DB::table('iclock_transaction')
                ->where('punch_time', '>=', Carbon::today())
                ->where('verify_type', 2)
                ->distinct('terminal_sn')
                ->count('terminal_sn')
        ];

        $recentActivity = DB::table('iclock_transaction as it')
            ->leftJoin('personnel_employee as pe', 'it.emp_code', '=', 'pe.emp_code')
            ->select([
                'it.emp_code',
                'pe.first_name',
                'pe.last_name',
                'it.punch_time',
                'it.punch_state',
                'it.terminal_alias',
                'it.verify_type'
            ])
            ->where('it.verify_type', 2) // NFC only
            ->where('it.punch_time', '>=', Carbon::today()->subDays(7))
            ->orderBy('it.punch_time', 'desc')
            ->limit(50)
            ->get();

        return view('nfc.dashboard', compact('stats', 'recentActivity'));
    }

    /**
     * NFC Device Management
     */
    public function devices()
    {
        $devices = DB::table('iclock_transaction')
            ->select([
                'terminal_sn',
                'terminal_alias',
                'area_alias',
                DB::raw('COUNT(*) as scan_count'),
                DB::raw('MAX(punch_time) as last_activity'),
                DB::raw('COUNT(DISTINCT emp_code) as unique_employees')
            ])
            ->where('verify_type', 2)
            ->where('punch_time', '>=', Carbon::today()->subDays(30))
            ->groupBy('terminal_sn', 'terminal_alias', 'area_alias')
            ->orderBy('last_activity', 'desc')
            ->get();

        return view('nfc.devices', compact('devices'));
    }

    /**
     * Bulk NFC Card Registration
     */
    public function bulkRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registrations' => 'required|array',
            'registrations.*.emp_code' => 'required|string|exists:personnel_employee,emp_code',
            'registrations.*.nfc_id' => 'required|string|unique:personnel_employee,card_no'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $successCount = 0;
            $errors = [];

            foreach ($request->registrations as $registration) {
                try {
                    DB::table('personnel_employee')
                        ->where('emp_code', $registration['emp_code'])
                        ->update([
                            'card_no' => $registration['nfc_id'],
                            'change_time' => Carbon::now()
                        ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to register {$registration['emp_code']}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully registered {$successCount} NFC cards",
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * NFC Analytics Data
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', '7d');
        
        switch ($period) {
            case '24h':
                $startDate = Carbon::now()->subHours(24);
                $groupBy = 'HOUR(punch_time)';
                break;
            case '7d':
                $startDate = Carbon::now()->subDays(7);
                $groupBy = 'DATE(punch_time)';
                break;
            case '30d':
                $startDate = Carbon::now()->subDays(30);
                $groupBy = 'DATE(punch_time)';
                break;
            default:
                $startDate = Carbon::now()->subDays(7);
                $groupBy = 'DATE(punch_time)';
        }

        // Scan frequency over time
        $scanFrequency = DB::table('iclock_transaction')
            ->select([
                DB::raw("{$groupBy} as period"),
                DB::raw('COUNT(*) as total_scans'),
                DB::raw('COUNT(DISTINCT emp_code) as unique_employees')
            ])
            ->where('verify_type', 2)
            ->where('punch_time', '>=', $startDate)
            ->groupBy(DB::raw($groupBy))
            ->orderBy('period')
            ->get();

        // Top locations
        $topLocations = DB::table('iclock_transaction')
            ->select([
                'area_alias',
                DB::raw('COUNT(*) as scan_count')
            ])
            ->where('verify_type', 2)
            ->where('punch_time', '>=', $startDate)
            ->groupBy('area_alias')
            ->orderBy('scan_count', 'desc')
            ->limit(10)
            ->get();

        // Device usage
        $deviceUsage = DB::table('iclock_transaction')
            ->select([
                'terminal_alias',
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('MAX(punch_time) as last_used')
            ])
            ->where('verify_type', 2)
            ->where('punch_time', '>=', $startDate)
            ->groupBy('terminal_alias')
            ->orderBy('usage_count', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'scan_frequency' => $scanFrequency,
                'top_locations' => $topLocations,
                'device_usage' => $deviceUsage,
                'period' => $period
            ]
        ]);
    }
}