<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
use App\Zone;
class RealIrrigation extends Model
{
    protected $fillable = [        
        'initTime', 'endTime','status','id_irrigation','id_farm','id_zone'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
    public function zone()
    {
        return $this->hasOne(Zone::class,'id','id_zone');
    }
}
