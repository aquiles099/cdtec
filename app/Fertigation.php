<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fertigation extends Model
{
    protected $fillable = [        
        'initTime', 'endTime','dilution','soluble','id_fertilizer','id_real_irrigation','id_volume'
    ];
}
