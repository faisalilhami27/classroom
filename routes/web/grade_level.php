<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'grade/level', 'middleware' => ['auth:employee', 'roles:developer|administrator']], function () {
  Route::get('/', 'GradeLevelController@index')->name('grade.level.index');
  Route::get('/edit', 'GradeLevelController@edit')->name('grade.level.edit');
  Route::post('/json', 'GradeLevelController@datatable')->name('grade.level.json');
  Route::post('/create', 'GradeLevelController@store')->name('grade.level.create');
  Route::put('/update', 'GradeLevelController@update')->name('grade.level.update');
  Route::delete('/delete', 'GradeLevelController@destroy')->name('grade.level.delete');
});
