<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Role;
class RoleController extends Controller
{
    public function all(){
        try {
            $response = [
                'message'=> 'Role list',
                'data' => Role::all(),
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
            $element = Role::find($id);
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
            'code'          => 'required|string|unique:roles',
            'description'   => 'required|string',
        ],[
            'code.required'          => 'El codigo es requerido',
            'code.unique'            => 'El codigo ya esta en uso',
            'code.string'            => 'El codigo debe ser una cadena de texto',
            'description.required'   => 'La descripcion es requerido',
            'description.string'     => 'La descripcion debe ser una cadena de texto',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $element = Role::create([
	            'code' => $request->get('code'),
	            'description' => $request->get('description'),         
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
    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'code'          => 'required|string|unique:roles,code,'.$id,
            'description'   => 'required|string',
        ],[
            'code.required'          => 'El codigo es requerido',
            'code.unique'            => 'El codigo ya esta en uso',
            'code.string'            => 'El codigo debe ser una cadena de texto',
            'description.required'   => 'La descripcion es requerido',
            'description.string'     => 'La descripcion debe ser una cadena de texto',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
        	$role= Role::find($id);
            if(is_null($role)){
                return response()->json(["message"=>"non-existent role"],404);
            }
            $role->fill($request->all());
           	$response = [
           	    'message'=> 'item updated successfully',
           	    'data' => $role,
           	];
           	$role->update();
           	return response()->json($response, 200);          
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function delete($id){
        try {
            $element = Role::find($id);
            if(is_null($element)){
                return response()->json(["message"=>"non-existent role"],404);
            }
            $response = [
                'message'=> 'item successfully deleted',
                'data' => $element,
            ];
            $element->delete();
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de eliminar los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
}
