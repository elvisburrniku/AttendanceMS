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
}
