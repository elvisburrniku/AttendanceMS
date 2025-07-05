<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Timetable;
use App\Models\Employee;

class ScheduleSeeder extends Seeder
{
    public function run()
    {
        // Create schedules
        $schedules = [
            [
                'schedule_name' => 'Regular Business Hours',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'break_start' => '12:00:00',
                'break_end' => '13:00:00',
                'is_default' => true,
                'company_id' => 1
            ],
            [
                'schedule_name' => 'Early Shift',
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'break_start' => '12:00:00',
                'break_end' => '13:00:00',
                'is_default' => false,
                'company_id' => 1
            ],
            [
                'schedule_name' => 'Late Shift',
                'start_time' => '10:00:00',
                'end_time' => '18:00:00',
                'break_start' => '13:00:00',
                'break_end' => '14:00:00',
                'is_default' => false,
                'company_id' => 1
            ],
            [
                'schedule_name' => 'Part Time',
                'start_time' => '09:00:00',
                'end_time' => '13:00:00',
                'break_start' => null,
                'break_end' => null,
                'is_default' => false,
                'company_id' => 1
            ]
        ];

        foreach ($schedules as $schedule) {
            Schedule::create($schedule);
        }

        // Create shifts for employees
        $employees = Employee::all();
        $scheduleIds = Schedule::pluck('id')->toArray();

        foreach ($employees as $employee) {
            // Assign random schedule to each employee
            $scheduleId = $scheduleIds[array_rand($scheduleIds)];
            
            Shift::create([
                'emp_id' => $employee->id,
                'schedule_id' => $scheduleId,
                'start_date' => now()->subDays(30),
                'end_date' => null,
                'is_active' => true,
                'company_id' => 1
            ]);
        }

        // Create timetables for the current week
        $employees = Employee::with('shift.schedule')->get();
        
        foreach ($employees as $employee) {
            if ($employee->shift && $employee->shift->schedule) {
                for ($i = 0; $i < 7; $i++) {
                    $date = now()->startOfWeek()->addDays($i);
                    
                    // Skip weekends for regular schedules
                    if ($date->isWeekend() && $employee->shift->schedule->schedule_name === 'Regular Business Hours') {
                        continue;
                    }
                    
                    Timetable::create([
                        'emp_id' => $employee->id,
                        'date' => $date->format('Y-m-d'),
                        'schedule_id' => $employee->shift->schedule_id,
                        'start_time' => $employee->shift->schedule->start_time,
                        'end_time' => $employee->shift->schedule->end_time,
                        'break_start' => $employee->shift->schedule->break_start,
                        'break_end' => $employee->shift->schedule->break_end,
                        'is_holiday' => false,
                        'company_id' => 1
                    ]);
                }
            }
        }
    }
}