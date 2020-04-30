<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Farm;
use App\Alarm;
use App\Zone;
use App\RealIrrigation;
use App\CloningErrors;

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
    protected function cloneBy($alarm){
        $zone=Zone::where("id_wiseconn",$alarm->zoneId)->first();
        $realIrrigation=RealIrrigation::where("id_wiseconn",$alarm->realIrrigationId)->first();
        if(is_null(Alarm::where("id_wiseconn",$alarm->id)->first())&&!is_null($zone)&&!is_null($realIrrigation)){
            $newAlarm= $this->alarmCreate($alarm,$farm,$zone,$realIrrigation);
            $this->info("New alarm, id:".$newAlarm->id);
        }else{
            $this->info("Elemento existente");
        }
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        try {
            $cloningErrors=CloningErrors::where("elements","/farms/id/alarms")->get();
            if(count($cloningErrors)>0){
                foreach ($cloningErrors as $key => $cloningError) {
                    $alarmsResponse = $this->requestWiseconn('GET',$cloningError->uri);
                    $alarms=json_decode($alarmsResponse->getBody()->getContents());
                    $this->info("==========Clonando pendientes por error en peticion (".count($alarms)." elementos)");
                    foreach ($alarms as $key => $alarm) {
                        $this->cloneBy($alarm);
                    }
                    $cloningError->delete();
                }
            }else{
                $farms=Farm::all();
                $initTime=Carbon::now(date_default_timezone_get())->subDays(5)->format('Y-m-d');
                $endTime=Carbon::now(date_default_timezone_get())->format('Y-m-d');
                foreach ($farms as $key => $farm) {
                    try {
                        $currentRequestUri='/farms/'.$farm->id_wiseconn.'/alarms/triggered/?endTime='.$endTime.'&initTime='.$initTime;
                        $currentRequestElement='/farms/id/alarms';
                        $id_wiseconn=$farm->id_wiseconn;
                        $alarmsResponse = $this->requestWiseconn('GET',$currentRequestUri);
                        $alarms=json_decode($alarmsResponse->getBody()->getContents());
                        $this->info("==========Clonando nuevos elementos (".count($alarms)." elementos)");
                        foreach ($alarms as $key => $alarm) {
                            $this->cloneBy($alarm);
                        }
                    } catch (\Exception $e) {
                        $this->error("Error:" . $e->getMessage());
                        $this->error("Linea:" . $e->getLine());
                        $this->error("currentRequestUri:" . $currentRequestUri);
                        $this->error("currentRequestElement:" . $currentRequestElement);
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
            
            # code...
            $this->info("Success: Clone farms, accounts and nodes data");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        }    
    }
}
