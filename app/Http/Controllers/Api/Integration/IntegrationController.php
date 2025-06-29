<?php
    namespace App\Http\Controllers\Api\Integration;

    use App\Http\Controllers\Controller;
    use App\Repositories\AlkhawarizmiRepository;
    use App\Repositories\IntegrationRepository;
    use Illuminate\Http\Request;
    use Validator;
    use Auth;
    use App\Http\Requests\Api\OrderRateFormRequest;
    use App\Http\Services\AlkhawarizmiService;

    class IntegrationController extends Controller
    {
        private IntegrationRepository $OrderRepository;

        public function __construct(IntegrationRepository $OrderRepository)
        {
            $this->OrderRepository = $OrderRepository;
        }

        public function orderCreate(Request $request)
        {
            return $this->OrderRepository->orderCreate($request);
        }


        public function getOrder(Request $request)
        {
            return $this->OrderRepository->getOrder($request);
        }

        public function orderUpdate(Request $request)
        {
            return $this->OrderRepository->orderUpdate($request);
        }
        public function trackOrder(Request $request)
        {
            return $this->OrderRepository->trackOrder($request);
        }

        public function orderCancel(Request $request)
        {
            return $this->OrderRepository->orderCancel($request);
        }



    }
