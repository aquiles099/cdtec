<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\Account;
use App\Node;
class CloneByFarmFarmsAccountsNodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebyfarm:farms:accounts:nodes:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone farms, accounts and nodes data by farm';

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
                    $this->info("New farm id:".$newFarm->id." / New account id:".$newAccount->id);
                    try {
                        $nodesResponse = $this->requestWiseconn($client,'GET','/farms/'.$farm->id.'/nodes');
                        $nodes=json_decode($nodesResponse->getBody()->getContents());
                        foreach ($nodes as $key => $node) {
                            if(is_null(Node::where("id_wiseconn",$node->id)->first())){
                                $newNode= $this->nodeCreate($node,$newFarm);
                                $this->info("New node id:".$newNode->id);
                            }
                        }
                    } catch (\Exception $e) {
                        $this->error("Error:" . $e->getMessage());
                        $this->error("Linea:" . $e->getLine());
                    }
                }
            }
            # code...
            $this->info("Success: Clone farms, accounts and nodes data by farm");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        }    
    }
}
