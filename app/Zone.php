<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
use App\Type;
use App\Polygon;
class Zone extends Model
{
    protected $fillable = [
        'name', 'description','latitude', 
        'longitude','type', 'kc','theoreticalFlow',
        'unitTheoreticalFlow','efficiency',
        'humidityRetention','max','min','criticalPoint1','criticalPoint2',
        'id_farm','id_pump_system','id_wiseconn'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
    public function types()
    {
        return $this->hasMany(Type::class,'id_zone','id');
    }
    public function polygons()
    {
        return $this->hasMany(Polygon::class,'id_zone','id');
    }
}
