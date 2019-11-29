<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Polygon;
use App\Zone;
class PolygonController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'lat'            => 'required|string|max:45',
            'lng'            => 'required|string|max:45',
            'type'           => 'required|string|max:45',
            'id_zone'        => 'required|integer',
        ],[
            'lat.required'            => 'El lat es requerido',
            'lat.max'                 => 'El lat debe contener como máximo 45 caracteres',
            'lng.required'            => 'El lng es requerido',
            'lng.max'                 => 'El lng debe contener como máximo 45 caracteres',
            'type.required'           => 'El type es requerido',
            'type.max'                => 'El type debe contener como máximo 45 caracteres',
            'id_zone.required'        => 'El id_zone es requerido',
            'id_zone.integer'         => 'El id_zone debe ser un número entero',   
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $zone = Zone::find($request->get('id_zone'));
            if(is_null($zone)){
                return response()->json(["message"=>"non-existent zone"],404);
            }
            $element = Polygon::create([
                'lat' => $request->get('lat'),
                'lng' => $request->get('lng'),
                'type' => $request->get('type'),
                'id_zone' => $request->get('id_zone'),
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
