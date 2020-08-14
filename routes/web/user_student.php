<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user/student', 'middleware' => ['auth:employee', 'roles:developer|administrator']], function () {
  Route::get('/', 'UserStudentController@index')->name('user.student.index');
  Route::get('/edit', 'UserStudentController@edit')->name('user.student.edit');
  Route::post('/json', 'UserStudentController@datatable')->name('user.student.json');
  Route::post('/reset', 'UserStudentController@resetPassword')->name('user.student.reset');
  Route::put('/update', 'UserStudentController@update')->name('user.student.update');
  Route::delete('/delete', 'UserStudentController@destroy')->name('user.student.delete');
});
