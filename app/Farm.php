<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    protected $fillable = [
        'name', 'description','latitude', 'longitude','postalAddress', 'timeZone','webhook'
    ];
}
