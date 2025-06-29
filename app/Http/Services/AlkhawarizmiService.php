<?php

namespace App\Http\Services;
use App\Http\Services\MapDistanceCalculator;
use App\Models\Order;
use App\Traits\HandleResponse;
use Illuminate\Http\Request;

class AlkhawarizmiService {

    use HandleResponse;

    public function getOrderRate($data){

        $mapDistanceCalculator = new MapDistanceCalculator();

        $data->merge([
            'origins' =>$data['pickup_lat'].','.$data['pickup_lng'],
            'destinations' => $data['lat'].','.$data['lng']
        ]);


         return [

                "distance"=>$mapDistanceCalculator->distanceMatrix($data)['distance'],
                "duration"=>$mapDistanceCalculator->distanceMatrix($data)['duration'],
                "fees"=>$this->service_fees??18,
                "balance"=>$this->distance??NULL,
                "expected_pickup"=> $this->expected_pickup??"2019-03-14 02:50:03",
                "expected_delivery"=>$this->expected_delivery??"2019-03-14 03:04:14"

         ];


    }

    public function track_order(Request $request , $id){



        $order = Order::select('id as order_id','lat' , 'lng' , 'ingr_branch_id')->with(['branch' => function($subQuery){
            $subQuery->select('id','lat','lng');
        }])->find($request->route('id'));


        return response()->json(

            [
                "order_id" => $order->order_id,
                "shop" => [
                    "lat" => $order->branch->lat,
                    "lng" => $order->branch->lng
                ],
                "customer" => [
                    "lat" => $order->lat,
                    "lng" => $order->lng
                ],
                "driver" => [
                    "lat" => $order->OperatorDetails->lat,
                    "lng" => $order->OperatorDetails->lng
                ]
            ]
            ,200
        );

    }



}




