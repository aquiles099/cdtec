<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use GuzzleHttp\Client;
use App\Farm;
use App\Irrigation;
use App\Zone;
use App\Node;
use App\Pump_system;
use App\Volume;
use Carbon\Carbon;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected function requestWiseconn($client,$method,$uri){
        return $client->request($method, $uri, [
            'headers' => [
                'api_key' => '9Ev6ftyEbHhylMoKFaok',
                'Accept'     => 'application/json'
            ]
        ]);
    }
    protected function volumeCreate($irrigation){
        return Volume::create([
            'value'=> isset($irrigation->volume)?$irrigation->volume->value:null,
            'unitName'=> isset($irrigation->volume)?$irrigation->volume->unitName:null,
            'unitAbrev'=> isset($irrigation->volume)?$irrigation->volume->unitAbrev:null,
            'type'=>isset($irrigation->volume)?$irrigation->volume->type:null
        ]);
    }
    protected function irrigationCreate($irrigation,$farm,$zone,$volume,$pumpSystem){
        return Irrigation::create([
            'value' => isset($irrigation->value)?$irrigation->value:null,
            'initTime' => isset($irrigation->initTime)?$irrigation->initTime:null,
            'endTime' =>isset($irrigation->endTime)?$irrigation->endTime:null,
            'status'=> isset($irrigation->status)?$irrigation->status:null,
            'sentToNetwork' => isset($irrigation->sentToNetwork)?$irrigation->sentToNetwork:null,
            'scheduledType' => isset($irrigation->scheduledType)?$irrigation->scheduledType:null,
            'groupingName'=> isset($irrigation->groupingName)?$irrigation->groupingName:null,
            'action' =>isset($irrigation->action)?$irrigation->action:null,
            'id_pump_system'=> isset($pumpSystem->id)?$pumpSystem->id:null,
            'id_zone'=> isset($zone->id)?$zone->id:null,
            'id_volume'=> isset($volume->id)?$volume->id:null,
            'id_farm'=> $farm->id,
            'id_wiseconn' => $irrigation->id
        ]); 
    }
    protected function test(){
        // dd([
        //     "initTime"=> Carbon::now(date_default_timezone_get())->format('Y-m-d'),
        //     "endTime"=>Carbon::now(date_default_timezone_get())->addDays(25)->format('Y-m-d'),
        //     "timezone"=>date_default_timezone_get()
        // ]);
        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);
        $initTime=Carbon::now(date_default_timezone_get())->format('Y-m-d');
        $endTime=Carbon::now(date_default_timezone_get())->addDays(25)->format('Y-m-d');
        try{
            $farms=Farm::all();
            foreach ($farms as $key => $farm) {
                $irrigationsResponse = $this->requestWiseconn($client,'GET','/farms/'.$farm->id_wiseconn.'/irrigations/?endTime='.$endTime.'&initTime='.$initTime);
                $irrigations=json_decode($irrigationsResponse->getBody()->getContents());
                dd($irrigations);
                foreach ($irrigations as $key => $irrigation) {
                    if(is_null(Irrigation::where("id_wiseconn",$irrigation->id)->first())){
                        $newVolume =$this->volumeCreate($irrigation);
                        $newIrrigationConnection =$this->irrigationCreate($irrigation,$farm,$zone,$volume,$pumpSystem);                
                    }  
                }                    
            }
        } catch (\Exception $e) {
            return response()->json(["message"=>"Error:" . $e->getMessage(),"linea"=>"Error:" . $e->getLine()]);
        } 
    }
}
