<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\Pump_system;
use App\CloningErrors;
class CloneByFarmPumpsystems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebyfarm:pumpsystems:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone pumpsystems data by farm';

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
    protected function pumpSystemCreate($pumpSystem,$farm){
        return Pump_system::create([
            'name' => $pumpSystem->name,
            'allowPumpSelection' => $pumpSystem->allowPumpSelection,
            'id_farm' => $farm->id,
            'id_wiseconn' => $pumpSystem->id,
        ]);
    }
    protected function cloneBy($pumpSystem,$farm){        
        if(is_null(Pump_system::where("id_wiseconn",$pumpSystem->id)->first()) && $pumpSystem->farmId==$farm->id_wiseconn){
            $newPumpSystem= $this->pumpSystemCreate($pumpSystem,$farm);
            $this->info("New PumpSystem, id:".$newPumpSystem->id);
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
                try {
                    $cloningErrors=CloningErrors::where("elements","/farms/id/pumpSystems")->get();
                    if(count($cloningErrors)>0){
                        foreach ($cloningErrors as $key => $cloningError) {
                            $pumpSystemsResponse = $this->requestWiseconn('GET',$cloningError->uri);
                            $pumpSystems=json_decode($pumpSystemsResponse->getBody()->getContents());
                            $this->info("==========Clonando pendientes por error en peticion (".count($pumpSystems)." elementos)");
                            foreach ($pumpSystems as $key => $pumpSystem) {
                                $this->cloneBy($pumpSystem,$farm);
                            }
                            $cloningError->delete();
                        }
                    }else{
                        try {
                            $currentRequestUri='/farms/'.$farm->id_wiseconn.'/pumpSystems';
                            $currentRequestElement='/farms/id/pumpSystems';
                            $id_wiseconn=$farm->id_wiseconn;
                            $pumpSystemsResponse =  $this->requestWiseconn('GET',$currentRequestUri);
                            $pumpSystems=json_decode($pumpSystemsResponse->getBody()->getContents());
                            $this->info("==========Clonando nuevos elementos (".count($pumpSystems)." elementos)");
                            foreach ($pumpSystems as $key => $pumpSystem) {
                                $this->cloneBy($pumpSystem,$farm);
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
            $this->info("Success: Clone pumpsystems data by farm");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        }
    }
}
