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
        // $user= User::create([
        //     'name' => 'Admin',
        //     'email' => 'e.bibaj@kosmontefoods.com',
        //     'password' => Hash::make('kosmonte53++..'),
        // ]);
        // $role = Role::create([
        //     'slug' => 'admin',
        //     'name' => 'Adminstrator',
        // ]);
        // $user->roles()->sync($role->id);

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
