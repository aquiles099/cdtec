<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RealIrrigation extends Model
{
    protected $fillable = [        
        'initTime', 'endTime','status','id_irrigation','id_farm'
    ];
}
