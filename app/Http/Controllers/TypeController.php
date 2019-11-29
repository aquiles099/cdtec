<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Type;
use App\Zone;
use App\RealIrrigation;
class TypeController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'description'        => 'required|string|max:45',
            'id_real_irrigation' => 'required|integer',
            'id_zone'            => 'required|integer',
        ],[
            'description.required'        => 'El description es requerido',
            'description.max'             => 'El description debe contener como máximo 45 caracteres',
            'id_real_irrigation.required' => 'El id_real_irrigation es requerido',
            'id_real_irrigation.max'      => 'El id_real_irrigation debe contener como máximo 45 caracteres',
            'id_zone.required'            => 'El id_zone es requerido',
            'id_zone.integer'             => 'El id_zone debe ser un número entero',   
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $realIrrigation = RealIrrigation::find($request->get('id_real_irrigation'));
            $zone = Zone::find($request->get('id_zone'));
            $messages=[];
            if(is_null($realIrrigation)||is_null($zone)){                
                if(is_null($realIrrigation)){
                array_push($messages,"non-existent real irrigation");
                }
                if(is_null($zone)){
                array_push($messages,"non-existent zone");
                }
                return response()->json(["message"=>$messages],404);
            }
            $element = Type::create([
                'description' => $request->get('description'),
                'id_real_irrigation' => $request->get('id_real_irrigation'),
                'id_zone' => $request->get('id_zone')
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
