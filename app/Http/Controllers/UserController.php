<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Role;
use App\FarmsUsers;
class UserController extends Controller
{
    public function all(){
        try {
            $response = [
                'message'=> 'User list',
                'data' => User::all(),
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
            $element = User::with("role")->find($id);
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
            'name'                  => 'required|string|max:45',
            'last_name'             => 'required|string|max:45',
            'email'                 => 'required|email|max:45|unique:users,email',
            'business'              => 'required|string|max:45',
            'office'                => 'required|string|max:45',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
            'region'                => 'required|string|max:45',
            'city'                  => 'required|string|max:45',
            'phone'                 => 'required|string|max:45',
            'id_role'               => 'required|integer',
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
            'id_role.required'               => 'El rol es requerido',
            'id_role.integer'                => 'El rol debe ser un entero',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
        	$role = Role::find($request->get('id_role'));
	        if(is_null($role)){
	            return response()->json(["message"=>"non-existent role"],404);
	        }
            $user= new User();
            $user->name=$request->get('name');
            $user->last_name=$request->get('last_name');
            $user->email=$request->get('email');
            $user->business=$request->get('business');
            $user->office=$request->get('office');
            $user->password= Hash::make($request->get('password'));
            $user->region=$request->get('region');
            $user->city=$request->get('city');
            $user->phone=$request->get('phone');
            $user->id_role=$request->get('id_role');
            $user->save();
            $response = [
                'message'=> 'item successfully registered',
                'data' => $user,
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
    protected function registerFarms(Request $request,$id){
        try{            
            $user = User::with("role")->find($id);
            FarmsUsers::where("id_user",$user->id)->delete();
            if(is_null($user)){
                return response()->json([
                    "message"=>"non-existent item",
                    "data"=>$user
                ],404);
            }
            foreach ($request->all() as $key => $value) {
                $farmUser= new FarmsUsers();
                $farmUser["id_user"]=$user["id"];
                $farmUser["id_farm"]=$value["id"];
                $farmUser->save();
            }
            $response = [
                'message'=> 'items registered successfully',
                'data' => $user,
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
            'name'                  => 'required|string|max:45',
            'last_name'             => 'required|string|max:45',
            'email'                 => 'required|email|max:45|unique:users,email,'.$id,
            'business'              => 'required|string|max:45',
            'office'                => 'required|string|max:45',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
            'region'                => 'required|string|max:45',
            'city'                  => 'required|string|max:45',
            'phone'                 => 'required|string|max:45',
            'id_role'               => 'required|integer',
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
            'id_role.required'               => 'El rol es requerido',
            'id_role.integer'                => 'El rol debe ser un entero',
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
                array_push($messages,"non-existent role");
                }
                if(is_null($user)){
                array_push($messages,"non-existent user");
                }
                return response()->json(["message"=>$messages],404);
            }
            $user->fill($request->all());
            $user->password= Hash::make($request->get('password'));
            $response = [
                'message'=> 'item updated successfully',
                'data' => $user,
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
    public function delete($id){
        try {
            $element = User::find($id);
            if(is_null($element)){
                return response()->json(["message"=>"non-existent User"],404);
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
    public function getFarms($id){
        try {
            $elements = FarmsUsers::where("id_user",$id)->select('farms.*','farms_users.*')
                ->join('farms', 'farms_users.id_farm', '=', 'farms.id')
                ->get();
            $response = [
                'message'=> 'items found successfully',
                'data' => $elements,
            ];
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
