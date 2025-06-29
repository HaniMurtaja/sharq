<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Foodics\FoodicsOrderRequest;
use App\Repositories\FoodicsOrderRepository;
    use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;
    use Auth;
    use App\Http\Requests\Api\OrderRateFormRequest;
    use App\Http\Services\OrderService;

    class FoodicsOrderController extends Controller
    {
        public function __construct(FoodicsOrderRepository $FoodicsOrderRepository)
        {
            $this->FoodicsOrderRepository = $FoodicsOrderRepository;
        }

        public function success(Request $request)
        {
            return $this->FoodicsOrderRepository->success($request);
        }

        public function webhook(FoodicsOrderRequest $request)
        {
            File::append(public_path('FoodicscreateOrder.text'),"-----------------"."\n".json_encode(\request()->all())."\n");

            return $this->FoodicsOrderRepository->webhook($request);
        }

    }
