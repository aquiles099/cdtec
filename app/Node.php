<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
class Node extends Model
{
    protected $fillable = [
        'name','lat', 'lng','nodeType','id_farm'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
}
