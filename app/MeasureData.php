<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeasureData extends Model
{
    protected $fillable = [        
        'value','time','id_measure'
    ];
}
