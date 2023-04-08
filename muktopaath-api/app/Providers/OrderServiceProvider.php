<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Assessment\Order;
use App\Models\Finance\Balance;
use App\Observers\OrderObserver;

class OrderServiceProvider extends ServiceProvider
{
    
    public function register()
    { 
        
    }

    public function boot() 
    {
        Order::observe(OrderObserver::class);
    }

}