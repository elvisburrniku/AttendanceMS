<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;
    public const LEAVE_WITHOUT_PAYMENT = 1;
    public const SICK_LEAVES = 2;
    public const PAID_LEAVE = 3;

    public $timestamps = false;

    protected $guarded = [];
}
