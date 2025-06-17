<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Position;
use App\Models\Area;
use Hash;
use Spatie\Permission\Traits\HasRoles;
use DB;
use App\Models\LeaveType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        
        $role = Role::firstOrCreate([
            'slug' => 'admin',
            'name' => 'Administrator',
        ]);
        
        $user->roles()->sync($role->id);

        $leaveTypes = [
            'LWOP',
            'Sick leave',
            'Paid Leave'
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create([
                'name' => $leaveType
            ]);
        }

        // Seed Departments
        $departments = [
            ['dept_code' => 'HR', 'dept_name' => 'Human Resources', 'is_default' => true, 'company_id' => 1],
            ['dept_code' => 'IT', 'dept_name' => 'Information Technology', 'is_default' => false, 'company_id' => 1],
            ['dept_code' => 'FIN', 'dept_name' => 'Finance', 'is_default' => false, 'company_id' => 1],
            ['dept_code' => 'MKT', 'dept_name' => 'Marketing', 'is_default' => false, 'company_id' => 1],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // Seed Positions
        $positions = [
            ['position_code' => 'MGR', 'position_name' => 'Manager', 'is_default' => false, 'company_id' => 1],
            ['position_code' => 'EMP', 'position_name' => 'Employee', 'is_default' => true, 'company_id' => 1],
            ['position_code' => 'SUPV', 'position_name' => 'Supervisor', 'is_default' => false, 'company_id' => 1],
            ['position_code' => 'INTERN', 'position_name' => 'Intern', 'is_default' => false, 'company_id' => 1],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }

        // Seed Areas
        $areas = [
            ['area_code' => 'MAIN', 'area_name' => 'Main Office', 'is_default' => true, 'company_id' => 1],
            ['area_code' => 'BRANCH1', 'area_name' => 'Branch Office 1', 'is_default' => false, 'company_id' => 1],
            ['area_code' => 'REMOTE', 'area_name' => 'Remote Work', 'is_default' => false, 'company_id' => 1],
        ];

        foreach ($areas as $area) {
            Area::create($area);
        }
    }
}
