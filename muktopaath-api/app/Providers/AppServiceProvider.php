<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\FileStorageSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    { 
        
    }

    public function boot() 
    {
        $url = $this->app->request->getRequestUri();
        $prefixarr = preg_split ("/\//", $url);
        if(count($prefixarr)>2){
            $prefix = $prefixarr[1]!=''?$prefixarr[1]:$prefixarr[2];
        if($prefix!=='' && $prefix!=="api"){
            $this->handleDatabaseConnections($prefix);
        }
        
    }

    }

    private function handleDatabaseConnections($prefix)
    {
        // if()
        Config::set('database.default',$prefix);
    }
}
