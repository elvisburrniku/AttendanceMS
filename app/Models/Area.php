<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = "personnel_area";
    
    public $timestamps = false;
    
    protected $fillable = [
        'area_code',
        'area_name',
        'is_default',
        'parent_area_id',
        'company_id'
    ];

    public function parentArea()
    {
        return $this->belongsTo(Area::class, 'parent_area_id');
    }

    public function childAreas()
    {
        return $this->hasMany(Area::class, 'parent_area_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'personnel_employee_area', 'area_id', 'employee_id');
    }
}
