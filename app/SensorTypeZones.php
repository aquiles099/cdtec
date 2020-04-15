<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
use App\Zone;
class SensorTypeZones extends Model
{
    protected $fillable = ["id_sensor_type","id_farm","id_zone"];
    protected $with = ['farm','zone'];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
    public function zone()
    {
        return $this->hasOne(Zone::class,'id','id_zone');
    }
    
}
