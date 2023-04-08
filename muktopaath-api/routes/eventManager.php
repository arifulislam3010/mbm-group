<?php


$router->group(['middleware' => ['authUser','auth:api']], function () use ($router) {
    $router->get('/all', 'EventController@index');
    $router->get('/details/{id}', 'EventController@details');
    $router->get('/users/{event_id}', 'EventController@allUsers');
    $router->get('/materials/{event_id}', 'EventController@allMaterials');
    $router->get('/materials/{event_id}', 'EventController@allMaterials');
    $router->get('trash', 'EventController@onlyTrashed');
    $router->post('/create', 'EventController@store');
    $router->put('/update', 'EventController@update');
    $router->delete('/{id}', 'EventController@destroy');

});


$router->group(['middleware' => ['authUser','auth:api']], function () use ($router) {
    
    $router->group(['prefix' => 'event-user'],function() use ($router){
        //Event user
        $router->get('/all', 'EventUserController@index');
        $router->post('/create', 'EventUserController@store');
        $router->put('/update', 'EventUserController@update');
        $router->delete('/{id}', 'EventUserController@destroy');
    });

});

$router->group(['middleware' => ['authUser','auth:api']], function () use ($router) {

    $router->group(['prefix' => 'learner/attendance'],function() use ($router){
        $router->get('/all', 'AttendanceController@index');
        $router->post('/create', 'AttendanceController@store');
        $router->put('/update', 'AttendanceController@update');
        $router->delete('/{id}', 'AttendanceController@destroy');
    });
});

$router->group(['middleware' => ['authUser','auth:api']], function () use ($router) {
    $router->group(['prefix' => 'material'],function() use ($router){
        $router->get('/all', 'MaterialController@index');
        $router->post('/create', 'MaterialController@store');
        $router->put('/update', 'MaterialController@update');
        $router->delete('/{id}', 'MaterialController@destroy');
    });

    
});

$router->group(['middleware' => ['authUser','auth:api']], function () use ($router) {
        $router->group(['prefix' => 'review'],function() use ($router){
        $router->get('/all', 'ReviewController@index');
        $router->post('/create', 'ReviewController@store');
        $router->put('/update', 'ReviewController@update');
        $router->delete('/{id}', 'ReviewController@destroy');
    });

});


$router->group(['middleware' => ['authUser','auth:api']], function () use ($router) {
    $router->group(['prefix' => 'discussion'],function() use ($router){
    $router->get('/{event_id}/all', 'EventDiscussionController@index');
    $router->post('/create', 'EventDiscussionController@store');
    $router->put('/update', 'EventDiscussionController@update');
    $router->delete('/{id}', 'EventDiscussionController@destroy');
});

});