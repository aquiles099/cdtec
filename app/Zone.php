<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
use App\Type;
use App\SouthWestBound;
use App\NorthEastBound;
class Zone extends Model
{
    protected $fillable = [
        'name', 'description','latitude', 
        'longitude','type', 'kc','theoreticalFlow',
        'unitTheoreticalFlow','efficiency',
        'humidityRetention','max','min','criticalPoint1','criticalPoint2',
        'id_farm','id_pump_system','id_wiseconn',
    ];
    protected $with = ['path','southWest','northEast'];
    protected $hidden   = [
        'id_zone',
        'id_bound',
        'created_at',
        'updated_at',
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
    public function path()
    {
        return $this->hasMany(Path::class,'id_zone');
    }
    public function southWest() {
        return $this->hasOne(SouthWestBound::class,'id_zone','id');
    }
    public function northEast() {
        return $this->hasOne(NorthEastBound::class,'id_zone','id');
    }
}
