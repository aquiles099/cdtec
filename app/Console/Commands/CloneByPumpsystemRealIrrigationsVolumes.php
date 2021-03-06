<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\RealIrrigation;
use App\Volume;
use App\Zone;
use App\Pump_system;
use App\CloningErrors;

class CloneByPumpsystemRealIrrigationsVolumes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebypumpsystem:realirrigations:volumes:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone real irrigations and volumes by pumpsystem';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    protected function requestWiseconn($method,$uri){
        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);
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
    protected function realIrrigationCreate($realIrrigation,$zone,$volume,$pumpSystem){
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
    protected function cloneBy($realIrrigation){
        $zone=Zone::where("id_wiseconn",$realIrrigation->zoneId)->first();
        $pumpSystem=Pump_system::where("id_wiseconn",$realIrrigation->pumpSystemId)->first();
        if(is_null(RealIrrigation::where("id_wiseconn",$realIrrigation->id)->first())&&!is_null($zone)&&!is_null($pumpSystem)){ 
            $newVolume =$this->volumeCreate($realIrrigation);
            $newRealIrrigation =$this->realIrrigationCreate($realIrrigation,$zone,$newVolume,$pumpSystem);
            $zone->touch();
            $this->info("New Volume id:".$newVolume->id." / New RealIrrigation id:".$newRealIrrigation->id);
        }else{
            $this->info("Elemento existente");
        }
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $initTime=Carbon::now(date_default_timezone_get())->subDays(10)->format('Y-m-d');
        $endTime=Carbon::now(date_default_timezone_get())->addDays(5)->format('Y-m-d');
        try{
            $pumpSystems=Pump_system::all();
            foreach ($pumpSystems as $key => $pumpSystem) {
                $cloningErrors=CloningErrors::where("elements","/pumpSystems/id/realIrrigations")->get();
                if(count($cloningErrors)>0){
                    foreach ($cloningErrors as $key => $cloningError) {
                        $realIrrigationsResponse = $this->requestWiseconn('GET',$cloningError->uri);
                        $realIrrigations=json_decode($realIrrigationsResponse->getBody()->getContents());
                        $this->info("==========Clonando pendientes por error en peticion (".count($realIrrigations)." elementos)");
                        foreach ($realIrrigations as $key => $realIrrigation) {
                            $this->cloneBy($realIrrigation);
                        }
                        $cloningError->delete();
                    }
                }else{
                    try {
                        $currentRequestUri='/pumpSystems/'.$pumpSystem->id_wiseconn.'/realIrrigations/?endTime='.$endTime.'&initTime='.$initTime;
                        $currentRequestElement='/pumpSystems/id/realIrrigations';
                        $id_wiseconn=$pumpSystem->id_wiseconn;
                        $realIrrigationsResponse = $this->requestWiseconn('GET',$currentRequestUri);
                        $realIrrigations=json_decode($realIrrigationsResponse->getBody()->getContents());
                        $this->info("==========Clonando nuevos elementos (".count($realIrrigations)." elementos)");
                        foreach ($realIrrigations as $key => $realIrrigation) {
                            $this->cloneBy($realIrrigation);
                        }
                    } catch (\Exception $e) {
                        $this->error("Error:" . $e->getMessage());
                        $this->error("Linea:" . $e->getLine());
                        $this->error("currentRequestUri:" . $currentRequestUri);
                        if(is_null(CloningErrors::where("elements",$currentRequestElement)->where("uri",$currentRequestUri)->where("id_wiseconn",$id_wiseconn)->first())){
                            $cloningError=new CloningErrors();
                            $cloningError->elements=$currentRequestElement;
                            $cloningError->uri=$currentRequestUri;
                            $cloningError->id_wiseconn=$id_wiseconn;
                            $cloningError->save();
                        }
                    }
                }
            }
        }catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        } 
        $this->info("Success: Clone real irrigations and volumes data by pumpsystem");
    } 
}