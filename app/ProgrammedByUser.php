<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProgrammedByUser extends Model
{
    protected $fillable = [        
        'name','id_irrigation'
    ];
}
