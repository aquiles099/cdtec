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
    protected function requestWiseconn($client,$method,$uri){
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
                try {
                    $currentRequestUri='/farms/'.$farm->id_wiseconn.'/pumpSystems';
                    $currentRequestElement='/farms/id/pumpSystems';
                    $id_wiseconn=$farm->id_wiseconn;
                    $pumpSystemsResponse =  $this->requestWiseconn($client,'GET',$currentRequestUri);
                    $pumpSystems=json_decode($pumpSystemsResponse->getBody()->getContents());
                    foreach ($pumpSystems as $key => $pumpSystem) {
                        if(is_null(Pump_system::where("id_wiseconn",$pumpSystem->id)->first()) && $pumpSystem->farmId==$farm->id_wiseconn){
                            $newPumpSystem= $this->pumpSystemCreate($pumpSystem,$farm);
                            $this->info("New PumpSystem, id:".$newPumpSystem->id);
                        }
                    }
                } catch (\Exception $e) {
                    $this->error("Error:" . $e->getMessage());
                    $this->error("Linea:" . $e->getLine());
                    $this->error("currentRequestUri:" . $currentRequestUri);
                    $cloningError=new CloningErrors();
                    $cloningError->elements=$currentRequestElement;
                    $cloningError->uri=$currentRequestUri;
                    $cloningError->save();
                } 
            }
            $this->info("Success: Clone pumpsystems data by farm");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        }
    }
}
