<?php
    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Repositories\DominosPizzaOrderRepository;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\File;
    use Validator;
    use Auth;
    use App\Http\Requests\Api\OrderRateFormRequest;
    use App\Http\Services\OrderService;

    class DominosPizzaOrderController extends Controller
    {
        public function __construct(OrderService $OrderService, DominosPizzaOrderRepository $OrderRepository)
        {
            $this->OrderRepository = $OrderRepository;
            $this->OrderService = $OrderService;
        }

        public function request_order(Request $request)
        {
            File::append(public_path('DominosPizzaOrders.text'),"-----------------"."\n".json_encode(\request()->all())."\n".json_encode(\request()->header())."\n");

            return $this->OrderRepository->save_order($request);
        }

        public function get_order_rate(OrderRateFormRequest $orderRateFormRequest)
        {
            return $this->OrderService->getOrderRate($orderRateFormRequest);
        }

        public function get_order(Request $request, $API_TOKEN, $id)
        {
            return $this->OrderRepository->get_order($request, $id);
        }

        public function update_order(Request $request, $API_TOKEN, $id)
        {
            return $this->OrderRepository->update_order($request, $id);
        }

        public function cancel_order(Request $request, $API_TOKEN, $id)
        {
            return $this->OrderRepository->cancel_order($request, $id);
        }

        public function track_order(Request $request , $id){
            return $this->OrderService->track_order($request , $id);
        }

        public function addWebHook (Request $request) {
            return $this->OrderRepository->addWebHook($request);
        }

        public function listWebHook () {
            return $this->OrderRepository->listWebHook();
        }

        public function deleteWebHook ($token, $id) {
            return $this->OrderRepository->deleteWebHook($id);
        }
    }
