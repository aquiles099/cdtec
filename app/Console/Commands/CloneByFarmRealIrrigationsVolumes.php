<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\RealIrrigation;
use App\Zone;
use App\Pump_system;
use App\Volume;
use Carbon\Carbon;

class CloneByFarmRealIrrigationsVolumes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebyfarm:realirrigations:volumes:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone real irrigations data by farm';

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
    protected function volumeCreate($realIrrigation){
        return Volume::create([
            'value'=> isset($realIrrigation->volume)?$realIrrigation->volume->value:null,
            'unitName'=> isset($realIrrigation->volume)?$realIrrigation->volume->unitName:null,
            'unitAbrev'=> isset($realIrrigation->volume)?$realIrrigation->volume->unitAbrev:null
        ]);
    }
    protected function realIirrigationCreate($realIrrigation,$farm,$zone,$volume,$pumpSystem){
        return RealIrrigation::create([
            'initTime' => isset($realIrrigation->initTime)?$realIrrigation->initTime:null,
            'endTime' =>isset($realIrrigation->endTime)?$realIrrigation->endTime:null,
            'status'=> isset($realIrrigation->status)?$realIrrigation->status:null,
            'id_farm'=> $farm->id,
            'id_pump_system'=> isset($pumpSystem->id)?$pumpSystem->id:null,
            'id_zone'=> isset($zone->id)?$zone->id:null,
            'id_wiseconn' => $realIrrigation->id
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
        $initTime=Carbon::now(date_default_timezone_get())->format('Y-m-d');
        $endTime=Carbon::now(date_default_timezone_get())->addDays(15)->format('Y-m-d');
        try{
            $farms=Farm::all();
            foreach ($farms as $key => $farm) {
                $realIrrigationsResponse = $this->requestWiseconn($client,'GET','/farms/'.$farm->id_wiseconn.'/realIrrigations/?endTime='.$endTime.'&initTime='.$initTime);
                $realIrrigations=json_decode($realIrrigationsResponse->getBody()->getContents());
                foreach ($realIrrigations as $key => $realIrrigation) {
                    $zone=Zone::where("id_wiseconn",$realIrrigation->zoneId)->first();
                    $pumpSystem=Pump_system::where("id_wiseconn",$realIrrigation->pumpSystemId)->first();
                    if(is_null(RealIrrigation::where("id_wiseconn",$realIrrigation->id)->first())&&!is_null($zone)&&!is_null($pumpSystem)){ 
                        $newVolume =$this->volumeCreate($realIrrigation);
                        $newRealIrrigation =$this->realIirrigationCreate($realIrrigation,$farm,$zone,$newVolume,$pumpSystem);                                                                 
                    }
                }                    
            }
            \Log::info("Success: Clone real irrigations and volumes data");
        } catch (\Exception $e) {
            \Log::error("Error:" . $e->getMessage());
            \Log::error("Linea:" . $e->getLine());
        }  
    }
}
