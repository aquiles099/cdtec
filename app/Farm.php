<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Account;
class Farm extends Model
{
    protected $fillable = [
        'name', 'description','latitude', 'longitude','postalAddress', 'timeZone','webhook','id_wiseconn'
    ];
    public function account()
    {
        return $this->hasOne(Account::class,'id_farm','id');
    }
}
