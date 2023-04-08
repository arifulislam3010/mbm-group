<?php
  
 
	// frontend start ==================== >
	$router->group(['prefix' => 'content'],function() use ($router){
		$router->get('/view', 'ContentBankController@index'); 
		//$router->get('/view_all', 'FrontendController@all');
		//$router->get('/view-details/{id}', 'FrontendController@details');
    	$router->get('/view/{id}', 'ContentBankController@show');
    	$router->get('list', 'ContentBankController@list');
	    $router->post('/create', 'ContentBankController@store');
	    $router->put('/update', 'ContentBankController@update');
	    $router->delete('/delete/{id}', 'ContentBankController@delete');
   		$router->post('/create-my_other_purchase', 'ContentBankController@myOtherPurchase');
		$router->post('/enroll/{content_id}', 'ContentBankController@enroll');

		// $router->get('/view_ordered_content/{id}', 'Learner\ContentBankController@details');
		// $router->post('/review-content/{id}', 'Learner\RateReviewController@store');
		// $router->get('/view-reviews/{id}', 'Learner\RateReviewController@reviews');
		// $router->get('/view-recomendations','Learner\ContentBankController@recomendations');
		// $router->post('/quiz-submit/{id}', 'Learner\ContentBankController@submit');
 
 
	});

	$router->group(['prefix' => 'question-bank'], function () use ($router) {
		
	    $router->get('/view', 'QuestionController@index');
	    $router->post('/create', 'QuestionController@store');
	    $router->get('/view/{id}', 'QuestionController@show');
	    $router->post('/create-folder', 'QuestionController@create_category');
	    $router->put('/update/{id}', 'QuestionController@update');
	    $router->delete('/delete/{id}', 'QuestionController@destroy');
	    $router->delete('/delete-folder/{id}', 'QuestionController@deleteFolder');
	    $router->get('/view-folder', 'QuestionController@folder_category');

	    $router->post('/search-folder', 'QuestionController@folderCategorySearch');

	    $router->get('/folders', 'QuestionController@folder');
	    $router->put('/folder-update/{id}', 'QuestionController@updatefolder');
	    $router->post('/view-folder-question', 'QuestionController@folderQuestion');
	});


