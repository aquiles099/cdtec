<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Zone;
use App\Measure;
use App\MeasureData;
use App\CloningErrors;
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
        $measure->lastMeasureDataUpdate=Carbon::today();
        $measure->update();
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
            //$zonesId=Zone::whereIn("name",["Estación Meteorológica","Estación Metereológica"])->pluck("id");
            //$measures=Measure::whereIn("id_zone",$zonesId)->get();
            $measures=Measure::distinct('id_wiseconn')->get();//all();
            $initTime=Carbon::now(date_default_timezone_get())->subDays(5)->format('Y-m-d');
            $endTime=Carbon::now(date_default_timezone_get())->addDays(1)->format('Y-m-d');
            foreach ($measures as $mKey => $measure) {
                try{
                    $currentRequestUri='/measures/'.$measure->id_wiseconn.'/data?initTime='.$initTime.'T00:00&endTime='.$endTime.'T00:00';
                    $currentRequestElement='/measures/id/data';
                    $id_wiseconn=$measure->id_wiseconn;
                    $measuresResponse = $this->requestWiseconn($client,'GET',$currentRequestUri);
                    $measuresData=json_decode($measuresResponse->getBody()->getContents());
                    foreach ($measuresData as $mDkey => $measureData) {
                        if(is_null(MeasureData::where("id_measure",$measure->id)->where("time",$measureData->time)->first())){
                            $newMeasureData = $this->measureDataCreate($measure,$measureData);
                            $this->info("New MeasureData id:".$newMeasureData->id." Measures:(".($mKey+1)."/".count($measures).") Measures data:(".($mDkey+1)."/".count($measuresData).")");
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
            $this->info("Success: Clone measures data by node");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        } 
    }
}
