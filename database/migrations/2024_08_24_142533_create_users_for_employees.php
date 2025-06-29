<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;
use App\Models\Role;

class CreateUsersForEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if employees table exists before trying to query it
        if (!Schema::hasTable('personnel_employee')) {
            return;
        }
        
        $role = Role::updateOrCreate([
            'slug' => 'employee',
            'name' => 'Employee',
        ]);
        $employees = Employee::all();
        foreach($employees as $employee) {
            $user = User::updateOrCreate(['email' => strtolower($employee['first_name'])],[
                'name' => $employee['first_name']. ' ' . $employee['last_name'],
                'password' => Hash::make(strtolower($employee['first_name'])),
            ]);
            $employee->update(['email' => strtolower($employee['first_name'])]);
            $user->roles()->sync($role->id);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
