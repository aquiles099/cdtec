<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
use App\Zone;
use App\SensorTypeZones;
class SensorType extends Model
{
    protected $fillable = [        
        'name', 'id_farm', 'group'
    ];
    protected $with = ['farm','zones'];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
    public function zones()
    {       
       return $this->hasMany(SensorTypeZones::class,'id_sensor_type');
    }
}
