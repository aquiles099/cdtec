<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountSettings extends Model
{
    //
    protected $fillable = [
        'api_key', 'name','password','id_account','id_user'
    ];
  	protected $with = ['account'];
    public function account()
    {
        return $this->hasOne(Account::class,'id','id_account');
    }
}
