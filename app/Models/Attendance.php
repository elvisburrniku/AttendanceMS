<?php

namespace App\Models;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model

{
    protected $table = 'iclock_transaction';

    protected $guarded = [];

    const CREATED_AT = 'upload_time';
    const UPDATED_AT = null;

    public function employee()
    {
        return $this->belongsTo(Employee::class,'emp_id');
    }

    protected $fillable = [
        'emp_code',
        'punch_time',
        'punch_state',
        'verify_type',
        'work_code',
        'terminal_sn',
        'terminal_alias',
        'area_alias',
        'longitude',
        'latitude',
        'gps_location',
        'mobile',
        'source',
        'purpose',
        'crc',
        'is_attendance',
        'reserved',
        'upload_time',
        'sync_status',
        'sync_time',
        'is_mask',
        'temperature',
        'emp_id',
        'terminal_id',
        'company_code',
    ];

    protected $dates = [
        'punch_time',
        'upload_time',
        'sync_time',
    ];

    public static function calculateOvertime($attendances)
    {
        $checkin = $attendances->where('punch_state', '0')->sortBy('punch_time')->first();
        $checkout = $attendances->where('punch_state', '1')->sortBy('punch_time')->first();
        $break_in = $attendances->where('punch_state', '3')->sortBy('punch_time')->first();
        $break_out = $attendances->where('punch_state', '2')->sortBy('punch_time')->first();

        $total = 0;

        if($checkin) {
            if($checkout) {
                $total += \Carbon\Carbon::parse($checkin->punch_time)->diffInSeconds(\Carbon\Carbon::parse($checkout->punch_time));
            } else {
                $total += \Carbon\Carbon::parse($checkin->punch_time)->diffInSeconds(\Carbon\Carbon::parse($checkin->punch_time));
            }
        }



        if($total > (8 * 60 * 60)) {
            $total = round((($total / 60) / 60) - 8, 2);
            return $total == 0 ? 0 : $total;
        }

        return 0;
    }
}