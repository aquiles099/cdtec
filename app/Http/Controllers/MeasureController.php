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
            'id_node'                => 'required',
            'id_zone'                => 'required',
            'id_farm'                => 'required',
            'id_physical_connection' => 'required'
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
            'id_zone.required'                => 'El id_zone es requerido',
            'id_farm.required'                => 'El id_farm es requerido',
            'id_physical_connection.required' => 'El id_physical_connection es requerido',
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
}
