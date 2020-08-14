<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'task', 'middleware' => ['auth:student,employee']], function () {
  Route::get('/student/get', 'TaskController@getStudentByClass')->name('task.get.class');
  Route::get('/get', 'TaskController@getTaskByClass')->name('task.get');
  Route::get('/edit', 'TaskController@edit')->name('task.edit');
  Route::get('/file/download/{id}', 'TaskController@downloadTaskFile')->name('task.download');
  Route::post('/change/show/score', 'TaskController@showScore')->name('task.show.grade');
  Route::post('/score', 'TaskController@fillStudentScore')->name('task.score');
  Route::post('/student', 'TaskController@getStudentTask')->name('task.student');
  Route::post('/status', 'TaskController@getDataByStatus')->name('task.status');
  Route::post('/all/student/task', 'TaskController@getAllStudentByTask')->name('task.all.student');
  Route::post('/create', 'TaskController@store')->name('task.create');
  Route::post('/send', 'TaskController@sendStudentTask')->name('task.send');
  Route::post('/update', 'TaskController@update')->name('task.update');
  Route::delete('/delete', 'TaskController@destroy')->name('task.delete');
  Route::delete('/cancel', 'TaskController@cancelSendTask')->name('task.cancel');
});
