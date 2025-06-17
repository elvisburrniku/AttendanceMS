
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = "personnel_department";
    
    public $timestamps = false;
    
    protected $fillable = [
        'dept_code',
        'dept_name', 
        'is_default',
        'parent_dept_id',
        'dept_manager_id',
        'company_id'
    ];

    public function parentDepartment()
    {
        return $this->belongsTo(Department::class, 'parent_dept_id');
    }

    public function childDepartments()
    {
        return $this->hasMany(Department::class, 'parent_dept_id');
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'dept_manager_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }
}
