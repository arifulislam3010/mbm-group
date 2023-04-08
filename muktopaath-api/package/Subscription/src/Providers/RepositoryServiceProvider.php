<?php

namespace Subscription\Providers;

use Illuminate\Support\ServiceProvider;
use Subscription\Interfaces\ProductInterface;
use Subscription\Interfaces\PackageInterface;
use Subscription\Interfaces\BillInterface;
use Subscription\Interfaces\CustomerPackageInterface;

use Subscription\Repositories\ProductRepository;
use Subscription\Repositories\PackageRepository;
use Subscription\Repositories\BillRepository;
use Subscription\Repositories\CustomerPackageRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    
    public function register()
    {
        $this->app->bind( ProductInterface::class,ProductRepository::class);
        $this->app->bind( PackageInterface::class,PackageRepository::class);
        $this->app->bind( BillInterface::class,BillRepository::class);
        $this->app->bind( CustomerPackageInterface::class,CustomerPackageRepository::class);
    }
}