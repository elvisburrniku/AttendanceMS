<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
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
    }
}
