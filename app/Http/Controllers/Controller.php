<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use GuzzleHttp\Client;
use App\Farm;
use App\Account;
use App\Zone;
use App\Hydraulic;
use App\PhysicalConnection;
use App\Node;
use App\Pump_system;
use App\Measure;
use Carbon\Carbon;
use DateTime;
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
    protected function physicalConnectionCreate($measure){
        return PhysicalConnection::create([
            'expansionPort'=> isset($measure->physicalConnection)?$measure->physicalConnection->expansionPort:null,
            'expansionBoard'=> isset($measure->physicalConnection)?$measure->physicalConnection->expansionBoard:null,
            'nodePort'=> isset($measure->physicalConnection)?$measure->physicalConnection->nodePort:null
        ]);
    }
    protected function measureCreate($measure,$farm,$zone,$node,$newPhysicalConnection){
        return Measure::create([
            'name' => $measure->name,
            'unit' => isset($measure->unit)?isset($measure->unit):null,
            'lastData' =>isset($measure->lastData)?isset($measure->lastData):null,
            'lastDataDate'=> isset($measure->lastDataDate)?(Carbon::parse($measure->lastDataDate)):null,
            'monitoringTime'=> isset($measure->monitoringTime)?$measure->monitoringTime:null,
            'sensorDepth' => isset($measure->sensorDepth)?isset($measure->sensorDepth):null,
            'depthUnit'=> isset($measure->depthUnit)?isset($measure->depthUnit):null,
            'sensorType'=> isset($measure->sensorType)?isset($measure->sensorType):null,
            'readType'=> isset($measure->readType)?isset($measure->readType):null,
            'id_farm' => $farm->id,
            'id_zone' => isset($zone->id)?$zone->id:null,
            'id_physical_connection' => isset($newPhysicalConnection->id)?$newPhysicalConnection->id:null,
            'id_node' => isset($node->id)?$node->id:null,
            'id_wiseconn' => $measure->id
        ]); 
    }
    protected function test(){
        // dd([
        //     "initTime"=> Carbon::now(date_default_timezone_get())->toDateTimeString(),
        //     "endTime"=>Carbon::now(date_default_timezone_get())->addDays(30)->toDateTimeString(),
        //     "timezone"=>date_default_timezone_get()
        // ]);
        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);
        try{
            $farms=Farm::all();
            foreach ($farms as $key => $farm) {
                $measuresResponse = $this->requestWiseconn($client,'GET','/farms/'.$farm->id_wiseconn.'/measures');
                $measures=json_decode($measuresResponse->getBody()->getContents());
                foreach ($measures as $key => $measure) {
                    if(is_null(Measure::where("id_wiseconn",$measure->id)->first())){
                        $newPhysicalConnection =$this->physicalConnectionCreate($measure);
                        if(isset($measure->farmId)&&isset($measure->nodeId)&&isset($measure->zoneId)){
                            $zone=Zone::where("id_wiseconn",$measure->zoneId)->first();
                            $node=Node::where("id_wiseconn",$measure->nodeId)->first();
                            if($measure->farmId==$farm->id_wiseconn&&!is_null($zone)&&!is_null($node)){ 
                                $newmeasure =$this->measureCreate($measure,$farm,$zone,$node,$newPhysicalConnection); 
                            }
                        }else{
                            $newmeasure =$this->measureCreate($measure,$farm,null,null,$newPhysicalConnection); 
                        }
                        
                    }  
                }
                    
            }
        } catch (\Exception $e) {
            return response()->json(["message"=>"Error:" . $e->getMessage(),"linea"=>"Error:" . $e->getLine()]);
        } 
    }
}
