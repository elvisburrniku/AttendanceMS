<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $table = 'att_shiftdetail';

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function timeInterval()
    {
        return $this->belongsTo(TimeInterval::class);
    }
}
