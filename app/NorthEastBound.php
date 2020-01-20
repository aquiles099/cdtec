<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NorthEastBound extends Model
{
    protected $fillable = ["id_zone","lat","lng"];
    protected $hidden   = [
        'id','id_zone',
        'created_at',
        'updated_at',
    ];
}
