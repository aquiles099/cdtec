<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CloningErrors extends Model
{
    protected $fillable = [
        'elements', 'uri', 'id_wiseconn'
    ];
}
