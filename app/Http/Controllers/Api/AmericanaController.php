<?php
    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\Api\Americana\AddWebhookRequest;
    use App\Repositories\AmericanaRepository;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\File;
    use Validator;
    use Auth;
    use App\Http\Requests\Api\OrderRateFormRequest;
    use App\Http\Services\OrderService;

    class AmericanaController extends Controller
    {
        public function __construct(OrderService $OrderService, AmericanaRepository $OrderRepository)
        {
            $this->OrderRepository = $OrderRepository;
            $this->OrderService = $OrderService;
        }

        public function request_order(Request $request)
        {
            File::append(public_path('AmericanaOrder.text'),"-----------------"."\n".json_encode(\request()->all())."\n");

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
            File::append(public_path('AmericanaUpdateOrder.text'),"-----------------"."\n".json_encode(\request()->all())."\n");

            return $this->OrderRepository->update_order($request, $id);
        }

        public function cancel_order(Request $request, $API_TOKEN, $id)
        {
            File::append(public_path('AmericanaCancelOrder.text'),"-----------------"."\n".json_encode(\request()->all())."\n");

            return $this->OrderRepository->cancel_order($request, $id);
        }

        public function track_order(Request $request , $API_TOKEN, $id){
            return $this->OrderRepository->track_order($request , $id);
        }

        public function addWebHook (AddWebhookRequest $request,$API_TOKEN) {
            return $this->OrderRepository->addWebHook($request);
        }


        public function listWebHook (Request $request,$API_TOKEN) {
            return $this->OrderRepository->listWebHook($request);
        }

        public function deleteWebHook (Request $request,$API_TOKEN, $id) {
            return $this->OrderRepository->deleteWebHook($id,$request);
        }
        public function auth (Request $request) {
            return $this->OrderRepository->auth($request);
        }
        public function sse ($id,Request $request) {
            return $this->OrderRepository->sse($id,$request);
        }
    }
