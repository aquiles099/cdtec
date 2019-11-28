<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\PhysicalConnection;
use App\Farm;
use App\Node;
use App\Zone;
use App\Hydraulic;
class HydraulicController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'                   => 'required|string|max:45',
            'type'                   => 'required|string|max:45',
            'id_farm'                => 'required|integer',
            'id_physical_connection' => 'required|string|max:45',
            'id_node'                => 'required|string|max:45',
            'id_zone'                => 'required|string|max:45',
        ],[
            'name.required'                   => 'El name es requerido',
            'name.max'                        => 'El name debe contener como máximo 45 caracteres',
            'type.required'                   => 'El type es requerido',
            'type.max'                        => 'El type debe contener como máximo 45 caracteres',
            'id_farm.required'                => 'El id_farm es requerido',
            'id_farm.integer'                 => 'El id_farm debe ser un número entero',
            'id_physical_connection.required' => 'El id_physical_connection es requerido',
            'id_physical_connection.integer'  => 'El id_physical_connection debe ser un número entero',
            'id_node.required'                => 'El id_node es requerido',
            'id_node.integer'                 => 'El id_node debe ser un número entero',
            'id_zone.required'                => 'El id_zone es requerido',
            'id_zone.integer'                 => 'El id_zone debe ser un número entero',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
            $farm = Farm::find($request->get('id_farm'));
            $physicalConnection = PhysicalConnection::find($request->get('id_physical_connection'));
            $zone = Zone::find($request->get('id_zone'));
            $node = Node::find($request->get('id_node'));
            $messages=[];
            if(is_null($farm)||is_null($physicalConnection)||is_null($zone)||is_null($node)){
                if(is_null($farm)){
                array_push($messages,"non-existent farm");
                }
                if(is_null($node)){
                array_push($messages,"non-existent node");
                }
                if(is_null($zone)){
                array_push($messages,"non-existent zone");
                }
                if(is_null($physicalConnection)){
                array_push($messages,"non-existent Physical Connection");
                }
                return response()->json(["message"=>$messages],404);
            }
            $element = Hydraulic::create([
                'name' => $request->get('name'),
                'type' => $request->get('type'),
                'id_farm' => $request->get('id_farm'),
                'id_physical_connection' => $request->get('id_physical_connection'),
                'id_zone' => $request->get('id_zone'),
                'id_node' => $request->get('id_node'),
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
            $element = Hydraulic::find($id);
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
