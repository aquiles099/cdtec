<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhysicalConnection extends Model
{
    protected $fillable = [
        'expansionPort', 'expansionBoard','nodePort','type'
    ];
}
