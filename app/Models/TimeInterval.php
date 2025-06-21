<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeInterval extends Model
{
    use HasFactory;

    protected $table = 'att_timeinterval';

    protected $fillable = [
        'alias',
        'use_mode',
        'in_time',
        'in_ahead_margin',
        'in_above_margin',
        'out_ahead_margin',
        'out_above_margin',
        'duration',
        'in_required',
        'out_required',
        'allow_late',
        'allow_leave_early',
        'work_day',
        'early_in',
        'min_early_in',
        'late_out',
        'min_late_out',
        'overtime_lv',
        'overtime_lv1',
        'overtime_lv2',
        'overtime_lv3',
        'multiple_punch',
        'available_interval_type',
        'available_interval',
        'work_time_duration',
        'func_key',
        'work_type',
        'day_change',
        'enable_early_in',
        'enable_late_out',
        'enable_overtime',
        'ot_rule',
        'color_setting',
        'enable_max_ot_limit',
        'max_ot_limit',
        'count_early_in_interval',
        'count_late_out_interval',
        'ot_pay_code_id',
        'overtime_policy',
        'company_id'
    ];

    protected $casts = [
        'in_time' => 'datetime:H:i',
        'day_change' => 'datetime:H:i',
        'enable_early_in' => 'boolean',
        'enable_late_out' => 'boolean',
        'enable_overtime' => 'boolean',
        'enable_max_ot_limit' => 'boolean',
        'count_early_in_interval' => 'boolean',
        'count_late_out_interval' => 'boolean'
    ];

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'time_interval_id');
    }

    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'att_shiftdetail', 'time_interval_id', 'shift_id')
                    ->withPivot('work_type', 'day_of_week')
                    ->withTimestamps();
    }

    public function getFormattedInTimeAttribute()
    {
        return $this->in_time ? $this->in_time->format('H:i') : null;
    }

    public function getDurationInHoursAttribute()
    {
        return round($this->duration / 60, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('use_mode', '>', 0);
    }

    public function scopeByWorkType($query, $workType)
    {
        return $query->where('work_type', $workType);
    }
}
