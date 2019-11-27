<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Farm;
use App\Volume;
use App\Zone;
use App\Pump_system;
use App\Irrigation;
class IrrigationController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'value'           => 'required|integer',
            'initTime'        => 'required|string|max:45',
            'endTime'         => 'required|string|max:45',
            'status'          => 'required|string|max:45',
            'sentToNetwork'   => 'required|integer',
            'scheduledType'   => 'required|string|max:45',
            'groupingName'    => 'required|string|max:45',
            'action'          => 'required|string|max:45',
            'id_pump_system'  => 'required|integer',
            'id_zone'         => 'required|integer',
            'id_volume'       => 'required|integer',
            'id_farm'         => 'required|integer',
        ],[
            'value.required'          => 'El value es requerido',
            'value.integer'           => 'El value debe ser un número entero',
            'initTime.required'       => 'El initTime es requerido',
            'initTime.max'            => 'El initTime debe contener como máximo 45 caracteres',
            'endTime.required'        => 'El endTime es requerido',
            'endTime.max'             => 'El endTime debe contener como máximo 45 caracteres',
            'status.required'         => 'El status es requerido',
            'status.max'              => 'El status debe contener como máximo 45 caracteres',
            'sentToNetwork.required'  => 'El sentToNetwork es requerido',
            'sentToNetwork.integer'   => 'El sentToNetwork debe ser un número entero',
            'scheduledType.required'  => 'El scheduledType es requerido',
            'scheduledType.max'       => 'El scheduledType debe contener como máximo 45 caracteres',
            'groupingName.required'   => 'El groupingName es requerido',
            'groupingName.max'        => 'El groupingName debe contener como máximo 45 caracteres',
            'action.required'         => 'El action es requerido',
            'action.max'              => 'El action debe contener como máximo 45 caracteres',
            'id_pump_system.required' => 'El id_pump_system es requerido',
            'id_pump_system.integer'  => 'El id_pump_system debe ser un número entero',
            'id_zone.required'        => 'El id_zone es requerido',
            'id_zone.integer'         => 'El id_zone debe ser un número entero',
            'id_volume.required'      => 'El id_volume es requerido',
            'id_volume.integer'       => 'El id_volume debe ser un número entero',
            'id_farm.required'        => 'El id_farm es requerido',
            'id_farm.integer'         => 'El id_farm debe ser un número entero',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $pumpSystem = Pump_system::find($request->get('id_pump_system'));
            $zone = Zone::find($request->get('id_zone'));
            $volume = Volume::find($request->get('id_volume'));
            $farm = Farm::find($request->get('id_farm'));
            $messages=[];
            if(is_null($farm)||is_null($pumpSystem)||is_null($zone)||is_null($volume)){
                if(is_null($farm)){
                array_push($messages,"non-existent farm");
                }
                if(is_null($volume)){
                array_push($messages,"non-existent Volume");
                }
                if(is_null($zone)){
                array_push($messages,"non-existent zone");
                }
                if(is_null($pumpSystem)){
                array_push($messages,"non-existent Pump System");
                }
                return response()->json(["message"=>$messages],404);
            }
            $element = Irrigation::create([
                'value' => $request->get('value'),
                'initTime' => $request->get('initTime'),
                'endTime' => $request->get('endTime'),
                'status' => $request->get('status'),
                'sentToNetwork' => $request->get('sentToNetwork')==1?true:false,
                'scheduledType' => $request->get('scheduledType'),
                'groupingName' => $request->get('groupingName'),
                'action' => $request->get('action'),
                'id_pump_system' => $request->get('id_pump_system'),
                'id_zone' => $request->get('id_zone'),
                'id_volume' => $request->get('id_volume'),
                'id_farm' => $request->get('id_farm'),                
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
        $validator = Validator::make($request->all(),[
            'value'           => 'required|integer',
            'initTime'        => 'required|string|max:45',
            'endTime'         => 'required|string|max:45',
            'status'          => 'required|string|max:45',
            'sentToNetwork'   => 'required|integer',
            'scheduledType'   => 'required|string|max:45',
            'groupingName'    => 'required|string|max:45',
            'action'          => 'required|string|max:45',
            'id_pump_system'  => 'required|integer',
            'id_zone'         => 'required|integer',
            'id_volume'       => 'required|integer',
            'id_farm'         => 'required|integer',
        ],[
            'value.required'          => 'El value es requerido',
            'value.integer'           => 'El value debe ser un número entero',
            'initTime.required'       => 'El initTime es requerido',
            'initTime.max'            => 'El initTime debe contener como máximo 45 caracteres',
            'endTime.required'        => 'El endTime es requerido',
            'endTime.max'             => 'El endTime debe contener como máximo 45 caracteres',
            'status.required'         => 'El status es requerido',
            'status.max'              => 'El status debe contener como máximo 45 caracteres',
            'sentToNetwork.required'  => 'El sentToNetwork es requerido',
            'sentToNetwork.integer'   => 'El sentToNetwork debe ser un número entero',
            'scheduledType.required'  => 'El scheduledType es requerido',
            'scheduledType.max'       => 'El scheduledType debe contener como máximo 45 caracteres',
            'groupingName.required'   => 'El groupingName es requerido',
            'groupingName.max'        => 'El groupingName debe contener como máximo 45 caracteres',
            'action.required'         => 'El action es requerido',
            'action.max'              => 'El action debe contener como máximo 45 caracteres',
            'id_pump_system.required' => 'El id_pump_system es requerido',
            'id_pump_system.integer'  => 'El id_pump_system debe ser un número entero',
            'id_zone.required'        => 'El id_zone es requerido',
            'id_zone.integer'         => 'El id_zone debe ser un número entero',
            'id_volume.required'      => 'El id_volume es requerido',
            'id_volume.integer'       => 'El id_volume debe ser un número entero',
            'id_farm.required'        => 'El id_farm es requerido',
            'id_farm.integer'         => 'El id_farm debe ser un número entero',
        ] );
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $pumpSystem = Pump_system::find($request->get('id_pump_system'));
            $zone = Zone::find($request->get('id_zone'));
            $volume = Volume::find($request->get('id_volume'));
            $farm = Farm::find($request->get('id_farm'));
            $element = Irrigation::find($id);
            $messages=[];
            if(is_null($farm)||is_null($pumpSystem)||is_null($zone)||is_null($volume)||is_null($element)){
                if(is_null($farm)){
                array_push($messages,"non-existent farm");
                }
                if(is_null($volume)){
                array_push($messages,"non-existent Volume");
                }
                if(is_null($zone)){
                array_push($messages,"non-existent zone");
                }
                if(is_null($pumpSystem)){
                array_push($messages,"non-existent Pump System");
                }
                if(is_null($element)){
                array_push($messages,"non-existent Irrigation");
                }
                return response()->json(["message"=>$messages],404);
            }
            // 
            $element->fill($request->all());
            $response = [
                'message'=> 'item successfully updated',
                'data' => $element,
            ];
            $element->update();
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
    public function get($id){
        try {            
            $element = Irrigation::find($id);
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
    public function updateAction(Request $request,$id){
        $validator = Validator::make($request->all(),[
            'action'          => 'required|string|max:45',
        ],[
            'action.required'         => 'El action es requerido',
            'action.max'              => 'El action debe contener como máximo 45 caracteres',
        ] );
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            $element = Irrigation::find($id);
            // 
            if(is_null($element)){                
                return response()->json(["message"=>"non-existent irrigation"],404);
            }
            $element->action=$request->get('action');
            $element->update();
            $response = [
                'message'=> 'item successfully updated',
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
    public function delete($id){
        try {
            $element = Irrigation::find($id);
            if(is_null($element)){
                return response()->json(["message"=>"non-existent Irrigation"],404);
            }
            $element->delete();
            $response = [
                'message'=> 'item successfully deleted',
                'data' => $element,
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
