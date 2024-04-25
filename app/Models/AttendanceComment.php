<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceComment extends Model
{
    use HasFactory;

    protected $fillable = ['attendance_id', 'text', 'date', 'employee_id'];
}
