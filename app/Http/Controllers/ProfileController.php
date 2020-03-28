<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Role;

class ProfileController extends Controller
{
    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:45',
            'last_name'             => 'required|string|max:45',
            'email'                 => 'required|email|max:45|unique:users,email,'.$id,
            'business'              => 'required|string|max:45',
            'office'                => 'required|string|max:45',
            'password'              => 'string|min:8|confirmed',
            'password_confirmation' => 'string',
            'region'                => 'required|string|max:45',
            'city'                  => 'required|string|max:45',
            'phone'                 => 'required|string|max:45',
        ],[
            'name.required'                  => 'El nombre es requerido',
            'name.string'                    => 'El nombre debe ser una cadena de caracteres',
            'name.max'                       => 'El nombre debe contener como máximo 45 caracteres',
            'last_name.required'             => 'El apellido es requerido',
            'last_name.string'               => 'El apellido debe ser una cadena de caracteres',
            'last_name.max'                  => 'El apellido debe contener como máximo 45 caracteres',
            'email.required'                 => 'El email es requerido',
            'email.email'                    => 'Formato de email incorrecto',
            'email.max'                      => 'El email debe contener como máximo 45 caracteres',
            'email.unique'                   => 'Ya existe un usuario con este email',
            'business.required'              => 'La empresa es requerido',
            'business.string'                => 'La empresa debe ser una cadena de caracteres',
            'business.max'                   => 'La empresa debe contener como máximo 45 caracteres',
            'office.required'                => 'La oficina es requerida',
            'office.string'                  => 'La oficina debe ser una cadena de caracteres',
            'office.max'                     => 'La oficina debe contener como máximo 45 caracteres',            
            'password.required'              => 'La contraseña es requerido',
            'password.string'                => 'La contraseña debe ser una cadena de caracteres',
            'password.min'                   => 'La contraseña debe de tener minimo 8 caracteres',
            'password_confirmation.string'   => 'La confirmación de la contraseña debe ser una cadena de caracteres',
            'password_confirmation.required' => 'La confirmación de la contraseña es requerida',
            'password.confirmed'             => 'Las contraseña no coinciden vuelva a intentar',
            'region.required'                => 'La region es requerido',
            'region.string'                  => 'La region debe ser una cadena de caracteres',
            'region.max'                     => 'La region debe contener como máximo 45 caracteres',
            'city.required'                  => 'La ciudad es requerido',
            'city.string'                    => 'La ciudad debe ser una cadena de caracteres',
            'city.max'                       => 'La ciudad debe contener como máximo 45 caracteres',
            'phone.required'                 => 'El telefono es requerido',
            'phone.string'                   => 'El telefono debe ser una cadena de caracteres',
            'phone.max'                      => 'El telefono debe contener como máximo 45 caracteres',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
            $role = Role::find($request->get('id_role'));
	        $user= User::find($id);
	        $messages=[];
            if(is_null($role)||is_null($user)){
                if(is_null($role)){
                array_push($messages,'Role no existente');
                }
                if(is_null($user)){
                array_push($messages,'Usuario no existente');
                }
                return response()->json(['message'=>$messages],404);
            }
            $user->fill($request->all());
            if($request->get('password')){
            	$user->password=Hash::make($request->get('password'));
            }
            $response = [
                'message'=> 'Perfil actualizado satisfactoriamente',
                'user' => $user,
            ];
            $user->update();
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
