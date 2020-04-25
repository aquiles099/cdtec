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
use App\SensorType;
use App\Path;
class FarmController extends Controller
{
    protected function getWiseconnFarms(){
        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);        
        try {
            $farmsResponse = $client->request('GET','/farms', [
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
        return json_decode($farmsResponse->getBody()->getContents());
    }
    protected function getWiseconnNodes($farm){
        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);        
        try {
            $nodesResponse = $client->request('GET','/farms/'.$farm->id_wiseconn.'/nodes', [
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
        return json_decode($nodesResponse->getBody()->getContents());
    }
    public function all(){
        try {
            $farms=Farm::with("account")->get();
            if(count($farms)==0){
                $wiseconnFarms = $this->getWiseconnFarms();
                foreach ($wiseconnFarms as $key => $wiseconnFarm) {
                    if(isset($wiseconnFarm->id)){
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
                            $nodesResponse = $this->getWiseconnNodes($farm);
                            foreach ($nodesResponse as $key => $node) {
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
    protected function getWiseconnZones($farm){
        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);        
        try {
            $zonesResponse = $client->request('GET','/farms/'.$farm->id_wiseconn.'/zones/', [
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
        return json_decode($zonesResponse->getBody()->getContents());
    }
    public function zones($id){
        try {            
            $initTime=Carbon::now(date_default_timezone_get())->subDays(2)->format('Y-m-d');
            $endTime=Carbon::now(date_default_timezone_get())->format('Y-m-d');
            $farm=Farm::find($id);
            $zones = Zone::where("id_farm",$farm->id)->get();
            $today = Carbon::today();
            $wiseconnZones=[];
            $wiseconnZones = $this->getWiseconnZones($farm);
            if((Carbon::parse(Carbon::parse($today)->format('Y-m-d').'T00:00:00.000000Z')->isAfter(Carbon::parse($farm->updated_at)->format('Y-m-d').'T00:00:00.000000Z'))||count($zones)==0||(count($zones)<count($wiseconnZones))){
                foreach ($wiseconnZones as $key => $wiseconnZone) {
                    if(isset($wiseconnZone->id)){
                        $zone=Zone::where("id_wiseconn",$wiseconnZone->id)->first();
                        if(is_null($zone)){
                            $pumpSystem=Pump_system::where("id_wiseconn",$wiseconnZone->pumpSystemId)->first();
                            $element = Zone::create([
                                'name' => $wiseconnZone->name,
                                'description' => $wiseconnZone->description,
                                'latitude' => $wiseconnZone->latitude,
                                'longitude' => $wiseconnZone->longitude,
                                'type' => $wiseconnZone->type,
                                'kc' => $wiseconnZone->kc,
                                'theoreticalFlow' => $wiseconnZone->theoreticalFlow,
                                'unitTheoreticalFlow' => $wiseconnZone->unitTheoreticalFlow,
                                'efficiency' => $wiseconnZone->efficiency,
                                'humidityRetention' => $wiseconnZone->humidityRetention,
                                'max' => $wiseconnZone->max,
                                'min' => $wiseconnZone->min,
                                'criticalPoint1' => $wiseconnZone->criticalPoint1,
                                'criticalPoint2' => $wiseconnZone->criticalPoint2,
                                'id_farm' => $farm->id,
                                'id_pump_system' => isset($pumpSystem->id)?$pumpSystem->id:null,
                                'id_wiseconn' => isset($wiseconnZone->id)?$wiseconnZone->id:null
                            ]); 
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
            }
            $response = [
                'message'=> 'Lista de zonas',
                'wiseconnZones'=>$wiseconnZones,
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
    public function sensorTypes($id){
        try {            
            $elements = SensorType::where("id_farm",$id)->with("zones")->get();
            $response = [
                'message'=> 'Lista de SensorTypes',
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
