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
    protected function farmCreate($farm){
        return Farm::create([
            'name' => $farm->name,
            'description' => $farm->description,
            'latitude' => $farm->latitude,
            'longitude' => $farm->longitude,
            'postalAddress' => $farm->postalAddress,
            'timeZone' => $farm->timeZone,
            'webhook' => $farm->webhook,
            'id_wiseconn' => $farm->id,
        ]);
    }
    protected function accountCreate($farm,$newFarm){
        return Account::create([
            'name' => $farm->account->name,
            'id_wiseconn' => $farm->account->id,
            'id_farm' => $newFarm->id
        ]);
    }
    protected function zoneCreate($zone,$newFarm){
        return Zone::create([
            'name' => $zone->name,
            'description' => $zone->description,
            'latitude' => $zone->latitude,
            'longitude' => $zone->longitude,
            'id_farm' => $newFarm->id,
            'kc' => $zone->kc,
            'theoreticalFlow' => $zone->theoreticalFlow,
            'unitTheoreticalFlow' => $zone->unitTheoreticalFlow,
            'efficiency' => $zone->efficiency,
            'humidityRetention' => $zone->humidityRetention,
            'max' => $zone->max,
            'min' => $zone->min,
            'criticalPoint1' => $zone->criticalPoint1,
            'criticalPoint2' => $zone->criticalPoint2,
            'id_pump_system' => $zone->pumpSystemId,
            'id_wiseconn' => $zone->id
        ]);
    }
    protected function nodeCreate($node,$newFarm){
        return Node::create([
            'name' => $node->name,
            'lat' => $node->lat,
            'lng' => $node->lng,
            'nodeType' => $node->nodeType,
            'id_farm' => $newFarm->id,
            'id_wiseconn' => $node->id
        ]);
    }
    protected function physicalConnectionCreate($hydraulic){
        return PhysicalConnection::create([
            'expansionPort'=> $hydraulic->physicalConnection->expansionPort,
            'expansionBoard'=> $hydraulic->physicalConnection->expansionBoard,
            'nodePort'=> $hydraulic->physicalConnection->nodePort
        ]);
    }
    protected function hydraulicCreate($hydraulic,$newFarm,$newZone,$newPhysicalConnection,$newNode){
        return Hydraulic::create([
            'name' => $hydraulic->name,
            'type' => $hydraulic->type,
            'id_farm' => $newFarm->id,
            'id_zone' => $newZone->id,
            'id_physical_connection' => $newPhysicalConnection->id,
            'id_node' => $newNode->id,
            'id_wiseconn' => $hydraulic->id
        ]); 
    }
    protected function pumpSystemCreate($pumpSystem,$newFarm){
        return Pump_system::create([
            'name'=>$pumpSystem->name, 
            'allowPumpSelection'=>$pumpSystem->allowPumpSelection,
            'id_farm'=>$newFarm->id,
            'id_wiseconn'=>$pumpSystem->id
        ]); 
    }
    public function test(){
        // dd([
        //     "initTime"=> Carbon::now(date_default_timezone_get())->toDateTimeString(),
        //     "endTime"=>Carbon::now(date_default_timezone_get())->addDays(30)->toDateTimeString(),
        //     "timezone"=>date_default_timezone_get()
        // ]);

        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);
        try {
            $farmsResponse =  $this->requestWiseconn($client,'GET','farms');
            $farmsResponse = 
            $farms=json_decode($farmsResponse->getBody()->getContents());
            foreach ($farms as $key => $farm) {
                if(is_null(Farm::where("id_wiseconn",$farm->id)->first())){
                    $newFarm= $this->farmCreate($farm);   
                    $newAccount= $this->accountCreate($farm,$newFarm);
                    try { 
                        $pumpSystemsResponse =  $this->requestWiseconn($client,'GET','/farms/'.$farm->id.'/pumpSystems');
                        $pumpSystems=json_decode($pumpSystemsResponse->getBody()->getContents());
                        foreach ($pumpSystems as $key => $pumpSystem) {
                            if(is_null(Pump_system::where("id_wiseconn",$pumpSystem->id)->first())){
                                $newPumpSystem= $this->pumpSystemCreate($pumpSystem,$newFarm);
                                try { 
                                    $zonesResponse =  $this->requestWiseconn($client,'GET','/farms/'.$farm->id.'/zones');
                                    $zones=json_decode($zonesResponse->getBody()->getContents());
                                    foreach ($zones as $key => $zone) {
                                            if(is_null(Farm::where("id_wiseconn",$zone->id)->first())){
                                                $newZone= $this->zoneCreate($zone,$newFarm);
                                                try{
                                                    $nodesResponse = $this->requestWiseconn($client,'GET','/farms/'.$farm->id.'/nodes');
                                                    $nodes=json_decode($nodesResponse->getBody()->getContents());
                                                    foreach ($nodes as $key => $node) {
                                                        if(is_null(Node::where("id_wiseconn",$node->id)->first())){
                                                            $newNode= $this->nodeCreate($node,$newFarm);
                                                            try{
                                                                $hydraulicsResponse = $this->requestWiseconn($client,'GET','/farms/'.$farm->id.'/hydraulics');
                                                                $hydraulics=json_decode($hydraulicsResponse->getBody()->getContents());
                                                                foreach ($hydraulics as $key => $hydraulic) {
                                                                    if(is_null(Hydraulic::where("id_wiseconn",$hydraulic->id)->first())){ 
                                                                        $newPhysicalConnection =$this->physicalConnectionCreate($hydraulic);
                                                                        $newHydraulic =$this->hydraulicCreate($hydraulic,$newFarm,$newZone,$newPhysicalConnection,$newNode);                                                                 
                                                                    }  
                                                                }
                                                            } catch (\Exception $e) {
                                                                \Log::error("Error:" . $e->getMessage());
                                                                \Log::error("Linea:" . $e->getLine());
                                                            }  
                                                        }  
                                                    }   
                                                } catch (\Exception $e) {
                                                    \Log::error("Error:" . $e->getMessage());
                                                    \Log::error("Linea:" . $e->getLine());
                                                }                               
                                            }  
                                    }
                                } catch (\Exception $e) {
                                    \Log::error("Error:" . $e->getMessage());
                                    \Log::error("Linea:" . $e->getLine());
                                } 
                            }
                        }
                        
                    } catch (\Exception $e) {
                        \Log::error("Error:" . $e->getMessage());
                        \Log::error("Linea:" . $e->getLine());
                    }                         
                }
                
            }
            # code...
            \Log::info("Success: Clone wiseconn data");
        } catch (\Exception $e) {
            \Log::error("Error:" . $e->getMessage());
            \Log::error("Linea:" . $e->getLine());
        }         
    }
}
