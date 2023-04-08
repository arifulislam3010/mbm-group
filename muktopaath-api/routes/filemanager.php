<?php

$router->group(['prefix' => 'user'],function() use ($router){

	$router->get('/lou', 'ContentBankController@index');
    $router->get('/create', 'ContentBankController@create');
    $router->post('/upload-file','UploadContentBankController@store');
    $router->put('/savename','UploadContentBankController@savename');
    $router->post('/delete-file', 'UploadContentBankController@removeFile');
    $router->get('/get-files', 'UploadContentBankController@get');
    $router->post('/profile-folder', 'UploadContentBankController@profilefolder');
    $router->get('/get-my-files', 'UploadContentBankController@getfiles');
    $router->post('/create-folder', 'UploadContentBankController@createfolder');
    $router->post('/delete-folder', 'UploadContentBankController@deletefolder');
    $router->post('/create-url', 'UploadContentBankController@url_upload');
    $router->post('/download-file', 'UploadContentBankController@download_file');
 
    $router->get('/storage-types', 'StorageTypeController@index');
    $router->get('/file-types', 'FileTypeController@index');
    $router->post('/file-types/create', 'FileTypeController@store');
    $router->post('/storage-types/create', 'StorageTypeController@store');
    $router->post('upload-cropped-image','UploadContentBankController@cropupload');

    $router->get('/file-storage-settings', 'FileStorageSettingController@index'); 
    $router->post('/file-storage-settings/create', 'FileStorageSettingController@store'); 

    $router->get('/userstorage-settings', 'UserFilestorageSettingController@index'); 
    $router->post('/userstorage-settings/create', 'UserFilestorageSettingController@store'); 
    
});