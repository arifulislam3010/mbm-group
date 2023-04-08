<?php
    
  Route::group(['prefix' => 'products'], function ()  {

    Route::get('/view','ProductController@index');
    Route::post('/create','ProductController@store');
    Route::put('/update','ProductController@update');
    Route::delete('/delete/{id}','ProductController@delete');

});


Route::group(['prefix' => 'packages'], function ()  {

    Route::get('/view','PackageController@index');
    Route::get('/view/{id}','PackageController@show');
    Route::post('/create','PackageController@store');
    Route::put('/update','PackageController@update');
    Route::delete('/delete/{id}','PackageController@delete');

});

Route::group(['prefix' => 'bills'], function ()  {

    Route::get('/view','BillController@index');
    Route::get('/view/{id}','BillController@show');
    Route::post('/create','BillController@store');
    Route::put('/update','BillController@update');
    Route::put('/approve','BillController@approve');
    Route::delete('/delete/{id}','BillController@delete');

});

Route::group(['prefix' => 'customer-package'], function ()  {

    Route::get('/view','CustomerPackageController@index');
    Route::get('/view/{id}','CustomerPackageController@show');
    Route::post('/create','CustomerPackageController@store');
    Route::put('/update','CustomerPackageController@update');
    Route::put('/approve','CustomerPackageController@approve');
    Route::delete('/delete/{id}','CustomerPackageController@delete');

});