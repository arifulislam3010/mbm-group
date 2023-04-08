<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'total'],function() {
    Route::get('/verified-users', 'DashboardController@totalVerifiedUsers');
    Route::get('/learner', 'DashboardController@totalLearner');
    Route::get('/course', 'DashboardController@totalCourse');
    Route::get('/upcoming-course', 'DashboardController@upcomingCourse');
    Route::get('/exam', 'DashboardController@totalExam');
    Route::get('/certificate', 'DashboardController@totalCertificate');
    Route::get('/pending_class_work', 'DashboardController@pendingClassWork');
    Route::get('/learner_by_gender', 'DashboardController@learnerByGender');
});


Route::get('/learnTrack', 'DashboardController@learntrack');
Route::get('/pass-rate', 'DashboardController@passRate');
Route::get('/course-completion-rate', 'DashboardController@courseCompletionRate');
Route::get('/upcoming', 'DashboardController@upcoming');
Route::get('/pending-task', 'DashboardController@pendingTask');
Route::get('/enrollment', 'DashboardController@enrollment');