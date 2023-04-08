<?php

//this is routes
$router->group(['middleware' => ['authUser','auth:api']], function () use ($router) {
    $router->group(['prefix' => 'balance'],function() use ($router){
        $router->get('/all', 'BalanceController@index');
        $router->post('/create', 'BalanceController@store');
        $router->put('/update', 'BalanceController@update');
        $router->delete('/{id}', 'BalanceController@destroy');
    });
});

$router->group(['middleware' => ['authUser','auth:api']], function () use ($router) {
    $router->group(['prefix' => 'payment-request'],function() use ($router){
        $router->get('/all', 'PaymentRequestController@index');
        $router->post('/create', 'PaymentRequestController@store');
        $router->put('/grant', 'PaymentRequestController@grant');
        $router->delete('/{id}', 'PaymentRequestController@destroy');
    });
});