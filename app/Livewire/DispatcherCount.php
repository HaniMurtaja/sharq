<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Settings\GeneralSettings;
use Livewire\WithPagination;
use Carbon\Carbon;

class DispatcherCount extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $orders_count = 0;



    // public function render()
    // {
    //     $settings = new GeneralSettings();

    //     // Set default start and end times
    //     try {
    //         $startTime = Carbon::createFromFormat('H:i', $settings->business_hours['start_time']);
    //         $endTime = Carbon::createFromFormat('H:i', $settings->business_hours['end_time']);
    //     } catch (\Exception $e) {
    //         // Fallback to today's start and end of day
    //         $startTime = Carbon::today()->startOfDay();
    //         $endTime = Carbon::today()->endOfDay();
    //     }


    //     $ordersQuery = Order::query();


    //     $hasSpecialHours = $ordersQuery->whereHas('shop', function ($query) {
    //         $query->whereHas('client', function ($q) {
    //             $q->where('has_special_business_hours', 1);
    //         });
    //     })->exists();

    //     // if ($hasSpecialHours) {
    //     //     try {
    //     //         // Get special business hours if they exist
    //     //         $specialSettings = new GeneralSettings();
    //     //         $startTime = Carbon::createFromFormat('H:i', $specialSettings->special_business_hours['start_time']);
    //     //         $endTime = Carbon::createFromFormat('H:i', $specialSettings->special_business_hours['end_time']);
    //     //     } catch (\Exception $e) {
    //     //         // Handle invalid special hours
    //     //         $startTime = Carbon::today()->startOfDay();
    //     //         $endTime = Carbon::today()->endOfDay();
    //     //     }
    //     // }

    //     // Apply order filters based on business hours
    //     if ($startTime->greaterThan($endTime)) {
    //         // If start time is greater than end time, it means the time range crosses midnight
    //         $ordersQuery->where(function ($query) use ($startTime, $endTime) {
    //             $query->whereDate('created_at', Carbon::yesterday())
    //                 ->whereTime('created_at', '>', $startTime)
    //                 ->orWhere(function ($subQuery) use ($endTime) {
    //                     $subQuery->whereDate('created_at', Carbon::today())
    //                         ->whereTime('created_at', '<', $endTime);
    //                 });
    //         });
    //     } else {
    //         // Normal case: within the same day
    //         $ordersQuery->whereDate('created_at', Carbon::today())
    //             ->whereTime('created_at', '>=', $startTime)
    //             ->whereTime('created_at', '<=', $endTime);
    //     }



    //     // Filter orders for clients
    //     if (auth()->user()->hasRole('Client')) {
    //         $ordersQuery->where('ingr_shop_id', auth()->id());
    //     }

    //     // Count total orders and paginate results
    //     $this->orders_count = $ordersQuery->count();
    //     $orders = $ordersQuery->orderBy('created_at', 'desc');

    //     // Return the view with data
    //     return view('livewire.all-order', [
    //         'orders_count' => $this->orders_count,
    //         'orders' => $orders->paginate(10),
    //     ]);
    // }





    public function render()
    {
        $settings = new GeneralSettings();



        $ordersQuery = Order::query();

        $ordersQuery->whereDate('created_at', Carbon::yesterday())
            ->orWhereDate('created_at', Carbon::today());


        if (auth()->user()->hasRole('Client')) {
            $ordersQuery->where('ingr_shop_id', auth()->id());
        }

        $this->orders_count = $ordersQuery->count();

       

        return view('livewire.dispatcher-count', [
            'orders_count' => $this->orders_count,
           
        ]);
    }
}
