<?php

// use App\Console\Commands\Muktopaath;

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));
 
/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

 $app->withFacades();

 $app->withEloquent();

 $app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'authUser' => App\Http\Middleware\AuthUser::class,
    'cors' => App\Http\Middleware\CorsMiddleware::class,
    'checkrole' => App\Http\Middleware\Checkrole::class,
    'redischeck' => App\Http\Middleware\RedisCheck::class,
    // 'csrf' => 'Laravel\Lumen\Http\Middleware\VerifyCsrfToken',
    'verified' => App\Http\Middleware\EnsureEmailIsVerified::class,
]);

$app->register(Laravel\Passport\PassportServiceProvider::class);
$app->register(Dusterio\LumenPassport\PassportServiceProvider::class);

$app->register(Fruitcake\Cors\CorsServiceProvider::class);

 $app->register(Ugiw\ConfigCache\ServiceProviders\ConfigCacheServiceProvider::class);
 
 if (!class_exists('Yaml')) {
    class_alias('Symfony\Component\Yaml\Yaml', 'Yaml');
}

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton('filesystem', function ($app) { return $app->loadComponent('filesystems', 'Illuminate\Filesystem\FilesystemServiceProvider', 'filesystem'); });



$app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
$app->register(App\Providers\GoogleDriveServiceProvider::class);
$app->register(App\Providers\RepositoryServiceProvider::class);
$app->register(App\Providers\MacroServiceProvider::class);
$app->register(App\Providers\OrderServiceProvider::class);
$app->register(\myGov\Logtracker\LogtrackerServiceProvider::class);
$app->register(Maatwebsite\Excel\ExcelServiceProvider::class);
$app->register(\Barryvdh\DomPDF\ServiceProvider::class);
$app->register(\SimpleSoftwareIO\QrCode\QrCodeServiceProvider::class);
// $app->register(Meneses\LaravelMpdf\LaravelMpdfServiceProvider::class);





/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/


$app->configure('app');
$app->configure('cors');
$app->configure('settings');
$app->configure('global');
$app->configure('database');
$app->configure('auth');
$app->configure('dompdf');

\Dusterio\LumenPassport\LumenPassport::routes($app, ['prefix' => 'v1/oauth']);
/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    App\Http\Middleware\CorsMiddleware::class,
    // App\Http\Middleware\AuthUser::class
    // App\Http\Middleware\RedisCheck::class
]);


// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(Illuminate\Mail\MailServiceProvider::class);
$app->register(Muktopaath\Course\CourseServiceProvider::class);
$app->register(Subscription\SubscriptionServiceProvider::class);
$app->register(Muktopaath\Course\Providers\RepositoryServiceProvider::class);
$app->register(Subscription\Providers\RepositoryServiceProvider::class);
$app->register(Laravel\Socialite\SocialiteServiceProvider::class);
$app->register(Muktopaath\Dashboard\DashboardServiceProvider::class);

$app->withFacades(true, [ 'Intervention\Image\Facades\Image' => 'Image', 
                        'Illuminate\Support\Facades\Mail' => 'Mail'
]);

$app->register(Intervention\Image\ImageServiceProvider::class);
$app->register(Illuminate\Redis\RedisServiceProvider::class);
/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->configure('mail');

$app->alias('mail.manager', Illuminate\Mail\MailManager::class);
$app->alias('mail.manager', Illuminate\Contracts\Mail\Factory::class);

$app->alias('mailer', Illuminate\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);
$app->alias('Socialite', Laravel\Socialite\Facades\Socialite::class);
$app->alias('QrCode', SimpleSoftwareIO\QrCode\Facades\QrCode::class);
$app->alias('PDF', Meneses\LaravelMpdf\Facades\LaravelMpdf::class);



$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\ContentBank',
    'prefix' => 'content-bank',
    'middleware'=>['auth:api','authUser','checkrole']
], function ($router) {
    require __DIR__.'/../routes/contentbank.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Assessment',
    'prefix' => 'assessment',
    'middleware'=>['authUser','checkrole']
], function ($router) {
    require __DIR__.'/../routes/assessment.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Assessment',
    'prefix' => 'classroom',
    'middleware'=>['authUser','checkrole']
], function ($router) {
    require __DIR__.'/../routes/classroom.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Question',
], function ($router) {
    require __DIR__.'/../routes/questions.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\AdminSettings',
    'prefix' => 'admin-settings',
], function ($router) {
    require __DIR__.'/../routes/adminsettings.php';
});


$app->router->group([
    'namespace' => 'Marketplace\Http\Controllers',
    'prefix' => 'marketplace',
    'middleware'=>['authUser']
], function ($router) {
    require __DIR__.'/../routes/promotion.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Promotion',
    'prefix' => 'promotion',
    'middleware'=>['authUser']
], function ($router) {
    require __DIR__.'/../routes/promotion.php';
});

$app->router->group([
    'namespace' => 'Subscription\Http\Controllers',
    'prefix' => 'subscription',
    'middleware'=>['authUser']
], function ($router) {
    require __DIR__.'/../package/Subscription/src/routes/subscription.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\MyAccount',
    'prefix' => 'my-account',
], function ($router) {
    require __DIR__.'/../routes/myaccount.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Filemanager',
    'prefix' => 'file-manager',
    'middleware'=>['authUser','checkrole']
], function ($router) {
    require __DIR__.'/../routes/filemanager.php';
});


$app->router->group([
    'namespace' => 'App\Http\Controllers\FrontAssessment',
    'prefix' => 'assessment-front',
    //'middleware' => ['redischeck']
], function ($router) {
    require __DIR__.'/../routes/assessment_front.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\ContentBankFront',
    'prefix' => 'content-bank-front',
], function ($router) {
    require __DIR__.'/../routes/contentBankFront.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\EventManager',
    'prefix' => 'event',
], function ($router) {
    require __DIR__.'/../routes/eventManager.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Finance',
    'prefix' => 'finance',
], function ($router) {
    require __DIR__.'/../routes/finance.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Communication',
    'prefix' => 'communication',
], function ($router) {
    require __DIR__.'/../routes/communication.php';
});

$app->router->group([
    'namespace' => 'Muktopaath\Course\Http\Controllers',
    'prefix' => 'course',
    'middleware'=>['authUser','checkrole']
], function ($router) {
    require __DIR__.'/../package/Course/src/routes/course.php';
});

$app->router->group([
    'namespace' => 'Muktopaath\Course\Http\Controllers\Api',
    'prefix' => 'learner-course',
    // 'middleware' => ['redischeck']
], function ($router) {
    require __DIR__.'/../package/Course/src/routes/course_front.php';
});


$app->router->group([
    'namespace' => 'Muktopaath\Dashboard\Http\Controllers',
    'prefix' => 'api/v3/dashboard',
    'middleware'=>['authUser']
 ], function ($router) {
    require __DIR__.'/../package/Dashboard/src/routes/dashboard.php';
 });


return $app;