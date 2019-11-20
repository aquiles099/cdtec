<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Farm;

class FarmController extends Controller
{
    public function all(){
        try {
            $response = [
                'message'=> 'Farm list',
                'data' => Farm::all(),
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
            $element = Farm::find($id);
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
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:45',
            'description'             => 'required|string|max:45',
            'latitude'     => 'required|string|max:45',
            'longitude'        => 'required|string|max:45',
            'postalAddress'       => 'required|string|max:45',
            'timeZone'        => 'required|string|max:45',
            'webhook'           => 'required|string|max:45'
        ],[
            'name.required'          => 'El name es requerido',
            'name.max'               => 'El name debe contener como máximo 45 caracteres',
            'description.required'           => 'El description es requerido',
            'description.max'                => 'El description debe contener como máximo 45 caracteres',
            'latitude.required'   => 'El latitude es requerido',
            'latitude.max'        => 'El latitude debe contener como máximo 45 caracteres',
            'longitude.required'      => 'El longitude es requerido',
            'longitude.max'           => 'El longitude debe contener como máximo 45 caracteres',
            'postalAddress.required'     => 'El postalAddress es requerido',
            'postalAddress.max'          => 'El postalAddress debe contener como máximo 45 caracteres',
            'timeZone.required'      => 'El timeZone es requerido',
            'timeZone.max'           => 'El timeZone debe contener como máximo 45 caracteres',
            'webhook.required'         => 'El webhook es requerido',
            'webhook.max'              => 'El webhook debe contener como máximo 45 caracteres'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $element = Farm::create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'latitude' => $request->get('latitude'),
            'longitude' => $request->get('longitude'),
            'postalAddress' => $request->get('postalAddress'),
            'timeZone' => $request->get('timeZone'),
            'webhook' => $request->get('webhook'),
        ]);
        $response = [
            'message'=> 'item successfully registered',
            'data' => $element,
        ];
        return response()->json($response, 200);
    }
}
