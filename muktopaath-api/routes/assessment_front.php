<?php


    $router->get('/course', 'AssessmentFrontController@course');
    $router->get('/course/all', 'AssessmentFrontController@all');
    $router->get('/course/details/{id}', 'AssessmentFrontController@details');
    $router->get('/testpdf', 'AssessmentFrontController@testpdf');
    
    $router->group(['middleware' => ['authUser','checkrole']], function () use ($router) {
        $router->post('calender', 'AssessmentFrontController@calender');
    });

    $router->group(['middleware' => ['authUser','checkrole'],'prefix' => 'course'],function() use ($router){

        $router->get('/lesson/{lesson_id}', 'Learner\LessonController@show');
        $router->get('/lesson-details/{lesson_id}', 'Learner\LessonController@show_details');
        $router->get('/checkprev/{syllabus_id}', 'Learner\LessonController@showstatus');
    });


     
    $router->group(['middleware' => ['authUser','checkrole'],'prefix' => 'batch'],function() use ($router){

        $router->get('/view-enrolled-batches', 'CourseController@enrolled_batches');
        $router->post('enroll/{batch_id}', 'CourseController@enroll');
        $router->post('/create-review/{id}', 'Learner\RateReviewController@store');
        $router->get('/view-reviews/{id}', 'Learner\RateReviewController@reviews');
        $router->get('/view-enroll-details/{enroll_id}', 'Learner\EnrollmentController@details');
        $router->post('/quiz-submit', 'Learner\QuizSubmissionController@store');
        //$router->get('/restricted-user-enroll', 'RestrictedUserEnrollController@enrollCourse');

    });
    
    $router->get('/batch/restricted-user-enroll', 'RestrictedUserEnrollController@enrollCourse');