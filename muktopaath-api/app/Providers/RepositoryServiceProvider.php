<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\Myaccount\UserRepositoryInterface;
use App\Interfaces\AdminSettings\LangRepositoryInterface;
use App\Interfaces\AdminSettings\CategoryRepositoryInterface;
use App\Interfaces\AdminSettings\DegreeRepositoryInterface;
use App\Interfaces\AdminSettings\EduLevelRepositoryInterface;
use App\Interfaces\AdminSettings\ProfessionRepositoryInterface;
use App\Interfaces\AdminSettings\WorkingFieldRepositoryInterface;
use App\Interfaces\Myaccount\InstitutionRepositoryInterface;
use App\Interfaces\EventManager\EventRepositoryInterface;
use App\Interfaces\EventManager\EventUserRepositoryInterface;
use App\Interfaces\EventManager\AttendanceRepositoryInterface;
use App\Interfaces\EventManager\MaterialRepositoryInterface;
use App\Interfaces\EventManager\ReviewRepositoryInterface;
use App\Interfaces\Finance\BalanceRepositoryInterface;
use App\Interfaces\Finance\PaymentRequestRepositoryInterface;
use App\Interfaces\Assessment\SAttendanceRepositoryInterface;
use App\Interfaces\Assessment\DiscussionRepositoryInterface;
use App\Interfaces\External\TeachersPortalRepositoryInterface;
use App\Interfaces\Assessment\TimelineRepositoryInterface;
use App\Interfaces\Promotion\PromotionInterface;
use App\Interfaces\Myaccount\RatingFeedbackInterface;
use App\Interfaces\ValidationRepositoryInterface;


use App\Repositories\Myaccount\UserRepository;
use App\Repositories\AdminSettings\LangRepository;
use App\Repositories\AdminSettings\CategoryRepository;
use App\Repositories\AdminSettings\DegreeRepository;
use App\Repositories\AdminSettings\EduLevelRepository;
use App\Repositories\AdminSettings\ProfessionRepository;
use App\Repositories\AdminSettings\WorkingFieldRepository;
use App\Repositories\Myaccount\InstitutionRepository;
use App\Repositories\EventManager\EventRepository;
use App\Repositories\EventManager\EventUserRepository;
use App\Repositories\EventManager\AttendanceRepository;
use App\Repositories\EventManager\MaterialRepository;
use App\Repositories\EventManager\ReviewRepository;
use App\Repositories\Finance\BalanceRepository;
use App\Repositories\Finance\PaymentRequestRepository;
use App\Repositories\External\TeachersPortalRepository;
use App\Repositories\Assessment\SAttendanceRepository;
use App\Repositories\Assessment\DiscussionRepository;
use App\Repositories\Assessment\TimelineRepository;
use App\Repositories\Promotion\PromotionRepository;
use App\Repositories\Myaccount\RatingFeedbackRepository;
use App\Repositories\ValidationRepository;

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
        $this->app->bind( UserRepositoryInterface::class,UserRepository::class);
        $this->app->bind( ValidationRepositoryInterface::class,ValidationRepository::class);
        $this->app->bind( LangRepositoryInterface::class,LangRepository::class);
        $this->app->bind( CategoryRepositoryInterface::class,CategoryRepository::class);
        $this->app->bind( DegreeRepositoryInterface::class,DegreeRepository::class);
        $this->app->bind( EduLevelRepositoryInterface::class,EduLevelRepository::class);
        $this->app->bind( ProfessionRepositoryInterface::class,ProfessionRepository::class);
        $this->app->bind( DegreeRepositoryInterface::class,DegreeRepository::class);
        $this->app->bind( WorkingFieldRepositoryInterface::class,WorkingFieldRepository::class);
        
        //Myaccount repositories
        $this->app->bind( InstitutionRepositoryInterface::class,InstitutionRepository::class);

        //Event
        $this->app->bind( EventRepositoryInterface::class,EventRepository::class);
        $this->app->bind( EventUserRepositoryInterface::class,EventUserRepository::class);
        $this->app->bind( AttendanceRepositoryInterface::class,AttendanceRepository::class);
        $this->app->bind( MaterialRepositoryInterface::class,MaterialRepository::class);
        $this->app->bind( ReviewRepositoryInterface::class,ReviewRepository::class);
        
        //Finance
        $this->app->bind( BalanceRepositoryInterface::class,BalanceRepository::class);
        $this->app->bind( PaymentRequestRepositoryInterface::class,PaymentRequestRepository::class);

        //attendance
        $this->app->bind( SAttendanceRepositoryInterface::class,SAttendanceRepository::class);

        //discussion
        $this->app->bind( DiscussionRepositoryInterface::class,DiscussionRepository::class);
        //timelines
        $this->app->bind( TimelineRepositoryInterface::class,TimelineRepository::class);
        $this->app->bind( PromotionInterface::class,PromotionRepository::class);
        $this->app->bind( RatingFeedbackInterface::class,RatingFeedbackRepository::class);

        
    }
}