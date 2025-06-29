<?php

namespace App\Http\Controllers\Api\LuluMarket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LuluMarket\LuluMarketRequest;
use App\Http\Services\LuluMarketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class LuluMarketController extends Controller
{


        public $LuluMarketService;
        public function __construct(LuluMarketService $LuluMarketService)
        {

            $this->LuluMarketService = $LuluMarketService;
        }


        public function CreateOrder (LuluMarketRequest $request) {

                    File::append(public_path('CreateLuluMarket.text'),"-----------------"."\n".json_encode(\request()->all())."\n");

            return $this->LuluMarketService->CreateOrder($request);

        }



        public function UpdateOrder ($id) {
            File::append(public_path('UpdateOrderLuluMarket.text'),"-----------------"."\n".json_encode(\request()->all())."\n");
            // dd($request->all());
            return $this->LuluMarketService->UpdateOrder($id);

        }




    }








