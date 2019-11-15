<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name', 'rut','razonsocial', 'rutlegal','direccion', 'telefono','email','comentario', 'habilitar','id_farm'
    ];
}
