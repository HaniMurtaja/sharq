<?php
    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Repositories\LoginextoldRepository;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\File;

    class LoginextoldController extends Controller
    {
        public function __construct(LoginextoldRepository $OrderRepository)
        {
            $this->OrderRepository = $OrderRepository;
        }

        public function request_order(Request $request)
        {
            File::append(public_path('LoginextOrder.text'),"-----------------"."\n".json_encode(\request()->all())."\n");

            return $this->OrderRepository->save_order($request);
        }

        public function update_order(Request $request, $API_TOKEN, $id)
        {
            File::append(public_path('LoginextOrder_update.text'),"-----------------"."\n".json_encode(\request()->all())."\n");

            return $this->OrderRepository->update_order($request, $id);
        }

        public function cancel_order(Request $request, $API_TOKEN, $id)
        {
            File::append(public_path('LoginextOrder_cancel.text'),"-----------------"."\n".json_encode(\request()->all())."\n");

            return $this->OrderRepository->cancel_order($request, $id);
        }

    }
