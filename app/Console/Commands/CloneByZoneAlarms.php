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
use App\CloningErrors;

class CloneByZoneAlarms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebyzone:alarms:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone alarms by zone';

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
    public function cloneBy($alarm,$zone){
        $farm=Farm::where("id_wiseconn",$alarm->farmId)->first();
        $realIrrigation=RealIrrigation::where("id_wiseconn",$alarm->realIrrigationId)->first();
        if(is_null(Alarm::where("id_wiseconn",$alarm->id)->first())&&!is_null($farm)&&!is_null($realIrrigation)){
            $newAlarm= $this->alarmCreate($alarm,$farm,$zone,$realIrrigation);
            $this->info("New Alarm id:".$newAlarm->id);
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
        $initTime=Carbon::now(date_default_timezone_get())->subDays(10)->format('Y-m-d');
        $endTime=Carbon::now(date_default_timezone_get())->addDays(5)->format('Y-m-d');
        try {
            $zones=Zone::all();
            foreach ($zones as $key => $zone) {
                $cloningErrors=CloningErrors::where("elements","/zones/id/alarms/triggered")->get();
                if(count($cloningErrors)>0){
                    foreach ($cloningErrors as $key => $cloningError) {
                        $alarmsResponse = $this->requestWiseconn('GET',$cloningError->uri);
                        $alarms=json_decode($alarmsResponse->getBody()->getContents());
                        $this->info("==========Clonando pendientes por error en peticion (".count($alarms)." elementos)");
                        foreach ($alarms as $key => $alarm) {
                            $this->cloneBy($alarm,$zone);
                        }
                        $cloningError->delete();
                    }
                }else{
                    try {
                        $currentRequestUri='/zones/'.$zone->id_wiseconn.'/alarms/triggered/?endTime='.$endTime.'&initTime='.$initTime;
                        $currentRequestElement='/zones/id/alarms/triggered';
                        $id_wiseconn=$zone->id_wiseconn;
                        $alarmsResponse = $this->requestWiseconn('GET',$currentRequestUri);
                        $alarms=json_decode($alarmsResponse->getBody()->getContents());
                        $this->info("==========Clonando nuevos elementos (".count($alarms)." elementos)");
                        foreach ($alarms as $key => $alarm) {
                            $this->cloneBy($alarm,$zone);
                        }
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                        $this->error($e->getLine());
                        $this->error("currentRequestUri:" . $currentRequestUri);
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
            $this->info("Success: Clone farms, accounts and nodes data by zone");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            $this->error($e->getLine());
        }    
    }
}
