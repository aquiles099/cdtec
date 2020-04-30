<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\Zone;
use App\Pump_system;
use Carbon\Carbon;
use App\CloningErrors;
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
    protected function cloneBy($zone){
        $pumpSystem=Pump_system::where("id_wiseconn",$zone->pumpSystemId)->first();
        if(is_null(Zone::where("id_wiseconn",$zone->id)->first()) && !is_null($pumpSystem)){
            $newZone= $this->zoneCreate($zone,$pumpSystem);
            if(isset($zone->id_farm)){
                $farm=Farm::find($zone->id_farm);
                $farm->touch();
            }
            $this->info("New Zone id:".$newZone->id);
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
        try { 
            $pumpSystems=Pump_system::all();
            foreach ($pumpSystems as $key => $pumpSystem) {
                $cloningErrors=CloningErrors::where("elements","/pumpSystems/id/zones")->get();
                if(count($cloningErrors)>0){
                    foreach ($cloningErrors as $key => $cloningError) {
                        $zonesResponse = $this->requestWiseconn('GET',$cloningError->uri);
                        $zones=json_decode($zonesResponse->getBody()->getContents());
                        $this->info("==========Clonando pendientes por error en peticion (".count($zones)." elementos)");
                        foreach ($zones as $key => $zone) {
                            $this->cloneBy($zone);
                        }
                        $cloningError->delete();
                    }
                }else{
                    try { 
                        $currentRequestUri='/pumpSystems/'.$pumpSystem->id_wiseconn.'/zones';
                        $currentRequestElement='/pumpSystems/id/zones';
                        $id_wiseconn=$pumpSystem->id_wiseconn;
                        $zonesResponse =  $this->requestWiseconn('GET',$currentRequestUri);
                        $zones=json_decode($zonesResponse->getBody()->getContents());
                        $this->info("==========Clonando nuevos elementos (".count($zones)." elementos)");
                        foreach ($zones as $key => $zone) {
                            $this->cloneBy($zone);                            
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
            $this->info("Success: Clone pumpsystems data by farm");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        } 
    }
}
