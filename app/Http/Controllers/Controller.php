<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use GuzzleHttp\Client;
use App\Farm;
use App\RealIrrigation;
use App\Zone;
use App\Node;
use App\Pump_system;
use App\Volume;
use App\Hydraulic;
use App\PhysicalConnection;
use App\Measure;
use App\Irrigation;
use Carbon\Carbon;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected function requestWiseconn($client,$method,$uri){
        return $client->request($method, $uri, [
            'headers' => [
                'api_key' => '9Ev6ftyEbHhylMoKFaok',
                'Accept'     => 'application/json'
            ]
        ]);
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function test()
    {
        $client = new Client([
            'base_uri' => 'https://apiv2.wiseconn.com',
            'timeout'  => 100.0,
        ]);
        $initTime=Carbon::now(date_default_timezone_get())->format('Y-m-d');
        $endTime=Carbon::now(date_default_timezone_get())->addDays(15)->format('Y-m-d');
        try{
            $farms=Farm::all();
            foreach ($farms as $key => $farm) {
                $irrigationsResponse = $this->requestWiseconn($client,'GET','/farms/'.$farm->id_wiseconn.'/irrigations/?endTime='.$endTime.'&initTime='.$initTime);
                $irrigations=json_decode($irrigationsResponse->getBody()->getContents());
                dd($irrigations);               
            }
            # code...
            return ("Success: Clone real irrigations and volumes data");
        } catch (\Exception $e) {
            return ["Error:" => $e->getMessage(),"Linea:" => $e->getLine()];
        }  
    }
}
