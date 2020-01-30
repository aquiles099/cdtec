<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Farm;
use App\Alarm;
use App\Zone;
use App\RealIrrigation;
use Carbon\Carbon;

class CloneByFarmAlarms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebyfarm:alarms:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone alarms by farm';

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
    protected function alarmCreate($alarm,$farm,$zone,$realIrrigation){
        return Alarm::create([
            'activationValue' => $alarm->activationValue,
            'description' => $alarm->description,
            'date' => $alarm->date,
            'id_farm' => $farm->id,
            'id_zone' => $zone->id,
            'id_real_irrigation' => $realIrrigation->id,
            'id_wiseconn' => $alarm->id,
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
        $initTime=Carbon::now(date_default_timezone_get())->format('Y-m-d');
        $endTime=Carbon::now(date_default_timezone_get())->addDays(15)->format('Y-m-d');
        try {
            $farms=Farm::all();
            foreach ($farms as $key => $farm) {
                $alarmsResponse = $this->requestWiseconn($client,'GET','/farms/'.$farm->id_wiseconn.'/alarms/triggered/?endTime='.$endTime.'&initTime='.$initTime);
                $alarms=json_decode($alarmsResponse->getBody()->getContents());
                foreach ($alarms as $key => $alarm) {
                    $zone=Zone::where("id_wiseconn",$alarm->zoneId)->first();
                    $realIrrigation=RealIrrigation::where("id_wiseconn",$alarm->realIrrigationId)->first();
                    if(is_null(Alarm::where("id_wiseconn",$alarm->id)->first())&&!is_null($zone)&&!is_null($realIrrigation)){
                        $newAlarm= $this->alarmCreate($alarm,$farm,$zone,$realIrrigation);
                        $this->info("New alarm, id:".$newAlarm->id);
                    }
                }                
            }
            # code...
            $this->info("Success: Clone farms, accounts and nodes data");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        }    
    }
}
