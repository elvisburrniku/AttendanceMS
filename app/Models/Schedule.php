<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;
    
    protected $table = 'att_attschedule';
    
    protected $fillable = [
        'slug',
        'start_date',
        'end_date',
        'employee_id',
        'shift_id'
    ];

    protected $dates = [
        'start_date',
        'end_date'
    ];
    
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    // Legacy schedules table relationship
    public function employees()
    {
        return $this->belongsToMany('App\Models\Employee', 'schedule_employees', 'schedule_id', 'emp_id');
    }

    public function scopeActive($query)
    {
        return $query->where('end_date', '>=', now()->format('Y-m-d'));
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function isActive()
    {
        $today = now()->format('Y-m-d');
        return $this->start_date <= $today && $this->end_date >= $today;
    }
}
