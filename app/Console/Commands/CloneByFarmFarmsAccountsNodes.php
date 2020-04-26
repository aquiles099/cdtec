<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\Account;
use App\Node;
use App\CloningErrors;
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
    protected function farmCreate($farm,$account){
        return Farm::create([
            'name' => $farm->name,
            'description' => $farm->description,
            'latitude' => $farm->latitude,
            'longitude' => $farm->longitude,
            'postalAddress' => $farm->postalAddress,
            'timeZone' => $farm->timeZone,
            'webhook' => $farm->webhook,
            'id_account' => $account->id,
            'id_wiseconn' => $farm->id,
        ]);
    }
    protected function accountCreate($farm){
        return Account::create([
            'name' => $farm->account->name,
            'id_wiseconn' => $farm->account->id
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
            $currentRequestUri='farms';
            $currentRequestElement='farms';
            $id_wiseconn=null;
            $farmsResponse = $this->requestWiseconn($client,'GET',$currentRequestUri);
            $farms=json_decode($farmsResponse->getBody()->getContents());
            foreach ($farms as $key => $farm) {
                if(is_null(Farm::where("id_wiseconn",$farm->id)->first())){
                    if(isset($farm->account)){
                        $account=Account::where("id_wiseconn",$farm->account->id)->first();
                        if(is_null($account)){
                            $account= $this->accountCreate($farm);
                            $this->info("New account id:".$account->id);
                        }
                        $newFarm= $this->farmCreate($farm,$account);
                        $this->info("New farm id:".$newFarm->id);
                        try {
                            $currentRequestUri='/farms/'.$farm->id.'/nodes';
                            $currentRequestElement='/farms/id/nodes';
                            $id_wiseconn=$farm->id;
                            $nodesResponse = $this->requestWiseconn($client,'GET',$currentRequestUri);
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
                            $this->error("currentRequestUri:" . $currentRequestUri);
                            $cloningError=new CloningErrors();
                            $cloningError->elements=$currentRequestElement;
                            $cloningError->uri=$currentRequestUri;
                            $cloningError->id_wiseconn=$id_wiseconn;
                            $cloningError->save();
                        } 
                    }
                }
            }
            # code...
            $this->info("Success: Clone farms, accounts and nodes data by farm");
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
}
