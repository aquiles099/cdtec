<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SensorType;
class SensorTypeController extends Controller
{
	public function all(){
        try {
            $response = [
                'message'=> 'Lista de SensorType',
                'data' => SensorType::with("zones")->get()
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
}
