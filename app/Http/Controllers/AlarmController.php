<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Alarm;
use App\Irrigation;
use App\Zone;
use App\Farm;

class AlarmController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'activationValue'       => 'required|integer',
            'date'                  => 'required|string|max:45',
            'id_farm'               => 'required|integer',
            'id_zone'               => 'required|integer',
            'id_irrigation'         => 'required|integer'
        ],[
            'activationValue.required'  => 'El activationValue es requerido',
            'activationValue.integer'   => 'El activationValue debe ser un número entero',
            'date.required'             => 'El date es requerido',
            'date.max'                  => 'El date debe contener como máximo 45 caracteres',
            'date.string'               => 'El date debe ser una cadena de carácteres',
            'id_farm.required'          => 'El id_farm es requerido',
            'id_farm.integer'           => 'El id_farm debe ser un número entero',
            'id_zone.required'          => 'El id_zone es requerido',
            'id_zone.integer'           => 'El id_zone debe ser un número entero',  
            'id_irrigation.required'    => 'El id_irrigation es requerido',
            'id_irrigation.integer'     => 'El id_irrigation debe ser un número entero',         
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
            $irrigation = Irrigation::find($request->get('id_irrigation'));
            $zone = Zone::find($request->get('id_zone'));
            $farm = Farm::find($request->get('id_farm'));
            $messages=[];
            if(is_null($irrigation)||is_null($zone)||is_null($farm)){                
                if(is_null($irrigation)){
                array_push($messages,"Irrigation no existente");
                }
                if(is_null($zone)){
                array_push($messages,"Zona no existente");
                }
                if(is_null($farm)){
                array_push($messages,"Campo no existente");
                }
                return response()->json(["message"=>$messages],404);
            }
            $element = Alarm::create([
                'activationValue' => $request->get('activationValue'),
                'date' => $request->get('date'),
                'id_farm' => $request->get('id_farm'),
                'id_zone' => $request->get('id_zone'),
                'id_irrigation' => $request->get('id_irrigation'),
            ]);
            $response = [
                'message'=> 'Alarma no existente',
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
