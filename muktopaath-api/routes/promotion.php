<?php
    
    // frontend start ==================== >
 
    // frontend end ==================== >
    
    $router->group(['prefix' => 'ad'],function() use ($router){

        $router->get('/list', 'AdvertisementController@list');
        $router->get('/view/{id}', 'AdvertisementController@showsingle');
        $router->get('/details', 'AdvertisementController@show');
        $router->post('/create', 'AdvertisementController@store');
        $router->put('/update', 'AdvertisementController@update');
        $router->put('/approve/{id}', 'AdvertisementController@approve');
        $router->delete('/delete/{id}', 'AdvertisementController@delete');

    }); 