<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Baraka\CreateClientRequest;
use App\Repositories\BarakaAppRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;
use Auth;
use App\Http\Services\OrderService;

class BarakaAppController extends Controller
{
    public function __construct(OrderService $OrderService, BarakaAppRepository $OrderRepository)
    {
        $this->OrderRepository = $OrderRepository;
        $this->OrderService = $OrderService;
    }

    public function request_order(Request $request)
    {
        File::append(public_path('BarakaApp.txt'),"-----------------"."\n".json_encode(\request()->all()). json_encode(\request()->header()). "\n");

        return $this->OrderRepository->save_order($request);
    }

    public function update_order(Request $request, $API_TOKEN, $id)
    {
        return $this->OrderRepository->update_order($request, $id);
    }

    public function cancel_order(Request $request, $API_TOKEN, $id)
    {
        return $this->OrderRepository->cancel_order($request, $id);
    }

    public function create_client(CreateClientRequest $request)
    {
        return $this->OrderRepository->create_client($request);
    }

}
