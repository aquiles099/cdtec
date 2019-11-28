<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\RealIrrigation;
use App\Irrigation;
use App\Farm;
use App\Zone;
use App\Pump_system;
class RealIrrigationController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'initTime'        => 'required|string|max:45',
            'endTime'         => 'required|string|max:45',
            'status'          => 'required|string|max:45',
            'id_irrigation'   => 'required|integer',
            'id_zone'         => 'required|integer',
            'id_farm'         => 'required|integer',
        ],[
            'initTime.required'       => 'El initTime es requerido',
            'initTime.max'            => 'El initTime debe contener como máximo 45 caracteres',
            'endTime.required'        => 'El endTime es requerido',
            'endTime.max'             => 'El endTime debe contener como máximo 45 caracteres',
            'status.required'         => 'El status es requerido',
            'status.max'              => 'El status debe contener como máximo 45 caracteres',            
            'id_irrigation.required'  => 'El id_pump_system es requerido',
            'id_irrigation.integer'   => 'El id_pump_system debe ser un número entero',
            'id_zone.required'        => 'El id_zone es requerido',
            'id_zone.integer'         => 'El id_zone debe ser un número entero',
            'id_farm.required'        => 'El id_farm es requerido',
            'id_farm.integer'         => 'El id_farm debe ser un número entero',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $irrigation = Irrigation::find($request->get('id_irrigation'));
            $zone = Zone::find($request->get('id_zone'));
            $farm = Farm::find($request->get('id_farm'));
            $pumpSystem = Pump_system::find($request->get('id_pump_system'));
            $messages=[];
            if(is_null($farm)||is_null($irrigation)||is_null($zone)||is_null($pumpSystem)){
                if(is_null($farm)){
                array_push($messages,"non-existent farm");
                }
                if(is_null($zone)){
                array_push($messages,"non-existent zone");
                }
                if(is_null($irrigation)){
                array_push($messages,"non-existent irrigation");
                }
                if(is_null($pumpSystem)){
                array_push($messages,"non-existent Pump System");
                }
                return response()->json(["message"=>$messages],404);
            }
            $element = RealIrrigation::create([
                'initTime' => $request->get('initTime'),
                'endTime' => $request->get('endTime'),
                'status' => $request->get('status'),
                'id_irrigation' => $request->get('id_irrigation'),
                'id_zone' => $request->get('id_zone'),
                'id_farm' => $request->get('id_farm'), 
                'id_pump_system' => $request->get('id_pump_system'),                                
            ]);
            $response = [
                'message'=> 'item successfully registered',
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
            $element = RealIrrigation::find($id);
            if(is_null($element)){
                return response()->json([
                    "message"=>"non-existent item",
                    "data"=>$element
                ],404);
            }
            $response = [
                'message'=> 'item found successfully',
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
}
