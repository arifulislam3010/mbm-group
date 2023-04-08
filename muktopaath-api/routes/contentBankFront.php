<?php

 
	// frontend start ==================== >
	
	$router->get('/content', 'FrontendController@content');
	$router->get('/content/all', 'FrontendController@all');
	$router->get('/content/details/{id}', 'FrontendController@details');
 
	// frontend end ==================== >
	$router->group(['prefix' => 'content'],function() use ($router){

		$router->post('/enroll/{content_id}', 'Learner\ContentBankController@enroll');
		$router->get('/view_ordered_content/{id}', 'Learner\ContentBankController@details');
		$router->post('/review-content/{id}', 'Learner\RateReviewController@store');
		$router->get('/view-reviews/{id}', 'Learner\RateReviewController@reviews');
		$router->get('/view-recomendations','Learner\ContentBankController@recomendations');
		$router->post('/quiz-submit/{id}', 'Learner\ContentBankController@submit');

	}); 

