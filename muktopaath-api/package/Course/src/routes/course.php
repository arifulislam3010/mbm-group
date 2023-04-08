<?php
    
    use Illuminate\Support\Facades\Route;

    Route::group(['prefix' => 'course'],function() {

        Route::get('/view', 'Course\CourseController@index');
        Route::get('/viewcourse', 'Course\CourseController@viewcourse');
        Route::get('view-details/{id}', 'Course\FrontendController@details');
        Route::post('/create', 'Course\CourseController@store');
        Route::delete('/delete/{id}', 'Course\CourseController@destry');
        Route::put('/update-publish-feature', 'Course\CourseController@publishFeature');
        Route::get('/lesson/{lesson_id}', 'Course\Learner\LessonController@show');
        Route::get('/lesson-details/{lesson_id}', 'Course\Learner\LessonController@show_details');
        Route::get('recommendation','Muktopaath\Course\Http\Controllers\Api\Solr\SolrController@recommendation');
    });  

Route::group(['prefix' => 'dashboard'], function () {

    Route::get('/completeness', 'Dashboard\ReportController@completeness');
    Route::get('/download', 'Dashboard\ReportController@download_report');
    Route::get('/district_data', 'Dashboard\ReportController@district_data');
    Route::get('/all_data', 'Dashboard\ReportController@all_data');
    Route::get('/trendline', 'Dashboard\ReportController@trendline');
    Route::get('/donut', 'Dashboard\ReportController@donut');
    Route::get('/stat_data', 'Dashboard\ReportController@stat_data');
    Route::get('/total-learners-location-list', 'Dashboard\ReportController@districtWiseLearner');
    Route::get('/divisions', 'Dashboard\ReportController@divisions');
    Route::get('/districts/{division_id}', 'Dashboard\ReportController@districts');
    Route::get('/upazilas/{district_id}', 'Dashboard\ReportController@upazilas');
    Route::get('/header-stats', 'Course\DashboardController@view');
});



    Route::group(['prefix' => 'template'],function() {
        Route::post('certificate-store', 'Course\CertificateController@store');
        //certificate template list
        Route::get('/view', 'Course\CertificateController@index');
        //certificate template by id
        Route::get('/view/{id}', 'Course\CertificateController@show');
    });
 

    Route::group(['prefix' => 'reports'],function() {

        Route::get('learners', 'Course\ReportController@learners');
        Route::get('learner-marksheet/{batch_id}/{enrollment_id}', 'Course\ReportController@marksheet');
        Route::get('learners-report', 'Course\ReportController@learners_report');
        Route::get('learner-courses/{user_id}', 'Course\ReportController@learner_courses');
        Route::get('learner-course-report/{user_id}', 'Course\ReportController@learner_course_report');
        Route::get('/learner-stats', 'Course\ReportController@learner_stats');
        Route::get('/learner-stat/{user_id}', 'Course\ReportController@learner_stat');
        Route::get('courses', 'Course\ReportController@courses');
        Route::get('courses-report', 'Course\ReportController@courses_report');
        Route::get('course-users/{batch_id}', 'Course\ReportController@course_users');
        Route::get('course-users-report/{batch_id}', 'Course\ReportController@course_users_report');
        Route::get('/course-stats', 'Course\ReportController@course_stats');
        Route::get('/course-stat/{batch_id}', 'Course\ReportController@course_stat');

    });




    Route::group(['prefix' => 'attendance'],function() {

        Route::post('/store/{syllabus_id}', 'Course\AttendanceController@store');//learner purpose
        Route::post('/update_attendance/{syllabus_id}', 'Course\AttendanceController@update');
        Route::get('/view/{syllabus_id}', 'Course\AttendanceController@index');
        Route::get('/download/{syllabus_id}', 'Course\AttendanceController@download');
        
    });

    Route::group(['prefix' => 'accounts'],function() {
        
        Route::get('/batch-payments', 'Course\AccountsController@batch_payments');
        Route::post('/request-payment', 'Course\AccountsController@request');
        Route::get('/view_all_requests', 'Course\AccountsController@view_all_requests');
        Route::get('/approve_request/{id}', 'Course\AccountsController@approve');
        Route::get('/reject_request/{id}', 'Course\AccountsController@reject');
        Route::get('/delete_request/{id}', 'Course\AccountsController@delete');
        Route::get('overall_transactions', 'Course\AccountsController@overall_transactions');
        Route::get('/check-status/{batch_id}', 'Course\AccountsController@payment_status');

    });


    Route::group(['prefix' => 'transactions'],function() {
        
        Route::post('/create', 'Course\TransactionController@create');
        Route::delete('/delete/{id}', 'Course\TransactionController@delete');

    });

    Route::group(['prefix' => 'evaluation'],function() {

        Route::get('/results/{id}', 'Course\SyllabusController@results');
        Route::get('/performance-stats/{id}', 'Course\DashboardController@performance_stats');
        Route::get('/user-answers/{id}', 'Course\SyllabusController@useranswers');
        Route::put('/update-user-answers/{id}', 'Course\SyllabusController@updatemarks');

    });


    Route::group(['prefix' => 'batch'],function() {

        Route::get('/view/{id}', 'Course\CourseBatchController@index');
        Route::post('/create/{id}', 'Course\CourseBatchController@add');
        Route::put('/update', 'Course\CourseBatchController@update');
        Route::put('/featured-orderise', 'Course\CourseBatchController@featured_orderise');
        Route::get('/find/{id}', 'Course\CourseBatchController@find');
        Route::post('/clone/{id}', 'Course\CourseBatchController@clone');
        Route::get('/transaction_report/{batch_id}', 'Course\CourseBatchController@transactionReport');
        Route::get('/learner_report/{batch_id}', 'Course\CourseBatchController@learnerReport');
        Route::get('/evaluation-results/{id}', 'Course\SyllabusController@results');
        Route::put('/increase_attempt', 'Course\SyllabusController@increase_attempt');
        Route::post('/manual_mark', 'Course\SyllabusController@manual_mark');
        Route::get('/evaluation/user-answers/{id}', 'Course\SyllabusController@useranswers');
        Route::delete('/delete/{id}', 'Course\CourseBatchController@destroy');
        Route::put('/update-publish-feature', 'Course\CourseBatchController@publishFeature');
        Route::get('/view-restricted-user/{id}', 'Course\CourseBatchController@getRestrictedUser');
        Route::post('/create-restricted-user', 'Course\CourseBatchController@addRestrictedUser');
        Route::put('/update-restricted-user', 'Course\CourseBatchController@updateRestrictedUser');
        Route::delete('delete-restricted-user/{id}', 'Course\CourseBatchController@deleteRestrictedUser');
        Route::post('create-csv-restricted-user', 'Course\CourseBatchController@addCsvRestrictedUser');
        Route::get('/view-enrolled-batches', 'Course\CourseController@enrolled_batches');
        Route::get('/download-idcard/{batch_id}', 'Course\CourseBatchController@idcards');
        Route::get('/enrolled_users/{batch_id}', 'Course\CourseBatchController@enrolled_users');
        Route::post('enroll/{batch_id}', 'Course\CourseController@enroll');
        Route::post('enroll-by-admin/{batch_id}', 'Course\CourseController@enroll_by_admin');
        Route::post('/create-review/{id}', 'Course\Learner\RateReviewController@store');
        Route::get('/view-reviews/{id}', 'Course\Learner\RateReviewController@reviews');
        Route::get('/view-enroll-details/{enroll_id}', 'Course\CourseBatchController@details');
        Route::post('/quiz-submit/{enroll_id}/{lesson_id}', 'Course\Learner\QuizSubmissionController@store');
        Route::get('/courseworks/{batch_id}', 'Course\SyllabusController@courseworks');
        Route::get('/view-syllabus/{id}', 'Course\SyllabusController@index');
        Route::get('/completed_lesson/{id}', 'Course\SyllabusController@completed_lesson');
        Route::put('/update-syllabus/{id}', 'Course\SyllabusController@update');
        Route::get('/view-syllabus-all', 'Course\SyllabusController@all');
        Route::get('/view-syllabus-child', 'Course\SyllabusController@child');
        Route::get('/discussions-one', 'Course\DiscussionController@index');
        Route::post('/discussions-store', 'Course\DiscussionController@store');
        Route::get('/restricted-user-enroll/send-token', 'Course\RestrictedUserEnrollController@sendToken');
        Route::get('/share/restricted-course', 'Course\RestrictedUserEnrollController@shareRestrictedCourse');
        Route::get('/repeating-class', 'Course\ClassMetaController@reapitingClass');
        Route::put('/approve_enroll/{id}', 'Course\ClassMetaController@update_status_enroll');
        Route::get('/course-certificate/{batch_id}', 'Course\CourseBatchController@certificate_info');
        Route::put('/approve-certificate', 'Course\CourseBatchController@update_certificate_status');
    });
 
    Route::group(['prefix' => 'order'],function() {
        Route::put('/approve-payment', 'Course\OrderPaymentStatusController@updatePaymentStatus');
    }); 

    Route::group(['prefix' => 'people'],function(){

        Route::get('/view/{id}', 'Course\CourseBatchController@getRestrictedUser');
        Route::post('/create', 'Course\CourseBatchController@addRestrictedUser');
        Route::put('/update', 'Course\CourseBatchController@updateRestrictedUser');
        Route::delete('delete/{id}', 'Course\CourseBatchController@deleteRestrictedUser');
        Route::post('create-teacher', 'Course\CourseBatchController@addCsvRestrictedUser');
        Route::post('add-people-info', 'Course\TrainingController@add_people_info');
        Route::put('update-people-info/{id}', 'Course\TrainingController@update_people_info');
        Route::post('create-student', 'Course\CourseBatchController@Course\addCsvRestrictedUser');
        Route::put('edit-name', 'Course\CourseBatchController@edit_name');
        Route::get('/enroll-send-token', 'Course\RestrictedUserEnrollController@sendToken');
        Route::get('/share-course', 'Course\RestrictedUserEnrollController@shareRestrictedCourse');
    });
