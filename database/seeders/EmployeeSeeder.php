<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Area;
use Hash;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $departments = Department::all();
        $positions = Position::all();
        $areas = Area::all();

        $employees = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@company.com',
                'card_no' => 'EMP001',
                'nickname' => 'John',
                'department' => 'IT',
                'position' => 'MGR',
                'area' => 'MAIN'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@company.com',
                'card_no' => 'EMP002',
                'nickname' => 'Sarah',
                'department' => 'HR',
                'position' => 'MGR',
                'area' => 'MAIN'
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike.wilson@company.com',
                'card_no' => 'EMP003',
                'nickname' => 'Mike',
                'department' => 'IT',
                'position' => 'EMP',
                'area' => 'MAIN'
            ],
            [
                'name' => 'Lisa Davis',
                'email' => 'lisa.davis@company.com',
                'card_no' => 'EMP004',
                'nickname' => 'Lisa',
                'department' => 'FIN',
                'position' => 'SUPV',
                'area' => 'MAIN'
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@company.com',
                'card_no' => 'EMP005',
                'nickname' => 'David',
                'department' => 'MKT',
                'position' => 'EMP',
                'area' => 'BRANCH1'
            ],
            [
                'name' => 'Emily Chen',
                'email' => 'emily.chen@company.com',
                'card_no' => 'EMP006',
                'nickname' => 'Emily',
                'department' => 'IT',
                'position' => 'EMP',
                'area' => 'REMOTE'
            ],
            [
                'name' => 'Robert Martinez',
                'email' => 'robert.martinez@company.com',
                'card_no' => 'EMP007',
                'nickname' => 'Robert',
                'department' => 'HR',
                'position' => 'EMP',
                'area' => 'MAIN'
            ],
            [
                'name' => 'Jennifer Lee',
                'email' => 'jennifer.lee@company.com',
                'card_no' => 'EMP008',
                'nickname' => 'Jennifer',
                'department' => 'FIN',
                'position' => 'EMP',
                'area' => 'MAIN'
            ],
            [
                'name' => 'Alex Thompson',
                'email' => 'alex.thompson@company.com',
                'card_no' => 'EMP009',
                'nickname' => 'Alex',
                'department' => 'MKT',
                'position' => 'SUPV',
                'area' => 'BRANCH1'
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@company.com',
                'card_no' => 'EMP010',
                'nickname' => 'Maria',
                'department' => 'IT',
                'position' => 'INTERN',
                'area' => 'MAIN'
            ]
        ];

        foreach ($employees as $empData) {
            // Get department, position, and area IDs
            $department = $departments->where('dept_code', $empData['department'])->first();
            $position = $positions->where('position_code', $empData['position'])->first();
            $area = $areas->where('area_code', $empData['area'])->first();

            // Create employee record (without user account for now)
            Employee::create([
                'emp_code' => $empData['card_no'],
                'card_no' => $empData['card_no'],
                'nickname' => $empData['nickname'],
                'first_name' => explode(' ', $empData['name'])[0],
                'last_name' => explode(' ', $empData['name'])[1] ?? '',
                'email' => $empData['email'],
                'mobile' => '555-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'address' => rand(100, 999) . ' Main Street, City, State',
                'hire_date' => now()->subDays(rand(30, 365)),
                'department_id' => $department->id,
                'position_id' => $position->id,
                'company_id' => 1,
                'is_active' => true,
                'status' => 1,
                'emp_type' => 1,
                'verify_mode' => 1,
                'app_status' => 1,
                'enable_payroll' => 1,
                'create_time' => now(),
                'change_time' => now(),
                'update_time' => now(),
            ]);
        }
    }
}