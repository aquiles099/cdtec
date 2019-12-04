<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
class Alarm extends Model
{
    protected $fillable = [
        'activationValue', 'date','id_farm','id_zone','id_irrigation','id_real_irrigation','description','id_wiseconn'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
}
