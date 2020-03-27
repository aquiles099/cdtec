<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\SouthWestBound;
use App\NorthEastBound;
class BoundController extends Controller
{
    //
    public function storeSouthWestBound(Request $request){
        $validator = Validator::make($request->all(), [
            'id_zone'            => 'required|integer',
            'lat'                => 'required',
            'lng'                => 'required',
        ],[
            'id_zone.required'    => 'El id_zone es requerido',
            'id_zone.integer'     => 'El id_zone debe ser un número entero',
            'lat.required'        => 'El description es requerido',
            'lng.required'        => 'El latitude es requerido',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $element = SouthWestBound::create([
                'id_zone' => $request->get('id_zone'),
                'lat' => $request->get('lat'),
                'lng' => $request->get('lng'),
            ]);
            $response = [
                'message'=> 'SouthWestBound registrado satisfactoriamente',
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
    public function storeNorthEastBound(Request $request){
        $validator = Validator::make($request->all(), [
            'id_zone'            => 'required|integer',
            'lat'                => 'required',
            'lng'                => 'required',
        ],[
            'id_zone.required'    => 'El id_zone es requerido',
            'id_zone.integer'     => 'El id_zone debe ser un número entero',
            'lat.required'        => 'El description es requerido',
            'lng.required'        => 'El latitude es requerido',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $element = NorthEastBound::create([
                'id_zone' => $request->get('id_zone'),
                'lat' => $request->get('lat'),
                'lng' => $request->get('lng'),
            ]);
            $response = [
                'message'=> 'NorthEastBound registrado satisfactoriamente',
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
