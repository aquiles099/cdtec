<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
    protected $fillable = [
        'activationValue', 'date','id_farm','id_zone','id_irrigation'
    ];
}
