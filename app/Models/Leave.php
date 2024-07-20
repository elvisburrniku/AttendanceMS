<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class Leave extends Model
{
    protected $fillable = [
        'type',
        'emp_id',
        'comment',
        'start_date',
        'end_date',
        'leave_type_id',
        'total_days',
    ];

    protected $appends = ['full_name'];

    public function getStartDateAttribute($date)
    {
        if ($date) {
            return $this->attributes['start_date'] = (new Carbon($date))->toDateString();
        }

        return null;
    }

    public function getEndDateAttribute($date)
    {
        if ($date) {
            return $this->attributes['end_date'] = (new Carbon($date))->toDateString();
        }

        return null;
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['full_name'] = optional($this->employee)->first_name . ' ' .optional($this->employee)->last_name;
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Scope a query to only include upcoming sickleaves.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    /**
     * Scope a query to only include past sickleaves.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePast($query)
    {
        return $query->where('start_date', '<=', now());
    }

    public static function getUpcoming($leaveType)
    {
        return self::where('emp_id', auth()->id())
            ->where('leave_type_id', $leaveType)
            ->upcoming()
            ->get();
    }

    public static function getPast($leaveType)
    {
        return self::where('emp_id', auth()->id())
            ->where('leave_type_id', $leaveType)
            ->past()
            ->get();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function countLeaveDays()
    {
        $start_date = Carbon::parse($this->start_date);
        $end_date = Carbon::parse($this->end_date);
        $resolution = CarbonInterval::day();

        return $start_date->diffFiltered($resolution, function ($date) {
            return $date->isWeekday();
        }, $end_date);
    }
}
