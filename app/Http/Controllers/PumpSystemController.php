<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Pump_system;
use App\Farm;
class PumpSystemController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'                   => 'required|string|max:45',
            'allowPumpSelection'     => 'required',
            'id_farm'                => 'required',
        ],[
            'name.required'                   => 'El name es requerido',
            'name.max'                        => 'El name debe contener como máximo 45 caracteres',
            'allowPumpSelection.required'     => 'El allowPumpSelection es requerido',
            'id_farm.required'                => 'El id_farm es requerido',
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
}
