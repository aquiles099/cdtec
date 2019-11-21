<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
class Hydraulic extends Model
{
    protected $fillable = [
        'name','type','id_physical_connection','id_node', 'id_farm'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
}
