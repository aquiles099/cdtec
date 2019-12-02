<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
class Account extends Model
{
    protected $fillable = [
        'name', 'rut','razonsocial', 'rutlegal','direccion', 'telefono','email','comentario', 'habilitar','id_farm','id_wiseconn'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
}
