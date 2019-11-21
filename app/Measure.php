<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
class Measure extends Model
{
    protected $fillable = [        
        'name', 'unit','lastData','lastDataDate','monitoringTime','sensorDepth','depthUnit','sensorType','readType','id_node','id_zone','id_farm','id_physical_connection'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
}
