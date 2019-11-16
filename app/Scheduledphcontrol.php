<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scheduledphcontrol extends Model
{
    protected $fillable = [        
        'setPoint', 'preIrrigationSeconds','postIrrigationSeconds','phAverage','CEAverage','pHInjectorId','id_irrigation'
    ];
}
