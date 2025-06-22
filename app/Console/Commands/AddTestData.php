<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:data {--clear : Clear existing test data before adding new data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add comprehensive test data to the attendance management system including employees, departments, positions, areas, shifts, schedules, and attendance records';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 Starting attendance management test data creation...');

        if ($this->option('clear')) {
            $this->clearExistingData();
        }

        try {
            DB::transaction(function () {
                $this->createDepartments();
                $this->createPositions();
                $this->createAreas();
                $this->createTimeIntervals();
                $this->createShifts();
                $this->createShiftDetails();
                $this->createEmployees();
                $this->createEmployeeAreaAssignments();
                $this->createAttendanceEmployeeRecords();
                $this->createEmployeeSchedules();
                $this->createAttendanceTransactions();
            });

            $this->info('✅ Test data created successfully!');
            $this->displaySummary();
            
        } catch (\Exception $e) {
            $this->error('❌ Error creating test data: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function clearExistingData()
    {
        $this->warn('🧹 Clearing existing test data...');
        
        $tables = [
            'iclock_transaction',
            'att_attschedule', 
            'personnel_employee_area',
            'att_attemployee',
            'att_shiftdetail',
            'personnel_employee',
            'att_attshift',
            'att_timeinterval',
            'personnel_area',
            'personnel_position', 
            'personnel_department'
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }

    private function createDepartments()
    {
        $this->info('📁 Creating departments...');
        
        $departments = [
            ['dept_code' => 'HR', 'dept_name' => 'Human Resources', 'is_default' => false, 'parent_dept_id' => null, 'company_id' => 1],
            ['dept_code' => 'IT', 'dept_name' => 'Information Technology', 'is_default' => false, 'parent_dept_id' => null, 'company_id' => 1],
            ['dept_code' => 'FIN', 'dept_name' => 'Finance', 'is_default' => false, 'parent_dept_id' => null, 'company_id' => 1],
            ['dept_code' => 'OPS', 'dept_name' => 'Operations', 'is_default' => false, 'parent_dept_id' => null, 'company_id' => 1],
            ['dept_code' => 'SALES', 'dept_name' => 'Sales & Marketing', 'is_default' => false, 'parent_dept_id' => null, 'company_id' => 1],
            ['dept_code' => 'IT-DEV', 'dept_name' => 'Development Team', 'is_default' => false, 'parent_dept_id' => 2, 'company_id' => 1],
            ['dept_code' => 'IT-INFRA', 'dept_name' => 'Infrastructure Team', 'is_default' => false, 'parent_dept_id' => 2, 'company_id' => 1],
            ['dept_code' => 'HR-REC', 'dept_name' => 'Recruitment', 'is_default' => false, 'parent_dept_id' => 1, 'company_id' => 1],
            ['dept_code' => 'SALES-DOM', 'dept_name' => 'Domestic Sales', 'is_default' => false, 'parent_dept_id' => 5, 'company_id' => 1],
            ['dept_code' => 'SALES-INT', 'dept_name' => 'International Sales', 'is_default' => false, 'parent_dept_id' => 5, 'company_id' => 1],
        ];

        DB::table('personnel_department')->insert($departments);
    }

    private function createPositions()
    {
        $this->info('👔 Creating positions...');
        
        $positions = [
            ['position_code' => 'CEO', 'position_name' => 'Chief Executive Officer', 'is_default' => false, 'parent_position_id' => null, 'company_id' => 1],
            ['position_code' => 'CTO', 'position_name' => 'Chief Technology Officer', 'is_default' => false, 'parent_position_id' => 1, 'company_id' => 1],
            ['position_code' => 'CFO', 'position_name' => 'Chief Financial Officer', 'is_default' => false, 'parent_position_id' => 1, 'company_id' => 1],
            ['position_code' => 'HR-MGR', 'position_name' => 'HR Manager', 'is_default' => false, 'parent_position_id' => 1, 'company_id' => 1],
            ['position_code' => 'DEV-LEAD', 'position_name' => 'Development Lead', 'is_default' => false, 'parent_position_id' => 2, 'company_id' => 1],
            ['position_code' => 'DEV-SR', 'position_name' => 'Senior Developer', 'is_default' => false, 'parent_position_id' => 5, 'company_id' => 1],
            ['position_code' => 'DEV-JR', 'position_name' => 'Junior Developer', 'is_default' => false, 'parent_position_id' => 6, 'company_id' => 1],
            ['position_code' => 'SYS-ADMIN', 'position_name' => 'System Administrator', 'is_default' => false, 'parent_position_id' => 2, 'company_id' => 1],
            ['position_code' => 'ANALYST', 'position_name' => 'Business Analyst', 'is_default' => false, 'parent_position_id' => 1, 'company_id' => 1],
            ['position_code' => 'SALES-MGR', 'position_name' => 'Sales Manager', 'is_default' => false, 'parent_position_id' => 1, 'company_id' => 1],
            ['position_code' => 'SALES-REP', 'position_name' => 'Sales Representative', 'is_default' => false, 'parent_position_id' => 10, 'company_id' => 1],
            ['position_code' => 'HR-SPEC', 'position_name' => 'HR Specialist', 'is_default' => false, 'parent_position_id' => 4, 'company_id' => 1],
        ];

        DB::table('personnel_position')->insert($positions);
    }

    private function createAreas()
    {
        $this->info('🏢 Creating work areas...');
        
        $areas = [
            ['area_code' => 'MAIN', 'area_name' => 'Main Office', 'is_default' => true, 'parent_area_id' => null, 'company_id' => 1],
            ['area_code' => 'FLOOR1', 'area_name' => 'First Floor', 'is_default' => false, 'parent_area_id' => 1, 'company_id' => 1],
            ['area_code' => 'FLOOR2', 'area_name' => 'Second Floor', 'is_default' => false, 'parent_area_id' => 1, 'company_id' => 1],
            ['area_code' => 'FLOOR3', 'area_name' => 'Third Floor', 'is_default' => false, 'parent_area_id' => 1, 'company_id' => 1],
            ['area_code' => 'DEV-LAB', 'area_name' => 'Development Lab', 'is_default' => false, 'parent_area_id' => 3, 'company_id' => 1],
            ['area_code' => 'HR-OFFICE', 'area_name' => 'HR Office', 'is_default' => false, 'parent_area_id' => 2, 'company_id' => 1],
            ['area_code' => 'SALES-FLOOR', 'area_name' => 'Sales Floor', 'is_default' => false, 'parent_area_id' => 2, 'company_id' => 1],
            ['area_code' => 'CONFERENCE', 'area_name' => 'Conference Rooms', 'is_default' => false, 'parent_area_id' => 1, 'company_id' => 1],
            ['area_code' => 'REMOTE', 'area_name' => 'Remote Work', 'is_default' => false, 'parent_area_id' => null, 'company_id' => 1],
            ['area_code' => 'WAREHOUSE', 'area_name' => 'Warehouse', 'is_default' => false, 'parent_area_id' => null, 'company_id' => 1],
        ];

        DB::table('personnel_area')->insert($areas);
    }

    private function createTimeIntervals()
    {
        $this->info('⏰ Creating time intervals...');
        
        $timeIntervals = [
            [
                'alias' => 'Standard Work Day', 'use_mode' => 1, 'in_time' => '09:00:00',
                'in_ahead_margin' => 30, 'in_above_margin' => 15, 'out_ahead_margin' => 30, 'out_above_margin' => 15,
                'duration' => 480, 'in_required' => 1, 'out_required' => 1, 'allow_late' => 15, 'allow_leave_early' => 15,
                'work_day' => 1.0, 'early_in' => 1, 'min_early_in' => 30, 'late_out' => 1, 'min_late_out' => 60,
                'overtime_lv' => 1, 'overtime_lv1' => 1, 'overtime_lv2' => 2, 'overtime_lv3' => 3, 'multiple_punch' => 1,
                'available_interval_type' => 1, 'available_interval' => 15, 'work_time_duration' => 480, 'func_key' => 1,
                'work_type' => 1, 'day_change' => '00:00:00', 'enable_early_in' => true, 'enable_late_out' => true,
                'enable_overtime' => true, 'color_setting' => '#4CAF50', 'enable_max_ot_limit' => true, 'max_ot_limit' => 120,
                'count_early_in_interval' => false, 'count_late_out_interval' => false, 'overtime_policy' => 1, 'company_id' => 1
            ],
            [
                'alias' => 'Early Morning Shift', 'use_mode' => 1, 'in_time' => '06:00:00',
                'in_ahead_margin' => 30, 'in_above_margin' => 15, 'out_ahead_margin' => 30, 'out_above_margin' => 15,
                'duration' => 480, 'in_required' => 1, 'out_required' => 1, 'allow_late' => 10, 'allow_leave_early' => 10,
                'work_day' => 1.0, 'early_in' => 1, 'min_early_in' => 30, 'late_out' => 1, 'min_late_out' => 60,
                'overtime_lv' => 1, 'overtime_lv1' => 1, 'overtime_lv2' => 2, 'overtime_lv3' => 3, 'multiple_punch' => 1,
                'available_interval_type' => 1, 'available_interval' => 15, 'work_time_duration' => 480, 'func_key' => 1,
                'work_type' => 1, 'day_change' => '00:00:00', 'enable_early_in' => true, 'enable_late_out' => true,
                'enable_overtime' => true, 'color_setting' => '#2196F3', 'enable_max_ot_limit' => true, 'max_ot_limit' => 120,
                'count_early_in_interval' => false, 'count_late_out_interval' => false, 'overtime_policy' => 1, 'company_id' => 1
            ],
            [
                'alias' => 'Night Shift', 'use_mode' => 1, 'in_time' => '22:00:00',
                'in_ahead_margin' => 30, 'in_above_margin' => 15, 'out_ahead_margin' => 30, 'out_above_margin' => 15,
                'duration' => 480, 'in_required' => 1, 'out_required' => 1, 'allow_late' => 15, 'allow_leave_early' => 15,
                'work_day' => 1.0, 'early_in' => 1, 'min_early_in' => 30, 'late_out' => 1, 'min_late_out' => 60,
                'overtime_lv' => 1, 'overtime_lv1' => 1, 'overtime_lv2' => 2, 'overtime_lv3' => 3, 'multiple_punch' => 1,
                'available_interval_type' => 1, 'available_interval' => 15, 'work_time_duration' => 480, 'func_key' => 1,
                'work_type' => 1, 'day_change' => '06:00:00', 'enable_early_in' => true, 'enable_late_out' => true,
                'enable_overtime' => true, 'color_setting' => '#9C27B0', 'enable_max_ot_limit' => true, 'max_ot_limit' => 120,
                'count_early_in_interval' => false, 'count_late_out_interval' => false, 'overtime_policy' => 1, 'company_id' => 1
            ],
            [
                'alias' => 'Flexible Hours', 'use_mode' => 1, 'in_time' => '10:00:00',
                'in_ahead_margin' => 60, 'in_above_margin' => 30, 'out_ahead_margin' => 60, 'out_above_margin' => 30,
                'duration' => 480, 'in_required' => 1, 'out_required' => 1, 'allow_late' => 30, 'allow_leave_early' => 30,
                'work_day' => 1.0, 'early_in' => 1, 'min_early_in' => 60, 'late_out' => 1, 'min_late_out' => 120,
                'overtime_lv' => 1, 'overtime_lv1' => 1, 'overtime_lv2' => 2, 'overtime_lv3' => 3, 'multiple_punch' => 1,
                'available_interval_type' => 1, 'available_interval' => 30, 'work_time_duration' => 480, 'func_key' => 1,
                'work_type' => 1, 'day_change' => '00:00:00', 'enable_early_in' => true, 'enable_late_out' => true,
                'enable_overtime' => true, 'color_setting' => '#FF9800', 'enable_max_ot_limit' => true, 'max_ot_limit' => 150,
                'count_early_in_interval' => true, 'count_late_out_interval' => true, 'overtime_policy' => 1, 'company_id' => 1
            ],
            [
                'alias' => 'Half Day Morning', 'use_mode' => 1, 'in_time' => '09:00:00',
                'in_ahead_margin' => 15, 'in_above_margin' => 10, 'out_ahead_margin' => 15, 'out_above_margin' => 10,
                'duration' => 240, 'in_required' => 1, 'out_required' => 1, 'allow_late' => 10, 'allow_leave_early' => 10,
                'work_day' => 0.5, 'early_in' => 1, 'min_early_in' => 15, 'late_out' => 1, 'min_late_out' => 30,
                'overtime_lv' => 1, 'overtime_lv1' => 1, 'overtime_lv2' => 2, 'overtime_lv3' => 3, 'multiple_punch' => 1,
                'available_interval_type' => 1, 'available_interval' => 10, 'work_time_duration' => 240, 'func_key' => 1,
                'work_type' => 1, 'day_change' => '00:00:00', 'enable_early_in' => true, 'enable_late_out' => true,
                'enable_overtime' => false, 'color_setting' => '#8BC34A', 'enable_max_ot_limit' => false, 'max_ot_limit' => 0,
                'count_early_in_interval' => false, 'count_late_out_interval' => false, 'overtime_policy' => 1, 'company_id' => 1
            ],
            [
                'alias' => 'Half Day Afternoon', 'use_mode' => 1, 'in_time' => '14:00:00',
                'in_ahead_margin' => 15, 'in_above_margin' => 10, 'out_ahead_margin' => 15, 'out_above_margin' => 10,
                'duration' => 240, 'in_required' => 1, 'out_required' => 1, 'allow_late' => 10, 'allow_leave_early' => 10,
                'work_day' => 0.5, 'early_in' => 1, 'min_early_in' => 15, 'late_out' => 1, 'min_late_out' => 30,
                'overtime_lv' => 1, 'overtime_lv1' => 1, 'overtime_lv2' => 2, 'overtime_lv3' => 3, 'multiple_punch' => 1,
                'available_interval_type' => 1, 'available_interval' => 10, 'work_time_duration' => 240, 'func_key' => 1,
                'work_type' => 1, 'day_change' => '00:00:00', 'enable_early_in' => true, 'enable_late_out' => true,
                'enable_overtime' => false, 'color_setting' => '#CDDC39', 'enable_max_ot_limit' => false, 'max_ot_limit' => 0,
                'count_early_in_interval' => false, 'count_late_out_interval' => false, 'overtime_policy' => 1, 'company_id' => 1
            ],
        ];

        DB::table('att_timeinterval')->insert($timeIntervals);
    }

    private function createShifts()
    {
        $this->info('📅 Creating shifts...');
        
        $shifts = [
            ['alias' => 'Standard 5-Day Shift', 'cycle_unit' => 1, 'shift_cycle' => 7, 'work_weekend' => false, 'weekend_type' => 1, 'work_day_off' => false, 'day_off_type' => 1, 'auto_shift' => 0, 'enable_ot_rule' => true, 'frequency' => 1, 'ot_rule' => '1', 'company_id' => 1],
            ['alias' => '6-Day Work Week', 'cycle_unit' => 1, 'shift_cycle' => 7, 'work_weekend' => true, 'weekend_type' => 1, 'work_day_off' => false, 'day_off_type' => 1, 'auto_shift' => 0, 'enable_ot_rule' => true, 'frequency' => 1, 'ot_rule' => '1', 'company_id' => 1],
            ['alias' => 'Early Bird Shift', 'cycle_unit' => 1, 'shift_cycle' => 7, 'work_weekend' => false, 'weekend_type' => 1, 'work_day_off' => false, 'day_off_type' => 1, 'auto_shift' => 0, 'enable_ot_rule' => true, 'frequency' => 1, 'ot_rule' => '1', 'company_id' => 1],
            ['alias' => 'Night Operations', 'cycle_unit' => 1, 'shift_cycle' => 7, 'work_weekend' => true, 'weekend_type' => 2, 'work_day_off' => false, 'day_off_type' => 1, 'auto_shift' => 0, 'enable_ot_rule' => true, 'frequency' => 1, 'ot_rule' => '2', 'company_id' => 1],
            ['alias' => 'Flexible Schedule', 'cycle_unit' => 1, 'shift_cycle' => 7, 'work_weekend' => false, 'weekend_type' => 1, 'work_day_off' => true, 'day_off_type' => 2, 'auto_shift' => 1, 'enable_ot_rule' => true, 'frequency' => 2, 'ot_rule' => '1', 'company_id' => 1],
            ['alias' => 'Part-Time Morning', 'cycle_unit' => 1, 'shift_cycle' => 7, 'work_weekend' => false, 'weekend_type' => 1, 'work_day_off' => false, 'day_off_type' => 1, 'auto_shift' => 0, 'enable_ot_rule' => false, 'frequency' => 1, 'ot_rule' => '0', 'company_id' => 1],
            ['alias' => 'Part-Time Afternoon', 'cycle_unit' => 1, 'shift_cycle' => 7, 'work_weekend' => false, 'weekend_type' => 1, 'work_day_off' => false, 'day_off_type' => 1, 'auto_shift' => 0, 'enable_ot_rule' => false, 'frequency' => 1, 'ot_rule' => '0', 'company_id' => 1],
        ];

        DB::table('att_attshift')->insert($shifts);
    }

    private function createShiftDetails()
    {
        $this->info('📋 Creating shift details...');
        
        $shiftDetails = [];
        $now = Carbon::now();

        // Standard 5-Day Shift (Mon-Fri, Standard Work Day)
        for ($day = 1; $day <= 5; $day++) {
            $shiftDetails[] = ['shift_id' => 1, 'time_interval_id' => 1, 'day_of_week' => $day, 'created_at' => $now, 'updated_at' => $now];
        }

        // 6-Day Work Week (Mon-Sat, Standard Work Day)
        for ($day = 1; $day <= 6; $day++) {
            $shiftDetails[] = ['shift_id' => 2, 'time_interval_id' => 1, 'day_of_week' => $day, 'created_at' => $now, 'updated_at' => $now];
        }

        // Early Bird Shift (Mon-Fri, Early Morning)
        for ($day = 1; $day <= 5; $day++) {
            $shiftDetails[] = ['shift_id' => 3, 'time_interval_id' => 2, 'day_of_week' => $day, 'created_at' => $now, 'updated_at' => $now];
        }

        // Night Operations (7 days, Night Shift)
        for ($day = 1; $day <= 7; $day++) {
            $shiftDetails[] = ['shift_id' => 4, 'time_interval_id' => 3, 'day_of_week' => $day, 'created_at' => $now, 'updated_at' => $now];
        }

        // Flexible Schedule (Mon-Fri, Flexible Hours)
        for ($day = 1; $day <= 5; $day++) {
            $shiftDetails[] = ['shift_id' => 5, 'time_interval_id' => 4, 'day_of_week' => $day, 'created_at' => $now, 'updated_at' => $now];
        }

        // Part-Time Morning (Mon-Fri, Half Day Morning)
        for ($day = 1; $day <= 5; $day++) {
            $shiftDetails[] = ['shift_id' => 6, 'time_interval_id' => 5, 'day_of_week' => $day, 'created_at' => $now, 'updated_at' => $now];
        }

        // Part-Time Afternoon (Mon-Fri, Half Day Afternoon)
        for ($day = 1; $day <= 5; $day++) {
            $shiftDetails[] = ['shift_id' => 7, 'time_interval_id' => 6, 'day_of_week' => $day, 'created_at' => $now, 'updated_at' => $now];
        }

        DB::table('att_shiftdetail')->insert($shiftDetails);
    }

    private function createEmployees()
    {
        $this->info('👥 Creating employees...');
        
        $employees = [
            ['create_time' => Carbon::now(), 'status' => 1, 'emp_code' => 'EMP001', 'first_name' => 'John', 'last_name' => 'Smith', 'nickname' => 'John', 'gender' => 'M', 'birthday' => '1985-03-15', 'address' => '123 Main St, City Center', 'mobile' => '+1234567890', 'email' => 'john.smith@company.com', 'hire_date' => '2020-01-15', 'verify_mode' => 1, 'emp_type' => 1, 'enable_payroll' => true, 'is_active' => true, 'department_id' => 2, 'position_id' => 2, 'company_id' => 1, 'card_no' => 'C001'],
            ['create_time' => Carbon::now(), 'status' => 1, 'emp_code' => 'EMP002', 'first_name' => 'Sarah', 'last_name' => 'Johnson', 'nickname' => 'Sarah', 'gender' => 'F', 'birthday' => '1988-07-22', 'address' => '456 Oak Ave, Downtown', 'mobile' => '+1234567891', 'email' => 'sarah.johnson@company.com', 'hire_date' => '2019-03-20', 'verify_mode' => 1, 'emp_type' => 1, 'enable_payroll' => true, 'is_active' => true, 'department_id' => 1, 'position_id' => 4, 'company_id' => 1, 'card_no' => 'C002'],
            ['create_time' => Carbon::now(), 'status' => 1, 'emp_code' => 'EMP003', 'first_name' => 'Michael', 'last_name' => 'Davis', 'nickname' => 'Mike', 'gender' => 'M', 'birthday' => '1990-11-08', 'address' => '789 Pine St, Westside', 'mobile' => '+1234567892', 'email' => 'michael.davis@company.com', 'hire_date' => '2021-06-10', 'verify_mode' => 1, 'emp_type' => 1, 'enable_payroll' => true, 'is_active' => true, 'department_id' => 6, 'position_id' => 5, 'company_id' => 1, 'card_no' => 'C003'],
            ['create_time' => Carbon::now(), 'status' => 1, 'emp_code' => 'EMP004', 'first_name' => 'Emily', 'last_name' => 'Brown', 'nickname' => 'Em', 'gender' => 'F', 'birthday' => '1992-02-14', 'address' => '321 Elm Dr, Eastside', 'mobile' => '+1234567893', 'email' => 'emily.brown@company.com', 'hire_date' => '2022-01-05', 'verify_mode' => 1, 'emp_type' => 1, 'enable_payroll' => true, 'is_active' => true, 'department_id' => 6, 'position_id' => 6, 'company_id' => 1, 'card_no' => 'C004'],
            ['create_time' => Carbon::now(), 'status' => 1, 'emp_code' => 'EMP005', 'first_name' => 'David', 'last_name' => 'Wilson', 'nickname' => 'Dave', 'gender' => 'M', 'birthday' => '1987-09-30', 'address' => '654 Maple Ln, Southside', 'mobile' => '+1234567894', 'email' => 'david.wilson@company.com', 'hire_date' => '2020-08-12', 'verify_mode' => 1, 'emp_type' => 1, 'enable_payroll' => true, 'is_active' => true, 'department_id' => 6, 'position_id' => 7, 'company_id' => 1, 'card_no' => 'C005'],
            ['create_time' => Carbon::now(), 'status' => 1, 'emp_code' => 'EMP006', 'first_name' => 'Jennifer', 'last_name' => 'Garcia', 'nickname' => 'Jen', 'gender' => 'F', 'birthday' => '1989-12-05', 'address' => '987 Cedar Rd, Northside', 'mobile' => '+1234567895', 'email' => 'jennifer.garcia@company.com', 'hire_date' => '2021-02-28', 'verify_mode' => 1, 'emp_type' => 1, 'enable_payroll' => true, 'is_active' => true, 'department_id' => 7, 'position_id' => 8, 'company_id' => 1, 'card_no' => 'C006'],
            ['create_time' => Carbon::now(), 'status' => 1, 'emp_code' => 'EMP007', 'first_name' => 'Robert', 'last_name' => 'Martinez', 'nickname' => 'Rob', 'gender' => 'M', 'birthday' => '1984-06-18', 'address' => '147 Birch St, Central', 'mobile' => '+1234567896', 'email' => 'robert.martinez@company.com', 'hire_date' => '2019-11-15', 'verify_mode' => 1, 'emp_type' => 1, 'enable_payroll' => true, 'is_active' => true, 'department_id' => 3, 'position_id' => 3, 'company_id' => 1, 'card_no' => 'C007'],
            ['create_time' => Carbon::now(), 'status' => 1, 'emp_code' => 'EMP008', 'first_name' => 'Lisa', 'last_name' => 'Anderson', 'nickname' => 'Lisa', 'gender' => 'F', 'birthday' => '1991-04-25', 'address' => '258 Spruce Ave, Westpark', 'mobile' => '+1234567897', 'email' => 'lisa.anderson@company.com', 'hire_date' => '2022-03-10', 'verify_mode' => 1, 'emp_type' => 1, 'enable_payroll' => true, 'is_active' => true, 'department_id' => 8, 'position_id' => 12, 'company_id' => 1, 'card_no' => 'C008'],
            ['create_time' => Carbon::now(), 'status' => 1, 'emp_code' => 'EMP009', 'first_name' => 'James', 'last_name' => 'Taylor', 'nickname' => 'Jim', 'gender' => 'M', 'birthday' => '1986-01-12', 'address' => '369 Willow Dr, Eastpark', 'mobile' => '+1234567898', 'email' => 'james.taylor@company.com', 'hire_date' => '2020-05-20', 'verify_mode' => 1, 'emp_type' => 1, 'enable_payroll' => true, 'is_active' => true, 'department_id' => 5, 'position_id' => 10, 'company_id' => 1, 'card_no' => 'C009'],
            ['create_time' => Carbon::now(), 'status' => 1, 'emp_code' => 'EMP010', 'first_name' => 'Maria', 'last_name' => 'Rodriguez', 'nickname' => 'Maria', 'gender' => 'F', 'birthday' => '1993-08-03', 'address' => '741 Poplar St, Riverside', 'mobile' => '+1234567899', 'email' => 'maria.rodriguez@company.com', 'hire_date' => '2022-07-15', 'verify_mode' => 1, 'emp_type' => 1, 'enable_payroll' => true, 'is_active' => true, 'department_id' => 9, 'position_id' => 11, 'company_id' => 1, 'card_no' => 'C010'],
            ['create_time' => Carbon::now(), 'status' => 1, 'emp_code' => 'EMP011', 'first_name' => 'Daniel', 'last_name' => 'Lee', 'nickname' => 'Dan', 'gender' => 'M', 'birthday' => '1988-10-20', 'address' => '852 Ash Blvd, Hillside', 'mobile' => '+1234567800', 'email' => 'daniel.lee@company.com', 'hire_date' => '2021-09-05', 'verify_mode' => 1, 'emp_type' => 1, 'enable_payroll' => true, 'is_active' => true, 'department_id' => 10, 'position_id' => 11, 'company_id' => 1, 'card_no' => 'C011'],
            ['create_time' => Carbon::now(), 'status' => 1, 'emp_code' => 'EMP012', 'first_name' => 'Amanda', 'last_name' => 'White', 'nickname' => 'Mandy', 'gender' => 'F', 'birthday' => '1990-05-16', 'address' => '963 Walnut Way, Lakeside', 'mobile' => '+1234567801', 'email' => 'amanda.white@company.com', 'hire_date' => '2021-12-01', 'verify_mode' => 1, 'emp_type' => 1, 'enable_payroll' => true, 'is_active' => true, 'department_id' => 4, 'position_id' => 9, 'company_id' => 1, 'card_no' => 'C012'],
        ];

        DB::table('personnel_employee')->insert($employees);
    }

    private function createEmployeeAreaAssignments()
    {
        $this->info('🏢 Assigning employees to areas...');
        
        $assignments = [
            // Tech team in Development Lab
            ['employee_id' => 1, 'area_id' => 5], ['employee_id' => 3, 'area_id' => 5], 
            ['employee_id' => 4, 'area_id' => 5], ['employee_id' => 5, 'area_id' => 5],
            // HR team in HR Office
            ['employee_id' => 2, 'area_id' => 6], ['employee_id' => 8, 'area_id' => 6], ['employee_id' => 12, 'area_id' => 6],
            // Infrastructure team on Third Floor
            ['employee_id' => 6, 'area_id' => 4],
            // Finance on Second Floor
            ['employee_id' => 7, 'area_id' => 3],
            // Sales team on Sales Floor
            ['employee_id' => 9, 'area_id' => 7], ['employee_id' => 10, 'area_id' => 7], ['employee_id' => 11, 'area_id' => 7]
        ];

        DB::table('personnel_employee_area')->insert($assignments);
    }

    private function createAttendanceEmployeeRecords()
    {
        $this->info('📊 Creating attendance employee records...');
        
        $attendanceEmployees = [];
        for ($i = 1; $i <= 12; $i++) {
            $attendanceEmployees[] = [
                'create_time' => Carbon::now(),
                'status' => 1,
                'enable_attendance' => true,
                'enable_schedule' => true,
                'enable_overtime' => true,
                'enable_holiday' => true,
                'enable_compensatory' => false,
                'emp_id' => $i
            ];
        }

        DB::table('att_attemployee')->insert($attendanceEmployees);
    }

    private function createEmployeeSchedules()
    {
        $this->info('📅 Creating employee schedules...');
        
        $now = Carbon::now();
        $schedules = [
            ['slug' => 'john-smith-q2-2025', 'start_date' => '2025-06-22', 'end_date' => '2025-09-22', 'employee_id' => 1, 'shift_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'sarah-johnson-q2-2025', 'start_date' => '2025-06-22', 'end_date' => '2025-09-22', 'employee_id' => 2, 'shift_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'michael-davis-q2-2025', 'start_date' => '2025-06-22', 'end_date' => '2025-09-22', 'employee_id' => 3, 'shift_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'emily-brown-q2-2025', 'start_date' => '2025-06-22', 'end_date' => '2025-09-22', 'employee_id' => 4, 'shift_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'david-wilson-q2-2025', 'start_date' => '2025-06-22', 'end_date' => '2025-09-22', 'employee_id' => 5, 'shift_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'jennifer-garcia-q2-2025', 'start_date' => '2025-06-22', 'end_date' => '2025-09-22', 'employee_id' => 6, 'shift_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'robert-martinez-q2-2025', 'start_date' => '2025-06-22', 'end_date' => '2025-09-22', 'employee_id' => 7, 'shift_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'lisa-anderson-q2-2025', 'start_date' => '2025-06-22', 'end_date' => '2025-09-22', 'employee_id' => 8, 'shift_id' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'james-taylor-q2-2025', 'start_date' => '2025-06-22', 'end_date' => '2025-09-22', 'employee_id' => 9, 'shift_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'maria-rodriguez-q2-2025', 'start_date' => '2025-06-22', 'end_date' => '2025-09-22', 'employee_id' => 10, 'shift_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'daniel-lee-q2-2025', 'start_date' => '2025-06-22', 'end_date' => '2025-09-22', 'employee_id' => 11, 'shift_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'amanda-white-q2-2025', 'start_date' => '2025-06-22', 'end_date' => '2025-09-22', 'employee_id' => 12, 'shift_id' => 5, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('att_attschedule')->insert($schedules);
    }

    private function createAttendanceTransactions()
    {
        $this->info('🕐 Creating attendance transactions...');
        
        $transactions = [
            // John Smith (EMP001) - Recent days
            ['emp_code' => 'EMP001', 'punch_time' => '2025-06-20 08:58:00', 'punch_state' => '0', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM001', 'terminal_alias' => 'Main Entrance', 'area_alias' => 'DEV-LAB', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567890', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP001', 'punch_time' => '2025-06-20 17:05:00', 'punch_state' => '1', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM001', 'terminal_alias' => 'Main Entrance', 'area_alias' => 'DEV-LAB', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567890', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP001', 'punch_time' => '2025-06-21 09:02:00', 'punch_state' => '0', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM001', 'terminal_alias' => 'Main Entrance', 'area_alias' => 'DEV-LAB', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567890', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP001', 'punch_time' => '2025-06-21 17:00:00', 'punch_state' => '1', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM001', 'terminal_alias' => 'Main Entrance', 'area_alias' => 'DEV-LAB', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567890', 'source' => 1, 'purpose' => 0],

            // Sarah Johnson (EMP002) - HR Manager
            ['emp_code' => 'EMP002', 'punch_time' => '2025-06-20 08:55:00', 'punch_state' => '0', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM002', 'terminal_alias' => 'HR Office', 'area_alias' => 'HR-OFFICE', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567891', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP002', 'punch_time' => '2025-06-20 17:10:00', 'punch_state' => '1', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM002', 'terminal_alias' => 'HR Office', 'area_alias' => 'HR-OFFICE', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567891', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP002', 'punch_time' => '2025-06-21 09:00:00', 'punch_state' => '0', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM002', 'terminal_alias' => 'HR Office', 'area_alias' => 'HR-OFFICE', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567891', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP002', 'punch_time' => '2025-06-21 17:15:00', 'punch_state' => '1', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM002', 'terminal_alias' => 'HR Office', 'area_alias' => 'HR-OFFICE', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567891', 'source' => 1, 'purpose' => 0],

            // David Wilson (EMP005) - Early Bird Shift
            ['emp_code' => 'EMP005', 'punch_time' => '2025-06-20 05:58:00', 'punch_state' => '0', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM001', 'terminal_alias' => 'Main Entrance', 'area_alias' => 'DEV-LAB', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567894', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP005', 'punch_time' => '2025-06-20 14:05:00', 'punch_state' => '1', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM001', 'terminal_alias' => 'Main Entrance', 'area_alias' => 'DEV-LAB', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567894', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP005', 'punch_time' => '2025-06-21 06:00:00', 'punch_state' => '0', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM001', 'terminal_alias' => 'Main Entrance', 'area_alias' => 'DEV-LAB', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567894', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP005', 'punch_time' => '2025-06-21 14:02:00', 'punch_state' => '1', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM001', 'terminal_alias' => 'Main Entrance', 'area_alias' => 'DEV-LAB', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567894', 'source' => 1, 'purpose' => 0],

            // Emily Brown (EMP004) - Senior Developer
            ['emp_code' => 'EMP004', 'punch_time' => '2025-06-20 09:15:00', 'punch_state' => '0', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM001', 'terminal_alias' => 'Main Entrance', 'area_alias' => 'DEV-LAB', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567893', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP004', 'punch_time' => '2025-06-20 18:00:00', 'punch_state' => '1', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM001', 'terminal_alias' => 'Main Entrance', 'area_alias' => 'DEV-LAB', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567893', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP004', 'punch_time' => '2025-06-21 08:45:00', 'punch_state' => '0', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM001', 'terminal_alias' => 'Main Entrance', 'area_alias' => 'DEV-LAB', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567893', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP004', 'punch_time' => '2025-06-21 17:30:00', 'punch_state' => '1', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM001', 'terminal_alias' => 'Main Entrance', 'area_alias' => 'DEV-LAB', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567893', 'source' => 1, 'purpose' => 0],

            // Jennifer Garcia (EMP006) - System Administrator
            ['emp_code' => 'EMP006', 'punch_time' => '2025-06-20 08:30:00', 'punch_state' => '0', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM003', 'terminal_alias' => 'Server Room', 'area_alias' => 'FLOOR3', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567895', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP006', 'punch_time' => '2025-06-20 17:45:00', 'punch_state' => '1', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM003', 'terminal_alias' => 'Server Room', 'area_alias' => 'FLOOR3', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567895', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP006', 'punch_time' => '2025-06-21 08:35:00', 'punch_state' => '0', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM003', 'terminal_alias' => 'Server Room', 'area_alias' => 'FLOOR3', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567895', 'source' => 1, 'purpose' => 0],
            ['emp_code' => 'EMP006', 'punch_time' => '2025-06-21 18:00:00', 'punch_state' => '1', 'verify_type' => 1, 'work_code' => '', 'terminal_sn' => 'TERM003', 'terminal_alias' => 'Server Room', 'area_alias' => 'FLOOR3', 'longitude' => -74.0060, 'latitude' => 40.7128, 'gps_location' => 'New York Office', 'mobile' => '1234567895', 'source' => 1, 'purpose' => 0],
        ];

        DB::table('iclock_transaction')->insert($transactions);
    }

    private function displaySummary()
    {
        $this->info('📊 Test Data Summary:');
        $this->table(['Entity', 'Records Created'], [
            ['Departments', DB::table('personnel_department')->count()],
            ['Positions', DB::table('personnel_position')->count()],
            ['Areas', DB::table('personnel_area')->count()],
            ['Employees', DB::table('personnel_employee')->count()],
            ['Time Intervals', DB::table('att_timeinterval')->count()],
            ['Shifts', DB::table('att_attshift')->count()],
            ['Shift Details', DB::table('att_shiftdetail')->count()],
            ['Employee Schedules', DB::table('att_attschedule')->count()],
            ['Employee Area Assignments', DB::table('personnel_employee_area')->count()],
            ['Attendance Employee Records', DB::table('att_attemployee')->count()],
            ['Attendance Transactions', DB::table('iclock_transaction')->count()],
        ]);
        
        $this->info('🎉 Your attendance management system is now ready for testing!');
        $this->info('💡 Use --clear flag to remove existing data before adding new test data.');
    }
}
