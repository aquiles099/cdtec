<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Zone;
use App\Farm;
use App\Pump_system;
use App\Measure;
use App\Irrigation;
use App\Hydraulic;
use App\Alarm;
use App\RealIrrigation;
use App\Polygon;
use App\PhysicalConnection;
class ZoneController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'                 => 'required|string|max:45',
            'description'          => 'required|string|max:45',
            'latitude'             => 'required|numeric|max:45',
            'longitude'            => 'required|numeric|max:45',
            'type'                 => 'required',
            'kc'                   => 'required|integer',
            'theoreticalFlow'      => 'required|integer',
            'unitTheoreticalFlow'  => 'required|string|max:45',
            'efficiency'           => 'required|integer',
            'humidityRetention'    => 'required|integer',
            'max'                  => 'required|integer',
            'min'                  => 'required|integer',
            'criticalPoint1'       => 'required|integer',
            'criticalPoint2'       => 'required|integer',
            'id_farm'              => 'required|integer',
            'id_pump_system'       => 'required|integer',
        ],[
            'name.required'                 => 'El name es requerido',
            'name.max'                      => 'El name debe contener como máximo 45 caracteres',
            'description.required'          => 'El description es requerido',
            'description.max'               => 'El description debe contener como máximo 45 caracteres',
            'latitude.required'             => 'El latitude es requerido',
            'latitude.max'                  => 'El latitude debe contener como máximo 45 caracteres',
            'latitude.numeric'              => 'El latitude debe ser un número real',
            'longitude.required'            => 'El longitude es requerido',
            'longitude.max'                 => 'El longitude debe contener como máximo 45 caracteres',
            'longitude.numeric'             => 'El longitude debe ser un número real',
            'type.required'                 => 'El type es requerido',
            'kc.required'                   => 'El kc es requerido',
            'kc.integer'                    => 'El kc debe ser un número entero',
            'theoreticalFlow.required'      => 'El theoreticalFlow es requerido',
            'theoreticalFlow.integer'       => 'El theoreticalFlow debe ser un número entero',
            'unitTheoreticalFlow.required'  => 'El unitTheoreticalFlow es requerido',
            'unitTheoreticalFlow.max'       => 'El unitTheoreticalFlow debe contener como máximo 45 caracteres',
            'efficiency.required'           => 'El efficiency es requiredo',
            'efficiency.integer'            => 'El efficiency debe ser un número entero',
            'humidityRetention.required'    => 'El humidityRetention es requiredo',
            'humidityRetention.integer'     => 'El humidityRetention debe ser un número entero',
            'max.required'                  => 'El max es requiredo',
            'max.integer'                   => 'El max debe ser un número entero',
            'min.required'                  => 'El min es requiredo',
            'min.integer'                   => 'El min debe ser un número entero',
            'criticalPoint1.required'       => 'El criticalPoint1 es requiredo',
            'criticalPoint1.integer'        => 'El criticalPoint1 debe ser un número entero',
            'criticalPoint2.required'       => 'El criticalPoint2 es requiredo',
            'criticalPoint2.integer'        => 'El criticalPoint2 debe ser un número entero',
            'id_farm.required'              => 'El id_farm es requiredo',
            'id_farm.integer'               => 'El id_farm debe ser un número entero',
            'id_pump_system.required'       => 'El id_pump_system es requiredo',
            'id_pump_system.integer'        => 'El id_pump_system debe ser un número entero',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
            $farm = Farm::find($request->get('id_farm'));
            $pumpSystem = Pump_system::find($request->get('id_pump_system'));
            $messages=[];
            if(is_null($farm)||is_null($pumpSystem)){
                if(is_null($farm)){
                array_push($messages,'Campo no existente');
                }
                if(is_null($pumpSystem)){
                array_push($messages,'Pump System no existente');
                }
                return response()->json(['message'=>$messages],404);
            }
            if($request->get('type')){
                foreach ($request->get('type') as $key => $type) {
                    Type::create([
                        'description'=>$type,
                        'id_zone'=>$newZone->id,
                    ]);
                }
            }
            $element = Zone::create([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'latitude' => $request->get('latitude'),
                'longitude' => $request->get('longitude'),
                'type' => $request->get('type'),
                'kc' => $request->get('kc'),
                'theoreticalFlow' => $request->get('theoreticalFlow'),
                'unitTheoreticalFlow' => $request->get('unitTheoreticalFlow'),
                'efficiency' => $request->get('efficiency'),
                'humidityRetention' => $request->get('humidityRetention'),
                'max' => $request->get('max'),
                'min' => $request->get('min'),
                'criticalPoint1' => $request->get('criticalPoint1'),
                'criticalPoint2' => $request->get('criticalPoint2'),
                'id_farm' => $request->get('id_farm'),
                'id_pump_system' => $request->get('id_pump_system'),
            ]);
            $response = [
                'message'=> 'Zona registrada satisfactoriamente',
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
    public function get($id){
        try {            
            $element = Zone::find($id);
            if(is_null($element)){
                return response()->json([
                    'message'=>'Zona no existente',
                    'data'=>$element
                ],404);
            }
            $response = [
                'message'=> 'Zona encontrada satisfactoriamente',
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
    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'name'                 => 'required|string|max:45',
            'description'          => 'required|string|max:45',
            'latitude'             => 'required|numeric|max:45',
            'longitude'            => 'required|numeric|max:45',
            'type'                 => 'required|string|max:45',
            'kc'                   => 'required|integer',
            'theoreticalFlow'      => 'required|integer',
            'unitTheoreticalFlow'  => 'required|string|max:45',
            'efficiency'           => 'required|integer',
            'humidityRetention'    => 'required|integer',
            'max'                  => 'required|integer',
            'min'                  => 'required|integer',
            'criticalPoint1'       => 'required|integer',
            'criticalPoint2'       => 'required|integer',
            'id_farm'              => 'required|integer',
            'id_pump_system'       => 'required|integer'
        ],[
            'name.required'                 => 'El name es requerido',
            'name.max'                      => 'El name debe contener como máximo 45 caracteres',
            'description.required'          => 'El description es requerido',
            'description.max'               => 'El description debe contener como máximo 45 caracteres',
            'latitude.required'             => 'El latitude es requerido',
            'latitude.max'                  => 'El latitude debe contener como máximo 45 caracteres',
            'latitude.numeric'              => 'El latitude debe ser un número real',
            'longitude.required'            => 'El longitude es requerido',
            'longitude.numeric'             => 'El longitude debe ser un número real',
            'type.required'                 => 'El type es requerido',
            'type.max'                      => 'El type debe contener como máximo 45 caracteres',
            'kc.required'                   => 'El kc es requerido',
            'kc.integer'                    => 'El kc debe ser un número entero',
            'theoreticalFlow.required'      => 'El theoreticalFlow es requerido',
            'theoreticalFlow.integer'       => 'El theoreticalFlow debe ser un número entero',
            'unitTheoreticalFlow.required'  => 'El unitTheoreticalFlow es requerido',
            'unitTheoreticalFlow.max'       => 'El unitTheoreticalFlow debe contener como máximo 45 caracteres',
            'efficiency.required'           => 'El efficiency es requiredo',
            'efficiency.integer'            => 'El efficiency debe ser un número entero',
            'humidityRetention.required'    => 'El humidityRetention es requiredo',
            'humidityRetention.integer'     => 'El humidityRetention debe ser un número entero',
            'max.required'                  => 'El max es requiredo',
            'max.integer'                   => 'El max debe ser un número entero',
            'min.required'                  => 'El min es requiredo',
            'min.integer'                   => 'El min debe ser un número entero',
            'criticalPoint1.required'       => 'El criticalPoint1 es requiredo',
            'criticalPoint1.integer'        => 'El criticalPoint1 debe ser un número entero',
            'criticalPoint2.required'       => 'El criticalPoint2 es requiredo',
            'criticalPoint2.integer'        => 'El criticalPoint2 debe ser un número entero',
            'id_farm.required'              => 'El id_farm es requiredo',
            'id_farm.integer'               => 'El id_farm debe ser un número entero',
            'id_pump_system.required'       => 'El id_pump_system es requiredo',
            'id_pump_system.integer'        => 'El id_pump_system debe ser un número entero',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $element = Zone::find($id);
            if(is_null($element)){
                return response()->json(['message'=>'Zona no existente'],404);
            }
            $element->fill($request->all());
            $response = [
                'message'=> 'Zona actualizada satisfactoriamente',
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
    protected function getWiseconnMeasures($zone){
        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);        
        try {
            $measuresResponse = $client->request('GET','/zones/'.$zone->id_wiseconn.'/measures/', [
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
        return json_decode($measuresResponse->getBody()->getContents());
    }
    public function measures($id){
        try {
            $zone=Zone::find($id);
            $today = Carbon::today();
            if(is_null($zone)){
                return response()->json(['message'=>'Zona no existente'],404);
            }
            $measures = Measure::where("id_zone",$zone->id)->get();
            $wiseconnMeasures=[];
            if((Carbon::parse(Carbon::parse($today)->format('Y-m-d').'T00:00:00.000000Z')->isAfter(Carbon::parse($zone->updated_at)->format('Y-m-d').'T00:00:00.000000Z'))||count($measures)==0){
                $wiseconnMeasures = $this->getWiseconnMeasures($zone);
                foreach ($wiseconnMeasures as $key => $wiseconnMeasure) {
                    if(isset($wiseconnMeasure->id)){
                        $measure=Measure::where("id_wiseconn",$wiseconnMeasure->id)->first();
                        $farm=Farm::where("id_wiseconn",$wiseconnMeasure->farmId)->first();
                        if(is_null($measure)){
                            $measure = Measure::create([
                                'name' => isset($wiseconnMeasure->name)?$wiseconnMeasure->name:null,
                                'unit' => isset($wiseconnMeasure->unit)?$wiseconnMeasure->unit:null,
                                'lastData' => isset($wiseconnMeasure->lastData)?$wiseconnMeasure->lastData:null,
                                'lastDataDate'=> isset($wiseconnMeasure->lastDataDate)?(Carbon::parse($wiseconnMeasure->lastDataDate)):null,
                                'monitoringTime' => isset($wiseconnMeasure->monitoringTime)?$wiseconnMeasure->monitoringTime:null,
                                'sensorType' => isset($wiseconnMeasure->sensorType)?$wiseconnMeasure->sensorType:null,
                                'id_zone' => isset($zone->id)?$zone->id:null,
                                'id_farm' => isset($farm->id)?$farm->id:null,
                                'id_wiseconn' => $wiseconnMeasure->id
                            ]);
                            if(isset($wiseconnMeasure->id_physical_connection)){
                                PhysicalConnection::create([
                                    'expansionPort'=> isset($wiseconnMeasure->physicalConnection)?$wiseconnMeasure->physicalConnection->expansionPort:null,
                                    'expansionBoard'=> isset($wiseconnMeasure->physicalConnection)?$wiseconnMeasure->physicalConnection->expansionBoard:null,
                                    'nodePort'=> isset($wiseconnMeasure->physicalConnection)?$wiseconnMeasure->physicalConnection->nodePort:null
                                ]);
                            }
                            $zone->touch();
                        }
                    }
                }
            }
            $response = [
                'message'=> 'Measure encontrado satisfactoriamente',
                'data' => Measure::where("id_zone",$zone->id)->get(),
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
            $elements = Irrigation::where("id_zone",$id)->with("farm")->with("volume")->with("pumpSystem")->get();
            $response = [
                'message'=> 'Irrigation encontrado satisfactoriamente',
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
    public function hydraulics($id){
        try {            
            $elements = Hydraulic::where("id_zone",$id)->get();
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
    public function alarmsTriggered(Request $request,$id){
        try {
            $elements = Alarm::where("id_zone",$id)->whereBetween('date', [$request->get('initTime'), $request->get('endTime')])->get();
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
    protected function getWiseconnRealIrrigations($zone,$initTime,$endTime){
        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);        
        try {
            $zonesResponse = $client->request('GET','/zones/'.$zone->id_wiseconn.'/realIrrigations/?endTime='.$endTime.'&initTime='.$initTime, [
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
    public function realIrrigations(Request $request,$id){
        try {
            $initTime=(Carbon::parse($request->input("initTime")))->format('Y-m-d');
            $endTime=(Carbon::parse($request->input("endTime")))->format('Y-m-d');
            $today = Carbon::today();
            $zone=Zone::find($id);
            $wiseconnRealIrrigations=[];
            if($zone){
                $farm=$zone->id_farm?Farm::find($zone->id_farm):null;
                $realIrrigations = RealIrrigation::where("id_zone",$zone->id)
                    ->where("initTime",">=",$initTime)
                    ->where(function ($q) use ($endTime) {
                        $q->where("endTime","<=",$endTime)->orWhere("status", "Running");
                    })->with("pumpSystem")->with("irrigations")->with("farm")->get();
                $isAfter=Carbon::parse(Carbon::parse($today)->format('Y-m-d').'T00:00:00.000000Z')->isAfter(Carbon::parse($zone->updated_at)->format('Y-m-d').'T00:00:00.000000Z');
                if($isAfter){
                    $wiseconnRealIrrigations = $this->getWiseconnRealIrrigations($zone,$initTime,$endTime);
                    foreach ($wiseconnRealIrrigations as $key => $wiseconnRealIrrigation) {
                        if(isset($wiseconnRealIrrigation->id)){
                            $realIrrigation=RealIrrigation::where("id_wiseconn",$wiseconnRealIrrigation->id)->first();
                            if(is_null($realIrrigation)){                                    
                                $pumpSystem=Pump_system::where("id_wiseconn",$wiseconnRealIrrigation->pumpSystemId)->first();
                                $realIrrigation= RealIrrigation::create([
                                    'initTime' => isset($wiseconnRealIrrigation->initTime)?$wiseconnRealIrrigation->initTime:null,
                                    'endTime' =>isset($wiseconnRealIrrigation->endTime)?$wiseconnRealIrrigation->endTime:null,
                                    'status'=> isset($wiseconnRealIrrigation->status)?$wiseconnRealIrrigation->status:null,
                                    'id_farm'=> isset($farm->id)?$farm->id:null,
                                    'id_pump_system'=> isset($pumpSystem->id)?$pumpSystem->id:null,
                                    'id_zone'=> isset($zone->id)?$zone->id:null,
                                    'id_wiseconn' => $wiseconnRealIrrigation->id
                                ]);
                            }                             
                        }
                    }
                }
                $zone->touch();
                $response = [
                    'message'=> 'Lista de RealIrrigation',
                    'zone'=> $zone,
                    'isAfter'=>$isAfter,
                    'wiseconnRealIrrigations'=>$wiseconnRealIrrigations,
                    'data' => RealIrrigation::where("id_zone",$zone->id)
                    ->where("initTime",">=",$initTime)
                    ->where(function ($q) use ($endTime) {
                        $q->where("endTime","<=",$endTime)->orWhere("status", "Running");
                    })->with("pumpSystem")->with("irrigations")->with("farm")->get()
                ];
                return response()->json($response, 200);
            }else{                
                return response()->json(['message'=>'Zona no existente'],404);
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
