<?php
    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Repositories\LoginextRepository;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\File;
    use Validator;
    use Auth;
    use App\Http\Services\OrderService;

    class LoginextController extends Controller
    {
        public function __construct(OrderService $OrderService, LoginextRepository $OrderRepository)
        {
            $this->OrderRepository = $OrderRepository;
            $this->OrderService = $OrderService;
        }

        public function request_order(Request $request)
        {
            File::append(public_path('LoginextOrder.txt'),"-----------------"."\n".json_encode(\request()->all()). json_encode(\request()->header()). "\n");
            return $this->OrderRepository->save_order($request);
        }

        public function update_order(Request $request, $API_TOKEN, $id)
        {
            File::append(public_path('update_order.txt'),"-----------------"."\n".json_encode(\request()->all()). json_encode(\request()->header()). "\n");
            return $this->OrderRepository->update_order($request, $id);
        }

        public function cancel_order(Request $request, $API_TOKEN, $id)
        {
            File::append(public_path('update_order.txt'),"-----------------"."\n".json_encode(\request()->all()). json_encode(\request()->header()). "\n");
            return $this->OrderRepository->cancel_order($request, $id);
        }

    }
