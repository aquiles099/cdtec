<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\Node;
use App\Hydraulic;
use App\PhysicalConnection;
use App\CloningErrors;
class CloneByFarmHydraulics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebyfarm:hydraulics:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone hydraulics data by farm';

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
        try{
            $farms=Farm::all();
            foreach ($farms as $key => $farm) {
                try{
                    $currentRequestUri='/farms/'.$farm->id_wiseconn.'/hydraulics';
                    $currentRequestElement='/farms/id/hydraulics';
                    $id_wiseconn=$farm->id_wiseconn;
                    $hydraulicsResponse = $this->requestWiseconn($client,'GET',$currentRequestUri);
                    $hydraulics=json_decode($hydraulicsResponse->getBody()->getContents());            
                    foreach ($hydraulics as $key => $hydraulic) {
                        $node=Node::where("id_wiseconn",$hydraulic->nodeId)->first();
                        if(is_null(Hydraulic::where("id_wiseconn",$hydraulic->id)->first())&&!is_null($node)){ 
                            $newPhysicalConnection =$this->physicalConnectionCreate($hydraulic);
                            $newHydraulic =$this->hydraulicCreate($hydraulic,$farm,$node,$newPhysicalConnection);
                            $this->info("New PhysicalConnection id:".$newPhysicalConnection->id." / New Hydraulic id:".$newHydraulic->id);  
                        }
                    }
                } catch (\Exception $e) {
                    $this->error("Error:" . $e->getMessage());
                    $this->error("Linea:" . $e->getLine());
                    $this->error("currentRequestUri:" . $currentRequestUri);
                    $cloningError=new CloningErrors();
                    $cloningError->elements=$currentRequestElement;
                    $cloningError->uri=$currentRequestUri;
                    $cloningError->id_wiseconn=$id_wiseconn;
                    $cloningError->save();
                }
            }
            $this->info("Success: Clone hydraulics and physical connections data by farm");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        }    
    }
}
