<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
use App\User;
class FarmsUsers extends Model
{
    protected $fillable = [
        'id_farm',
        'id_user'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
    public function user()
    {
        return $this->hasOne(User::class,'id','id_user');
    }
}
