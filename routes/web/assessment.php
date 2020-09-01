<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'assessment', 'middleware' => ['auth:employee']], function () {
  Route::get('/task', 'AssessmentController@getTaskAssessment')->name('assessment.task');
  Route::get('/exam', 'AssessmentController@getExamAssessment')->name('assessment.exam');
  Route::get('/export/task/{id}', 'AssessmentController@taskExport')->name('assessment.task.export');
  Route::get('/export/exam/{id}', 'AssessmentController@examExport')->name('assessment.exam.export');
});
