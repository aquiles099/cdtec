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

class CloneByZoneMeasures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clonebyzone:measures:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone measures data by zone';

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
    protected function physicalConnectionCreate($measure){
        return PhysicalConnection::create([
            'expansionPort'=> isset($measure->physicalConnection->expansionPort)?$measure->physicalConnection->expansionPort:null,
            'expansionBoard'=> isset($measure->physicalConnection->expansionBoard)?$measure->physicalConnection->expansionBoard:null,
            'nodePort'=> isset($measure->physicalConnection->nodePort)?$measure->physicalConnection->nodePort:null
        ]);
    }
    protected function sensorTypeZoneCreate($sensorType,$zone){
        $sensorTypeZone=SensorTypeZones::where("id_sensor_type",$sensorType->id)->where("id_zone",$zone->id)->first();
        if(is_null($sensorTypeZone)){
            SensorTypeZones::create([
                "id_sensor_type"=>$sensorType->id,
                "id_zone" => isset($zone->id)?$zone->id:null,
            ]);
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
        $sensorType=SensorType::where("name",$name)->first();
        if(is_null($sensorType)){
            $newSensorType=SensorType::create([
                "name"=>$name,
                "group"=>$group,
                "id_farm" => isset($farm->id)?$farm->id:null,
            ]);
            $this->sensorTypeZoneCreate($newSensorType,$zone);
            return $newSensorType;
        }else{
            $this->sensorTypeZoneCreate($sensorType,$zone);
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
            $zones=Zone::all();
            foreach ($zones as $key => $zone) {
                $measuresResponse = $this->requestWiseconn($client,'GET','/zones/'.$zone->id_wiseconn.'/measures');
                $measures=json_decode($measuresResponse->getBody()->getContents());
                foreach ($measures as $key => $measure) {                    
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
                    if(!is_null($farm)&&!is_null($zone)){
                        $measureRegistered=Measure::where("id_wiseconn",$measure->id)->where("id_farm",$farm->id)->where("id_zone",$zone->id)->first();
                        if(is_null($measureRegistered)){
                            $newPhysicalConnection =$this->physicalConnectionCreate($measure);
                            $newmeasure =$this->measureCreate($measure,$farm,$zone,$node,$newPhysicalConnection);
                            if(isset($measure->sensorType)){
                                $newSensorType=$this->sensorTypeCreate($measure,$farm,$zone);
                                if(!is_null($newSensorType)){
                                    $this->info("New SensorType id:".$newSensorType->id);
                                }
                            }
                            $this->info("New Measure id:".$newmeasure->id." / New PhysicalConnection id:".$newPhysicalConnection->id);
                        }else{
                            $measureUpdated =$this->measureUpdate($measure,$measureRegistered,$farm,$zone,$node);
                            $this->info("Measure updated:".$measureUpdated->id);
                        }
                    }
                }
            }
            $this->info("Success: Clone measures data by zone by zone");
        } catch (\Exception $e) {
            $this->error("Error:" . $e->getMessage());
            $this->error("Linea:" . $e->getLine());
        } 
    }
}
