<?php
    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Repositories\MaqlopaRepository;
    use Illuminate\Http\Request;
    use Validator;
    use Auth;
    use App\Http\Services\OrderService;

    class MaqlopaController extends Controller
    {
        public function __construct( MaqlopaRepository $OrderRepository)
        {
            $this->OrderRepository = $OrderRepository;
        }

        public function request_order(Request $request)
        {
            return $this->OrderRepository->save_order($request);
        }

        public function update_order(Request $request, $id)
        {
            return $this->OrderRepository->update_order($request, $id);
        }

        public function cancel_order(Request $request,  $id)
        {
            return $this->OrderRepository->cancel_order($request, $id);
        }

    }
