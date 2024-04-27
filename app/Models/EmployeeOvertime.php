<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOvertime extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'date', 'total_hr', 'approved'];
}
