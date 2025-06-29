<?php
namespace App\Http\Controllers\Admin;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\Api\AmericanaWebHookRequestResource;
use App\Http\Services\AutoDispatcherService;
use App\Models\Order;
use App\Models\User;
use App\Repositories\FirebaseRepository;
use App\Traits\LocationTrait;
use App\Traits\OrderCreationDateValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    use OrderCreationDateValidation, LocationTrait;
    public function __construct(FirebaseRepository $firebaseRepository)
    {
        $this->firebaseRepository = $firebaseRepository;
    }
    public function showLogin(AutoDispatcherService $AutoDispatcherService)
    {
//        $orders = Order::with('shop', 'branch', 'driver')->whereIn('status', [
//            '13',
//        ])->
//        where(function ($q){
//            $q->whereDate('created_at', Carbon::yesterday())
//                ->orWhereDate('created_at', Carbon::today());
//        })
//            ->get();
//foreach ($orders as $order) {
//            $AutoDispatcherService->autoDispatch($order);
//        }

        return view('admin.auth.login');
    }
    public function compareDataTypesAndFields(array $data1, array $data2): void
    {
        // Check if keys are the same
        $keys1 = array_keys($data1);
        $keys2 = array_keys($data2);

        if ($keys1 !== $keys2) {
            echo "Field names do not match.\n";
            echo "Missing or extra fields:\n";
            print_r(array_diff($keys1, $keys2));
            return;
        }

        echo "Field names match.\n";

        // Check data types for each field
        foreach ($data1 as $key => $value) {
            $type1 = gettype($data1[$key]);
            $type2 = gettype($data2[$key]);

            if ($type1 !== $type2) {
                echo "Data type mismatch for field '$key': $type1 (JSON1) vs $type2 (JSON2)\n";
            }
        }

        echo "Data types checked.\n";
    }
    public function test_fathy()
    {
        $order = Order::whereNull('city')->cursor();
        foreach ($order as $ordez) {
            $ordez->city = @$ordez->branch->city_id;
            $ordez->save();
        }
dd('aaa');
        $orderData = new AmericanaWebHookRequestResource($order);
        return $orderData;
//        OrderLog::create([
//            'order_id' =>  $order->id,
//            'status' => OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
//            'action' => 'Request Cancel Order',
//            'user_id' => $order->ingr_shop_id,
//            'description' => ' Client request to cancel order ',
//        ]);
//        ini_set('max_execution_time', '-1'); //300 seconds = 5 minutes
//        ini_set('memory_limit', '-1');
//       $user = Operator::with('operator')->get();
//        foreach ($user as $operator) {
//        $this->firebaseRepository->delete_all($operator->id);
//        }

    }

    private function sendOrderToWebhook($url, $data)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response  = curl_exec($ch);
        $error_msg = curl_error($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($response, true);
        if ($error_msg) {
            $ss = ['Webhook delivered successfully',
                'url'        => $url,
                'response'   => $response,
                'http_code'  => $httpCode,
                'sent_data'  => $jsonData,
                'result'     => $result,
                'curl_error' => $error_msg,

            ];

        } else {
            $ss = ['Webhook delivered successfully',
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
                'result'    => $result,

            ];
        }

        dd($ss, $data);
    }
    public function login(LoginRequest $request)
    {

        $data = $request->validated();
        // dd(Auth::attempt($data));
        if (Auth::attempt($data)) {
            $user = Auth::user();

            if ($user) {
                $user->firebase_token = $request->input('firebase_token');
                $user->save();
                if ($user->user_role == UserRole::BRANCH) {
                    // dd($user->branch?->is_active);
                    if ($user->branch?->is_active != 1) {
                        return redirect()->back()->with('success', 'Unactive branch');
                    }
                }

            }
// dd($user->user_role);
            if ($user->user_role == UserRole::REPORTS) {
                return redirect()->route('reports');
            }

            return redirect()->route('index');
        } else {
            return redirect()->back()->with('success', __('Invalid Email or Password'));
        }
    }

    public function saveFirebaseToken(Request $request)
    {
        // dd(999);
        // dd($request->input('token'));
        $user = User::findOrFail(auth()->id());

        if ($user) {
            $user->firebase_token = $request->input('token');
            $user->save();

            return response()->json(['message' => 'Firebase token saved successfully'], 200);
        }

        return response()->json(['error' => 'User not authenticated'], 401);
    }
}
