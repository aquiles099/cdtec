<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Farm;
use App\Node;
use App\Zone;
use App\Measure;
use App\PhysicalConnection;
class CloneByFarmMeasures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebyfarm:measures:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone measures data by farm';

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
    protected function physicalConnectionCreate($measure){
        return PhysicalConnection::create([
            'expansionPort'=> isset($measure->physicalConnection)?$measure->physicalConnection->expansionPort:null,
            'expansionBoard'=> isset($measure->physicalConnection)?$measure->physicalConnection->expansionBoard:null,
            'nodePort'=> isset($measure->physicalConnection)?$measure->physicalConnection->nodePort:null
        ]);
    }
    protected function measureCreate($measure,$farm,$zone,$node,$newPhysicalConnection){
        return Measure::create([
            'name' => $measure->name,
            'unit' => isset($measure->unit)?($measure->unit):null,
            'lastData' =>isset($measure->lastData)?($measure->lastData):null,
            'lastDataDate'=> isset($measure->lastDataDate)?(Carbon::parse($measure->lastDataDate)):null,
            'monitoringTime'=> isset($measure->monitoringTime)?$measure->monitoringTime:null,
            'sensorDepth' => isset($measure->sensorDepth)?($measure->sensorDepth):null,
            'depthUnit'=> isset($measure->depthUnit)?($measure->depthUnit):null,
            'sensorType'=> isset($measure->sensorType)?($measure->sensorType):null,
            'readType'=> isset($measure->readType)?($measure->readType):null,
            'id_farm' => isset($farm->id)?$farm->id:null,
            'id_zone' => isset($zone->id)?$zone->id:null,
            'id_node' => isset($node->id)?$node->id:null,
            'id_physical_connection' => isset($newPhysicalConnection->id)?$newPhysicalConnection->id:null,
            'id_wiseconn' => $measure->id
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
                $measuresResponse = $this->requestWiseconn($client,'GET','/farms/'.$farm->id_wiseconn.'/measures');
                $measures=json_decode($measuresResponse->getBody()->getContents());
                foreach ($measures as $key => $measure) {
                    if(is_null(Measure::where("id_wiseconn",$measure->id)->first())){
                        $newPhysicalConnection =$this->physicalConnectionCreate($measure);
                        $farm=null;$node=null;$zone=null;
                        if(isset($measure->farmId)){
                            $farm=Farm::where("id_wiseconn",$measure->farmId)->first();
                        }
                        if(isset($measure->nodeId)){
                            $node=Node::where("id_wiseconn",$measure->nodeId)->first();
                        }
                        if(isset($measure->zoneId)){
                            $zone=Zone::where("id_wiseconn",$measure->zoneId)->first(); 
                        }
                        $newmeasure =$this->measureCreate($measure,$farm,$zone,$node,$newPhysicalConnection);
                    }  
                }                    
            }
            $this->info("Success: Clone measures data by farm");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        } 
    }
}
