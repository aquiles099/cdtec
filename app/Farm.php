<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Account;
class Farm extends Model
{
    protected $fillable = [
        'name', 'description','latitude', 'longitude','postalAddress', 'timeZone','webhook'
    ];
    public function accounts()
    {
        return $this->hasMany(Account::class,'id_farm','id');
    }
}
