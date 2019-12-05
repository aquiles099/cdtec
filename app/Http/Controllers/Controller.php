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
        try{
            $nodes=Node::all();
            foreach ($nodes as $key => $node) {
                $measuresResponse = $this->requestWiseconn($client,'GET','/nodes/'.$node->id_wiseconn.'/measures');
                $measures=json_decode($measuresResponse->getBody()->getContents());
                foreach ($measures as $key => $measure) {
                    $measure=Farm::where("id_wiseconn",$alarm->farmId)->first();
                    $realIrrigation=RealIrrigation::where("id_wiseconn",$alarm->realIrrigationId)->first();
                    if(is_null(Alarm::where("id_wiseconn",$alarm->id)->first())&&!is_null($farm)&&!is_null($realIrrigation)){
                        $newAlarm= $this->alarmCreate($alarm,$farm,$zone,$realIrrigation);
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
