<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class AuthController extends Controller
{
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        try {
			if (!$token=JWTAuth::attempt($credentials)) {
				return response()->json(['error' => 'Usuario o contraseña Incorrectos'], 400);
			}
            if (!JWTAuth::user()->active) {
                return response()->json(['error' => 'Su cuenta no esta activa'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json([
        	"token"=>$token,
        	"user"=>JWTAuth::user()
        ],200);
    }
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'required|string|email|max:45|unique:users',
            'id_role' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ],[
            'name.required'                  => 'El nombre es requerido',
            'name.max'                       => 'El nombre debe contener como máximo 45 caracteres',
            'last_name.required'             => 'El apellido es requerido',
            'last_name.max'                  => 'El apellido debe contener como máximo 45 caracteres',
            'email.unique'                   => 'Este email ya se encuentra en uso',
            'email.email'                    => 'El email debe de tener un formato ejemplo@ejemplo.com',
            'email.required'                 => 'El email es requerido',
            'id_role.required'               => 'El rol es requerido',
            'password.min'                   => 'La contraseña debe de tener minimo 8 caracteres',
            'password_confirmation.required' => 'La confirmación de la contraseña es requerida',
            'password.confirmed'             => 'Las contraseña no coinciden vuelva a intentar',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $user = User::create([
            'name' => $request->get('name'),
            'last_name' => $request->get('last_name'),
            'email' => $request->get('email'),
            'rut' => $request->get('rut'),
            'id_role' => $request->get('id_role'),           
            'password' => Hash::make($request->get('password')),
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user','token'),201);
    }
    public function getAuthenticatedUser(){
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }
}
