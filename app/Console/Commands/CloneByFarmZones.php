<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Farm;
use App\Zone;
class CloneByFarmZones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebyfarm:zones:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone zones data by farm';

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
    protected function zoneCreate($zone,$farm){
        return Zone::create([
            'name' => $zone->name,
            'description' => $zone->description,
            'latitude' => $zone->latitude,
            'longitude' => $zone->longitude,
            'id_farm' => $farm->id,
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
            $farms=Farm::all();
            foreach ($farms as $key => $farm) {
                $zonesResponse =  $this->requestWiseconn($client,'GET','/farms/'.$farm->id_wiseconn.'/zones');
                $zones=json_decode($zonesResponse->getBody()->getContents());
                foreach ($zones as $key => $zone) {
                    if(is_null(Zone::where("id_wiseconn",$zone->id)->first()) && $zone->farmId==$farm->id_wiseconn){
                        $newZone= $this->zoneCreate($zone,$farm);                                                      
                    }
                }
            }
            \Log::info("Success: Clone pumpsystems data");
        } catch (\Exception $e) {
            \Log::error("Error:" . $e->getMessage());
            \Log::error("Linea:" . $e->getLine());
        } 
    }
}
