<?php

Route::group(['middleware' => 'authUser'] , function() {
    //User Course Enrollment
      Route::get('enroll-course-details/{id}' , 'CourseController@EnrollCourseDetails');
      Route::get('enrolled/check/{id}','CourseController@EnrollmentCheck');
      Route::post('my-courses-enrollment-list' , 'CourseController@myCourseEnrollmentList');
      Route::post('my-courses-enrollment-list-change' , 'CourseController@myCourseEnrollmentListChange');
    
    // order 
    
    Route::post('my-purchase' , 'Order\OrderController@purchase');
    Route::get('my-purchase/{id}' , 'Order\OrderController@purchaseDetails');
    Route::post('my-purchase/payment' , 'Order\OrderController@purchasePayment');
    
    Route::get('my-referral' , 'Order\OrderController@referral');
    
    //App
    Route::get('content-view/{id}' , 'CourseController@content');
    Route::post('syllabus-submission' , 'CourseController@syllabusSubmission');
    Route::post('syllabus-completeness-submission' , 'CourseController@Completeness');
    Route::post('journey/status/app/{id}' , 'CourseController@journeyStatusUpdateApp');
    
    Route::post('journey/status/{id}' , 'CourseController@journeyStatusUpdate');
    Route::get('journey/status/{id}' , 'CourseController@journeyStatusGet');
    Route::post('course/completeness/{id}' , 'CourseController@courseCompletenessUpdate');
    Route::get('course/completeness/{id}' , 'CourseController@courseCompletenessGet');
    
    Route::get('course/wishlists/{id}' , 'WishlistController@wishlists');
    Route::get('my/wishlist','WishlistController@wishlistall');
    Route::get('wishlist/check/{id}','WishlistController@wishlistCheck');

    //Course Enrollment
    Route::post('course-enrollment' , 'CourseController@courseEntrollment');
    Route::post('course-enrollmentv3' , 'CourseController@courseEntrollmentV3');
    
    Route::post('course-enrollment/attachment' , 'CourseController@courseEntrollmentAttachment');
    Route::post('course-enrollment/attach' , 'CourseController@attach');
    //  course ratting
    Route::post('rating/{id}' , 'RatingController@CourseRatingSubmit');    
    //Course Assignment 
    Route::post('assignment/load_peer_review' , 'AssignmentController@load_for_peer_review');
    Route::post('assignment/peer_review_submit' , 'AssignmentController@peer_review_submit');

    // Course discussion
    Route::post('discussion/{id}', 'DiscussController@submit');
    Route::post('discussion/get/{id}', 'DiscussController@get_data');
    Route::post('discussion/get_all/{id}', 'DiscussController@get_all_data');
    Route::post('discussion/reply/{id}', 'DiscussController@reply_submit');

    // Course content feedback
    Route::post('content/feedback/{course_batch_id}' , 'CourseController@courseContentFeedbackStore');
    Route::get('content/feedback/{id}/{unit_id}/{lesson_id}' , 'CourseController@courseContentFeedback');



    // contribution
    // Route::get('contributions' , 'UserRoleAsController@index'); 
    // Route::get('contributor/{type}' , 'UserRoleAsController@apply'); 
    // Route::post('apply/course/contribution' , 'UserRoleAsController@applyCourse'); 
    // Route::post('apply/course/contribution/check' , 'UserRoleAsController@applyCourseCheck');
    
    // disscuss
    // Route::post('discussion/{id}', 'DiscussController@submit');
    // Route::post('discussion/get/{id}', 'DiscussController@get_data');
    // Route::post('discussion/get_all/{id}', 'DiscussController@get_all_data');

    //CalenderEvents
    // Route::post('calender/event', 'CalendarEventController@store')->name('calendar_event_store'); 
    // Route::post('calender/event/update/{id}', 'CalendarEventController@update')->name('calendar_event_update');
    // Route::post('calender/event/get', 'CalendarEventController@index')->name('calendar_event_get'); 
    // Route::post('calender/event/course', 'CalendarEventController@get_course')->name('calendar_event_course');

    // Route::post('discussion/reply/{id}', 'DiscussController@reply_submit');
    
    // Blog Api
    
    // Route::get('blog/post/get', 'Blog\BlogPostController@index'); 
    // Route::post('blog/post/store', 'Blog\BlogPostController@store'); 
    // Route::put('blog/post/update', 'Blog\BlogPostController@update'); 
    // Route::delete('blog/post/delete/{id}','Blog\BlogPostController@delete');
    // // Like Dislike
    // Route::post('blog/post/like/', 'Blog\BlogPostLikeController@index');
    // Route::post('blog/post/comment/', 'Blog\BlogPostCommentController@store');

    // Route::post('discussion/reply/{id}', 'DiscussController@reply_submit');
   
    
    // Route::get('tutorials/get','TutorialController@index');
    // Route::post('tutorials/store','TutorialController@store');
    // Route::put('tutorials/update','TutorialController@update');
    // Route::delete('tutorials/delete/{id}','TutorialController@delete');
   

    
    // //Message
    // Route::get('notification/all' , 'NotificationController@userInbox');
    // Route::post('notification/reply' , 'NotificationController@replyUser');
    // Route::post('notification/read' , 'NotificationController@readStatus');
    // Route::get('gamification' , 'GamificationController@badges');
    // Route::post('gamification/course' , 'GamificationController@courses');
    // Route::get('gamification/points' , 'GamificationController@points');
    // Route::get('gamification/point/{id}' , 'GamificationController@pointDetails');
    // Route::get('gamification/badges' , 'GamificationController@allBadges');

    // // user setting
    // Route::get('verify/{type}' , 'UserSettingController@verify');
    // Route::get('change/{type}' , 'UserSettingController@change');
    // Route::get('verify-token/{type}/{token}' , 'UserSettingController@verifyToken');

    // //Favorite User Category List
    // Route::post('favorite-user-cat-list', 'FavoriteCategoryListController@index');

    // Route::get('user' , 'UserSettingController@user');
    
    // //BMDC
    // Route::post('bmdc-check', 'OutsideRestApiController@checkData');

    //  Route::get('survey-update' , 'UserSettingController@survey');

   
   // Route::get('/partner/course/list','Course\PartnerCourseController@course_list');
   // Route::get('/partner/course/show/{course_id}','Course\PartnerCourseController@course_show');
   // Route::get('/partner/course/{course_id}/batches','Course\PartnerCourseController@course_batch')->middleware(['partner']);
   // Route::post('/partner/course/enrollment/{batch_id}','Course\PartnerCourseController@course_enrollment')->middleware(['partner']);
   // Route::get('/partner/course/enrollment/{access_code}','Course\PartnerCourseController@course_enrollment_accesscode');
   // Route::post('/partner/course/enrollment/certificate-approved/{batch_id}','Course\PartnerCourseController@course_enrollment_certificate_approved');
   // Route::get('/partner/course/batch-wise/{batch_id}/participant','Course\PartnerCourseController@batch_participant');
   // Route::get('/partner/course/batch/{batch_id}/units','Course\PartnerCourseBatchController@courseBatchUnit');
   // Route::get('/partner/course/batch/{order_number}/lessons','Course\PartnerCourseBatchController@courseBatchUnitLesson');
   // Route::post('/partner/course/batch/{batch_id}/certification','Course\PartnerCourseBatchController@courseBatchCertification');
   // Route::get('/partner/certificate/eligible/participants/list/{batch_id}/','Course\PartnerCourseBatchController@courseCompleteUserList');
   // Route::get('/partner/course/user/list/{batch_id}/certificate','Course\PartnerCourseBatchController@courseCertificateList');

});

  Route::get('/user-info/{username}', 'UserController@info');
  Route::get('reviews/{course_id}' , 'RatingController@course_reviews');

  Route::get('/institution-info/{username}', 'UserController@institutionsInfos');
  Route::get('/user-courses/{username}', 'UserController@user_courses');

  Route::get('/batch/restricted-user-enroll', 'RestrictedUserEnrollController@enrollCourse');

  Route::get('enrollment-list/{username}' , 'CourseController@UserEnrollmentList');

  /* --- for pyament */
  Route::get('payment/ekpay/{batch_id}/{user_id}','Order\OrderController@ekpayPayment');
  Route::get('paymentStatus/{id}/ekpay/success','Order\OrderController@ekpaySuccess');
  Route::get('paymentStatus/{id}/ekpay/fail','Order\OrderController@ekpayFail');
  Route::get('paymentStatus/{id}/ekpay/cancel','Order\OrderController@ekpayCancel');
    ////-------from client credential routes--------------////


    //     Route::post('login/sso-mobile' , 'SsoController@loginMobile');
    // // Route::get('test/access/token' , function(){return 1;});
    // Route::post('old_user_check', 'UserController@oldUserCheck');
    // Route::post('login' , 'UserController@login');
    // Route::post('sso-login-user' , 'SsoController@login');
    // Route::post('sso-login' , 'SsoController@loginapi');
    // //Register
    // Route::post('registration' , 'UserController@registration');
    // Route::get('email/unique' , 'UserController@emailUnique');
    // Route::get('phone/unique' , 'UserController@phoneUnique');
    // Route::get('emailorphone/check' , 'UserController@emailOrPhoneCcheck');
    // Route::get('password/check' , 'UserController@passwordCheck');
    // Route::get('user/profile/{username}','UserController@profile');
    // Route::post('user/profile/enrolle/course','UserController@profileEnrCourse');
    // Route::post('user/profile/faciliate/course','UserController@profileFacCourse');
    // //Courses
    // Route::get('course-categories','CategoryController@index');
    
    Route::get('details/{id}','CourseController@courseDetails');
    Route::get('course-preview/{id}/{token}','CourseController@courseDetailsPreview');
    Route::post('courses','CourseController@allCourses');
    Route::post('courses-change','CourseController@allCoursesChange');
    // Route::get('partners','PartnerController@allPartner');
    // Route::get('partner/{username}','PartnerController@partner');
    
    //Course Checkout
    Route::get('checkout-list' , 'CourseController@checkoutList');

    // forgot 
    // Route::post('forgot-password/Check' , 'Auth\ForgotPasswordController@CheckEmailPhone');
    // Route::post('resend/verification' , 'UserSettingController@ResendVarification');
    // Route::post('phone/verification' , 'UserSettingController@PhoneVerification');
    // Route::get('secret-login/{username}' , 'UserSettingController@SecretLogin');

    // Route::post('course-search-solr','Solr\SolrController@course');
    // Route::post('course-search-key','Solr\SolrController@searchkey');
    
    Route::get('course-search-solr','Solr\SolrController@course');
    Route::post('course-search-key','Solr\SolrController@searchkey');
    Route::get('search-suggest','Solr\SolrController@solrSuggester');
    Route::get('search-filter','Solr\SolrController@searchFilter');
    Route::get('recommendation','Solr\SolrController@recommendation');

    // old site courses
    Route::post('old_courses', 'CourseController@loadOldCourses');

    // basic data route
    // Route::get('statistic','BasicController@statistic'); 
    // Route::get('basic-info','BasicController@maininfo'); 
    // Route::get('languanges','BasicController@languanges'); 
    // Route::get('languange/{id}','BasicController@languange'); 
    // Route::get('division','BasicController@division'); 
    // Route::get('district/{id}','BasicController@district'); 
    // Route::get('sub/district/{id}','BasicController@subdistrict');
    // Route::get('education/level','BasicController@educationLevel');
    // Route::get('degree/{id}','BasicController@degree');
    // Route::post('tags/search' , 'BasicController@tags');
    // // Course certificate
    // Route::post('certificate/search' , 'CertificateController@search');
    Route::post('search','CourseController@search');
    // Route::post('course/search/change','CourseController@searchchange');
    Route::get('gamification-check','CourseController@gamificationcheck');

    // //article
    // Route::get('articles', 'ArticleController@index');
    // Route::get('popup', 'ArticleController@popup');
    // Route::post('articles/{id}', 'ArticleController@show');
    // Route::get('article-details/{id}', 'ArticleController@details'); 
    // Route::post('tags', 'Blog\BlogPostController@get_tags')->name('blog_post_get_tags');
    //Tutorial
   //  Route::get('tutorials/', 'TutorialController@index');
   //  Route::get('tutorial-details/{id}', 'TutorialController@details');
    
   
   // //Blog Api
   //  Route::post('blogs/', 'BlogController@index');
   //  // Route::post('populerblogs/', 'BlogController@populerblogs');
   //  Route::get('blogs-details/{id}', 'BlogController@details');
    



   //  // Restricted course enrollement
   //  Route::post('restricted_course_enrollement', 'RestrictedCourseEnrollementController@index');

   //  Route::get('leaderboard' , 'GamificationController@leaderboard');
   //  Route::post('leaderboard' , 'GamificationController@leaderboard');

   //  Route::post('feedback' , 'HomeController@feedback');


   //  Route::post('certificate-check' , 'CertificateController@check');
   //  // tag search 
    
   //  Route::post('gamification/useage-validity' , 'Points\PointUsageController@useage_validity');
   //  Route::post('gamification/use' , 'Points\PointUsageController@usepoint');



    ///---------client credential routes ends----------------//
