<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Zone;
use App\Pump_system;
use Carbon\Carbon;
class CloneByPumpsystemZones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebypumpsystem:zones:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone zones data by pumpsystem';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    protected function requestWiseconn($client,$method,$uri){
        return $client->request($method, $uri, [
            'headers' => [
                'api_key' => '9Ev6ftyEbHhylMoKFaok',
                'Accept'     => 'application/json'
            ]
        ]);
    }
    protected function zoneCreate($zone,$pumpSystem){
        return Zone::create([
            'name' => isset($zone->name)?$zone->name:null,
            'description' => isset($zone->description)?$zone->description:null,
            'latitude' => isset($zone->latitude)?$zone->latitude:null,
            'longitude' => isset($zone->longitude)?$zone->longitude:null,
            'id_farm' => isset($zone->farmId)?$zone->farmId:null,
            'kc' => isset($zone->kc)?$zone->kc:null,
            'theoreticalFlow' => isset($zone->theoreticalFlow)?$zone->theoreticalFlow:null,
            'unitTheoreticalFlow' => isset($zone->unitTheoreticalFlow)?$zone->unitTheoreticalFlow:null,
            'efficiency' => isset($zone->efficiency)?$zone->efficiency:null,
            'humidityRetention' => isset($zone->humidityRetention)?$zone->humidityRetention:null,
            'max' => isset($zone->max)?$zone->max:null,
            'min' => isset($zone->min)?$zone->min:null,
            'criticalPoint1' => isset($zone->criticalPoint1)?$zone->criticalPoint1:null,
            'criticalPoint2' => isset($zone->criticalPoint2)?$zone->criticalPoint2:null,
            'id_pump_system' => isset($pumpSystem->id)?$pumpSystem->id:null,
            'id_wiseconn' => isset($zone->id)?$zone->id:null
        ]);
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);
        try { 
            $pumpSystems=Pump_system::all();
            foreach ($pumpSystems as $key => $pumpSystem) {
                $zonesResponse =  $this->requestWiseconn($client,'GET','/pumpSystems/'.$pumpSystem->id_wiseconn.'/zones');
                $zones=json_decode($zonesResponse->getBody()->getContents());                
                foreach ($zones as $key => $zone) {
                    $pumpSystem=Pump_system::where("id_wiseconn",$zone->pumpSystemId)->first();
                    if(is_null(Zone::where("id_wiseconn",$zone->id)->first()) && !is_null($pumpSystem)){
                        $newZone= $this->zoneCreate($zone,$pumpSystem);
                        $this->info("New Zone, id:".$newZone->id);                                                      
                    }
                }
            }
            $this->info("Success: Clone pumpsystems data by farm");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        } 
    }
}
