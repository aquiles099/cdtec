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
use App\SensorType;
use App\SensorTypeZones;
use App\CloningErrors;
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
    protected function physicalConnectionCreate($measure){
        return PhysicalConnection::create([
            'expansionPort'=> isset($measure->physicalConnection)?$measure->physicalConnection->expansionPort:null,
            'expansionBoard'=> isset($measure->physicalConnection)?$measure->physicalConnection->expansionBoard:null,
            'nodePort'=> isset($measure->physicalConnection)?$measure->physicalConnection->nodePort:null
        ]);
    }
    protected function sensorTypeZoneCreate($sensorType,$farm,$zone){
        if($sensorType){
            $sensorTypeZone=SensorTypeZones::where("id_sensor_type",$sensorType->id)->where("id_farm",$farm->id)->where("id_zone",$zone->id)->first();
            if(is_null($sensorTypeZone)){
                SensorTypeZones::create([
                    "id_sensor_type"=>$sensorType->id,
                    "id_farm" => isset($farm->id)?$farm->id:null,
                    "id_zone" => isset($zone->id)?$zone->id:null,
                ]);
            } 
        }               
    }
    protected function getNameAndGroup($sensorType){
        switch (strtolower($sensorType)) {
            //clima
            case 'temperature':
            return ['name'=>'Temperatura','group'=>'Clima'];
            break;
            case 'humidity':
            return ['name'=>'Humedad Relativa','group'=>'Clima'];
            break;
            case 'wind velocity':
            return ['name'=>'Velocidad Viento','group'=>'Clima'];
            break;
            case 'solar radiation':
            return ['name'=>'Radiación Solar','group'=>'Clima'];
            break;
            case 'wind direction':
            return ['name'=>'Dirección Viento','group'=>'Clima'];
            break;
            case 'atmospheric preassure':
            return ['name'=>'Presión Atmosférica','group'=>'Clima'];
            break;
            case 'wind gust':
            return ['name'=>'Ráfaga Viento','group'=>'Clima'];
            break;
            case 'chill hours':
            return ['name'=>'Horas Frío','group'=>'Clima'];
            break;
            case 'chill portion':
            return ['name'=>'Porción Frío','group'=>'Clima'];
            break;
            case 'daily etp':
            return ['name'=>'Etp Diaria','group'=>'Clima'];
            break;
            case 'daily et0':
            return ['name'=>'Et0 Diaria','group'=>'Clima'];
            break;
            //humedad
            case 'salinity':
            return ['name'=>'Salinidad','group'=>'Humedad'];
            break;
            case 'soil temperature':
            return ['name'=>'Temperatura Suelo','group'=>'Humedad'];
            break;
            case 'soil moisture':
            return ['name'=>'Humedad Suelo','group'=>'Humedad'];
            break;
            case 'soil humidity':
            return ['name'=>'Humedad de Tubo','group'=>'Humedad'];
            break;
            case 'added soild moisture':
            return ['name'=>'Suma Humedades','group'=>'Humedad'];
            break;
            //Riego
            case 'irrigation':
            return ['name'=>'Riego','group'=>'Riego'];
            break;
            case 'irrigation volume':
            return ['name'=>'Volumen Riego','group'=>'Riego'];
            break;
            case 'daily irrigation time':
            return ['name'=>'Tiempo de Riego Diario','group'=>'Riego'];
            break;
            case 'flow':
            return ['name'=>'Caudal','group'=>'Riego'];
            break;
            case 'daily irrigation volume by pump system':
            return ['name'=>'Volumen de Riego Diario por Equipo','group'=>'Riego'];
            break;
            case 'daily irrigation time by pump system':
            return ['name'=>'Tiempo de Riego Diario por Equipo','group'=>'Riego'];
            break;
            case 'irrigation by pump system':
            return ['name'=>'Riego por Equipo','group'=>'Riego'];
            break;
            case 'flow by zone':
            return ['name'=>'Caudal por Sector','group'=>'Riego'];
            break;
            default:
            return ['name'=>$sensorType,'group'=>'Otros'];
            break;
        }
    }
    protected function sensorTypeCreate($measure,$farm,$zone){
        $name=$this->getNameAndGroup($measure->sensorType)['name'];
        $group=$this->getNameAndGroup($measure->sensorType)['group'];
        $sensorType=SensorType::where("name",$name)->where("id_farm",$farm->id)->first();
        if(is_null($sensorType)){
            $newSensorType=new SensorType();
            $newSensorType->name=$name;
            $newSensorType->group=$group;
            $newSensorType->id_farm=isset($farm->id)?$farm->id:null;
            $newSensorType->save();
            return $newSensorType;
        }
        return null;
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
    protected function measureUpdate($measure,$measureRegistered,$farm,$zone,$node){
        $measureRegistered->name=isset($measure->name)?$measure->name:null;
        $measureRegistered->unit=isset($measure->unit)?$measure->unit:null;
        $measureRegistered->lastData=isset($measure->lastData)?$measure->lastData:null;
        $measureRegistered->lastDataDate=isset($measure->lastDataDate)?(Carbon::parse($measure->lastDataDate)):null;
        $measureRegistered->monitoringTime=isset($measure->monitoringTime)?$measure->monitoringTime:null;
        $measureRegistered->sensorDepth=isset($measure->sensorDepth)?$measure->sensorDepth:null;
        $measureRegistered->depthUnit=isset($measure->depthUnit)?$measure->depthUnit:null;
        $measureRegistered->sensorType=isset($measure->sensorType)?$measure->sensorType:null;
        $measureRegistered->id_farm=isset($farm->id)?$farm->id:null;
        $measureRegistered->id_zone=isset($zone->id)?$zone->id:null;
        $measureRegistered->id_node=isset($node->id)?$node->id:null;
        $measureRegistered->update();
        return $measureRegistered; 
    }
    protected function cloneBy($measure){
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
        $measuresToClone=[
            "temperature",
            "humidity",
            "wind velocity",
            "solar radiation",
            "wind direction",
            "atmospheric preassure",
            "wind gust",
            "chill hours",
            "chill portion",
            "daily etp",
            "daily et0",
            "salinity",
            "soil temperature",
            "soil humidity",
            "added soild moisture",
            "irrigation",
            "irrigation volume",
            "daily irrigation time",
            "flow",
            "daily irrigation volume by pump system",
            "daily irrigation time by pump system",
            "irrigation by pump system",
            "flow by zone"
        ];
        if(!isset($measure->sensorType)){
            $measure->sensorType=$measure->name;
        }
        if(array_search(strtolower($measure->name), $measuresToClone)>=0||array_search(strtolower($measure->sensorType), $measuresToClone)>=0){
            $measureRegistered=Measure::where("id_wiseconn",$measure->id)->first();
            if(is_null($measureRegistered)){
                $newPhysicalConnection =$this->physicalConnectionCreate($measure);
                $newmeasure =$this->measureCreate($measure,$farm,$zone,$node,$newPhysicalConnection);
                if(isset($measure->sensorType)){
                    $newSensorType=$this->sensorTypeCreate($measure,$farm,$zone);
                    $this->sensorTypeZoneCreate($newSensorType,$farm,$zone);
                }
                $this->info("New Measure id:".$newmeasure->id." / New PhysicalConnection id:".$newPhysicalConnection->id);
            }else {
                $measureUpdated =$this->measureUpdate($measure,$measureRegistered,$farm,$zone,$node);
                $this->info("Measure updated:".$measureUpdated->id);
            }  
        } 
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
            $farms=Farm::all();
            foreach ($farms as $key => $farm) {
                if($farm->active_cloning==1){
                    try{
                        $cloningErrors=CloningErrors::where("elements","/farms/id/measures")->get();
                        if(count($cloningErrors)>0){
                            foreach ($cloningErrors as $key => $cloningError) {
                                $measuresResponse = $this->requestWiseconn('GET',$cloningError->uri);
                                $measures=json_decode($measuresResponse->getBody()->getContents());
                                $this->info("==========Clonando pendientes por error en peticion (".count($measures)." elementos)");
                                foreach ($measures as $key => $measure) {                               
                                    $this->cloneBy($measure);
                                }
                                $cloningError->delete();
                            }
                        }else{
                            try{
                                $currentRequestUri='/farms/'.$farm->id_wiseconn.'/measures';
                                $currentRequestElement='/farms/id/measures';
                                $id_wiseconn=$farm->id_wiseconn;
                                $measuresResponse = $this->requestWiseconn('GET',$currentRequestUri);
                                $measures=json_decode($measuresResponse->getBody()->getContents());
                                $this->info("==========Clonando nuevos elementos (".count($measures)." elementos)");
                                foreach ($measures as $key => $measure) {
                                    $this->cloneBy($measure);
                                }
                            } catch (\Exception $e) {
                                $this->error("Error:" . $e->getMessage());
                                $this->error("Linea:" . $e->getLine());
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
                    } catch (\Exception $e) {
                        $this->error("Error:" . $e->getMessage());
                        $this->error("Linea:" . $e->getLine());
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
