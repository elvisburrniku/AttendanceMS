<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'personnel_employee';
    
    public function getRouteKeyName()
    {
        return 'name';
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'personnel_employee_area');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function check()
    {
        return $this->hasMany(Check::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'emp_id');
    }

    public function comments()
    {
        return $this->hasMany(AttendanceComment::class, 'employee_id');
    }

    public function latetime()
    {
        return $this->hasMany(Latetime::class);
    }
    public function leave()
    {
        return $this->hasMany(Leave::class);
    }
    public function overtime()
    {
        return $this->hasMany(Overtime::class);
    }
    public function schedules()
    {
        return $this->hasMany('App\Models\Schedule', 'employee_id');
    }

    public function overtimes()
    {
        return $this->hasMany(EmployeeOvertime::class, 'employee_id');
    }

    public function getTotalOvertimeByDate($date)
    {
        $check_attd = $this->overtimes
            ->filter(function ($overtime) use ($date) {
                return date('Y-m-d', strtotime($overtime->date)) === $date;
            })
            ->first();
        if(!$check_attd) {
            $check_attd = new \stdClass();
            $attendances = $this->attendances->filter(function ($attendance) use ($date) {
                return date('Y-m-d', strtotime($attendance->punch_time)) === $date;
            });
            $check_attd->total_hr = Attendance::calculateOvertime($attendances);
            $check_attd->approved = false;
            $check_attd->date = $date;
        }

        return $check_attd;
    }
}
