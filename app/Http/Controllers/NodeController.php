<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Node;
use App\Measure;
use App\Farm;

class NodeController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'                 => 'required|string|max:45',
            'lat'                  => 'required|string|max:45',
            'lng'                  => 'required|string|max:45',
            'nodeType'             => 'required|string|max:45',
            'id_farm'              => 'required|integer',
        ],[
            'name.required'                 => 'El name es requerido',
            'name.max'                      => 'El name debe contener como máximo 45 caracteres',
            'lat.required'                  => 'El lat es requerido',
            'lat.max'                       => 'El lat debe contener como máximo 45 caracteres',
            'lng.required'                  => 'El lng es requerido',
            'lng.max'                       => 'El lng debe contener como máximo 45 caracteres',
            'nodeType.required'             => 'El nodeType es requerido',
            'nodeType.max'                  => 'El nodeType debe contener como máximo 45 caracteres',
            'id_farm.required'              => 'El id_farm es requerido',
            'id_farm.integer'               => 'El id_farm debe ser un número entero',           
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
            $farm = Farm::find($request->get('id_farm'));
            if(is_null($farm)){
                return response()->json(["message"=>"non-existent farm"],404);
            }
            $element = Node::create([
                'name' => $request->get('name'),
                'lat' => $request->get('lat'),
                'lng' => $request->get('lng'),
                'nodeType' => $request->get('nodeType'),
                'id_farm' => $request->get('id_farm'),                
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
    public function measures($id){
        try {            
            $elements = Measure::where("id_node",$id)->get();
            $response = [
                'message'=> 'items found successfully',
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
    public function get($id){
        try {            
            $element = Node::with("farm")->find($id);
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
