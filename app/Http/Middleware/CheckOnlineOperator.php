<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Operator;
use Illuminate\Http\Request;
use App\Models\ClientBranches;
use App\Models\OperatorDetail;
use App\Traits\HandleResponse;
use App\Settings\GeneralSettings;
use Symfony\Component\HttpFoundation\Response;

class CheckOnlineOperator
{
    use HandleResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\HandleResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $operator = OperatorDetail::where('operator_id',auth()->user()->id)->first();
        if (!$operator) {
            return $this->send_response(FALSE, 400, 'not found', NULL);
        }
        if ($operator->status == 4) {
            return $this->send_response(FALSE, 400, 'you are not available change your status', NULL);
        }
        return $next($request);
    }

    public function handleOldFunction(Request $request, Closure $next): Response
    {
        // dd($request->all());
        $user = Operator::where('id', auth()->user()->id)->first();
        //validate user is operator
        if (!$user) {
            return $this->send_response(FALSE, 400, 'not found', NULL);
        }
        $operator = $user->operator;
        //validate user is operator
        if (!$operator) {
            return $this->send_response(FALSE, 400, 'not found', NULL);
        }


        $settings = new GeneralSettings();
        $currentTime = Carbon::now('Asia/Riyadh');
        $order = Order::findOrFail($request->order_id);


        $branch_id_test = $order->ingr_branch_id ?? $order->pickup_id;
        $branch = ClientBranches::find($branch_id_test);
        $branchLatitude = $branch?->lat;
        $branchLongitude = $branch?->lng;

        $branchLatitude = $branchLatitude ?? 0;
        $branchLongitude = $branchLongitude ?? 0;

        $operator_detail = OperatorDetail::WhereHas('operator.driverOrders', function ($query) use ($order, $settings, $currentTime) {
            $query->whereHas('order', function ($query) use ($order) {
                $query->where('ingr_branch_id', $order->ingr_branch_id);
            })


                ->whereRaw('DATE_ADD(created_at, INTERVAL ? MINUTE) >= ?', [$settings->time_multi_order_assign, $currentTime])
                ->orderBy('created_at', 'desc')
                ->limit(1);
        })
            ->select(
                'id',
                'operator_id',
                'status',
                'lat',
                'lng',
                'created_at'
            )
            ->orderBy('created_at', 'desc')

            ->first();

        //validate operator is online
        // dd($operator->status);
        if ($operator->status == 4  && !$operator_detail) {
            // dd(99);
            // dd($operator->status);
            return $this->send_response(FALSE, 400, 'you are not available change your status', NULL);
        }
        // dd(45);

        return $next($request);
    }
}
