<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\Account;
use App\Zone;
class SyncWithWiseconn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncWithWiseconn:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync with wiseconn API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
            $farmsResponse = $client->request('GET', 'farms', [
                'headers' => [
                    'api_key' => '9Ev6ftyEbHhylMoKFaok',
                    'Accept'     => 'application/json'
                ]
            ]);
            $farms=json_decode($farmsResponse->getBody()->getContents());
            foreach ($farms as $key => $farm) {
                if(is_null(Farm::where("id_wiseconn",$farm->id)->first())){
                    $newFarm = Farm::create([
                        'name' => $farm->name,
                        'description' => $farm->description,
                        'latitude' => $farm->latitude,
                        'longitude' => $farm->longitude,
                        'postalAddress' => $farm->postalAddress,
                        'timeZone' => $farm->timeZone,
                        'webhook' => $farm->webhook,
                        'id_wiseconn' => $farm->id,
                    ]);
                    $newAccount = Account::create([
                        'name' => $farm->account->name,
                        'id_wiseconn' => $farm->account->id,
                        'id_farm' => $newFarm->id
                    ]); 
                    $zonesResponse = $client->request('GET', '/farms/'.$farm->id.'/zones', [
                        'headers' => [
                            'api_key' => '9Ev6ftyEbHhylMoKFaok',
                            'Accept'     => 'application/json'
                        ]
                    ]);
                    $zones=json_decode($zonesResponse->getBody()->getContents());
                    foreach ($zones as $key => $zone) {
                        if(is_null(Farm::where("id_wiseconn",$zone->id)->first())){
                            $newZone = Zone::create([
                                'name' => $zone->name,
                                'description' => $zone->description,
                                'latitude' => $zone->latitude,
                                'longitude' => $zone->longitude,
                                'id_farm' => $newFarm->id,
                                'kc' => $zone->kc,
                                'theoreticalFlow' => $zone->theoreticalFlow,
                                'unitTheoreticalFlow' => $zone->unitTheoreticalFlow,
                                'efficiency' => $zone->efficiency,
                                'humidityRetention' => $zone->humidityRetention,
                                'max' => $zone->max,
                                'min' => $zone->min,
                                'criticalPoint1' => $zone->criticalPoint1,
                                'criticalPoint2' => $zone->criticalPoint2,
                                'id_pump_system' => $zone->pumpSystemId,
                            ]);      
                        }  
                    }                        
                }
                
            }
            
            \Log::info("Success: Sync with wiseconn successfully");
        } catch (\Exception $e) {
            \Log::error("Error:" . $e->getMessage());
            \Log::error("Linea:" . $e->getLine());
        }      
    }
}
