<?php

namespace App\Http\Controllers\Api\lyve;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Lyve\LyveOrderRequest;
use App\Http\Services\LyveOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class LyveOrderController extends Controller
{


        public $LyveOrderServiceService;
        public function __construct(LyveOrderService $OrderService)
        {

            $this->LyveOrderServiceService = $OrderService;
        }


        public function CreateOrder (LyveOrderRequest $request) {
                    File::append(public_path('CreateOrderLyve.text'),"-----------------"."\n".json_encode(\request()->all())."\n".json_encode(\request()->header())."\n");


            return $this->LyveOrderServiceService->CreateOrder($request);

        }


        public function CancelOrder ($id) {
            File::append(public_path('cancelOrderLyve.text'),"-----------------"."\n".json_encode(\request()->all())."\n");
            // dd($request->all());
            return $this->LyveOrderServiceService->cancelOrder($id);

        }
        public function UpdateOrder ($id) {
            File::append(public_path('UpdateOrderLyve.text'),"-----------------"."\n".json_encode(\request()->all())."\n");
            // dd($request->all());
            return $this->LyveOrderServiceService->UpdateOrder($id);

        }
    public function track_order( $id){
        return $this->LyveOrderServiceService->track_order($id);
    }



    }








