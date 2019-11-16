<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fertilizer extends Model
{
    protected $fillable = [        
        'name','dilution','description'
    ];
}
