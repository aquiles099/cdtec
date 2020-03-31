<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
class Account extends Model
{
    protected $fillable = [
        'name', 'rut','razonsocial', 'rutlegal','direccion', 'telefono','email','comentario', 'habilitar','id_wiseconn'
    ];
    public function farms()
   	{
       return $this->hasMany(Farm::class,'id_account');
   	}
}
