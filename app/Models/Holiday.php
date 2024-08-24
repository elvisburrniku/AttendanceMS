<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Holiday extends Model
{
    protected $fillable = [
        'type',
        'comment',
        'date',
        'observedOn',
    ];

    public function getDateAttribute($date)
    {
        return (new Carbon($date))->format('Y-m-d');
    }

    public function getObservedOnAttribute($date)
    {
        return (new Carbon($date))->format('Y-m-d');
    }

    public function setDateAttribute($date)
    {
        $this->attributes['date'] = (new Carbon($date));
    }

    public function setObservedOnAttribute($date)
    {
        $this->attributes['observedOn'] = (new Carbon($date));
    }
}
