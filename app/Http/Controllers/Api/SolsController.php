<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Repositories\SolsRepository;
use Illuminate\Http\Request;
use App\Http\Services\OrderService;

    class SolsController extends Controller
    {
        public function __construct(OrderService $OrderService, SolsRepository $OrderRepository)
        {
            $this->OrderRepository = $OrderRepository;
            $this->OrderService = $OrderService;

        }

        public function request_order(Request $request)
        {
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


    }
