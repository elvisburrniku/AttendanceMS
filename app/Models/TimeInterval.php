<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeInterval extends Model
{
    use HasFactory;

    protected $table = 'att_timeinterval';

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}
