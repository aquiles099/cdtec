<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'name', 'description','latitude', 
        'longitude','type', 'kc','theoreticalFlow',
        'unitTheoreticalFlow','efficiency',
        'humidityRetention','max','min','criticalPoint1','criticalPoint2',
        'id_farm','id_pump_system'
    ];
}
