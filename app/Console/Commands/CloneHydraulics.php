<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Farm;
use App\Zone;
use App\Node;
use App\Hydraulic;
use App\PhysicalConnection;
class CloneHydraulics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clone:hydraulics:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone hydraulics data';

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
    protected function hydraulicCreate($hydraulic,$farm,$zone,$node,$newPhysicalConnection){
        return Hydraulic::create([
            'name' => $hydraulic->name,
            'type' => $hydraulic->type,
            'id_farm' => $farm->id,
            'id_zone' => $zone->id,
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
                $hydraulicsResponse = $this->requestWiseconn($client,'GET','/farms/'.$farm->id_wiseconn.'/hydraulics');
                $hydraulics=json_decode($hydraulicsResponse->getBody()->getContents());
                $nodes=Node::all();
                $zones=Zone::all();
                foreach ($nodes as $key => $node) { 
                    foreach ($zones as $key => $zone) {
                        foreach ($hydraulics as $key => $hydraulic) {
                            if(is_null(Hydraulic::where("id_wiseconn",$hydraulic->id)->first())&&$hydraulic->farmId==$farm->id_wiseconn&&$hydraulic->nodeId==$node->id_wiseconn){ 
                                $newPhysicalConnection =$this->physicalConnectionCreate($hydraulic);
                                $newHydraulic =$this->hydraulicCreate($hydraulic,$farm,$zone,$node,$newPhysicalConnection);                                                                 
                            }  
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error:" . $e->getMessage());
            \Log::error("Linea:" . $e->getLine());
        } 
    }
}
