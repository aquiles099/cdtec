<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Pump_system;
use App\Farm;
use App\Zone;
class PumpSystemController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'                   => 'required|string|max:45',
            'allowPumpSelection'     => 'required|integer',
            'id_farm'                => 'required|integer',
        ],[
            'name.required'                   => 'El name es requerido',
            'name.max'                        => 'El name debe contener como máximo 45 caracteres',
            'allowPumpSelection.required'     => 'El allowPumpSelection es requerido',
            'allowPumpSelection.integer'      => 'El allowPumpSelection debe ser un número entero',
            'id_farm.required'                => 'El id_farm es requerido',
            'id_farm.integer'                 => 'El id_farm debe ser un número entero',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
            $farm = Farm::find($request->get('id_farm'));
            if(is_null($farm)){
                return response()->json(["message"=>"non-existent farm"],404);
            }
            switch ($request->get('allowPumpSelection')) {
                case '1':
                    $allowPumpSelection=true;
                    break;
                case '0':
                    $allowPumpSelection=false;
                    break;                
                default:
                    break;
            }
            $element = Pump_system::create([
                'name' => $request->get('name'),
                'allowPumpSelection' => $allowPumpSelection,
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
    public function get($id){
        try {            
            $element = Pump_system::with("farm")->find($id);
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
    public function zones($id){
        try {
            $elements = Zone::where("id_pump_system",$id)->with("polygons")->with("types")->get();
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
    public function irrigations($id){
        try {            
            $elements = Irrigation::where("id_pump_system",$id)->with("zone")->with("volume")->with("farm")->get();
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
    public function realIrrigations($id){
        try {            
            $elements = RealIrrigation::where("id_pump_system",$id)->with("zone")->with("irrigations")->with("farm")->get();
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
    public function tanks($id){
        try {            
            $element = Pump_system::find($id);
            if(is_null($element)){
                return response()->json([
                    "message"=>"non-existent item",
                    "data"=>$element->name
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
