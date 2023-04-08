<?php

namespace Muktopaath\Dashboard;

use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/dashboard.php');
    }

    public function register()
    {
        
    }
}