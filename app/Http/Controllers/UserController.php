<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $userData=$request->get("user_data");
        $farmsData=$request->get("farms_data");
        $validator = Validator::make($userData, [
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
        	$role = Role::find($userData['id_role']);
	        if(is_null($role)){
	            return response()->json(["message"=>"non-existent role"],404);
	        }
            $user= new User();
            $user->name=isset($userData['name'])?$userData['name']:null;
            $user->last_name=isset($userData['last_name'])?$userData['last_name']:null;
            $user->email=isset($userData['email'])?$userData['email']:null;
            $user->business=isset($userData['business'])?$userData['business']:null;
            $user->office=isset($userData['office'])?$userData['office']:null;
            $user->password=isset($userData['password'])?$userData['password']:null;
            $user->region=isset($userData['region'])?$userData['region']:null;
            $user->city=isset($userData['city'])?$userData['city']:null;
            $user->phone=isset($userData['phone'])?$userData['phone']:null;
            $user->id_role=isset($userData['id_role'])?$userData['id_role']:null;
            $user->save();
            $response = [
                'message'=> 'item successfully registered',
                'data' => $user,
                'farmsData' => $farmsData,
            ];
            $this->registerFarmsUsers($farmsData,$user);            
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    protected function registerFarmsUsers($farmsData,$user){
        if(isset($farmsData)){
            foreach ($farmsData as $key => $value) {
                $farmUser= new FarmsUsers();
                $farmUser["id_user"]=$user["id"];
                $farmUser["id_farm"]=$value["id"];
                $farmUser->save();
            }
        }
    }
    public function update(Request $request,$id){
        $userData=$request->get("user_data");
        $farmsData=$request->get("farms_data");
        $validator = Validator::make($userData, [
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
            $role = Role::find($userData['id_role']);
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
            $user->fill($userData);
            $response = [
                'message'=> 'item updated successfully',
                'data' => $user,
            ];
            $user->update();
            FarmsUsers::where("id_user",$user->id)->delete();
            $this->registerFarmsUsers($farmsData,$user);  
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
            $elements = FarmsUsers::where("id_user",$id)->get();
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
