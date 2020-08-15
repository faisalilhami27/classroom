<?php

use Illuminate\Support\Facades\Route;

// question bank
Route::group(['prefix' => 'question', 'middleware' => ['auth:employee', 'roles:developer|guru']], function () {
  Route::get('/', 'QuestionBankController@index')->name('question.index');
  Route::get('/edit', 'QuestionBankController@edit')->name('question.edit');
  Route::get('/get/subject', 'QuestionBankController@getSubject')->name('question.get.subject');
  Route::get('/check/answer', 'QuestionBankController@checkAnswer')->name('question.check.answer');
  Route::get('/link/export', 'QuestionBankController@LinkExport')->name('question.link.export');
  Route::get('/export/{level}/{subject}', 'QuestionBankController@export')->name('question.export');
  Route::post('/json', 'QuestionBankController@datatable')->name('question.json');
  Route::post('/upload/image', 'QuestionBankController@uploadImageQuestion')->name('question.upload');
  Route::post('/create', 'QuestionBankController@store')->name('question.create');
  Route::post('/import', 'QuestionBankController@import')->name('question.import');
  Route::post('/store/answer', 'QuestionBankController@storeAnswerQuestion')->name('question.store.answer');
  Route::post('/update', 'QuestionBankController@update')->name('question.update');
  Route::delete('/delete', 'QuestionBankController@destroy')->name('question.delete');
});

// exam rules
Route::group(['prefix' => 'exam/rules', 'middleware' => ['auth:employee', 'roles:developer|guru']], function () {
  Route::get('/', 'ExamRulesController@index')->name('rules.index');
  Route::get('/edit', 'ExamRulesController@edit')->name('rules.edit');
  Route::post('/json', 'ExamRulesController@datatable')->name('rules.json');
  Route::post('/create', 'ExamRulesController@store')->name('rules.create');
  Route::put('/update', 'ExamRulesController@update')->name('rules.update');
  Route::delete('/delete', 'ExamRulesController@destroy')->name('rules.delete');
});

// manage exam
Route::group(['prefix' => 'manage/exam', 'middleware' => ['auth:employee', 'roles:developer|guru']], function () {
  Route::get('/', 'ManageExamController@index')->name('manage.exam.index');
  Route::get('/get/subject/class', 'ManageExamController@getSubjectOrClass')->name('manage.exam.get');
  Route::get('/get/amount', 'ManageExamController@getAmountQuestion')->name('manage.exam.get.amount');
  Route::get('/edit', 'ManageExamController@edit')->name('manage.exam.edit');
  Route::get('/rules', 'ManageExamController@getTextRules')->name('manage.exam.rules');
  Route::get('/detail', 'ManageExamController@show')->name('manage.exam.detail');
  Route::post('/json/student/score', 'ManageExamController@datatableStudentScore')->name('manage.exam.json.student.score');
  Route::post('/json/student', 'ManageExamController@datatableStudent')->name('manage.exam.json.student');
  Route::post('/json', 'ManageExamController@datatable')->name('manage.exam.json');
  Route::post('/temporary/json', 'ManageExamController@showQuestionHaveBeenSavedDatatable')->name('manage.exam.saved.json');
  Route::post('/question/json', 'ManageExamController@showQuestionDatatable')->name('manage.exam.question.json');
  Route::post('/create', 'ManageExamController@store')->name('manage.exam.create');
  Route::post('/create/question', 'ManageExamController@storeAccommodateQuestion')->name('manage.exam.create.question');
  Route::post('/activate/exam', 'ManageExamController@activateExam')->name('manage.exam.activate.exam');
  Route::put('/update', 'ManageExamController@update')->name('manage.exam.update');
  Route::delete('/delete', 'ManageExamController@destroy')->name('manage.exam.delete');
  Route::delete('/delete/temporary/question', 'ManageExamController@destroyTemporaryQuestionById')->name('manage.exam.delete.id');
});

