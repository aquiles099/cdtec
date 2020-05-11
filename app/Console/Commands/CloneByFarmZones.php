<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\Zone;
use App\Path;
use App\Type;
use App\NorthEastBound;
use App\SouthWestBound;
use App\CloningErrors;
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
    protected function zoneCreate($zone,$farm){
        $newZone=Zone::create([
            'name' => isset($zone->name)?$zone->name:null,
            'description' => isset($zone->description)?$zone->description:null,
            'latitude' => isset($zone->latitude)?$zone->latitude:null,
            'longitude' => isset($zone->longitude)?$zone->longitude:null,
            'id_farm' => isset($farm->id)?$farm->id:null,
            'kc' => isset($zone->kc)?$zone->kc:null,
            'theoreticalFlow' => isset($zone->theoreticalFlow)?$zone->theoreticalFlow:null,
            'unitTheoreticalFlow' => isset($zone->unitTheoreticalFlow)?$zone->unitTheoreticalFlow:null,
            'efficiency' => isset($zone->efficiency)?$zone->efficiency:null,
            'humidityRetention' => isset($zone->humidityRetention)?$zone->humidityRetention:null,
            'max' => isset($zone->max)?$zone->max:null,
            'min' => isset($zone->min)?$zone->min:null,
            'criticalPoint1' => isset($zone->criticalPoint1)?$zone->criticalPoint1:null,
            'criticalPoint2' => isset($zone->criticalPoint2)?$zone->criticalPoint2:null,
            'id_pump_system' => isset($zone->pumpSystemId)?$zone->pumpSystemId:null,
            'id_wiseconn' => isset($zone->id)?$zone->id:null
        ]);
        if(isset($zone->type)){
            foreach ($zone->type as $key => $type) {
                Type::create([
                    'description'=>$type,
                    'id_zone'=>$newZone->id,
                ]);
            }
        }
        if(isset($zone->polygon->path)){
            foreach ($zone->polygon->path as $key => $path) {
                Path::create([
                    'id_zone' => $newZone->id,
                    'lat' => $path->lat,
                    'lng' => $path->lng,
                ]);
            }
        }
        if(isset($zone->polygon->bounds->southWest)){
            SouthWestBound::create([
                'id_zone' => $newZone->id,
                'lat' => $zone->polygon->bounds->southWest->lat,
                'lng' => $zone->polygon->bounds->southWest->lng,
            ]);
        }
        if(isset($zone->polygon->bounds->northEast)){
            NorthEastBound::create([
                'id_zone' => $newZone->id,
                'lat' => $zone->polygon->bounds->northEast->lat,
                'lng' => $zone->polygon->bounds->northEast->lng,
            ]);
        }
        return $newZone;
    }
    public function cloneBy($zone){
        $farm=Farm::where("id_wiseconn",$zone->farmId)->first();
        if(is_null(Zone::where("id_wiseconn",$zone->id)->first()) && !is_null($farm)){
            $newZone= $this->zoneCreate($zone,$farm); 
            $farm->touch();
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
            $farms=Farm::all();
            foreach ($farms as $key => $farm) {
                if($farm->active_cloning==1){
                    try {
                        $cloningErrors=CloningErrors::where("elements","/farms/id/zones")->get();
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
                                $currentRequestUri='/farms/'.$farm->id_wiseconn.'/zones';
                                $currentRequestElement='/farms/id/zones';
                                $id_wiseconn=$farm->id_wiseconn;
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
                    } catch (\Exception $e) {
                        $this->error("Error:" . $e->getMessage());
                        $this->error("Linea:" . $e->getLine());
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
