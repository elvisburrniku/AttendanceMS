<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'att_attshift';

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}
