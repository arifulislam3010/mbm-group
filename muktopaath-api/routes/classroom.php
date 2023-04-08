<?php
    
    // frontend start ==================== >
 
    // frontend end ==================== >
    
    $router->group(['prefix' => 'class'],function() use ($router){

        $router->get('/view', 'CourseController@index');
        $router->get('view-details/{id}', 'FrontendController@details');
        $router->post('/create', 'CourseController@store');
        $router->put('/update', 'CourseBatchController@update');
        //$router->delete('/delete/{id}', 'CourseController@destry');
        $router->delete('/delete/{id}', 'CourseController@destroy');
        $router->get('/show/{id}', 'CourseBatchController@find');

    }); 

    $router->group(['prefix' => 'api_configure'],function() use ($router){
        
        $router->post('/store', 'SyllabusController@configure_api');
        $router->get('/check', 'SyllabusController@check_configuration');
    });

    $router->group(['prefix' => 'timeline'],function() use ($router){
        
        $router->get('/view/{id}', 'TimelineController@show');
        $router->get('/view', 'TimelineController@view');
        $router->post('/create', 'TimelineController@store');
        $router->put('/update/{id}', 'TimelineController@update');
        $router->delete('/delete/{id}', 'TimelineController@delete');
        $router->delete('/delete-comment/{id}', 'TimelineController@deleteComment');
        $router->post('/post-comment', 'TimelineCommentsController@store');
        $router->put('/update-comment', 'TimelineCommentsController@commentUpdate');
    });

    // people's route moved into package/course
    Route::group(['prefix' => 'people'],function(){

        Route::get('/view/{id}', 'CourseBatchController@getRestrictedUser');
        Route::post('/create', 'CourseBatchController@addRestrictedUser');
        Route::get('/joining_list/{id}', 'CourseBatchController@joininglist');
        Route::put('/update', 'CourseBatchController@updateRestrictedUser');
        Route::delete('delete/{id}', 'CourseBatchController@deleteRestrictedUser');
        Route::post('create-teacher', 'CourseBatchController@addCsvRestrictedUser');
        Route::post('create-student', 'CourseBatchController@addCsvRestrictedUser');
        Route::get('/enroll-send-token', 'RestrictedUserEnrollController@sendToken');
        Route::get('/share-course', 'RestrictedUserEnrollController@shareRestrictedCourse');
    });

    $router->group(['prefix' => 'evaluation'],function() use ($router){

        $router->get('/results/{id}', 'SyllabusController@results');
        $router->get('/user-answers/{id}', 'SyllabusController@useranswers');
        $router->put('/update-user-answers/{id}', 'SyllabusController@updatemarks');

    });

    $router->group(['prefix' => 'dashboard'],function() use ($router){
        $router->get('/header-stats', 'DashboardController@view');
    });

    $router->group(['prefix' => 'schedule'],function() use ($router){
        //single session works
        $router->get('/view-one/{id}', 'SyllabusController@viewOne');
        $router->post('/create-one', 'SyllabusController@createOne');
        $router->put('/update-one', 'SyllabusController@updateOne');
        $router->put('/upload-class-recording', 'SyllabusController@classRecording');
        $router->delete('/delete-one/{id}', 'SyllabusController@deleteOne');
        //single classwork 
        $router->get('/classwork-view-one/{id}', 'SyllabusController@classwork_viewOne');
        $router->post('/classwork-create-one', 'SyllabusController@classwork_createOne');
        $router->put('/classwork-update-one', 'SyllabusController@classwork_updateOne');
        $router->delete('/classwork-delete-one/{id}', 'SyllabusController@classwork_deleteOne');
        //ends here
        $router->get('/view-syllabus/{id}', 'SyllabusController@index');
        //new
        $router->get('/classworks/{batch_id}', 'SyllabusController@classworks');
        //new ends here
        $router->put('/update-syllabus/{id}', 'SyllabusController@update');
        $router->get('/view-syllabus-all', 'SyllabusController@all');
        $router->get('/next-class/{batch_id}', 'SyllabusController@nextclass');
        $router->get('/view-syllabus-child', 'SyllabusController@child');
        $router->get('/view-discussions', 'DiscussionController@index');
        $router->post('/put-chat', 'DiscussionController@store');
        $router->get('/lesson/{lesson_id}', 'Learner\LessonController@show');
        $router->get('/lesson-details/{lesson_id}', 'Learner\LessonController@show_details');
    });


    $router->group(['prefix' => 'reviews'],function() use ($router){

        $router->post('/create/{id}', 'Learner\RateReviewController@store');
        $router->get('/view/{id}', 'Learner\RateReviewController@reviews');
    }); 


    $router->group(['prefix' => 'attendance'],function() use ($router){

        $router->post('/store/{syllabus_id}', 'AttendanceController@store');//learner purpose
        $router->post('/update_attendance/{syllabus_id}', 'AttendanceController@update');//teacher purpose
        $router->get('/view/{syllabus_id}', 'AttendanceController@index');
        $router->get('/download/{syllabus_id}', 'AttendanceController@download');
        
    });






    //      
    //         
    //         $router->get('/course/all', 'FrontendController@all');



    
    // //=========== syllabus start ====================
  
    // //=========== syllabus end ====================


    // //enrollments

    //         $router->get('batch/units/{batch_id}', 'UnitLessonController@units');
    //         $router->get('batch/lessons/{batch_id}/{unit_id}', 'UnitLessonController@lesson_of_unit');
    //         $router->post('/batch/units/create', 'UnitLessonController@create_unit');
    //         $router->post('/batch/lessons/create', 'UnitLessonController@create_lessons');
    //         $router->get('/categories', 'CourseController@category');