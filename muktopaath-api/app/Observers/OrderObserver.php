<?php

namespace App\Observers;
use App\Models\Assessment\Order;
use App\Models\Finance\Balance;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function updated(Order $order)
    {
        Log::info("Updated order status".$order);
    }
}