<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Account;
use App\Farm;
class AccountController extends Controller
{
    public function all(){
        try {
            $response = [
                'message'=> 'Account list',
                'data' => Account::all(),
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
            $element = Account::find($id);
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
            'rut'             => 'required|string|max:45',
            'razonsocial'     => 'required|string|max:45',
            'rutlegal'        => 'required|string|max:45',
            'direccion'       => 'required|string|max:45',
            'telefono'        => 'required|string|max:45',
            'email'           => 'required|string|max:45',
            'comentario'      => 'required|string|max:45',
            'habilitar'       => 'required|string|max:45',
            'id_farm'         => 'required'
        ],[
            'name.required'          => 'El name es requerido',
            'name.max'               => 'El name debe contener como máximo 45 caracteres',
            'rut.required'           => 'El rut es requerido',
            'rut.max'                => 'El rut debe contener como máximo 45 caracteres',
            'razonsocial.required'   => 'El razonsocial es requerido',
            'razonsocial.max'        => 'El razonsocial debe contener como máximo 45 caracteres',
            'rutlegal.required'      => 'El rutlegal es requerido',
            'rutlegal.max'           => 'El rutlegal debe contener como máximo 45 caracteres',
            'direccion.required'     => 'El direccion es requerido',
            'direccion.max'          => 'El direccion debe contener como máximo 45 caracteres',
            'telefono.required'      => 'El telefono es requerido',
            'telefono.max'           => 'El telefono debe contener como máximo 45 caracteres',
            'email.required'         => 'El email es requerido',
            'email.max'              => 'El email debe contener como máximo 45 caracteres',
            'comentario.required'    => 'El comentario es requerido',
            'comentario.max'         => 'El comentario debe contener como máximo 45 caracteres',
            'habilitar.required'     => 'El habilitar es requerido',
            'habilitar.max'          => 'El habilitar debe contener como máximo 45 caracteres',
            'id_farm.required'       => 'El id_farm es requerido',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $farm = Farm::find($request->get('id_farm'));
        if(is_null($farm)){
            return response()->json(["message"=>"non-existent farm"],404);
        }
        $element = Account::create([
            'name' => $request->get('name'),
            'rut' => $request->get('rut'),
            'razonsocial' => $request->get('razonsocial'),
            'direccion' => $request->get('direccion'),
            'telefono' => $request->get('telefono'),
            'email' => $request->get('email'),
            'comentario' => $request->get('comentario'),
            'habilitar' => $request->get('habilitar'),
            'id_farm' => $request->get('id_farm'),
        ]);
        $response = [
            'message'=> 'item successfully registered',
            'data' => $element,
        ];
        return response()->json($response, 200);
    }
    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:45',
            'rut'             => 'required|string|max:45',
            'razonsocial'     => 'required|string|max:45',
            'rutlegal'        => 'required|string|max:45',
            'direccion'       => 'required|string|max:45',
            'telefono'        => 'required|string|max:45',
            'email'           => 'required|string|max:45',
            'comentario'      => 'required|string|max:45',
            'habilitar'       => 'required|string|max:45',
            'id_farm'         => 'required'
        ],[
            'name.required'          => 'El name es requerido',
            'name.max'               => 'El name debe contener como máximo 45 caracteres',
            'rut.required'           => 'El rut es requerido',
            'rut.max'                => 'El rut debe contener como máximo 45 caracteres',
            'razonsocial.required'   => 'El razonsocial es requerido',
            'razonsocial.max'        => 'El razonsocial debe contener como máximo 45 caracteres',
            'rutlegal.required'      => 'El rutlegal es requerido',
            'rutlegal.max'           => 'El rutlegal debe contener como máximo 45 caracteres',
            'direccion.required'     => 'El direccion es requerido',
            'direccion.max'          => 'El direccion debe contener como máximo 45 caracteres',
            'telefono.required'      => 'El telefono es requerido',
            'telefono.max'           => 'El telefono debe contener como máximo 45 caracteres',
            'email.required'         => 'El email es requerido',
            'email.max'              => 'El email debe contener como máximo 45 caracteres',
            'comentario.required'    => 'El comentario es requerido',
            'comentario.max'         => 'El comentario debe contener como máximo 45 caracteres',
            'habilitar.required'     => 'El habilitar es requerido',
            'habilitar.max'          => 'El habilitar debe contener como máximo 45 caracteres',
            'id_farm.required'       => 'El id_farm es requerido',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
            $farm = Farm::find($request->get('id_farm'));
            $account = Account::find($id);
            $messages=[];
            if(is_null($farm)||is_null($account)){
                if(is_null($farm)){
                array_push($messages,"non-existent farm");
                }
                if(is_null($account)){
                array_push($messages,"non-existent account");
                }
                return response()->json(["message"=>$messages],404);
            }
            $account->fill($request->all());
            $response = [
                'message'=> 'item updated successfully',
                'data' => $account,
            ];
            $count->update();
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
