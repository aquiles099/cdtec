<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoppedByUser extends Model
{
    protected $fillable = [
        'name', 'id_real_irrigation'
    ];
}
