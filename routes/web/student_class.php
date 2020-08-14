<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'class', 'middleware' => ['auth:employee', 'roles:developer|administrator|guru']], function () {
  Route::get('/', 'StudentClassController@index')->name('class.index');
  Route::get('/edit', 'StudentClassController@edit')->name('class.edit');
  Route::post('/json', 'StudentClassController@datatable')->name('class.json');
  Route::post('/json/student', 'StudentClassController@datatableStudent')->name('class.json.student');
  Route::post('/subject', 'StudentClassController@getSubject')->name('class.subject');
  Route::post('/create', 'StudentClassController@store')->name('class.create');
  Route::put('/update', 'StudentClassController@update')->name('class.update');
  Route::delete('/delete', 'StudentClassController@destroy')->name('class.delete');
  Route::delete('/delete/student', 'StudentClassController@destroyStudent')->name('class.delete.student');
});

Route::group(['prefix' => 'class', 'middleware' => ['auth:student,employee']], function () {
  Route::get('/get/user/class', 'StudentClassController@getClassByUser')->name('class.get.class');
  Route::get('/get', 'StudentClassController@getClassById')->name('class.get');
  Route::post('/join/class', 'StudentClassController@studentJoinClass')->name('class.join.class');
});
