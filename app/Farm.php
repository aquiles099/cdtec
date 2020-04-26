<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Account;
use App\Zone;
class Farm extends Model
{
    protected $fillable = [
        'name', 'description','latitude', 'longitude','postalAddress', 'timeZone','webhook','id_account','cloning_error','id_wiseconn'
    ];
  	protected $with = ['account'];
    public function account()
    {
        return $this->hasOne(Account::class,'id','id_account');
    }
    public function zones()
    {       
       return $this->hasMany(Zone::class,'id_farm');
    }
}
