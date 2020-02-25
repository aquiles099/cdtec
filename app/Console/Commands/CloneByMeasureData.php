<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Zone;
use App\Measure;
use App\MeasureData;
class CloneByMeasureData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebymeasure:data:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone data by measure';

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
    protected function measureDataCreate($measure,$measureData){
        return MeasureData::create([
            'id_measure'=> isset($measure->id)?$measure->id:null,
            'value'=> isset($measureData->value)?$measureData->value:null,
            'time'=> isset($measureData->time)?$measureData->time:null
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
            $zonesId=Zone::whereIn("name",["Estación Meteorológica","Estación Metereológica"])->pluck("id");
            $measures=Measure::whereIn("id_zone",$zonesId)->get();
            $initTime=Carbon::now(date_default_timezone_get())->subDays(2)->format('Y-m-d');
            $endTime=Carbon::now(date_default_timezone_get())->format('Y-m-d');
            foreach ($measures as $mKey => $measure) {
                $measuresResponse = $this->requestWiseconn($client,'GET','/measures/'.$measure->id_wiseconn.'/data?initTime='.$initTime.'T00:00&endTime='.$endTime.'T00:00');
                $measuresData=json_decode($measuresResponse->getBody()->getContents());
                foreach ($measuresData as $mDkey => $measureData) {
                    if(is_null(MeasureData::where("id_measure",$measure->id)->where("time",$measureData->time)->first())){
                        $newMeasureData = $this->measureDataCreate($measure,$measureData);
                        $this->info("New MeasureData id:".$newMeasureData->id." Measures:(".($mKey+1)."/".count($measures).") Measures data:(".($mDkey+1)."/".count($measuresData).")");                        
                    }  
                }
            }
            $this->info("Success: Clone measures data by node");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        } 
    }
}
