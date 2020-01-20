<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Zone;
use App\Path;
class PathController extends Controller
{
    //
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'id_zone'   => 'required',
            'lat'          => 'required|numeric',
            'lng'          => 'required|numeric',
        ],[
            'id_zone.required'   => 'El id_zone es requerido',
            'lat.required'          => 'El lat es requerido',
            'lat.numeric'           => 'El lat debe ser un número real',
            'lng.required'          => 'El lng es requerido',
            'lng.numeric'           => 'El lng debe ser un número real',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
            $zone = Zone::find($request->get('id_zone'));
            $messages=[];
            if(is_null($zone)){
                if(is_null($zone)){
                array_push($messages,"non-existent zone");
                }
                return response()->json(["message"=>$messages],404);
            }
            $element = Path::create([
                'id_zone' => $request->get('id_zone'),
                'lat' => $request->get('lat'),
                'lng' => $request->get('lng'),
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
