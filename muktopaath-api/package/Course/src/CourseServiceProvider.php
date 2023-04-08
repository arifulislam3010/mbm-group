<?php

namespace Muktopaath\Course;

use Illuminate\Support\ServiceProvider;

class CourseServiceProvider extends ServiceProvider{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/course.php');
    }

    public function register()
    {
        
    }
}