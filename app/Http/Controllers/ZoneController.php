<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Zone;
use App\Farm;
use App\Pump_system;
class ZoneController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'                 => 'required|string|max:45',
            'description'          => 'required|string|max:45',
            'latitude'             => 'required|string|max:45',
            'longitude'            => 'required|string|max:45',
            'type'                 => 'required|string|max:45',
            'kc'                   => 'required',
            'theoreticalFlow'      => 'required',
            'unitTheoreticalFlow'  => 'required|string|max:45',
            'efficiency'           => 'required',
            'humidityRetention'    => 'required',
            'max'                  => 'required',
            'min'                  => 'required',
            'criticalPoint1'       => 'required',
            'criticalPoint2'       => 'required',
            'id_farm'              => 'required',
            'id_pump_system'       => 'required'
        ],[
            'name.required'                 => 'El name es requerido',
            'name.max'                      => 'El name debe contener como máximo 45 caracteres',
            'description.required'          => 'El description es requerido',
            'description.max'               => 'El description debe contener como máximo 45 caracteres',
            'latitude.required'             => 'El latitude es requerido',
            'latitude.max'                  => 'El latitude debe contener como máximo 45 caracteres',
            'longitude.required'            => 'El longitude es requerido',
            'longitude.max'                 => 'El longitude debe contener como máximo 45 caracteres',
            'type.required'                 => 'El type es requerido',
            'type.max'                      => 'El type debe contener como máximo 45 caracteres',
            'kc.required'                   => 'El kc es requerido',
            'theoreticalFlow.required'      => 'El theoreticalFlow es requerido',
            'unitTheoreticalFlow.required'  => 'El unitTheoreticalFlow es requerido',
            'unitTheoreticalFlow.max'       => 'El unitTheoreticalFlow debe contener como máximo 45 caracteres',
            'efficiency.required'           => 'El efficiency es requiredo',
            'humidityRetention.required'    => 'El humidityRetention es requiredo',
            'max.required'                  => 'El max es requiredo',
            'min.required'                  => 'El min es requiredo',
            'criticalPoint1.required'       => 'El criticalPoint1 es requiredo',
            'criticalPoint2.required'       => 'El criticalPoint2 es requiredo',
            'id_farm.required'              => 'El id_farm es requiredo',
            'id_pump_system.required'       => 'El id_pump_system es requiredo'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
            $farm = Farm::find($request->get('id_farm'));
            $pumpSystem = Pump_system::find($request->get('id_pump_system'));
            $messages=[];
            if(is_null($farm)||is_null($pumpSystem)){
                if(is_null($farm)){
                array_push($messages,"non-existent farm");
                }
                if(is_null($pumpSystem)){
                array_push($messages,"non-existent Pump System");
                }
                return response()->json(["message"=>$messages],404);
            }
            $element = Zone::create([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'latitude' => $request->get('latitude'),
                'longitude' => $request->get('longitude'),
                'type' => $request->get('type'),
                'kc' => $request->get('kc'),
                'theoreticalFlow' => $request->get('theoreticalFlow'),
                'unitTheoreticalFlow' => $request->get('unitTheoreticalFlow'),
                'efficiency' => $request->get('efficiency'),
                'humidityRetention' => $request->get('humidityRetention'),
                'max' => $request->get('max'),
                'min' => $request->get('min'),
                'criticalPoint1' => $request->get('criticalPoint1'),
                'criticalPoint2' => $request->get('criticalPoint2'),
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
}
