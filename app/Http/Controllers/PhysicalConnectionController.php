<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\PhysicalConnection;
class PhysicalConnectionController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'expansionPort'      => 'required|integer',
            'expansionBoard'     => 'required|string|max:45',
            'nodePort'           => 'required|integer',
            'type'               => 'required|string|max:45',
        ],[
            'expansionPort.required'       => 'El expansionPort es requerido',
            'expansionPort.integer'        => 'El expansionPort debe ser un número entero',
            'expansionBoard.required'      => 'El expansionBoard es requerido',
            'expansionBoard.max'           => 'El expansionBoard debe contener como máximo 45 caracteres',
            'nodePort.required'            => 'El nodePort es requerido',
            'nodePort.integer'             => 'El nodePort debe ser un número entero',
            'type.required'                => 'El type es requerido',
            'type.max'                     => 'El type debe contener como máximo 45 caracteres',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $element = PhysicalConnection::create([
                'expansionPort' => $request->get('expansionPort'),
                'expansionBoard' => $request->get('expansionBoard'),
                'nodePort' => $request->get('nodePort'),
                'type' => $request->get('type'),
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
