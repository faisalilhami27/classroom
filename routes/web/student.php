<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'student', 'middleware' => ['auth:employee', 'roles:developer|administrator']], function () {
  Route::get('/', 'StudentController@index')->name('student.index');
  Route::get('/json', 'StudentController@getDataStudent')->name('student.json');
  Route::get('/check/email', 'StudentController@checkEmail')->name('student.email');
  Route::get('/check/phone/number', 'StudentController@checkPhoneNumber')->name('student.phone');
  Route::get('/edit', 'StudentController@edit')->name('student.edit');
  Route::put('/update', 'StudentController@update')->name('student.update');
  Route::delete('/delete', 'StudentController@destroy')->name('student.delete');
});
