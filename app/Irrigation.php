<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
use App\Zone;
use App\Volume;
use App\Pump_system;
class Irrigation extends Model
{
    protected $fillable = [        
        'initTime', 'endTime','status','sentToNetwork','scheduledType','groupingName','action','id_pump_system','id_zone','id_volume','id_farm'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
    public function zone()
    {
        return $this->hasOne(Zone::class,'id','id_zone');
    }
    public function volume()
    {
        return $this->hasOne(Volume::class,'id','id_volume');
    }
    public function pumpSystem()
    {
        return $this->hasOne(Pump_system::class,'id','id_pump_system');
    }
}
