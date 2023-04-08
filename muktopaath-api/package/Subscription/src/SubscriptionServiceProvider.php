<?php

namespace Subscription;

use Illuminate\Support\ServiceProvider;

class SubscriptionServiceProvider extends ServiceProvider{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/subscription.php');
    }

    public function register()
    {
        
    }
}