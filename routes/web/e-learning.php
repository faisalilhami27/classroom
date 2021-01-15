<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'material', 'middleware' => ['auth:employee', 'roles:developer|guru']], function () {
  Route::get('/', 'MaterialController@index')->name('material.index');
  Route::get('/edit', 'MaterialController@edit')->name('material.edit');
  Route::get('/get/subject', 'MaterialController@getSubject')->name('material.get.subject');
  Route::post('/json', 'MaterialController@datatable')->name('material.json');
  Route::post('/create', 'MaterialController@store')->name('material.create');
  Route::post('/update', 'MaterialController@update')->name('material.update');
  Route::delete('/delete', 'MaterialController@destroy')->name('material.delete');
});

Route::group(['prefix' => 'learning', 'middleware' => ['auth:employee']], function () {
  Route::get('/', 'HomeController@index')->name('learning.index');
});

Route::group(['prefix' => 'e-learning', 'middleware' => ['auth:employee,student']], function () {
  Route::get('/get/material', 'LearningController@getMaterialByClass')->name('learning.get.material');
  Route::get('/load/discussion', 'DiscussionController@loadDiscussion')->name('learning.load.discussion');
  Route::get('/load/answer/discussion', 'DiscussionController@loadAnswerDiscussion')->name('learning.load.answer.discussion');
  Route::post('/discussion', 'DiscussionController@makeDiscussion')->name('learning.discussion');
  Route::put('/update/discussion', 'DiscussionController@updateDiscussion')->name('learning.update.discussion');
  Route::delete('/delete/discussion', 'DiscussionController@destroyDiscussion')->name('learning.delete.discussion');
  Route::post('/answer/discussion', 'DiscussionController@answerDiscussion')->name('learning.answer.discussion');
  Route::put('/update/answer/discussion', 'DiscussionController@updateAnswerDiscussion')->name('learning.update.answer.discussion');
  Route::delete('/delete/answer/discussion', 'DiscussionController@destroyAnswerDiscussion')->name('learning.delete.answer.discussion');
});
