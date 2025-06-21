<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'personnel_employee';

    protected $guarded = [];

    public $timestamps = false;

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

    public function position()
    {
        return $this->belongsTo(Position::class);
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
        return $this->hasMany(Leave::class, 'emp_id');
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

    public function attAttemployee()
    {
        return $this->hasOne(AttAttemployee::class, 'emp_id');
    }

    public function employeeProfile()
    {
        return $this->hasOne(PersonelEmployeeProfile::class, 'emp_id');
    }

    public function weekendOvertimes()
    {
        return $this->hasMany(EmployeeOvertime::class, 'employee_id')->where(function ($query) {
            $query->whereRaw("strftime('%w', date) = '6'") // Saturday
                  ->orWhereRaw("strftime('%w', date) = '0'"); // Sunday
        });
    }

    public function weekdayOvertimes()
    {
        return $this->hasMany(EmployeeOvertime::class, 'employee_id')->where(function ($query) {
            $query->whereRaw("strftime('%w', date) >= '1'") // Monday
                  ->whereRaw("strftime('%w', date) <= '5'"); // Friday
        });
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

    protected $fillable = [
        'emp_code',
        'emp_code_digit',
        'first_name',
        'last_name',
        'nickname',
        'card_no',
        'department_id',
        'position_id',
        'hire_date',
        'gender',
        'birthday',
        'emp_type',
        'contact_tel',
        'office_tel',
        'mobile',
        'national',
        'city',
        'address',
        'postcode',
        'email',
        'dev_privilege',
        'app_status',
        'app_role',
        'verify_mode',
        'status',
        'company_id',
        'enable_payroll',
        'is_active',
        'create_time',
        'change_time',
        'update_time',
        'attendance_sn',
        'password',
    ];

    protected $dates = [
        'hire_date',
        'birthday',
        'create_time',
        'change_time',
        'update_time',
    ];

        return $check_attd;
    }
}