<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Farm;
use App\Account;
use App\Zone;
use App\Node;
use App\Hydraulic;
use App\Pump_system;
use App\Measure;
use App\Irrigation;
use App\RealIrrigation;
use App\Alarm;
use App\Type;
use App\SouthWestBound;
use App\NorthEastBound;
use App\SensorType;
use App\SensorTypeZones;
use App\Path;
use App\CloningErrors;
class FarmController extends Controller
{
    protected function requestWiseconn($client,$method,$uri){
        return $client->request($method, $uri, [
            'headers' => [
                'api_key' => '9Ev6ftyEbHhylMoKFaok',
                'Accept'     => 'application/json'
            ]
        ]);
    }
    public function all(){
        try {
            //forzando no clonar desde controlador por lentitud en tiempo de respuesta 
            //$cloningErrors=CloningErrors::where("elements","/farms")->where("uri","/farms")->get();
            //if(count($cloningErrors)>0){
            if(false){
                $farmsIdsToClone=[520,1378,185,2110,719];
                foreach ($cloningErrors as $key => $cloningError) {
                    try{
                        $wiseconnFarms = json_decode(($this->requestWiseconn(new Client([
                            'base_uri' => 'https://apiv2.wiseconn.com',
                            'timeout'  => 100.0,
                        ]),'GET',$cloningError->uri))->getBody()->getContents());
                        foreach ($wiseconnFarms as $key => $wiseconnFarm) {
                            if(isset($wiseconnFarm->id)){
                                if(array_search($wiseconnFarm->id, $farmsIdsToClone)){
                                    $farm=Farm::where("id_wiseconn",$wiseconnFarm->id)->first();
                                    if(is_null($farm)){
                                        if(isset($wiseconnFarm->account)){
                                            $account=Account::where("id_wiseconn",$wiseconnFarm->account->id)->first();
                                            if(!$account){
                                                $account = Account::create([
                                                    'name' => $wiseconnFarm->account->name,
                                                    'id_wiseconn' => $wiseconnFarm->account->id,
                                                ]);
                                            }
                                        }
                                        $farm=Farm::create([
                                            'name' => $wiseconnFarm->name,
                                            'description' => $wiseconnFarm->description,
                                            'latitude' => $wiseconnFarm->latitude,
                                            'longitude' => $wiseconnFarm->longitude,
                                            'postalAddress' => $wiseconnFarm->postalAddress,
                                            'timeZone' => $wiseconnFarm->timeZone,
                                            'webhook' => $wiseconnFarm->webhook,
                                            'id_account' => $account?$account->id:null,
                                            'id_wiseconn' => $wiseconnFarm->id,
                                        ]);
                                        try{
                                            $nodesResponse = json_decode(($this->requestWiseconn(new Client([
                                                'base_uri' => 'https://apiv2.wiseconn.com',
                                                'timeout'  => 100.0,
                                            ]),'GET','/farms/'.$farm->id_wiseconn.'/nodes'))->getBody()->getContents());
                                            foreach ($nodesResponse as $key => $node) {
                                                if(isset($node->id)){
                                                    if(is_null(Node::where("id_wiseconn",$node->id)->first())){
                                                        Node::create([
                                                            'name' => $node->name,
                                                            'lat' => $node->lat,
                                                            'lng' => $node->lng,
                                                            'nodeType' => $node->nodeType,
                                                            'id_farm' => $farm->id,
                                                            'id_wiseconn' => $node->id
                                                        ]);
                                                    }   
                                                }
                                            }
                                        } catch (\Exception $e) {
                                            return response()->json([
                                                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                                                'error' => $e->getMessage(),
                                                'linea' => $e->getLine()
                                            ], 500);
                                        }
                                    }
                                }
                            }
                        }
                        $cloningError->delete();
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                            'error' => $e->getMessage(),
                            'linea' => $e->getLine()
                        ], 500);
                    }     
                }
            }
            $response = [
                'message'=> 'Lista de campos',
                'data' => Farm::with("account")->get(),
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function get($id){
        try {            
            $element = Farm::with("account")->find($id);
            if(is_null($element)){
                return response()->json([
                    'message'=>'Campo no exitente',
                    'data'=>$element
                ],404);
            }
            $response = [
                'message'=> 'Campo encontrado satisfactoriamente',
                'data' => $element,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:45',
            'description'     => 'required|string|max:45',
            'latitude'        => 'required|string|max:45',
            'longitude'       => 'required|string|max:45',
            'postalAddress'   => 'required|string|max:45',
            'timeZone'        => 'required|string|max:45',
            'webhook'         => 'required|string|max:45'
        ],[
            'name.required'            => 'El name es requerido',
            'name.max'                 => 'El name debe contener como máximo 45 caracteres',
            'description.required'     => 'El description es requerido',
            'description.max'          => 'El description debe contener como máximo 45 caracteres',
            'latitude.required'        => 'El latitude es requerido',
            'latitude.max'             => 'El latitude debe contener como máximo 45 caracteres',
            'longitude.required'       => 'El longitude es requerido',
            'longitude.max'            => 'El longitude debe contener como máximo 45 caracteres',
            'postalAddress.required'   => 'El postalAddress es requerido',
            'postalAddress.max'        => 'El postalAddress debe contener como máximo 45 caracteres',
            'timeZone.required'        => 'El timeZone es requerido',
            'timeZone.max'             => 'El timeZone debe contener como máximo 45 caracteres',
            'webhook.required'         => 'El webhook es requerido',
            'webhook.max'              => 'El webhook debe contener como máximo 45 caracteres'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $element = Farm::create([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'latitude' => $request->get('latitude'),
                'longitude' => $request->get('longitude'),
                'postalAddress' => $request->get('postalAddress'),
                'timeZone' => $request->get('timeZone'),
                'webhook' => $request->get('webhook'),
            ]);
            $response = [
                'message'=> 'Campo registrado satisfactoriamente',
                'data' => $element,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:45',
            'description'     => 'required|string|max:45',
            'webhook'         => 'required|string|max:45',
        ],[
            'name.required'            => 'El name es requerido',
            'name.max'                 => 'El name debe contener como máximo 45 caracteres',
            'description.required'     => 'El description es requerido',
            'description.max'          => 'El description debe contener como máximo 45 caracteres',
            'webhook.required'         => 'El webhook es requerido',
            'webhook.max'              => 'El webhook debe contener como máximo 45 caracteres'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $element = Farm::find($id);
            if(is_null($element)){
                return response()->json(["message"=>"Campo no existente"],404);
            }
            $element->fill($request->all());
            $response = [
                'message'=> 'Campo actualizado satisfactoriamente',
                'data' => $element,
            ];
            $element->update();
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    /*protected function getWiseconnElements($zone,$initTime,$endTime){
        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);        
        try {
            $realIrrigationsResponse = $client->request('GET','/zones/'.$zone->id_wiseconn.'/realIrrigations/?endTime='.$endTime.'&initTime='.$initTime, [
                'headers' => [
                    'api_key' => '9Ev6ftyEbHhylMoKFaok',
                    'Accept'     => 'application/json'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
        return json_decode($realIrrigationsResponse->getBody()->getContents());
    }*/
    protected function zoneCreate($zone,$farm){
        $newZone=Zone::create([
            'name' => isset($zone->name)?$zone->name:null,
            'description' => isset($zone->description)?$zone->description:null,
            'latitude' => isset($zone->latitude)?$zone->latitude:null,
            'longitude' => isset($zone->longitude)?$zone->longitude:null,
            'id_farm' => isset($farm->id)?$farm->id:null,
            'kc' => isset($zone->kc)?$zone->kc:null,
            'theoreticalFlow' => isset($zone->theoreticalFlow)?$zone->theoreticalFlow:null,
            'unitTheoreticalFlow' => isset($zone->unitTheoreticalFlow)?$zone->unitTheoreticalFlow:null,
            'efficiency' => isset($zone->efficiency)?$zone->efficiency:null,
            'humidityRetention' => isset($zone->humidityRetention)?$zone->humidityRetention:null,
            'max' => isset($zone->max)?$zone->max:null,
            'min' => isset($zone->min)?$zone->min:null,
            'criticalPoint1' => isset($zone->criticalPoint1)?$zone->criticalPoint1:null,
            'criticalPoint2' => isset($zone->criticalPoint2)?$zone->criticalPoint2:null,
            'id_pump_system' => isset($zone->pumpSystemId)?$zone->pumpSystemId:null,
            'id_wiseconn' => isset($zone->id)?$zone->id:null
        ]);
        if(isset($zone->type)){
            foreach ($zone->type as $key => $type) {
                Type::create([
                    'description'=>$type,
                    'id_zone'=>$newZone->id,
                ]);
            }
        }
        if(isset($zone->polygon->path)){
            foreach ($zone->polygon->path as $key => $path) {
                Path::create([
                    'id_zone' => $newZone->id,
                    'lat' => $path->lat,
                    'lng' => $path->lng,
                ]);
            }
        }
        if(isset($zone->polygon->bounds->southWest)){
            SouthWestBound::create([
                'id_zone' => $newZone->id,
                'lat' => $zone->polygon->bounds->southWest->lat,
                'lng' => $zone->polygon->bounds->southWest->lng,
            ]);
        }
        if(isset($zone->polygon->bounds->northEast)){
            NorthEastBound::create([
                'id_zone' => $newZone->id,
                'lat' => $zone->polygon->bounds->northEast->lat,
                'lng' => $zone->polygon->bounds->northEast->lng,
            ]);
        }
        return $newZone;
    }
    public function zones($id){
        try {
            $farm=Farm::find($id);
            $zones = Zone::where("id_farm",$farm->id)->get();
            //forzando no clonar desde controlador por lentitud en tiempo de respuesta 
            //$cloningErrors=CloningErrors::where("elements","/farms/id/zones")->where("uri","/farms/".$farm->id_wiseconn."/zones")->where("id_wiseconn",$farm->id_wiseconn)->get();
            //if(count($cloningErrors)>0){
            if(false){
                foreach ($cloningErrors as $key => $cloningError) {
                    try{
                        $wiseconnZones = json_decode(($this->requestWiseconn(new Client([
                            'base_uri' => 'https://apiv2.wiseconn.com',
                            'timeout'  => 100.0,
                        ]),'GET',$cloningError->uri))->getBody()->getContents());
                        foreach ($wiseconnZones as $key => $wiseconnZone) {
                            if(isset($wiseconnZone->id)){
                                $zone=Zone::where("id_wiseconn",$wiseconnZone->id)->first();
                                if(is_null($zone)){
                                    $pumpSystem=Pump_system::where("id_wiseconn",$wiseconnZone->pumpSystemId)->first();
                                    $element = $this->zoneCreate($wiseconnZone,$farm);
                                    if(isset($wiseconnZone->polygon->path)){
                                        foreach ($wiseconnZone->polygon->path as $key => $path) {
                                            Path::create([
                                                'id_zone' => $element->id,
                                                'lat' => $path->lat,
                                                'lng' => $path->lng,
                                            ]);
                                        }
                                    }
                                    $farm->touch();
                                }
                            }
                        }
                        $cloningError->delete();
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                            'error' => $e->getMessage(),
                            'linea' => $e->getLine()
                        ], 500);
                    }
                }
            }
            $response = [
                'message'=> 'Lista de zonas',
                'data' => Zone::where("id_farm",$farm->id)->get(),
            ];
            return response()->json($response, 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function hydraulics($id){
        try {            
            $elements = Hydraulic::where("id_farm",$id)
            ->with("farm")
            ->with("zone")
            ->with("node")
            ->with("physicalConnection")
            ->get();
            $response = [
                'message'=> 'Lista de Hydraulic',
                'data' => $elements,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function pumpsystems($id){
        try {            
            $elements = Pump_system::where("id_farm",$id)->with("farm")->get();
            $response = [
                'message'=> 'Lista de PumpSystem',
                'data' => $elements,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function measures($id){
        try {
            $elements = Measure::where("id_farm",$id)->with("zone")->get();
            $response = [
                'message'=> 'Lista de Measure',
                'data' => $elements,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function nodes($id){
        try {            
            $elements = Node::where("id_farm",$id)->with("farm")->get();
            $response = [
                'message'=> 'Lista de Node',
                'data' => $elements,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function irrigations($id){
        try {            
            $elements = Irrigation::where("id_farm",$id)->with("zone")->with("volume")->with("pumpSystem")->get();
            $response = [
                'message'=> 'Lista de Irrigation',
                'data' => $elements,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function realIrrigations($id){
        try {            
            $elements = RealIrrigation::where("id_farm",$id)->with("zone")->with("pumpSystem")->with("irrigations")->get();
            $response = [
                'message'=> 'Lista de RealIrrigations',
                'data' => $elements,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function alarmsTriggered(Request $request,$id){
        try {
            $elements = Alarm::where("id_farm",$id)->whereBetween('date', [$request->get('initTime'), $request->get('endTime')])->get();
            $response = [
                'message'=> 'Lista de Alarm',
                'data' => $elements,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function webhookUpdate(Request $request,$id){
        try {
            $element = Farm::find($id);
            if(is_null($element)){
                return response()->json(['message'=>'Campo no existente'],404);
            }
            $element->webhook=$request->get("webhook");
            $element->update();
            $response = [
                'message'=> 'Campo actualizado satisfactoriamente',
                'data' => $element,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    protected function sensorTypeZoneCreate($sensorType,$farm,$zone){
        $sensorTypeZone=SensorTypeZones::where("id_sensor_type",$sensorType->id)->where("id_farm",$farm->id)->where("id_zone",$zone->id)->first();
        if(is_null($sensorTypeZone)){
            SensorTypeZones::create([
                "id_sensor_type"=>$sensorType->id,
                "id_farm" => isset($farm->id)?$farm->id:null,
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
        $sensorType=SensorType::where("name",$name)->where("id_farm",$farm->id)->first();
        if(is_null($sensorType)){
            $newSensorType=SensorType::create([
                "name"=>$name,
                "group"=>$group,
                "id_farm" => isset($farm->id)?$farm->id:null,
            ]);
            $this->sensorTypeZoneCreate($newSensorType,$farm,$zone);
            return $newSensorType;
        }else{
            $this->sensorTypeZoneCreate($sensorType,$farm,$zone);
        }
        return null;
    }
    public function sensorTypes($id){
        try {
            $farm=Farm::find($id);
            if(is_null($farm)){
                return response()->json([
                    "message"=>"Campo no existente",
                    "data"=>$farm
                ],404);
            }
            //forzando no clonar desde controlador por lentitud en tiempo de respuesta 
            //$cloningErrors=CloningErrors::where("elements","/farms/id/measures")->where("uri","/farms/".$farm->id_wiseconn."/measures")->where("id_wiseconn",$farm->id_wiseconn)->get();
            //if(count($cloningErrors)>0){
            if(false){
                foreach ($cloningErrors as $key => $cloningError) {
                    try{
                        $measures=json_decode(($this->requestWiseconn(new Client([
                            'base_uri' => 'https://apiv2.wiseconn.com',
                            'timeout'  => 100.0,
                        ]),'GET',$cloningError->uri))->getBody()->getContents());
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
                             $measureRegistered=Measure::where("id_wiseconn",$measure->id)
                             ->where("id_farm",$farm->id)
                             ->where("id_zone",$zone->id)
                             ->first();
                                if(is_null($measureRegistered)){
                                    if(isset($measure->sensorType)){
                                        $newSensorType=$this->sensorTypeCreate($measure,$farm,$zone);
                                    }

                                }
                            }
                        }
                        $cloningError->delete();
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
                            'error' => $e->getMessage(),
                            'linea' => $e->getLine()
                        ], 500);
                    }
                }
                $farm->touch();
            }
        $response = [
            'message'=> 'Lista de SensorTypes',
            'data' => SensorType::where("id_farm",$farm->id)->with("zones")->get(),
        ];
        return response()->json($response, 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
            'error' => $e->getMessage(),
            'linea' => $e->getLine()
        ], 500);
    }
}
public function weatherStation($id){
    try {
        $weatherStation = Zone::where("id_farm",$id)
        ->where("name","Estación Meteorológica")
        ->orWhere("name","Estación Metereológica")
        ->first();
        $response = [
            'message'=> 'Lista de zonas',
            'data' => $weatherStation,
        ];
        return response()->json($response, 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Ha ocurrido un error al tratar de obtener los datos.',
            'error' => $e->getMessage(),
            'linea' => $e->getLine()
        ], 500);
    }
}
}
