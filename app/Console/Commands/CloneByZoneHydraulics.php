<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\Node;
use App\Hydraulic;
use App\PhysicalConnection;
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
            $zones=Zone::all();
            foreach ($zones as $key => $zone) {
                $hydraulicsResponse = $this->requestWiseconn($client,'GET','/zones/'.$zone->id_wiseconn.'/hydraulics');
                $hydraulics=json_decode($hydraulicsResponse->getBody()->getContents());            
                foreach ($hydraulics as $key => $hydraulic) {
                    $farm=Node::where("id_wiseconn",$hydraulic->farmId)->first();
                    if(is_null(Hydraulic::where("id_wiseconn",$hydraulic->id)->first())&&!is_null($node)){ 
                        $newPhysicalConnection =$this->physicalConnectionCreate($hydraulic);
                        $newHydraulic =$this->hydraulicCreate($hydraulic,$farm,$node,$newPhysicalConnection);                                                                 
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
