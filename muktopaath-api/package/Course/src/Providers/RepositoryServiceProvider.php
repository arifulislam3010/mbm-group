<?php

namespace Muktopaath\Course\Providers;

use Illuminate\Support\ServiceProvider;
use Muktopaath\Course\Interfaces\DemoInterface;
use Muktopaath\Course\Interfaces\AccountsInterface;
use Muktopaath\Course\Interfaces\JourneyInterface;
use Muktopaath\Course\Interfaces\ReportInterface;
use Muktopaath\Course\Interfaces\SAttendanceRepositoryInterface;
use Muktopaath\Course\Interfaces\TransactionInterface;

use Muktopaath\Course\Repositories\DemoRepository;
use Muktopaath\Course\Repositories\JourneyRepository;
use Muktopaath\Course\Repositories\AccountsRepository;
use Muktopaath\Course\Repositories\TransactionRepository;
use Muktopaath\Course\Repositories\ReportRepository;
use Muktopaath\Course\Repositories\SAttendanceRepository;

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
        $this->app->bind( DemoInterface::class,DemoRepository::class);
        $this->app->bind( AccountsInterface::class,AccountsRepository::class);
        $this->app->bind( JourneyInterface::class,JourneyRepository::class);
        $this->app->bind( ReportInterface::class,ReportRepository::class);
        $this->app->bind( SAttendanceRepositoryInterface::class,SAttendanceRepository::class);
        $this->app->bind( TransactionInterface::class,TransactionRepository::class);
    }
}