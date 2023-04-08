<?php



$router->get('/', function () use ($router) {
    return $this->getRouter()->getCurrentRoute()->getPrefix();
    return $results = DB::select("SELECT * FROM test");
    return 'usermanagement';
});   

$router->post('/password/reset_code', 'UserController@sendPasswordResetCode');
$router->post('/verify/resend_code', 'UserController@resendcodeverify');
$router->get('/verification-by-mail', 'UserController@verifyBymail');
$router->put('/verification-by-phone', 'UserController@verifyBySms');
$router->get('/password/reset_code_verification', 'UserController@passwordResetCodeVerify');
$router->put('/password/reset', 'UserController@passwordReset');

//google signin
$router->get('auth/google', 'GoogleController@redirectToGoogle');
$router->get('auth/google/callback', 'GoogleController@handleGoogleCallback');
// $router->put('/', 'UserController@verifyBySms');

$router->post('/user/register', 'UserController@register');
$router->post('/user/partner-register', 'UserController@partner_register');
$router->post('/user/login', 'UserController@login');
$router->post('/user/autologin', 'UserController@autologin');
$router->post('/user/sociallogin', 'UserController@sociallogin');

$router->post('/user/mygov/login', 'MygovController@login');
$router->post('/user/mygov/register', 'MygovController@register');
$router->post('/user/mygov/sendotp', 'MygovController@sendOtp');
$router->post('/user/mygov/forgetpassword', 'MygovController@forgetpassword');

$router->get('institutions/view-types-public', 'InstitutionController@types');

