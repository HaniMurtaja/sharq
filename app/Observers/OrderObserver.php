<?php

namespace App\Observers;

use App\Enum\OrderStatus;
use App\Enum\UserRole;
use App\Events\CreateOrder;
use App\Events\OrderCreateWidget;
use App\Events\OrderUpdateStatusWidget;
use App\Models\Order;
use App\Traits\LocationTrait;

class OrderObserver
{
    use LocationTrait;
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // dd(22,@$order->branch);
        $order->city = @$order->branch->city_id;
        $this->checkAutoDispatch(@$order);

        try {
            if ($order->lat && $order->lng) {
                $branchLatitude = @$order->branch->lat;
                $branchLongitude = @$order->branch->lng;
                $location = $this->getTravelTime($branchLatitude, $branchLongitude, $order->lat, $order->lng);
                $order->delivery_duration = $location['duration'];
                $order->delivery_distance = $location['distance'];
            }
        } catch (\Exception $exception) {
        }
        //    $data = New \App\Http\Resources\Admin\Dispatcher\OrderResource($order);
        // event(new OrderCreateWidget($data));
        $order->save();

        // try {
        //     $firebase = new \App\Repositories\FirebaseRepositoryTest();
        //     $firebase->saveBranches(collect([@$order->branch]));
        // } catch (\Throwable $e) {
        // }
    }



    public function checkAutoDispatch ($order) {

        $branch = $order?->branch ?? $order?->branchIntegration;
        if($order?->cityData?->auto_dispatch == 1) {
            $order->status = OrderStatus::AUTO_DISPATCH;
            $order->save();
            return;
        }

        if($order?->shop?->client?->auto_dispatch == 1) {
            $order->status = OrderStatus::AUTO_DISPATCH;
            $order->save();
            return;
        }

        if($branch?->auto_dispatch == 1) {
            $order->status = OrderStatus::AUTO_DISPATCH;
            $order->save();
            return;
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {



        //        if ($order->isDirty('status')) {
        //            $data = New \App\Http\Resources\Admin\Dispatcher\OrderResource($order);
        //            event(new OrderUpdateStatusWidget($data));
        //        }
    }

    public function save(Order $order): void
    {
        // try {
        //     $firebase = new \App\Repositories\FirebaseRepositoryTest();
        //     $firebase->saveBranches(collect([@$order->branch]));
        // } catch (\Throwable $e) {
        // }

        //     if ($order->isDirty('status')) {
        //     $data = New \App\Http\Resources\Admin\Dispatcher\OrderResource($order);
        //     event(new OrderUpdateStatusWidget($data));
        // }

    }

    public function updating(Order $order): void
    {

        //     if ($order->isDirty('status')) {
        //     $data = New \App\Http\Resources\Admin\Dispatcher\OrderResource($order);
        //     event(new OrderUpdateStatusWidget($data));

        // }
    }


    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
