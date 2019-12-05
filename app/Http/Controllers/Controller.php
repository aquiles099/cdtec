<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use GuzzleHttp\Client;
use App\Farm;
use App\RealIrrigation;
use App\Zone;
use App\Node;
use App\Pump_system;
use App\Volume;
use App\Hydraulic;
use App\PhysicalConnection;
use App\Measure;
use App\Irrigation;
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
    protected function alarmCreate($alarm,$farm,$zone,$realIrrigation){
        return Alarm::create([
            'activationValue' => $alarm->activationValue,
            'description' => $alarm->description,
            'date' => $alarm->date,
            'id_farm' => $farm->id,
            'id_zone' => $zone->id,
            'id_real_irrigation' => $realIrrigation->id,
            'id_wiseconn' => $alarm->id,
        ]);
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function test()
    {
        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);
        $initTime=Carbon::now(date_default_timezone_get())->format('Y-m-d');
        $endTime=Carbon::now(date_default_timezone_get())->addDays(15)->format('Y-m-d');
        try{
            $farms=Farm::all();
            foreach ($farms as $key => $farm) {
                $irrigationsResponse = $this->requestWiseconn($client,'GET','/farms/'.$farm->id_wiseconn.'/irrigations/?endTime='.$endTime.'&initTime='.$initTime);
                $irrigations=json_decode($irrigationsResponse->getBody()->getContents());
                dd($irrigations);
                foreach ($realIrrigations as $key => $realIrrigation) {
                    $zone=Zone::where("id_wiseconn",$realIrrigation->zoneId)->first();
                    $pumpSystem=Pump_system::where("id_wiseconn",$realIrrigation->pumpSystemId)->first();
                    if(is_null(RealIrrigation::where("id_wiseconn",$realIrrigation->id)->first())&&!is_null($zone)&&!is_null($pumpSystem)){ 
                        $newVolume =$this->volumeCreate($realIrrigation);
                        $newRealIrrigation =$this->realIrrigationCreate($realIrrigation,$farm,$zone,$newVolume,$pumpSystem);                                                                 
                    }
                }                    
            }
            # code...
            return ("Success: Clone real irrigations and volumes data");
        } catch (\Exception $e) {
            return ["Error:" => $e->getMessage(),"Linea:" => $e->getLine()];
        }  
    }
}
