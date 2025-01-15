<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IclockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'emp_id', 'transaction_time', 'type',
    ];

    // Relationship: A transaction has many payload effect punches
    public function attPayloadEffectPunches()
    {
        return $this->hasMany(AttPayloadEffectPunch::class, 'trans_id');
    }

    // Relationship: A transaction belongs to an employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }
}
