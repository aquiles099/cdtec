<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhControl extends Model
{
    protected $fillable = [        
        'setPoint', 'preIrrigationSeconds','postIrrigationSeconds','phAverage','CEAverage','pHInjectorId','id_real_irrigation'
    ];
}
