<?php
    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Repositories\MaestropizzaRepository;
    use Illuminate\Http\Request;
    use Validator;
    use Auth;
    use App\Http\Services\OrderService;

    class MaestropizzaController extends Controller
    {
        public function __construct(OrderService $OrderService, MaestropizzaRepository $OrderRepository)
        {
            $this->OrderRepository = $OrderRepository;
            $this->OrderService = $OrderService;
        }

        public function request_order(Request $request)
        {
            return $this->OrderRepository->save_order($request);
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
        public function track_order(Request $request , $API_TOKEN, $id){
            return $this->OrderRepository->track_order($request , $id);
        }
    }
