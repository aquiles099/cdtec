<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Measure extends Model
{
    protected $fillable = [        
        'name', 'unit','lastData','lastDataDate','monitoringTime','sensorDepth','depthUnit','sensorType','readType','id_node','id_zone','id_farm','id_physical_connection'
    ];
}
