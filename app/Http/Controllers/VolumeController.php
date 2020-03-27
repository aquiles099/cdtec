<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Volume;
class VolumeController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'value'           => 'required|integer',
            'unitName'        => 'required|string|max:45',
            'unitAbrev'       => 'required|string|max:45',
            'type'            => 'required|string|max:45'
        ],[
            'value.required'          => 'El value es requerido',
            'value.integer'           => 'El value debe ser un número entero',
            'unitName.required'       => 'El unitName es requerido',
            'unitName.max'            => 'El unitName debe contener como máximo 45 caracteres',
            'unitAbrev.required'      => 'El unitAbrev es requerido',
            'unitAbrev.max'           => 'El unitAbrev debe contener como máximo 45 caracteres',
            'type.required'           => 'El type es requerido',
            'type.max'                => 'El type debe contener como máximo 45 caracteres'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $element = Volume::create([
                'value' => $request->get('value'),
                'unitName' => $request->get('unitName'),
                'unitAbrev' => $request->get('unitAbrev'),
                'type' => $request->get('type'),               
            ]);
            $response = [
                'message'=> 'Volume registrado satisfactoriamente',
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
