<?php


/** @var \Laravel\Lumen\Routing\Router $router */
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;
// use App\Http\Controllers\Communication\MailController;
use App\Jobs\ExampleJob;
use App\Mail\SendEmail;
use App\Mail\MailSender;
use Illuminate\Support\Facades\Mail;
//use Mail;


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
}); 

$router->get('/kawla', function () use ($router) {
    return 211;
}); 

Route::group(['prefix' => 'api/audit-panel-data'], function () {
    
    /*************Default Logs API******************/
       Route::get('/', '\myGov\Logtracker\Http\Controllers\LogtrackerController@logApidata');


    /**************Only for MongoDB**************** */
    Route::get('/log-synchronous', [LogtrackerController::class,'getUnsynchronousData']);
    Route::post('/log-synchronous', [LogtrackerController::class,'synchronousProcess']);
});

// $router->get('/mail/{id}', 'Communication\VerifyMailController@getSms');
$router->get('/sms', 'Communication\VerifyMailController@getSms');

// $router->post('cache-images/{w?}x{h?}x1/{path1}/{path2}/{src}', 'Filemanager/UploadContentBankController@resize_image');


    $router->get('muktopaath/uploads/videos/{file_path}',function($file_path) use($router){
        $file_path = str_replace('&','.',$file_path);
        $getData = explode('<::>', $file_path);
        $getDataArrLen = count($getData);

        $getBasePath = config('global.storage_path');
        
        if($getDataArrLen>0){
            if($getData[0]=='manual') $getPath = $getBasePath . '/' . $getData[$getDataArrLen - 2] . '/' . $getData[$getDataArrLen - 1];
            else{
                list($src,$ext) = explode('.', $file_path);        
                $getPath = $getBasePath . '/' . base64_decode($src) . '.' . $ext;
            }
        }else{
            list($src,$ext) = explode('.', $file_path);
            $getPath = $getBasePath . '/' . base64_decode($src) . '.' . $ext;
        }
        $file =   $getPath;
        return $file;
    });

    $router->get('cache-images/{w}x{h}x1/{path1}/{path2}/{file_path}', function($w=100, $h=100, $path1, $path2, $file_path) use ($router){

        /*$cacheImage = Image::cache(function($img) use($src, $w, $h){
            return $img->make("storage/uploads/images/user-3" . $src)->resize($w,$h);
        }, 10, true); // for 10 mins in cache
        
        return Response::make($cacheImage, 200, array('Content-type' => 'image/jpeg'));*/
        $file_path = str_replace('&','.',$file_path);
        $getData = explode('<::>', $file_path);
        $getDataArrLen = count($getData);
        $getBasePath = config('global.storage_path') . $path1 . '/' . $path2;
        
        if($getDataArrLen>0){
            if($getData[0]=='manual') $getPath = $getBasePath . '/' . $getData[$getDataArrLen - 2] . '/' . $getData[$getDataArrLen - 1];
            else{
                list($src,$ext) = explode('.', $file_path);        
                $getPath = $getBasePath . '/' . base64_decode($src) . '.' . $ext;
            }
        }else{
            list($src,$ext) = explode('.', $file_path);
            $getPath = $getBasePath . '/' . base64_decode($src) . '.' . $ext;
        }
        
        //return base64_decode($src);
        //return $getPath;
        
        if(file_exists($getPath)){
            $img = Image::make($getPath);//->resize($w, $h);
            $img->resize($w, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            return $img->response('jpg');
        }else return 'File not exists.';
    });
    Route::get('redis/check', function(){

        Redis::set('key', 'value');
        Redis::del('key', 'value');

        return $value = Redis::get('key');

    });
    Route::get('test/email', function(){
        $details = [
            'title' => 'Mail from ItSolutionStuff.com',
            'body' => 'This is for testing email using smtp'
        ];

        Mail::to('')->send(new MailSender($details));

        return "HTML Email Sent. Check your inbox.";
    });
    Route::get('artisan/Command', function(){

        return "ok";
    });

    Route::get('version-api', function(){
        return '2023-01-16';
    });
    $router->get('/content-migrate/{skip}', 'MigrationController@contentMigrate');
    $router->get('/content-migrate-duration/{skip}', 'MigrationController@contentMigrateDuration');
    $router->get('/course-completeness-migrate/{skip}', 'MigrationController@courseCompleteness');
    $router->get('/course-completeness-migrate2/{from}/{take}', 'MigrationController@courseCompleteness2');
    $router->get('/course-completeness-migrate3/{skip}', 'MigrationController@courseCompleteness3');
    $router->get('/single-course-completeness-migrate/{id}', 'MigrationController@SingleCourseCompleteness');

    
    // $router->get('/lou', 'ContentBankController@index');
    // $router->get('/create', 'ContentBankController@create');
    // $router->post('/api/upload', 'api\UploadContentBankController@store');
    // $router->get('/storage-types', 'StorageTypeController@index');
    // $router->get('/file-types', 'FileTypeController@index');
    // $router->post('/file-types/create', 'FileTypeController@store');
    // $router->post('/storage-types/create', 'StorageTypeController@store');

    // $router->get('/file-storage-settings', 'FileStorageSettingController@index'); 
    // $router->post('/file-storage-settings/create', 'FileStorageSettingController@store'); 

    // $router->get('/userstorage-settings', 'UserFilestorageSettingController@index'); 
    // $router->post('/userstorage-settings/create', 'UserFilestorageSettingController@store'); 