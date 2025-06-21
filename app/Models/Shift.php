<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'att_attshift';

    protected $fillable = [
        'alias',
        'cycle_unit',
        'shift_cycle',
        'work_weekend',
        'weekend_type',
        'work_day_off',
        'day_off_type',
        'auto_shift',
        'enable_ot_rule',
        'frequency',
        'ot_rule',
        'company_id'
    ];

    protected $casts = [
        'work_weekend' => 'boolean',
        'work_day_off' => 'boolean',
        'enable_ot_rule' => 'boolean'
    ];

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'shift_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'shift_id');
    }

    public function employees()
    {
        return $this->hasManyThrough(Employee::class, Schedule::class, 'shift_id', 'id', 'id', 'employee_id');
    }

    public function timeIntervals()
    {
        return $this->belongsToMany(TimeInterval::class, 'att_shiftdetail', 'shift_id', 'time_interval_id')
                    ->withPivot('work_type', 'day_of_week')
                    ->withTimestamps();
    }

    public function getWorkingHoursAttribute()
    {
        return $this->timeIntervals->sum(function($interval) {
            return $interval->duration / 60; // Convert minutes to hours
        });
    }

    public function scopeActive($query)
    {
        return $query->whereHas('schedules', function($q) {
            $q->where('end_date', '>=', now()->format('Y-m-d'));
        });
    }
}
