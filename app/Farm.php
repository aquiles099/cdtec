<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Account;
class Farm extends Model
{
    protected $fillable = [
        'name', 'description','latitude', 'longitude','postalAddress', 'timeZone','webhook','id_account','id_wiseconn'
    ];
  	protected $with = ['account'];
    public function account()
    {
        return $this->hasOne(Account::class,'id','id_account');
    }
}
