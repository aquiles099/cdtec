<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pump_system extends Model
{
    protected $fillable = [
        'name', 'allowPumpSelection'
    ];
}
