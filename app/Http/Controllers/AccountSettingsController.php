<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\AccountSettings;
use App\Account;
class AccountSettingsController extends Controller
{
    public function all(){
        try {
            $response = [
                'message'=> 'Lista de configuración de cuentas',
                'data' => AccountSettings::all(),
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
            $element = AccountSettings::find($id);
            if(is_null($element)){
                return response()->json([
                    'message'=>'No existe la configuración de la cuenta',
                    'data'=>$element
                ],404);
            }
            $response = [
                'message'=> 'Configuración de cuenta encontrada',
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
    public function generateApiKey(){
        $exist=true;
        while ($exist) {
            $length=rand(15, 30);
            $key = implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, $length), 6));
            $exist=(is_null(AccountSettings::where("api_key",$key)->first()))?false:true;
        }
        return response()->json(['data'=>$key], 200);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string',
            'api_key'       => 'required|string|max:45|unique:account_settings',
            'password'      => 'required|string',
            'id_account'    => 'required',
        ],[
            'name.required'          => 'El nombre es requerido',
            'api_key.required'       => 'El api_key es requerido',
            'api_key.max'            => 'El api_key debe tener un máximo de 45 caracteres',
            'api_key.unique'         => 'Este api_key ya se encuentra en uso',
            'password.required'      => 'La contraseña es requerida',
            'id_account.required'    => 'Debe seleccionar una cuenta',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }        
        try{
            $account = Account::find($request->get('id_account'));
            if(is_null($account)){
                return response()->json(['message'=>'Cuenta no existente'],404);
            }
            $element = AccountSettings::create([
                'name' => $request->get('name'),
                'password' => $request->get('password'),
                'api_key' => $request->get('api_key'), 
                'id_account' => $request->get('id_account'),         
            ]);
            $response = [
                'message'=> 'Configuracion de cuenta registrada satisfactoriamente',
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
            'name'          => 'required|string',
            'api_key'       => 'required|string|max:45',
            'password'      => 'required|string',
            'id_account'    => 'required',
        ],[
            'name.required'          => 'El nombre es requerido',
            'api_key.required'       => 'El api_key es requerido',
            'api_key.max'            => 'El api_key debe tener un máximo de 45 caracteres',
            'password.required'      => 'La contraseña es requerida',
            'id_account.required'    => 'Debe seleccionar una cuenta',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
            $account = Account::find($request->get('id_account'));
            if(is_null($account)){
                return response()->json(['message'=>'Cuenta no existente'],404);
            }
            $accountSettings= AccountSettings::find($id);
            if(is_null($accountSettings)){
                return response()->json(['message'=>'Configuracion de cuenta no existente'],404);
            }
            $apiKeyExist= AccountSettings::where("id","!=",$accountSettings->id)->where("api_key",$accountSettings->api_key)->first();
            if(!is_null($apiKeyExist)){
                return response()->json(['message'=>'Este api_key ya se encuentra en uso en otra cuenta configurada'],404);
            }
            $accountSettings->fill($request->all());
            $response = [
                'message'=> 'Configuracion de cuenta actualizada satisfactoriamente',
                'data' => $accountSettings,
            ];
            $accountSettings->update();
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
            $element = AccountSettings::find($id);
            if(is_null($element)){
                return response()->json(['message'=>'Configuración de cuenta no existente'],404);
            }
            $response = [
                'message'=> 'Configuración de cuenta eliminado satisfactoriamente',
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
