<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduledFertigation extends Model
{
    protected $fillable = [        
        'initTime', 'endTime','proportion','preirrigation','postirrigation','id_fertilizer','id_volume','id_irrigation'
    ];
}
