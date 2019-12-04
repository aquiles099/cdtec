<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Farm;
use App\Zone;
use App\Node;
use App\PhysicalConnection;
class Hydraulic extends Model
{
    protected $fillable = [
        'name','type','id_physical_connection','id_node', 'id_farm','id_zone','id_wiseconn'
    ];
    public function farm()
    {
        return $this->hasOne(Farm::class,'id','id_farm');
    }
    public function zone()
    {
        return $this->hasOne(Zone::class,'id','id_zone');
    }
    public function node()
    {
        return $this->hasOne(Node::class,'id','id_node');
    }
    public function physicalConnection()
    {
        return $this->hasOne(PhysicalConnection::class,'id','id_physical_connection');
    }
}
