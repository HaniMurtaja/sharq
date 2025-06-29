<?php
    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\Api\FoodicsOnline\AddWebhookRequest;
    use App\Repositories\FoodicsOnlineRepository;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\File;
    use Validator;
    use Auth;

    class FoodicsOnlineController extends Controller
    {
        public function __construct( FoodicsOnlineRepository $OrderRepository)
        {
            $this->OrderRepository = $OrderRepository;
        }

        public function request_order(Request $request)
        {
            File::append(public_path('FoodicsOnlineOrders.text'),"-----------------"."\n".json_encode(\request()->all())."\n".json_encode(\request()->header())."\n");

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

        public function get_order(Request $request, $API_TOKEN, $id)
        {
            return $this->OrderRepository->get_order($request, $id);
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

    }
