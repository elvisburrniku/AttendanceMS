<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttPayloadEffectPunch extends Model
{
    use HasFactory;

    protected $fillable = [
        'trans_id', 'effect_time', 'status',
    ];

    // Relationship: An effect punch belongs to a transaction
    public function iclockTransaction()
    {
        return $this->belongsTo(IclockTransaction::class, 'trans_id');
    }
}
