<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Polygon;
use App\Bound;
class PolygonController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'id_bound'            => 'required'
        ],[
            'id_bound.required'   => 'El lat es requerido', 
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $element = Polygon::create([
                'id_bound' => $request->get('id_bound'),
            ]);
            $response = [
                'message'=> 'Polygon registrado satisfactoriamente',
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
