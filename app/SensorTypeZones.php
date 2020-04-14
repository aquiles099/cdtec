<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Zone;
class SensorTypeZones extends Model
{
    protected $fillable = ["id_sensor_type","id_zone"];
    protected $with = ['zone'];
    public function zone()
    {
        return $this->hasOne(Zone::class,'id','id_zone');
    }
    
}
