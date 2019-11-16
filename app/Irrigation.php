<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Irrigation extends Model
{
    protected $fillable = [        
        'initTime', 'endTime','status','sentToNetwork','scheduledType','groupingName','id_pump_system','id_zone','id_volume'
    ];
}
