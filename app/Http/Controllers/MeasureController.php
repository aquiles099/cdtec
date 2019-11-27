<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Zone;
use App\Node;
use App\Farm;
use App\PhysicalConnection;
use App\Measure;
class MeasureController extends Controller{    
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'                   => 'required|string|max:45',
            'unit'                   => 'required|string|max:45',
            'lastData'               => 'required',
            'lastDataDate'           => 'required',
            'monitoringTime'         => 'required|string|max:45',
            'sensorDepth'            => 'required|string|max:45',
            'depthUnit'              => 'required|string|max:45',
            'sensorType'             => 'required|string|max:45',
            'readType'               => 'required|string|max:45',
            'id_node'                => 'required|integer',
            'id_zone'                => 'required|integer',
            'id_farm'                => 'required|integer',
            'id_physical_connection' => 'required|integer'
        ],[
            'name.required'                   => 'El name es requerido',
            'name.max'                        => 'El name debe contener como máximo 45 caracteres',
            'unit.required'                   => 'El unit es requerido',
            'unit.max'                        => 'El unit debe contener como máximo 45 caracteres',
            'lastData.required'               => 'El lastData es requerido',
            'lastDataDate.required'           => 'El lastDataDate es requerido',
            'monitoringTime.required'         => 'El monitoringTime es requerido',
            'monitoringTime.max'              => 'El monitoringTime debe contener como máximo 45 caracteres',
            'sensorDepth.required'            => 'El sensorDepth es requerido',
            'sensorDepth.max'                 => 'El sensorDepth debe contener como máximo 45 caracteres',
            'depthUnit.required'              => 'El depthUnit es requerido',
            'depthUnit.max'                   => 'El depthUnit debe contener como máximo 45 caracteres',
            'sensorType.required'             => 'El sensorType es requerido',
            'sensorType.max'                  => 'El sensorType debe contener como máximo 45 caracteres',
            'readType.required'               => 'El readType es requerido',
            'readType.max'                    => 'El readType debe contener como máximo 45 caracteres',
            'id_node.required'                => 'El id_node es requerido',
            'id_node.integer'                 => 'El id_node debe ser un número entero',
            'id_zone.required'                => 'El id_zone es requerido',
            'id_zone.integer'                 => 'El id_zone debe ser un número entero',
            'id_farm.required'                => 'El id_farm es requerido',
            'id_farm.integer'                 => 'El id_farm debe ser un número entero',
            'id_physical_connection.required' => 'El id_physical_connection es requerido',
            'id_physical_connection.integer'  => 'El id_physical_connection debe ser un número entero',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
            $node = Node::find($request->get('id_node'));
            $zone = Zone::find($request->get('id_zone'));
            $farm = Farm::find($request->get('id_farm'));
            $physicalConnection = PhysicalConnection::find($request->get('id_physical_connection'));
            $messages=[];
            if(is_null($node)||is_null($zone)||is_null($farm)||is_null($physicalConnection)){                
                if(is_null($node)){
                array_push($messages,"non-existent node");
                }
                if(is_null($zone)){
                array_push($messages,"non-existent zone");
                }
                if(is_null($farm)){
                array_push($messages,"non-existent farm");
                }
                if(is_null($physicalConnection)){
                array_push($messages,"non-existent Physical Connection");
                }
                return response()->json(["message"=>$messages],404);
            }
            $element = Measure::create([
                'name' => $request->get('name'),
                'unit' => $request->get('unit'),
                'lastData' => $request->get('lastData'),
                'lastDataDate' => $request->get('lastDataDate'),
                'monitoringTime' => $request->get('monitoringTime'),
                'sensorDepth' => $request->get('sensorDepth'),
                'depthUnit' => $request->get('depthUnit'),
                'sensorType' => $request->get('sensorType'),
                'readType' => $request->get('readType'),
                'id_node' => $request->get('id_node'),
                'id_zone' => $request->get('id_zone'),
                'id_farm' => $request->get('id_farm'),
                'id_physical_connection' => $request->get('id_physical_connection')
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
            $element = Measure::with("physicalConnection")->find($id);
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
    public function data($id){
        try {            
            $element = Measure::find($id);
            if(is_null($element)){
                return response()->json([
                    "message"=>"non-existent item",
                    "data"=>$element
                ],404);
            }
            $response = [
                'message'=> 'item found successfully',
                'data' => $element->lastDataDate,
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
