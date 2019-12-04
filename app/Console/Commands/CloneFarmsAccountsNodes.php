<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\Account;
use App\Node;
// use App\Zone;
// use App\Hydraulic;
// use App\PhysicalConnection;
// use App\Pump_system;
use Carbon\Carbon;
// use DateTime;
class CloneFarmsAccountsNodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clone:farms:accounts:nodes:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone farms, accounts and nodes data';

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
    protected function farmCreate($farm){
        return Farm::create([
            'name' => $farm->name,
            'description' => $farm->description,
            'latitude' => $farm->latitude,
            'longitude' => $farm->longitude,
            'postalAddress' => $farm->postalAddress,
            'timeZone' => $farm->timeZone,
            'webhook' => $farm->webhook,
            'id_wiseconn' => $farm->id,
        ]);
    }
    protected function accountCreate($farm,$newFarm){
        return Account::create([
            'name' => $farm->account->name,
            'id_wiseconn' => $farm->account->id,
            'id_farm' => $newFarm->id
        ]);
    }
    protected function nodeCreate($node,$newFarm){
        return Node::create([
            'name' => $node->name,
            'lat' => $node->lat,
            'lng' => $node->lng,
            'nodeType' => $node->nodeType,
            'id_farm' => $newFarm->id,
            'id_wiseconn' => $node->id
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
            $farmsResponse =  $this->requestWiseconn($client,'GET','farms');
            $farms=json_decode($farmsResponse->getBody()->getContents());
            foreach ($farms as $key => $farm) {
                if(is_null(Farm::where("id_wiseconn",$farm->id)->first())){
                    $newFarm= $this->farmCreate($farm);   
                    $newAccount= $this->accountCreate($farm,$newFarm);
                    try {
                        $nodesResponse = $this->requestWiseconn($client,'GET','/farms/'.$farm->id.'/nodes');
                        $nodes=json_decode($nodesResponse->getBody()->getContents());
                        foreach ($nodes as $key => $node) {
                            if(is_null(Node::where("id_wiseconn",$node->id)->first())){
                                $newNode= $this->nodeCreate($node,$newFarm);
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error:" . $e->getMessage());
                        \Log::error("Linea:" . $e->getLine());
                    }
                }
            }
            # code...
            \Log::info("Success: Clone farms, accounts and nodes data");
        } catch (\Exception $e) {
            \Log::error("Error:" . $e->getMessage());
            \Log::error("Linea:" . $e->getLine());
        }    
    }
}