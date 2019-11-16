<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expansion extends Model
{
    protected $fillable = [
        'nodePort','expansionBoard','id_node'
    ];
}
