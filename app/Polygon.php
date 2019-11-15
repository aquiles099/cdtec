<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Polygon extends Model
{
    protected $fillable = [
        'lat', 'lng','type','id_zone'
    ];
}
