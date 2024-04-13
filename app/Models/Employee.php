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


    

}
