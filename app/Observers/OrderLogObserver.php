<?php

namespace App\Observers;

use App\Models\OrderLog;

class OrderLogObserver
{
    public function creating(OrderLog $orderLog)
    {
        $orderLog->user_id = auth()->id();
    }
}
