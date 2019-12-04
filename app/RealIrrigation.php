<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
use App\Zone;
use App\Pump_system;
use App\Irrigation;
class RealIrrigation extends Model
{
    protected $fillable = [        
    'initTime', 'endTime','status','id_irrigation','id_farm','id_zone','id_pump_system','id_wiseconn'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
    public function zone()
    {
        return $this->hasOne(Zone::class,'id','id_zone');
    }
    public function pumpSystem()
    {
        return $this->hasOne(Pump_system::class,'id','id_pump_system');
    }
    public function irrigations()
    {
        return $this->hasOne(Irrigation::class,'id','id_irrigation');
    }
}
