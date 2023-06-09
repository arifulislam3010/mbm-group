<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Muktopaath::class,
        \AdamTyn\Lumen\Artisan\StorageLinkCommand::class,
        Commands\RepositoryMakeCommand::class,
        Commands\InterfaceMakeCommand::class,
        \Laravelista\LumenVendorPublish\VendorPublishCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}