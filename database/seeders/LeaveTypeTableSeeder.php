<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
