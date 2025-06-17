<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $table = "personnel_position";
    
    public $timestamps = false;
    
    protected $fillable = [
        'position_code',
        'position_name',
        'is_default',
        'parent_position_id',
        'company_id'
    ];

    public function parentPosition()
    {
        return $this->belongsTo(Position::class, 'parent_position_id');
    }

    public function childPositions()
    {
        return $this->hasMany(Position::class, 'parent_position_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id');
    }
}