$router->group(['middleware' => ['authUser','checkrole','auth:api']], function () use ($router) {

Route::post('course-search-solr','TeacherVerification@course');
Route::post('course-search-key','TeacherVerification@searchkey');

$router->post('/user/singlelogin', 'UserController@singleLogin');



$router->get('emis','TeacherVerification@emis');
$router->get('pdsuser/{pdsid}','TeacherVerification@pdsifuseduser');

$router->get('course-journey/{pdsid}','TeacherVerification@courseJourney');
$router->get('pdsusercourse/{pdsid}','TeacherVerification@pdsusercourse');
$router->get('pdsverification/{type}/{pdsid}','TeacherVerification@pdsidverification');
$router->get('otpsend/{id}','TeacherVerification@otpsend');
$router->get('otpsend-verify/{otp}/{id}','TeacherVerification@otpsendverify');

$router->post('teacher-verification','TeacherVerification@verification');

$router->get('/coursed/{id}','Api\CourseController@courseDetails');

	$router->post('create-user-by-admin', 'UserController@create_user');

	$router->group(['prefix' => 'user'], function () use ($router) {
  
		$router->get('/profile-info', 'UserController@profile_info');
		$router->get('/training', 'UserController@shift_to_training_module');
		$router->post('/profile_photo', 'UserController@profile_photo');
		$router->delete('/delete/{id}', 'UserController@delete');
		$router->post('/update-profile', 'UserController@update');
		$router->get('/profile-download/{id}', 'UserController@download');
		$router->post('/search', 'UserController@search');
		$router->post('/search_list', 'UserController@search_list');
		$router->put('/approve/{id}', 'UserController@approve');
		$router->put('/block/{id}', 'UserController@block');
		$router->put('/update', 'UserController@updateUserinfo');
		$router->post('/create', 'UserController@store');

		$router->post('/create-new', 'UserController@store');
		$router->get('/view', 'UserController@index');
		$router->get('/view/{id}', 'UserController@showinfo');
		$router->get('/view-info', 'UserController@info');
		$router->post('/switch-account/{institute}', 'UserController@switchaccount');
		$router->post('/switch-role/{role_id}', 'UserController@switchrole');
		$router->post('/logout', 'UserController@logoutApi');
		$router->get('gamification' , 'GamificationController@badges');
		$router->post('gamification/course' , 'GamificationController@courses');
		$router->get('gamification/points' , 'GamificationController@points');
		$router->get('gamification/point/{id}' , 'GamificationController@pointDetails');
		$router->get('gamification/badges' , 'GamificationController@allBadges');

 
	});

	$router->group(['prefix' => 'activity'], function () use ($router) {

		$router->get('/viewlogs/{id}','ActivityLogController@show');
		$router->get('/view','ActivityLogController@index');

});

$router->group(['prefix' => 'feedback'], function () use ($router) {

	$router->get('/view', 'RatingFeedbackController@view');
	$router->post('/create', 'RatingFeedbackController@store');
	$router->put('/update', 'RatingFeedbackController@update');
	$router->put('/approve/{id}', 'RatingFeedbackController@approve');
	
});


$router->group(['prefix' => 'sharing'], function () use ($router) {

	$router->post('/create', 'SharingController@create');
	$router->get('/shared-with-me','SharingController@shared_with_me');

});

$router->group(['prefix' => 'institutions'], function () use ($router) {

	$router->get('/view-types', 'InstitutionController@types');
	$router->get('/view', 'InstitutionController@index');
	$router->get('/view-info/{id}', 'InstitutionController@show');
	$router->get('/view_all', 'InstitutionController@all');
	$router->get('/view-unapproved', 'InstitutionController@unapproved');
	$router->post('/create', 'InstitutionController@store');
	$router->put('/update', 'InstitutionController@update');
	$router->post('approve/{id}', 'InstitutionController@approve');
	$router->post('add-partner-by-admin', 'InstitutionController@addpartner');
	$router->post('create-user-by-admin', 'InstitutionController@create_user');

});

	
$router->group(['prefix' => 'system-user'], function () use ($router) {
	
	$router->get('/view-userrole/{service}/{id}', 'RoleController@show');
	$router->get('/role-access-view/{role}', 'RoleController@specificaccess');
	$router->delete('/delete/{service}/{id}', 'RoleController@delete');
	$router->post('/delete-multiple', 'RoleController@deletemultiple');
	$router->get('/view/{service}', 'RoleController@servicewiserole');
	$router->get('/users-access-given', 'RoleController@access_given');
	$router->post('/view', 'RoleController@UserRole');
	// $router->post('/create-services-users', 'RoleController@UserRoleStore');
	$router->post('/create', 'RoleController@UserRoleStore');
	$router->put('/update', 'RoleController@UserRoleStore');
});

$router->group(['prefix' => 'system-user'], function () use ($router) {
	
	$router->get('/view-userrole/{service}/{id}', 'RoleController@show');
	$router->get('/role-access-view/{role}', 'RoleController@specificaccess');
	$router->delete('/delete/{service}/{id}', 'RoleController@delete');
	$router->post('/delete-multiple', 'RoleController@deletemultiple');
	$router->get('/view/{service}', 'RoleController@servicewiserole');
	$router->get('/users-access-given', 'RoleController@access_given');
	$router->post('/view', 'RoleController@UserRole');
	// $router->post('/create-services-users', 'RoleController@UserRoleStore');
	$router->post('/create', 'RoleController@UserRoleStore');
	$router->put('/update', 'RoleController@UserRoleStore');
});


$router->group(['prefix' => 'blogs'], function () use ($router) {

	$router->get('/view','BlogController@index');
	$router->post('/store','Blog\BlogPostController@store');
	$router->put('/update','Blog\BlogPostController@update');
	$router->put('/publish','Blog\BlogPostController@publish');
	$router->put('/feature','Blog\BlogPostController@feature');
	$router->delete('/delete/{id}','Blog\BlogPostController@delete');

});

$router->group(['prefix' => 'articles'], function () use ($router) {

	$router->get('/view','Articles\ArticleController@index');
	$router->get('/view-categories','Articles\ArticleController@view_categories');
	$router->get('/view/{id}','Articles\ArticleController@details');
	$router->get('/view-categorywise/{id}','Articles\ArticleController@show');
	$router->get('/view-details/{id}','Articles\ArticleController@details');
	$router->post('/store','Articles\ArticleController@store');
	$router->put('/update/{id}','Articles\ArticleController@update');
	$router->put('/publish','Articles\ArticleController@publish');
	$router->delete('/delete/{id}','Articles\ArticleController@delete');

});


$router->group(['prefix' => 'tutorials'], function () use ($router) {

	$router->get('/view','Tutorials\TutorialController@index');
	$router->get('/view-details/{id}','Tutorials\TutorialController@details');
	$router->post('/store','Tutorials\TutorialController@store');
	$router->put('/update/{id}','Tutorials\TutorialController@update');
	$router->put('/publish','Tutorials\TutorialController@publish');
	$router->delete('/delete/{id}','Tutorials\TutorialController@delete');

});


// $router->group(['prefix' => 'blog'], function () use ($router) {
// 	$router->get('post/get', 'Blog\BlogPostController@index')->name('blog_post_get'); 
//     $router->post('post/store', 'Blog\BlogPostController@store')->name('blog_post_store'); 
//     $router->put('post/update', 'Blog\BlogPostController@update')->name('blog_post_update'); 
//     $router->delete('post/delete/{id}','Blog\BlogPostController@delete')->name('blog_post_delete');
//     // Like Dislike
//     $router->post('post/like/', 'Blog\BlogPostLikeController@index')->name('blog_post_like');
//     $router->post('post/comment/', 'Blog\BlogPostCommentController@store')->name('blog_post_comment');
// });

// $router->group(['prefix' => 'tutorial'], function () use ($router) {
// 	$router->get('get','Tutorial\TutorialController@index')->name('tutorial_get');
//     $router->post('store','Tutorial\TutorialController@store')->name('tutorial_store');
//     $router->put('update','Tutorial\TutorialController@update')->name('tutorial_update');
//     $router->delete('delete/{id}','Tutorial\TutorialController@delete')->name('tutorial_delete');
// });



//$router->get('/users/search', 'UserController@search');
    
});



$router->group(['prefix' => 'public/blogs'], function () use ($router) {

	$router->get('/view','BlogController@index');
	$router->get('/view/{id}','BlogController@show');

});

$router->group(['prefix' => 'public/articles'], function () use ($router) {

	$router->get('/view','Articles\ArticleController@index');
	$router->get('/popup_and_news','Articles\ArticleController@popup_and_news');
	$router->get('/view-categories','Articles\ArticleController@view_categories');
	$router->get('/view/{id}','Articles\ArticleController@details');
	$router->get('/view-categorywise/{id}','Articles\ArticleController@show');
	$router->get('/view-details/{id}','Articles\ArticleController@details');

});


$router->group(['prefix' => 'public/tutorials'], function () use ($router) {

	$router->get('/view','Tutorials\TutorialController@index');
	$router->get('/view-details/{id}','Tutorials\TutorialController@details');

});

$router->group(['prefix' => 'public_feedbacks'], function () use ($router) {

	$router->get('/view', 'RatingFeedbackController@view');
	$router->get('/view_all', 'RatingFeedbackController@view_all');
	
});

$router->group(['prefix' => 'institutions'], function () use ($router) {
	
	$router->get('/view_all', 'InstitutionController@all');

});

$router->group(['prefix' => 'partners'], function () use ($router) {
	
	$router->get('/view', 'InstitutionController@partners');

});

$router->group(['prefix' => 'public/institutions'], function () use ($router) {

	$router->get('/view','InstitutionController@public_index');

});