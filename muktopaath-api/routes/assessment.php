<?php
    
    // frontend start ==================== >
 
    // frontend end ==================== >
 

    $router->group(['prefix' => 'course'],function() use ($router){

        $router->get('/view', 'CourseController@index');
        $router->get('view-details/{id}', 'FrontendController@details');
        $router->post('/create', 'CourseController@store');
        $router->delete('/delete/{id}', 'CourseController@destry');
        $router->put('/update-publish-feature', 'CourseController@publishFeature');
        $router->get('/lesson/{lesson_id}', 'Learner\LessonController@show');
        $router->get('/lesson-details/{lesson_id}', 'Learner\LessonController@show_details');
        
    }); 



    $router->group(['prefix' => 'template'],function() use ($router){
        
        $router->post('/view', 'TemplateController@index');
        $router->post('/create', 'TemplateController@store');
        $router->get('/edit/{id}', 'TemplateController@edit');
        $router->put('/update', 'TemplateController@update');
        $router->delete('/delete/{id}', 'TemplateController@destroy');
    });


    $router->group(['prefix' => 'attendance'],function() use ($router){
        $router->post('/store/{syllabus_id}', 'AttendanceController@store');//learner purpose
        $router->get('/view/{syllabus_id}', 'AttendanceController@index');
        $router->get('/download/{syllabus_id}', 'AttendanceController@download');
    });


    $router->group(['prefix' => 'batch'],function() use ($router){

        $router->get('/view/{id}', 'CourseBatchController@index');
        $router->post('/create/{id}', 'CourseBatchController@add');
        $router->put('/update', 'CourseBatchController@update');
        $router->get('/find/{id}', 'CourseBatchController@find');
        $router->get('/evaluation-results/{id}', 'SyllabusController@results');
        $router->delete('/delete/{id}', 'CourseBatchController@destroy');
        $router->put('/update-publish-feature', 'CourseBatchController@publishFeature');
        $router->get('/view-restricted-user/{id}', 'CourseBatchController@getRestrictedUser');
        $router->post('/create-restricted-user', 'CourseBatchController@addRestrictedUser');
        $router->put('/update-restricted-user', 'CourseBatchController@updateRestrictedUser');
        $router->delete('delete-restricted-user/{id}', 'CourseBatchController@deleteRestrictedUser');
        $router->post('create-csv-restricted-user', 'CourseBatchController@addCsvRestrictedUser');
        $router->get('/view-enrolled-batches', 'CourseController@enrolled_batches');
        $router->post('enroll/{batch_id}', 'CourseController@enroll');
        $router->post('enroll-by-admin/{batch_id}', 'CourseController@enroll_by_admin');
        $router->post('/create-review/{id}', 'Learner\RateReviewController@store');
        $router->get('/view-reviews/{id}', 'Learner\RateReviewController@reviews');
        $router->get('/view-enroll-details/{enroll_id}', 'CourseBatchController@details');
        $router->post('/quiz-submit/{enroll_id}/{lesson_id}', 'Learner\QuizSubmissionController@store');
        $router->get('/view-syllabus/{id}', 'SyllabusController@index');
        $router->put('/update-syllabus/{id}', 'SyllabusController@update');
        $router->get('/view-syllabus-all', 'SyllabusController@all');
        $router->get('/view-syllabus-child', 'SyllabusController@child');
        $router->get('/discussions-one', 'DiscussionController@index');
        $router->post('/discussions-store', 'DiscussionController@store');
        $router->get('/restricted-user-enroll/send-token', 'RestrictedUserEnrollController@sendToken');
        $router->get('/share/restricted-course', 'RestrictedUserEnrollController@shareRestrictedCourse');
        $router->get('/repeating-class', 'ClassMetaController@reapitingClass');
    });

     $router->group(['prefix' => 'order'],function() use ($router){
        $router->put('/approve-payment', 'OrderPaymentStatusController@updatePaymentStatus');
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