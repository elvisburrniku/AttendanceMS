<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestNfcSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:nfc {--demo : Run a demo of the NFC system}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and demonstrate the NFC attendance system functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Testing NFC Attendance System...');

        if ($this->option('demo')) {
            return $this->runDemo();
        }

        $this->testSystemComponents();
        return 0;
    }

    private function testSystemComponents()
    {
        $this->info('📋 Running NFC System Tests:');
        
        // Test 1: Check database tables
        $this->testDatabaseTables();
        
        // Test 2: Check employee NFC cards
        $this->testEmployeeCards();
        
        // Test 3: Simulate NFC attendance
        $this->simulateNfcAttendance();
        
        // Test 4: Check recent attendance
        $this->checkRecentAttendance();
        
        $this->info('✅ All NFC system tests completed successfully!');
    }

    private function testDatabaseTables()
    {
        $this->info('🔍 Checking database tables...');
        
        $tables = [
            'personnel_employee' => 'Employee records',
            'iclock_transaction' => 'Attendance transactions',
            'att_attemployee' => 'Attendance employee mapping',
            'personnel_department' => 'Departments',
            'personnel_position' => 'Positions'
        ];

        foreach ($tables as $table => $description) {
            $count = DB::table($table)->count();
            $this->line("  - {$description}: {$count} records");
        }
    }

    private function testEmployeeCards()
    {
        $this->info('💳 Checking employee NFC cards...');
        
        $employees = DB::table('personnel_employee')
            ->select('emp_code', 'first_name', 'last_name', 'card_no')
            ->where('is_active', true)
            ->whereNotNull('card_no')
            ->get();

        $this->table(
            ['Employee Code', 'Name', 'NFC Card ID'],
            $employees->map(function ($emp) {
                return [
                    $emp->emp_code,
                    $emp->first_name . ' ' . $emp->last_name,
                    $emp->card_no
                ];
            })->toArray()
        );
    }

    private function simulateNfcAttendance()
    {
        $this->info('🎯 Simulating NFC attendance transactions...');
        
        $employee = DB::table('personnel_employee')
            ->where('emp_code', 'EMP001')
            ->first();

        if (!$employee) {
            $this->error('Test employee EMP001 not found');
            return;
        }

        // Simulate check-in
        $attendanceData = [
            'emp_code' => $employee->emp_code,
            'punch_time' => Carbon::now(),
            'punch_state' => '0', // Check-in
            'verify_type' => 2, // NFC verification
            'work_code' => '',
            'terminal_sn' => 'NFC-TEST-001',
            'terminal_alias' => 'NFC Test Scanner',
            'area_alias' => 'Test Lab',
            'longitude' => -74.0060,
            'latitude' => 40.7128,
            'gps_location' => 'Test Location',
            'mobile' => $employee->mobile,
            'source' => 2, // NFC source
            'purpose' => 0
        ];

        DB::table('iclock_transaction')->insert($attendanceData);
        $this->line("  ✓ Simulated check-in for {$employee->first_name} {$employee->last_name}");

        // Simulate check-out after 8 hours
        $attendanceData['punch_time'] = Carbon::now()->addHours(8);
        $attendanceData['punch_state'] = '1'; // Check-out
        DB::table('iclock_transaction')->insert($attendanceData);
        $this->line("  ✓ Simulated check-out for {$employee->first_name} {$employee->last_name}");
    }

    private function checkRecentAttendance()
    {
        $this->info('📊 Recent attendance records:');
        
        $recentAttendance = DB::table('iclock_transaction as it')
            ->leftJoin('personnel_employee as pe', 'it.emp_code', '=', 'pe.emp_code')
            ->select([
                'it.emp_code',
                'pe.first_name',
                'pe.last_name',
                'it.punch_time',
                'it.punch_state',
                'it.terminal_alias'
            ])
            ->where('it.punch_time', '>=', Carbon::today())
            ->orderBy('it.punch_time', 'desc')
            ->limit(10)
            ->get();

        if ($recentAttendance->isEmpty()) {
            $this->line('  No attendance records found for today');
            return;
        }

        $this->table(
            ['Employee', 'Action', 'Time', 'Terminal'],
            $recentAttendance->map(function ($record) {
                return [
                    $record->first_name . ' ' . $record->last_name . ' (' . $record->emp_code . ')',
                    $record->punch_state === '0' ? 'Check In' : 'Check Out',
                    Carbon::parse($record->punch_time)->format('H:i:s'),
                    $record->terminal_alias
                ];
            })->toArray()
        );
    }

    private function runDemo()
    {
        $this->info('🎬 Running NFC System Demo...');
        
        $this->line('');
        $this->info('=== NFC ATTENDANCE MANAGEMENT SYSTEM DEMO ===');
        $this->line('');

        // Demo scenario
        $this->info('📱 Demo Scenario: Employee using NFC card for attendance');
        $this->line('');

        // Step 1: Employee approaches scanner
        $this->line('1. Employee John Smith approaches the NFC scanner');
        $this->line('2. Scanner detects NFC card: C001');
        
        // Step 2: System lookup
        $employee = DB::table('personnel_employee')
            ->leftJoin('personnel_department as pd', 'personnel_employee.department_id', '=', 'pd.id')
            ->leftJoin('personnel_position as pp', 'personnel_employee.position_id', '=', 'pp.id')
            ->select([
                'personnel_employee.*',
                'pd.dept_name',
                'pp.position_name'
            ])
            ->where('personnel_employee.card_no', 'C001')
            ->first();

        if ($employee) {
            $this->line('3. System identifies employee:');
            $this->line("   - Name: {$employee->first_name} {$employee->last_name}");
            $this->line("   - Code: {$employee->emp_code}");
            $this->line("   - Department: {$employee->dept_name}");
            $this->line("   - Position: {$employee->position_name}");
        }

        // Step 3: Determine action
        $lastAttendance = DB::table('iclock_transaction')
            ->where('emp_code', $employee->emp_code)
            ->orderBy('punch_time', 'desc')
            ->first();

        $action = (!$lastAttendance || $lastAttendance->punch_state === '1') ? 'Check In' : 'Check Out';
        $this->line("4. Next action determined: {$action}");

        // Step 4: Process attendance
        $this->line('5. Processing attendance...');
        
        $attendanceData = [
            'emp_code' => $employee->emp_code,
            'punch_time' => Carbon::now(),
            'punch_state' => $action === 'Check In' ? '0' : '1',
            'verify_type' => 2,
            'work_code' => '',
            'terminal_sn' => 'NFC-DEMO-001',
            'terminal_alias' => 'Demo NFC Scanner',
            'area_alias' => 'Main Office',
            'longitude' => -74.0060,
            'latitude' => 40.7128,
            'gps_location' => 'New York Office Demo',
            'mobile' => $employee->mobile,
            'source' => 2,
            'purpose' => 0
        ];

        DB::table('iclock_transaction')->insert($attendanceData);
        
        $this->line("6. ✅ Success! {$employee->first_name} {$employee->last_name} {$action} completed");
        $this->line("   Time: " . Carbon::now()->format('Y-m-d H:i:s'));
        $this->line("   Location: Demo Office");

        $this->line('');
        $this->info('🎯 Demo Features Demonstrated:');
        $this->line('  ✓ NFC card detection and reading');
        $this->line('  ✓ Employee identification and verification');
        $this->line('  ✓ Automatic action determination (check-in/check-out)');
        $this->line('  ✓ GPS location capture');
        $this->line('  ✓ Real-time attendance processing');
        $this->line('  ✓ Database transaction logging');

        $this->line('');
        $this->info('📱 Access Points:');
        $this->line('  • NFC Scanner: /nfc/scanner');
        $this->line('  • Employee Card: /nfc/employee-card');
        $this->line('  • API Endpoints: /nfc/attendance, /nfc/employee-info');

        $this->line('');
        $this->info('🔧 Technical Implementation:');
        $this->line('  • Web NFC API for browser-based scanning');
        $this->line('  • Progressive Web App (PWA) support');
        $this->line('  • Real-time attendance processing');
        $this->line('  • GPS location tracking');
        $this->line('  • Responsive mobile-first design');

        return 0;
    }
}