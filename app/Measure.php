<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
use App\PhysicalConnection;
class Measure extends Model
{
    protected $fillable = [        
        'name', 'unit','lastData','lastDataDate','monitoringTime','sensorDepth','depthUnit','sensorType','readType','id_node','id_zone','id_farm','id_physical_connection'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
    public function physicalConnection()
    {
        return $this->hasOne(PhysicalConnection::class,'id','id_physical_connection');
    }
}