// assign student
Route::group(['prefix' => 'assign/student', 'middleware' => ['auth:employee', 'roles:developer|guru']], function () {
  Route::get('/', 'AssignExamStudentController@index')->name('assign.index');
  Route::post('/json', 'AssignExamStudentController@datatable')->name('assign.json');
  Route::post('/generate', 'AssignExamStudentController@generateRandomQuestion')->name('assign.generate');
  Route::post('/json/search', 'AssignExamStudentController@datatableSearchStudent')->name('assign.json.search');
  Route::post('/json/assign', 'AssignExamStudentController@datatableAssignStudent')->name('assign.json.student');
  Route::post('/create', 'AssignExamStudentController@store')->name('assign.create');
  Route::delete('/delete', 'AssignExamStudentController@destroy')->name('assign.delete');
});

// remedial
Route::group(['prefix' => 'remedial', 'middleware' => ['auth:employee', 'roles:developer|guru']], function () {
  Route::get('/', 'RemedialController@index')->name('remedial.index');
  Route::get('/get/student', 'RemedialController@getStudent')->name('remedial.student');
  Route::post('/json', 'RemedialController@datatable')->name('remedial.json');
  Route::post('/json/student', 'RemedialController@datatableStudent')->name('remedial.json.student');
  Route::post('/json/student/score', 'RemedialController@datatableStudentScore')->name('remedial.json.student.score');
  Route::post('/create', 'RemedialController@store')->name('remedial.create');
});

// supplementary
Route::group(['prefix' => 'supplementary', 'middleware' => ['auth:employee', 'roles:developer|guru']], function () {
  Route::get('/', 'SupplementaryController@index')->name('supplementary.exam.index');
  Route::get('/get/student', 'SupplementaryController@getStudent')->name('supplementary.exam.student');
  Route::post('/json', 'SupplementaryController@datatable')->name('supplementary.exam.json');
  Route::post('/json/student', 'SupplementaryController@datatableStudent')->name('supplementary.exam.json.student');
  Route::post('/json/student/score', 'SupplementaryController@datatableStudentScore')->name('supplementary.exam.json.student.score');
  Route::post('/create', 'SupplementaryController@store')->name('supplementary.exam.create');
});

// progress
Route::group(['prefix' => 'progress', 'middleware' => ['auth:employee', 'roles:developer|guru']], function () {
  Route::get('/', 'ExamProgressController@index')->name('progress.index');
  Route::post('/chart', 'ExamProgressController@chartScoreExam')->name('progress.chart');
  Route::post('/json', 'ExamProgressController@datatable')->name('progress.json');
  Route::post('/json/student', 'ExamProgressController@datatableStudent')->name('progress.json.student');
  Route::post('/json/student/score', 'ExamProgressController@datatableStudentScore')->name('progress.json.student.score');
});

// front end
Route::group(['prefix' => 'exam', 'middleware' => ['auth:employee,student']], function () {
  Route::get('/get', 'ManageExamController@getExamByClass')->name('exam.get');
  Route::get('/remedial', 'RemedialController@getRemedialByClass')->name('exam.remedial');
  Route::get('/supplementary', 'SupplementaryController@getSupplementaryExamByClass')->name('exam.supplementary');
  Route::get('/school/config', 'StudentExamController@getSchoolConfig')->name('exam.config.school');
  Route::get('/count/violation', 'StudentExamController@countViolation')->name('exam.violation');
  Route::get('/rules/page', 'StudentExamController@getExamRules')->name('exam.rules');
  Route::get('/get/question', 'StudentExamController@getQuestionAndAnswer')->name('exam.get.question');
  Route::get('/all/score', 'StudentExamController@getAllScoreStudentExam')->name('exam.all.score');
  Route::get('/score/student', 'StudentExamController@getScoreStudentExam')->name('exam.score');
  Route::get('/score/student/remedial', 'RemedialController@getScoreStudentRemedial')->name('exam.score.remedial');
  Route::get('/score/student/supplementary', 'SupplementaryController@getScoreStudentSupplementaryExam')->name('exam.score.supplementary');
  Route::post('/start', 'StudentExamController@startExam')->name('exam.start');
  Route::post('/add/violation', 'StudentExamController@addViolation')->name('exam.add.violation');
  Route::post('/insert/answer', 'StudentExamController@storeAllStudentAnswer')->name('exam.insert.answer');
  Route::post('/student/answer', 'StudentExamController@answerQuestionStudent')->name('exam.answer');
  Route::post('/student/hesitate/answer', 'StudentExamController@hesitateAnswerQuestionStudent')->name('exam.hesitate.answer');
  Route::post('/chart', 'StudentExamController@chartScoreExam')->name('exam.chart');
});
