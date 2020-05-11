<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\Irrigation;
use App\Zone;
use App\Pump_system;
use App\Volume;
use Carbon\Carbon;
use App\CloningErrors;
class CloneByFarmIrrigationsVolumes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebyfarm:irrigations:volumes:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone irrigations data by farm';

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
    protected function volumeCreate($irrigation){
        return Volume::create([
            'value'=> isset($irrigation->volume->value)?$irrigation->volume->value:null,
            'unitName'=> isset($irrigation->volume->unitName)?$irrigation->volume->unitName:null,
            'unitAbrev'=> isset($irrigation->volume->unitAbrev)?$irrigation->volume->unitAbrev:null
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
    protected function cloneBy($irrigation,$farm){
        $zone=Zone::where("id_wiseconn",$irrigation->zoneId)->first();
        $pumpSystem=Pump_system::where("id_wiseconn",$irrigation->pumpSystemId)->first();
        if(is_null(Irrigation::where("id_wiseconn",$irrigation->id)->first())&&!is_null($zone)&&!is_null($pumpSystem)){ 
            $newVolume =$this->volumeCreate($irrigation);
            $newIrrigation =$this->irrigationCreate($irrigation,$farm,$zone,$newVolume,$pumpSystem);
            $this->info("New Volume id:".$newVolume->id." / New Irrigation id:".$newIrrigation->id);
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
            $farms=Farm::all();
            foreach ($farms as $key => $farm) {
                if($farm->active_cloning==1){
                    try{
                        $cloningErrors=CloningErrors::where("elements","/farms/id/irrigations")->get();
                        if(count($cloningErrors)>0){
                            foreach ($cloningErrors as $key => $cloningError) {
                                $irrigationsResponse = $this->requestWiseconn('GET',$cloningError->uri);
                                $irrigations=json_decode($irrigationsResponse->getBody()->getContents());
                                $this->info("==========Clonando pendientes por error en peticion (".count($irrigations)." elementos)");
                                foreach ($irrigations as $key => $irrigation) {
                                    $this->cloneBy($irrigation,$farm);
                                }
                                $cloningError->delete();
                            }
                        }else{
                            try{
                                $currentRequestUri='/farms/'.$farm->id_wiseconn.'/irrigations/?endTime='.$endTime.'&initTime='.$initTime;
                                $currentRequestElement='/farms/id/irrigations';
                                $id_wiseconn=$farm->id_wiseconn;
                                $irrigationsResponse = $this->requestWiseconn('GET',$currentRequestUri);
                                $irrigations=json_decode($irrigationsResponse->getBody()->getContents());
                                $this->info("==========Clonando nuevos elementos (".count($irrigations)." elementos)");
                                foreach ($irrigations as $key => $irrigation) {
                                    $this->cloneBy($irrigation,$farm);
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
                    } catch (\Exception $e) {
                        $this->error("Error:" . $e->getMessage());
                        $this->error("Linea:" . $e->getLine());
                    }
                }
            }
            $this->info("Success: Clone irrigations and volumes data by farm");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());            
        }  
    }
}
