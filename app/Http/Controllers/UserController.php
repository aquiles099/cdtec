<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
class UserController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:45',
            'last_name'        => 'required|string|max:45',
            'email'            => 'required|email|max:45|unique:users',
            'business'         => 'string|max:45',
            'office'           => 'required|string|max:45',
            'password'         => 'required|string|max:45',
            'region'           => 'required|string|max:45',
            'city'             => 'required|string|max:45',
            'phone'            => 'required|string|max:45',
            'id_role'          => 'required|integer',
        ],[
            'name.required'       => 'El nombre es requerido',
            'name.string'         => 'El nombre debe ser una cadena de caracteres',
            'name.max'            => 'El nombre debe contener como máximo 45 caracteres',
            'last_name.required'  => 'El apellido es requerido',
            'last_name.string'    => 'El apellido debe ser una cadena de caracteres',
            'last_name.max'       => 'El apellido debe contener como máximo 45 caracteres',
            'email.required'      => 'El email es requerido',
            'email.email'         => 'Formato de email incorrecto',
            'email.max'           => 'El email debe contener como máximo 45 caracteres',
            'email.unique'        => 'Ya existe un usuario con este email',
            'business.string'     => 'La empresa debe ser una cadena de caracteres',
            'business.max'        => 'La empresa debe contener como máximo 45 caracteres',
            'office.string'       => 'La oficina debe ser una cadena de caracteres',
            'office.max'          => 'La oficina debe contener como máximo 45 caracteres',
            'password.required'   => 'La contraseña es requerido',
            'password.string'     => 'La contraseña debe ser una cadena de caracteres',
            'password.max'        => 'La contraseña debe contener como máximo 45 caracteres',
            'region.required'     => 'La region es requerido',
            'region.string'       => 'La region debe ser una cadena de caracteres',
            'region.max'          => 'La region debe contener como máximo 45 caracteres',
            'city.required'       => 'La ciudad es requerido',
            'city.string'         => 'La ciudad debe ser una cadena de caracteres',
            'city.max'            => 'La ciudad debe contener como máximo 45 caracteres',
            'phone.required'      => 'El telefono es requerido',
            'phone.string'        => 'El telefono debe ser una cadena de caracteres',
            'phone.max'           => 'El telefono debe contener como máximo 45 caracteres',
            'id_role.required'    => 'El rol es requerido',
            'id_role.integer'     => 'El rol debe ser un entero',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $element = User::create([
                'name'      => $request->get('name'),
                'last_name' => $request->get('last_name'),
                'email'     => $request->get('email'),
                'business'  => $request->get('business'),   
                'office'    => $request->get('office'),
                'password'  => $request->get('password'),
                'region'    => $request->get('region'),
                'city'      => $request->get('city'),   
                'phone'     => $request->get('phone'),  
                'id_role'   => $request->get('id_role'),         
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
