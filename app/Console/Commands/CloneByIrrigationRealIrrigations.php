<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Irrigation;
use App\RealIrrigation;
use App\Zone;
use App\Pump_system;
use App\Volume;
use Carbon\Carbon;

class CloneByIrrigationRealIrrigations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebyirrigation:realirrigations:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone realirrigations by irrigation';

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
            'value'=> isset($realIrrigation->volume->value)?$realIrrigation->volume->value:null,
            'unitName'=> isset($realIrrigation->volume->unitName)?$realIrrigation->volume->unitName:null,
            'unitAbrev'=> isset($realIrrigation->volume->unitAbrev)?$realIrrigation->volume->unitAbrev:null
        ]);
    }
    protected function realIrrigationCreate($realIrrigation,$farm,$zone,$volume,$pumpSystem){
        return RealIrrigation::create([
            'initTime' => isset($realIrrigation->initTime)?$realIrrigation->initTime:null,
            'endTime' =>isset($realIrrigation->endTime)?$realIrrigation->endTime:null,
            'status'=> isset($realIrrigation->status)?$realIrrigation->status:null,
            'id_farm'=> isset($farm->id)?$farm->id:null,
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
            $irrigations=Irrigation::all();
            foreach ($irrigations as $key => $irrigation) {
                $realIrrigationsResponse = $this->requestWiseconn($client,'GET','/irrigations/'.$irrigation->id_wiseconn.'/realIrrigations/?endTime='.$endTime.'&initTime='.$initTime);
                $realIrrigations=json_decode($realIrrigationsResponse->getBody()->getContents());
                foreach ($realIrrigations as $key => $realIrrigation) {
                    $zone=Zone::where("id_wiseconn",$realIrrigation->zoneId)->first();
                    $pumpSystem=Pump_system::where("id_wiseconn",$realIrrigation->pumpSystemId)->first();
                    if(is_null(RealIrrigation::where("id_wiseconn",$realIrrigation->id)->first())&&!is_null($zone)&&!is_null($pumpSystem)){ 
                        $newVolume =$this->volumeCreate($realIrrigation);
                        $this->info("New Volume, id:".$newVolume->id);
                        $newRealIrrigation =$this->realIrrigationCreate($realIrrigation,$farm,$zone,$newVolume,$pumpSystem);
                        $this->info("New RealIrrigation, id:".$newRealIrrigation->id);
                    }
                }
            }
            $this->info("Success: Clone real irrigations and volumes data by irrigation");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        }  
    }
}
