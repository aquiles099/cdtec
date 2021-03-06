<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\Zone;
use App\Node;
use App\Hydraulic;
use App\PhysicalConnection;
use App\CloningErrors;

class CloneByZoneHydraulics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebyzone:hydraulics:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone hydraulics data by zone';

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
    protected function physicalConnectionCreate($hydraulic){
        return PhysicalConnection::create([
            'expansionPort'=> $hydraulic->physicalConnection->expansionPort,
            'expansionBoard'=> $hydraulic->physicalConnection->expansionBoard,
            'nodePort'=> $hydraulic->physicalConnection->nodePort
        ]);
    }
    protected function hydraulicCreate($hydraulic,$farm,$node,$newPhysicalConnection){
        return Hydraulic::create([
            'name' => $hydraulic->name,
            'type' => $hydraulic->type,
            'id_farm' => $farm->id,
            'id_physical_connection' => $newPhysicalConnection->id,
            'id_node' => $node->id,
            'id_wiseconn' => $hydraulic->id
        ]); 
    }
    protected function cloneBy($hydraulic){
        $farm=Node::where("id_wiseconn",$hydraulic->farmId)->first();
        if(is_null(Hydraulic::where("id_wiseconn",$hydraulic->id)->first())&&!is_null($node)){ 
            $newPhysicalConnection =$this->physicalConnectionCreate($hydraulic);
            $newHydraulic =$this->hydraulicCreate($hydraulic,$farm,$node,$newPhysicalConnection);
            $this->info("New PhysicalConnection id:".$newPhysicalConnection->id." / New Hydraulic id:".$newHydraulic->id);
        }else{
            $this->info("Elemento exitente");
        }  
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        try{
            $zones=Zone::all();
            foreach ($zones as $key => $zone) {
                $cloningErrors=CloningErrors::where("elements","/zones/id/hydraulics")->get();
                if(count($cloningErrors)>0){
                    foreach ($cloningErrors as $key => $cloningError) {
                        $hydraulicsResponse = $this->requestWiseconn('GET',$cloningError->uri);
                        $hydraulics=json_decode($hydraulicsResponse->getBody()->getContents());
                        $this->info("==========Clonando pendientes por error en peticion (".count($hydraulics)." elementos)");
                        foreach ($hydraulics as $key => $hydraulic) {
                            $this->cloneBy($hydraulic);
                        }
                        $cloningError->delete();
                    }
                }else{
                    try{
                        $currentRequestUri='/zones/'.$zone->id_wiseconn.'/hydraulics';
                        $currentRequestElement='/zones/id/hydraulics';
                        $id_wiseconn=$zone->id_wiseconn;
                        $hydraulicsResponse = $this->requestWiseconn('GET',$currentRequestUri);
                        $hydraulics=json_decode($hydraulicsResponse->getBody()->getContents());
                        $this->info("==========Clonando nuevos elementos (".count($hydraulics)." elementos)");
                        foreach ($hydraulics as $key => $hydraulic) {
                            $this->cloneBy($hydraulic);
                            
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
            $this->info("Success: Clone hydraulics and newPhysicalConnections data by zone");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        }    
    }
}
