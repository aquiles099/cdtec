<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hydraulic extends Model
{
    protected $fillable = [
        'name','type','id_physical_connection','id_node', 'id_farm'
    ];
}
