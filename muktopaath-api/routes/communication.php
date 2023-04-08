<?php
use App\Jobs\ExampleJob;

// Route::get('test/email', function(){

// $send_mail = 'rahibsh.me@gmail.com';

// dispatch(new ExampleJob);

// dd('send mail successfully !!');
// });

$router->get('test/email', 'CommunicationController@mail');