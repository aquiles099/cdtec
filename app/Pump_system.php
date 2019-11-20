<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pump_system extends Model
{
    protected $fillable = [
        'name', 'allowPumpSelection','id_farm'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
}
